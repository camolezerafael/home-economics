<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\FromTo;
use App\Models\PaymentType;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use NumberFormatter;

class TransactionController extends CrudController
{
    protected string $defaultPath = 'transaction';

    protected string $basePage = 'Transactions';

    protected string $modelClass = Transaction::class;

    protected string $formRequest = TransactionRequest::class;

    protected $defaultList = false;

    protected $listPath = 'Transactions/Index';

    #[\Override]
    protected function viewAttributes(): array
    {
        return [
            'routePath' => $this->defaultPath,
            'basePage' => __($this->basePage),
            'viewPath' => $this->defaultPath,
            'homePage' => Str::of($this->defaultPath)->headline()->pluralStudly()->snake()->toString(),
            'singularItem' => __('Movement'),
            'pluralItem' => __('Movements'),
        ];
    }

    public function index(Request $request): Response
    {
        $viewAttributes = $this->viewAttributes();

        $filterDate = $request->get('f_date', Carbon::now()->format('Y-m'));
        $filterAccountsCsv = $request->has('f_acc')
            ? (is_array($request->get('f_acc')) ? implode(',', $request->get('f_acc')) : $request->get('f_acc'))
            : 'all';
        $filterPaid = $request->get('f_pay', 'all');

        $filterAccounts = explode(',', $filterAccountsCsv); // array version for the UI

        $model = new $this->modelClass;

        return Inertia::render($this->listPath, [
            'viewAttributes' => fn () => $viewAttributes,
            'comboAccounts' => fn () => self::comboAccounts(true, true, $filterDate),
            'comboPaid' => fn () => self::comboPaid(true),
            'f_date' => fn () => $filterDate,
            'f_acc' => fn () => $filterAccounts,
            'f_pay' => fn () => $filterPaid,
            'items' => fn () => $this->parseCollectionData($model->getAmountsPaginated($filterDate, $filterAccountsCsv, $filterPaid, 10)),
            'monthTotals' => fn () => $model->getMonthTotals($filterDate),
            'monthBalance' => fn () => $model->getFullBalance($filterDate, $filterAccountsCsv, 'all'),
            'finalBalance' => fn () => $model->getFullBalance($filterDate, $filterAccountsCsv, 1, true),
            'estimatedBalance' => fn () => $model->getFullBalance($filterDate, $filterAccountsCsv, 'all', true),
        ]);
    }

    private function parseCollectionData($data)
    {
        $parsedData = [];
        foreach ($data as $type => $item) {
            $parsedData[$type] = TransactionResource::collection($item);
        }

        return $parsedData;
    }

    public function create(): JsonResponse
    {
        $viewAttributes = $this->viewAttributes();
        $item = new $this->modelClass;
        $item->_token = csrf_token();
        $item->_uri = "/$this->defaultPath";

        $isTransfer = request()->get('transaction_type') === Transaction::TYPE_TRANS ||
                      request()->get('type') === Transaction::TYPE_TRANS;

        $aggregates = $this->buildAggregates($isTransfer);

        return response()->json(
            compact(
                'item',
                'viewAttributes',
                'aggregates'
            )
        );
    }

    public function edit($id): JsonResponse
    {
        $viewAttributes = $this->viewAttributes();
        $item = $this->modelClass::query()->where('user_id', Auth::id())->findOrFail($id);
        $item->_token = csrf_token();
        $item->_method = 'PATCH';
        $item->_uri = "/$this->defaultPath/$item->id";

        $isTransfer = $item->isTransfer();

        if ($isTransfer) {
            $pairedTransfer = $item->pairedTransfer();

            $item->amount = abs($item->amount);

            if ($item->date_payment) {
                $item->date_due = $item->date_payment;
            }

            if ($pairedTransfer) {
                // O formulário sempre trabalha com origem (account_id) e destino (account_to_id).
                $isOutgoing = $item->getRawOriginal('amount') < 0;
                $ownAccountId = $item->account_id;

                $item->account_id = $isOutgoing ? $ownAccountId : $pairedTransfer->account_id;
                $item->account_to_id = $isOutgoing ? $pairedTransfer->account_id : $ownAccountId;
            }
        }

        $aggregates = $this->buildAggregates($isTransfer);

        return response()->json(
            compact(
                'item',
                'viewAttributes',
                'aggregates'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $columns = $this->getValidatedData($request);
        $columns['user_id'] = Auth::id();

        DB::transaction(function () use ($request, $columns) {
            $itemFrom = new $this->modelClass;

            if ($request->post('transaction_type') === Transaction::TYPE_TRANS) {
                $itemTo = new $this->modelClass;

                $columns['status'] = 1;
                $columns['date_payment'] = $columns['date_due'];
                $columns = $this->withDefaultTransferPaymentType($columns);

                $to = $columns;

                $columns['amount'] *= -1;
                $to['account_id'] = $columns['account_to_id'];

                $itemTo->fill($to);
                $itemTo->save();
            } elseif ($columns['status']) {
                $columns['date_payment'] = $columns['date_due'];
            }

            $itemFrom->fill($columns);
            $itemFrom->save();
        });

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $item = $this->modelClass::query()->where('user_id', Auth::id())->findOrFail($id);
        $columns = $this->getValidatedData($request);

        if ($item->isTransfer()) {
            return $this->updateTransfer($item, $columns);
        }

        $item->update($columns);

        if ($item->wasChanged('status') && $item->status) {
            $item->date_payment = Carbon::now();
            $item->save();
        }

        return back();
    }

    /**
     * Atualiza as duas pernas da transferência (saída negativa e entrada positiva).
     *
     * @param  array<string, mixed>  $columns
     */
    private function updateTransfer(Transaction $item, array $columns): RedirectResponse
    {
        $amount = abs(floatval($columns['amount']));
        $columns = $this->withDefaultTransferPaymentType($columns);

        $pairedTransfer = $item->pairedTransfer();
        $isOutgoing = $item->getRawOriginal('amount') < 0;

        $accountIdFrom = $columns['account_id'] ?? null;
        $accountIdTo = $columns['account_to_id'] ?? null;
        if (empty($accountIdTo) && $pairedTransfer) {
            $accountIdTo = $pairedTransfer->account_id;
        }

        if (empty($accountIdFrom) || empty($accountIdTo)) {
            return back()->withErrors(['account_id' => 'Both from and to accounts are required for transfers.']);
        }

        $pairedAccountId = $isOutgoing ? $accountIdTo : $accountIdFrom;
        $pairedAccount = $pairedTransfer ? Account::query()->where('user_id', Auth::id())->find($pairedAccountId) : null;

        if ($pairedTransfer && ! $pairedAccount) {
            return back()->withErrors(['account_id' => 'Invalid account for paired transfer.']);
        }

        $columns['status'] = 1;
        $columns['date_payment'] = $columns['date_due'];
        $columns['amount'] = $isOutgoing ? -$amount : $amount;
        $columns['account_id'] = $isOutgoing ? $accountIdFrom : $accountIdTo;

        DB::transaction(function () use ($item, $columns, $pairedTransfer, $pairedAccount, $pairedAccountId, $isOutgoing, $amount) {
            $item->update($columns);

            if (! $pairedTransfer) {
                Log::warning('Paired transfer not found for transaction ID: '.$item->id);

                return;
            }

            $pairedAmount = $isOutgoing ? $amount : -$amount;

            // Escrita direta evita o mutator de amount, que usaria a conta da perna atual.
            DB::table('transactions')
                ->where('id', $pairedTransfer->id)
                ->update([
                    'description' => $columns['description'],
                    'amount' => $pairedAmount * (10 ** ($pairedAccount->decimal_precision ?? 1)),
                    'account_id' => $pairedAccountId,
                    'date_due' => $columns['date_due'],
                    'date_payment' => $columns['date_payment'],
                    'status' => 1,
                    'payment_type_id' => $columns['payment_type_id'],
                ]);
        });

        return back();
    }

    /**
     * @param  array<string, mixed>  $columns
     * @return array<string, mixed>
     */
    private function withDefaultTransferPaymentType(array $columns): array
    {
        if (empty($columns['payment_type_id'])) {
            $defaultPaymentType = PaymentType::firstForUser(Auth::id());
            if ($defaultPaymentType) {
                $columns['payment_type_id'] = $defaultPaymentType->id;
            }
        }

        return $columns;
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    private function buildAggregates(bool $isTransfer): array
    {
        $aggregates = [
            'accounts' => self::comboAccounts(),
            'types' => self::comboTypes(),
        ];

        if (! $isTransfer) {
            $aggregates['paid'] = self::comboPaid();
            $aggregates['from_tos'] = self::comboFromTos();
            $aggregates['categories'] = self::comboCategories();
            $aggregates['payment_types'] = self::comboPaymentTypes();
        }

        return $aggregates;
    }

    public function changeTransactionStatus(int $transaction): JsonResponse
    {
        $transaction = Transaction::query()->where('user_id', Auth::id())->findOrFail($transaction);

        if ($transaction->isTransfer()) {
            return response()->json([
                'status' => $transaction->status,
                'date_payment' => $transaction->date_payment?->format('Y-m-d H:i:s'),
            ]);
        }

        $transaction->updateOrFail([
            'status' => ! $transaction->status,
            'date_payment' => ! $transaction->status ? Carbon::now() : null,
        ]);

        return response()->json([
            'status' => $transaction->fresh()->status,
            'date_payment' => $transaction->fresh()->date_payment?->format('Y-m-d H:i:s'),
        ]);
    }

    public static function comboAccounts($withAll = false, $withBalance = false, $date = null): array
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        $balances = $withBalance ? Transaction::finalBalancesByAccount($date) : null;

        $options = [];
        if ($withAll) {
            $options['all'] = [
                'id' => 'all',
                'name' => __('All'),
                'balance' => $withBalance ? $formatter->format($balances['total']) : 0,
            ];
        }

        foreach (Account::query()->where('user_id', Auth::id())->get() as $row) {
            $options[$row->id] = [
                'id' => $row->id,
                'name' => $row->name,
                'label' => $row->name,
                'balance' => $withBalance ? $formatter->format($balances['accounts'][$row->id] ?? 0.0) : '',
            ];
        }

        return $options;
    }

    public static function comboPaid($withAll = false): array
    {
        $options = $withAll ? ['all' => __('All')] : [];

        return $options + [
            '0' => __('To Pay'),
            '1' => __('Paid'),
        ];
    }

    public static function comboTypes($withAll = false): array
    {
        $options = $withAll ? ['all' => __('All')] : [];

        return $options + [
            Transaction::TYPE_RECEI => __('Receipts'),
            Transaction::TYPE_FIXEX => __('Fixed Expenses'),
            Transaction::TYPE_VAREX => __('Variable Expenses'),
            Transaction::TYPE_PEOPL => __('People'),
            Transaction::TYPE_TAXES => __('Taxes'),
            Transaction::TYPE_TRANS => __('Transferences'),
        ];
    }

    public static function comboFromTos($withAll = false): array
    {
        return self::comboNames(FromTo::class, $withAll);
    }

    public static function comboCategories($withAll = false): array
    {
        return self::comboNames(Category::class, $withAll);
    }

    public static function comboPaymentTypes($withAll = false): array
    {
        return self::comboNames(PaymentType::class, $withAll);
    }

    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     * @return array<int|string, string>
     */
    private static function comboNames(string $modelClass, bool $withAll): array
    {
        $options = $withAll ? ['all' => __('All')] : [];

        foreach ($modelClass::query()->where('user_id', Auth::id())->get() as $row) {
            $options[$row->id] = $row->name;
        }

        return $options;
    }
}

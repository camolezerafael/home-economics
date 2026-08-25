<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::observe(\App\Observers\TransactionObserver::class);
    }

    protected $table = 'transactions';

    public const TYPE_RECEI = 'RECEI';

    public const TYPE_FIXEX = 'FIXEX';

    public const TYPE_VAREX = 'VAREX';

    public const TYPE_PEOPL = 'PEOPL';

    public const TYPE_TAXES = 'TAXES';

    public const TYPE_TRANS = 'TRANS';

    public const TYPES = [
        self::TYPE_RECEI,
        self::TYPE_FIXEX,
        self::TYPE_VAREX,
        self::TYPE_PEOPL,
        self::TYPE_TAXES,
        self::TYPE_TRANS,
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'from_to_id',
        'payment_type_id',
        'transaction_type',
        'description',
        'amount',
        'status',
        'date_due',
        'date_payment',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'category_id' => 'integer',
        'from_to_id' => 'integer',
        'payment_type_id' => 'integer',
        'amount' => 'integer',
        'status' => 'boolean',
        'date_due' => 'datetime',
        'date_payment' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function fromTo(): BelongsTo
    {
        return $this->belongsTo(FromTo::class, 'from_to_id');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus(Builder $query, bool $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByMonth(Builder $query, string $yearMonth): Builder
    {
        [$year, $month] = explode('-', $yearMonth);

        return $query->whereYear('date_due', $year)->whereMonth('date_due', $month);
    }

    public function scopeIncoming(Builder $query): Builder
    {
        return $query->where('transaction_type', self::TYPE_RECEI);
    }

    public function scopeOutgoing(Builder $query): Builder
    {
        return $query->whereNotIn('transaction_type', [self::TYPE_RECEI, self::TYPE_TRANS]);
    }

    public function scopeTransfers(Builder $query): Builder
    {
        return $query->where('transaction_type', self::TYPE_TRANS);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    public function getAmountAttribute($value): float
    {
        $precision = $this->account?->decimal_precision ?? 2;

        return $value / pow(10, $precision);
    }

    public function setAmountAttribute($value): void
    {
        $precision = $this->account?->decimal_precision ?? 2;
        $this->attributes['amount'] = (int) round($value * pow(10, $precision));
    }

    public function getRawAmountAttribute(): int
    {
        return $this->attributes['amount'] ?? 0;
    }

    public function isTransfer(): bool
    {
        return $this->transaction_type === self::TYPE_TRANS;
    }

    public function isIncome(): bool
    {
        return $this->transaction_type === self::TYPE_RECEI;
    }

    public function isExpense(): bool
    {
        return in_array($this->transaction_type, [self::TYPE_FIXEX, self::TYPE_VAREX, self::TYPE_PEOPL, self::TYPE_TAXES]);
    }

    public function canToggleStatus(): bool
    {
        return ! $this->isTransfer();
    }

    public function getStatusLabel(): string
    {
        return $this->status ? 'Pago' : 'A Pagar';
    }

    public function toggleStatus(): bool
    {
        if (! $this->canToggleStatus()) {
            return false;
        }

        $newStatus = ! $this->status;
        $this->status = $newStatus;
        $this->date_payment = $newStatus ? Carbon::now() : null;
        $this->save();

        return true;
    }

    public function markAsPaid(): bool
    {
        if ($this->status) {
            return true;
        }

        $this->status = true;
        $this->date_payment = Carbon::now();
        $this->save();

        return true;
    }

    public function markAsPending(): bool
    {
        if (! $this->canToggleStatus()) {
            return false;
        }

        if (! $this->status) {
            return true;
        }

        $this->status = false;
        $this->date_payment = null;
        $this->save();

        return true;
    }

    public static function getFullBalance(string $dateFilter, string $accountFilter = 'all', string $paidFilter = 'all', bool $backwardsBalance = false): array
    {
        $userId = auth()->id();
        [$year, $month] = explode('-', $dateFilter);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $dateFilter.'-01')->endOfMonth();

        $incomingQuery = DB::table('balance_incoming')->where('user_id', $userId);
        $outgoingQuery = DB::table('balance_outgoing')->where('user_id', $userId);
        $transferQuery = DB::table('balance_transfer')->where('user_id', $userId);
        $initialQuery = DB::table('accounts')->where('user_id', $userId);

        if ($accountFilter !== 'all') {
            $accountIds = explode(',', $accountFilter);
            $incomingQuery->whereIn('account_id', $accountIds);
            $outgoingQuery->whereIn('account_id', $accountIds);
            $transferQuery->whereIn('account_id', $accountIds);
            $initialQuery->whereIn('id', $accountIds);
        }

        if ($backwardsBalance) {
            $incomingQuery->where('date_due', '<=', $dateEnd);
            $outgoingQuery->where('date_due', '<=', $dateEnd);
            $transferQuery->where('date_due', '<=', $dateEnd);
        } else {
            $incomingQuery->whereYear('date_due', $year)->whereMonth('date_due', $month);
            $outgoingQuery->whereYear('date_due', $year)->whereMonth('date_due', $month);
            $transferQuery->whereYear('date_due', $year)->whereMonth('date_due', $month);
        }

        if ($paidFilter !== 'all') {
            $isPaid = $paidFilter === '1';
            $incomingQuery->where('status', $isPaid);
            $outgoingQuery->where('status', $isPaid);
            $transferQuery->where('status', $isPaid);
        }

        $incoming = (float) $incomingQuery->sum('amount');
        $outgoing = abs((float) $outgoingQuery->sum('amount'));
        $initial = (float) $initialQuery->sum('initial_balance');
        $transfer = (float) $transferQuery->sum('amount');

        $precision = 2;

        $monthBalance = ($incoming - $outgoing + $transfer) / pow(10, $precision);
        $finalBalance = ($incoming + $initial - $outgoing + $transfer) / pow(10, $precision);
        $finalBalanceTransfer = ($incoming + $initial - $outgoing + $transfer) / pow(10, $precision);

        return [
            'month_balance' => $monthBalance,
            'final_balance' => $finalBalance,
            'final_balance_transfer' => $finalBalanceTransfer,
        ];
    }

    public static function getMonthTotals(string $dateFilter): array
    {
        $userId = auth()->id();
        [$year, $month] = explode('-', $dateFilter);
        $results = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereYear('date_due', $year)->whereMonth('date_due', $month)
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'RECEI' AND status = 1 THEN amount ELSE 0 END) as recei_paid,
                SUM(CASE WHEN transaction_type = 'RECEI' THEN amount ELSE 0 END) as recei_total,
                SUM(CASE WHEN transaction_type IN ('FIXEX','VAREX','PEOPL','TAXES') AND status = 1 THEN amount ELSE 0 END) as expen_paid,
                SUM(CASE WHEN transaction_type IN ('FIXEX','VAREX','PEOPL','TAXES') THEN amount ELSE 0 END) as expen_total
            ")
            ->first();

        $precision = 2;
        $divisor = pow(10, $precision);

        $receiPaid = ($results->recei_paid ?? 0) / $divisor;
        $receiTotal = ($results->recei_total ?? 0) / $divisor;
        $expenPaid = ($results->expen_paid ?? 0) / $divisor;
        $expenTotal = ($results->expen_total ?? 0) / $divisor;

        $receiPct = ($receiTotal > 0) ? round(($receiPaid / $receiTotal) * 100, 2) : (($receiPaid > 0) ? 100 : 0);
        $expenPct = ($expenTotal > 0) ? round(($expenPaid / $expenTotal) * 100, 2) : (($expenPaid > 0) ? 100 : 0);

        return [
            'RECEI' => [
                'PAID' => $receiPaid,
                'TO_PAY' => $receiTotal - $receiPaid,
                'TOTAL' => $receiTotal,
                'PERC' => $receiPct,
            ],
            'EXPEN' => [
                'PAID' => $expenPaid,
                'TO_PAY' => $expenTotal - $expenPaid,
                'TOTAL' => $expenTotal,
                'PERC' => $expenPct,
            ],
        ];
    }

    public static function groupTransfers($transfers): array
    {
        $grouped = [];
        $used = [];

        foreach ($transfers as $i => $t1) {
            if (isset($used[$i])) {
                continue;
            }

            $match = null;
            foreach ($transfers as $j => $t2) {
                if ($i === $j || isset($used[$j])) {
                    continue;
                }

                if ($t1->description === $t2->description
                    && abs($t1->getRawAmountAttribute()) === abs($t2->getRawAmountAttribute())
                    && Carbon::parse($t1->date_due)->format('Y-m-d') === Carbon::parse($t2->date_due)->format('Y-m-d')
                    && $t1->getRawAmountAttribute() < 0
                    && $t2->getRawAmountAttribute() > 0) {
                    $match = $j;
                    break;
                }
            }

            if ($match !== null) {
                $used[$i] = $used[$match] = true;
                $outgoing = $t1->getRawAmountAttribute() < 0 ? $t1 : $t2;
                $incoming = $t1->getRawAmountAttribute() > 0 ? $t1 : $t2;

                $grouped[] = (object) [
                    'id' => $outgoing->id.'_'.$incoming->id,
                    'description' => $outgoing->description,
                    'amount' => abs($outgoing->getRawAmountAttribute()) / pow(10, $outgoing->account?->decimal_precision ?? 2),
                    'date_due' => $outgoing->date_due,
                    'account_from' => $outgoing->account?->name ?? '—',
                    'account_to' => $incoming->account?->name ?? '—',
                    'transfer_ids' => [$outgoing->id, $incoming->id],
                    'status' => 1,
                ];
            } else {
                $used[$i] = true;
                $grouped[] = $t1;
            }
        }

        return $grouped;
    }

    public static function getAmountsPaginated(string $dateFilter, string $accountFilter, string $paidFilter, int $perPage = 15): array
    {
        [$year, $month] = explode('-', $dateFilter);

        $query = self::query()
            ->with(['account', 'category', 'fromTo', 'paymentType'])
            ->where('user_id', auth()->id())
            ->whereYear('date_due', $year)->whereMonth('date_due', $month);

        if ($accountFilter !== 'all') {
            if (str_contains($accountFilter, ',')) {
                $accountIds = explode(',', $accountFilter);
                $query->whereIn('account_id', $accountIds);
            } else {
                $query->where('account_id', $accountFilter);
            }
        }

        if ($paidFilter !== 'all') {
            $query->where('status', $paidFilter === '1');
        }

        $query->orderBy('date_due', 'asc');

        $types = [
            self::TYPE_RECEI,
            self::TYPE_FIXEX,
            self::TYPE_VAREX,
            self::TYPE_PEOPL,
            self::TYPE_TAXES,
            self::TYPE_TRANS,
        ];

        $results = [];
        foreach ($types as $type) {
            $typeQuery = clone $query;
            $typeQuery->where('transaction_type', $type);

            if ($type === self::TYPE_TRANS) {
                $items = $typeQuery->get();
                $grouped = self::groupTransfers($items);
                $collection = collect($grouped);
                $page = request()->input('page', 1);
                $results[$type] = new LengthAwarePaginator(
                    $collection->forPage($page, $perPage),
                    $collection->count(),
                    $perPage,
                    $page
                );
            } else {
                $results[$type] = $typeQuery->paginate($perPage);
            }
        }

        return $results;
    }

    public static function keys(): array
    {
        return ['id', 'user_id', 'account_id', 'category_id', 'from_to_id', 'payment_type_id', 'transaction_type', 'description', 'amount', 'status', 'date_due', 'date_payment'];
    }
}

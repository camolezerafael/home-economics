<?php

	namespace App\Http\Controllers;

	use App\Http\Requests\TransactionRequest;
	use App\Models\Account;
	use App\Models\Category;
	use App\Models\FromTo;
	use App\Models\PaymentType;
	use App\Models\Transaction;
	use App\Resources\TransactionResource;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Http\Request;
	use Illuminate\Support\Carbon;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Str;
	use Inertia\Inertia;
	use Inertia\Response;
	use NumberFormatter;

	class TransactionController extends CrudController
	{
		protected string $defaultPath = 'transaction';
		protected string $basePage    = 'Transactions';
		protected        $modelClass  = Transaction::class;
		protected        $formRequest = TransactionRequest::class;
		protected        $defaultList = false;
		protected        $listPath    = 'Transactions/Index';

		#[\Override]
		protected function viewAttributes(): array
		{
			return [
				'routePath'    => $this->defaultPath,
				'basePage'     => __( $this->basePage ),
				'viewPath'     => $this->defaultPath,
				'homePage'     => Str::of( $this->defaultPath )->headline()->pluralStudly()->snake()->toString(),
				'singularItem' => __( 'Movement' ),
				'pluralItem'   => __( 'Movements' ),
			];
		}

		public function index( Request $request ): Response
		{
			$viewAttributes = $this->viewAttributes();

			// Filters (keep both string and array forms where needed)
			$f_date = $request->get( 'f_date', Carbon::now()->format( 'Y-m' ) );
			$f_acc_str = $request->has('f_acc')
				? (is_array($request->get('f_acc')) ? implode(',', $request->get('f_acc')) : $request->get('f_acc'))
				: 'all';
			$f_pay = $request->get( 'f_pay', 'all' );

			$f_acc = explode(',', $f_acc_str); // array version for the UI

			$model = new $this->modelClass();

			// Use lazy props so partial reloads with `only` compute just what's needed
			return Inertia::render( $this->listPath, [
				'viewAttributes'    => fn () => $viewAttributes,
				'comboAccounts'     => fn () => self::comboAccounts( true, true, $f_date ),
				'comboPaid'         => fn () => self::comboPaid( true ),
				'f_date'            => fn () => $f_date,
				'f_acc'             => fn () => $f_acc,
				'f_pay'             => fn () => $f_pay,
				'items'             => fn () => $this->parseCollectionData( $model->getAmountsPaginated( $f_date, $f_acc_str, $f_pay, 10 ) ),
				'monthTotals'       => fn () => $model->getMonthTotals( $f_date ),
				'monthBalance'      => fn () => $model->getFullBalance( $f_date, $f_acc_str, 'all' ),
				'finalBalance'      => fn () => $model->getFullBalance( $f_date, $f_acc_str, 1, true ),
				'estimatedBalance'  => fn () => $model->getFullBalance( $f_date, $f_acc_str, 'all', true ),
			] );
		}

		private function parseCollectionData( $data )
		{
			$parsedData = [];
			foreach ($data as $type => $item) {
				$parsedData[ $type ] = TransactionResource::collection( $item );
			}
			return $parsedData;
		}

	public function create(): JsonResponse
	{
		$viewAttributes = $this->viewAttributes();
		$item           = new $this->modelClass();
		$item->_token   = csrf_token();
		$item->_uri     = "/$this->defaultPath";

		// Check if this is for a transfer (based on request parameter or default)
		$isTransfer = request()->get('transaction_type') === Transaction::TYPE_TRANS || 
		              request()->get('type') === Transaction::TYPE_TRANS;

		$aggregates = [
			'accounts'       => self::comboAccounts(),
			'types'          => self::comboTypes(),
		];

		// Only include non-transfer aggregates if not a transfer
		if (!$isTransfer) {
			$aggregates['paid'] = self::comboPaid();
			$aggregates['from_tos'] = self::comboFromTos();
			$aggregates['categories'] = self::comboCategories();
			$aggregates['payment_types'] = self::comboPaymentTypes();
		}

		return response()->json(
			compact(
				'item',
				'viewAttributes',
				'aggregates'
			)
		);
	}

	public function edit( $id ): JsonResponse
	{
		$viewAttributes = $this->viewAttributes();
		$item           = $this->modelClass::query()->findOrFail( $id );
		$item->_token   = csrf_token();
		$item->_method  = 'PATCH';
		$item->_uri     = "/$this->defaultPath/$item->id";

		$isTransfer = $item->transaction_type === Transaction::TYPE_TRANS;

		// For transfers, handle special data preparation
		if ( $isTransfer ) {
			// Ensure amount is positive
			$item->amount = abs( $item->amount );
			
			// Use date_payment as date_due for display
			if ( $item->date_payment ) {
				$item->date_due = $item->date_payment;
			}
			
			// Find the paired transfer to get account_id_to
			// Use getOriginal to get the raw database value before accessor
			$originalDateDue = $item->getOriginal('date_due');
			$originalAmount = $item->getOriginal('amount');
			
			// Normalize date to Y-m-d format for comparison
			$dateForComparison = \Carbon\Carbon::parse($originalDateDue)->format('Y-m-d');
			
			$pairedTransfer = $this->modelClass::query()
				->where( 'transaction_type', Transaction::TYPE_TRANS )
				->where( 'user_id', $item->user_id )
				->where( 'description', $item->description )
				->whereRaw( 'DATE(date_due) = ?', [ $dateForComparison ] )
				->where( 'id', '!=', $item->id )
				->whereRaw( 'ABS(amount) = ?', [ abs( $originalAmount ) ] )
				->whereRaw( '(amount * ?) < 0', [ $originalAmount ] )
				->first();
			
			if ( $pairedTransfer ) {
				$item->account_id_to = $pairedTransfer->account_id;
			}
		}

		$aggregates = [
			'accounts'       => self::comboAccounts(),
			'types'          => self::comboTypes(),
		];

		// Only include non-transfer aggregates if not a transfer
		if (!$isTransfer) {
			$aggregates['paid'] = self::comboPaid();
			$aggregates['from_tos'] = self::comboFromTos();
			$aggregates['categories'] = self::comboCategories();
			$aggregates['payment_types'] = self::comboPaymentTypes();
		}

		return response()->json(
			compact(
				'item',
				'viewAttributes',
				'aggregates'
			)
		);
	}

		public function store( Request $request ): RedirectResponse
		{
			$columns              = $request->validate( ( new $this->formRequest )->rules() );
			$columns[ 'user_id' ] = Auth::id();

			$itemFrom = new $this->modelClass;

		if ( $request->post( 'transaction_type' ) === Transaction::TYPE_TRANS ) {
			$itemTo = new $this->modelClass;

			$columns[ 'status' ]       = 1;
			$columns[ 'date_payment' ] = $columns[ 'date_due' ];
			
			// Set default payment_type_id for transfers if not provided
			if ( empty( $columns[ 'payment_type_id' ] ) ) {
				$defaultPaymentType = PaymentType::query()
					->where( 'user_id', Auth::id() )
					->first();
				if ( $defaultPaymentType ) {
					$columns[ 'payment_type_id' ] = $defaultPaymentType->id;
				}
			}

			$to = $columns;

			$columns[ 'amount' ] *= -1;
			$to[ 'account_id' ]  = $request->post( 'account_id_to' );

			$itemTo->fill( $to );
			$itemTo->save();
		} elseif ( $columns[ 'status' ] ) {
			$columns[ 'date_payment' ] = $columns[ 'date_due' ];
		}

		$itemFrom->fill( $columns );
		$itemFrom->save();

		return back();
	}

		/**
		 * Update the specified resource in storage.
		 *
		 * @param int $id
		 * @param Request $request
		 * @return RedirectResponse
		 */
	public function update( int $id, Request $request ): RedirectResponse
	{
		$item    = $this->modelClass::query()->findOrFail( $id );
		$columns = $request->validate( ( new $this->formRequest )->rules() );

		// Handle transfers specially - update both transactions in the pair
		if ( $item->transaction_type === Transaction::TYPE_TRANS ) {
			// Ensure amount is positive (form sends positive)
			$amount = abs( floatval( $columns[ 'amount' ] ) );
			
			// Set default payment_type_id for transfers if not provided
			if ( empty( $columns[ 'payment_type_id' ] ) ) {
				$defaultPaymentType = PaymentType::query()
					->where( 'user_id', Auth::id() )
					->first();
				if ( $defaultPaymentType ) {
					$columns[ 'payment_type_id' ] = $defaultPaymentType->id;
				}
			}
			
			// Find the paired transfer - use original values before any updates
			$originalDateDue = $item->getOriginal('date_due');
			$originalAmount = $item->getOriginal('amount');
			$originalDescription = $item->getOriginal('description');
			
			// Normalize date to Y-m-d format for comparison
			$dateForComparison = \Carbon\Carbon::parse($originalDateDue)->format('Y-m-d');
			
			// Find the paired transfer - look for opposite sign and same absolute amount
			// Try multiple approaches to find the pair
			$pairedTransfer = $this->modelClass::query()
				->where( 'transaction_type', Transaction::TYPE_TRANS )
				->where( 'user_id', $item->user_id )
				->where( 'description', $originalDescription )
				->whereRaw( 'DATE(date_due) = ?', [ $dateForComparison ] )
				->where( 'id', '!=', $item->id )
				->whereRaw( 'ABS(amount) = ?', [ abs( $originalAmount ) ] )
				->whereRaw( '(amount * ?) < 0', [ $originalAmount ] )
				->first();
			
			// Fallback: if not found by exact match, try finding by account relationship
			if ( !$pairedTransfer ) {
				// Look for transfers with same description and date, but different account
				$pairedTransfer = $this->modelClass::query()
					->where( 'transaction_type', Transaction::TYPE_TRANS )
					->where( 'user_id', $item->user_id )
					->where( 'description', $originalDescription )
					->whereRaw( 'DATE(date_due) = ?', [ $dateForComparison ] )
					->where( 'id', '!=', $item->id )
					->where( 'account_id', '!=', $item->account_id )
					->first();
			}
			
			// Determine which transaction we're updating (outgoing = negative, incoming = positive)
			$isOutgoing = $originalAmount < 0;
			$accountIdFrom = $columns[ 'account_id' ] ?? null;
			
			// Get account_id_to from validated columns or request, or use paired transfer's account_id as fallback
			$accountIdTo = $columns[ 'account_id_to' ] ?? $request->input( 'account_id_to' );
			if ( empty( $accountIdTo ) && $pairedTransfer ) {
				$accountIdTo = $pairedTransfer->account_id;
			}
			
			// Validate that we have both account IDs
			if ( empty( $accountIdFrom ) || empty( $accountIdTo ) ) {
				return back()->withErrors( [ 'account_id' => 'Both from and to accounts are required for transfers.' ] );
			}
			
			// Prepare common fields for both transactions
			$columns[ 'status' ] = 1; // Transfers are always paid
			$columns[ 'date_payment' ] = $columns[ 'date_due' ];
			
			// Update the current transaction
			if ( $isOutgoing ) {
				// This is the outgoing transaction (negative amount)
				$columns[ 'amount' ] = -$amount;
				$columns[ 'account_id' ] = $accountIdFrom;
			} else {
				// This is the incoming transaction (positive amount)
				$columns[ 'amount' ] = $amount;
				$columns[ 'account_id' ] = $accountIdTo;
			}
			
			// Update current transaction
			$item->update( $columns );
			
			// Update the paired transaction if found
			if ( $pairedTransfer ) {
				// Determine the account ID for the paired transfer
				$pairedAccountId = $isOutgoing ? $accountIdTo : $accountIdFrom;
				
				// Get the account for the paired transfer to use correct decimal precision
				$pairedAccount = \App\Models\Account::find( $pairedAccountId );
				
				if ( !$pairedAccount ) {
					return back()->withErrors( [ 'account_id' => 'Invalid account for paired transfer.' ] );
				}
				
				// Calculate amount with correct decimal precision for the paired account
				$pairedAmountValue = $isOutgoing ? $amount : -$amount;
				$pairedAmountRaw = $pairedAmountValue * (10 ** ($pairedAccount->decimal_precision ?? 1));
				
				// Update paired transfer directly using DB to avoid mutator issues
				DB::table('transactions')
					->where('id', $pairedTransfer->id)
					->update([
						'description' => $columns[ 'description' ],
						'amount' => $pairedAmountRaw,
						'account_id' => $pairedAccountId,
						'date_due' => $columns[ 'date_due' ],
						'date_payment' => $columns[ 'date_payment' ],
						'status' => 1,
						'payment_type_id' => $columns[ 'payment_type_id' ],
					]);
			} else {
				// If paired transfer not found, log or handle error
				\Log::warning('Paired transfer not found for transaction ID: ' . $item->id);
			}
		} else {
			$item->update( $columns );

			if ( $item->wasChanged( 'status' ) && $item->status ) {
				$item->date_payment = \Carbon\Carbon::now();
				$item->save();
			}
		}

		return back();
	}

		public function changeTransactionStatus( Transaction $transaction ): bool
		{
			$updated = $transaction->updateOrFail( [
				'status'       => !$transaction->status,
				'date_payment' => !$transaction->status ? Carbon::now() : null
			] );

			return $updated;
		}

		public static function comboAccounts( $withAll = false, $withBalance = false, $date = null ): array
		{
			// Simple per-request memoization to avoid repeated expensive balance calculations
			static $balanceCache = [];
			$localeFormatter = new NumberFormatter( app()->getLocale(), NumberFormatter::CURRENCY );

			$formatBalance = function($val) use ($localeFormatter) {
				return $localeFormatter->format($val);
			};

			$getBalanceFor = function($accountId) use (&$balanceCache, $date) {
				$key = ($date ?? 'null') . '|' . ($accountId === null ? 'null' : (string)$accountId);
				if (!array_key_exists($key, $balanceCache)) {
					$balanceCache[$key] = Transaction::getFullBalance( $date, $accountId ?? 'all', '1', true )->final_balance_transfer;
				}
				return $balanceCache[$key];
			};

			$balanceFormatted = 0;
			if ( $withBalance ) {
				$balanceFormatted = $formatBalance($getBalanceFor(null)); // overall (all accounts)
			}

			$all[ 'all' ] = [ 'id' => 'all', 'name' => __('All'), 'balance' => $balanceFormatted ];

			$options = $withAll ? $all : [];

			Account::query()
				->where( 'user_id', Auth::id() )
				->get()
				->each( static function ( $row ) use ( $withBalance, $date, &$options, $formatBalance, $getBalanceFor ) {
					$label = $row->name;
					$balanceOut = '';
					if ( $withBalance ) {
						$balanceOut = $formatBalance($getBalanceFor($row->id));
					}
					$options[ $row->id ] = [ 'id' => $row->id, 'name' => $row->name, 'label' => $label, 'balance' => $balanceOut ];
				} );

			return $options;
		}

		public static function comboPaid( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			return $options + [
					'0' => __('To Pay' ),
					'1' => __('Paid' ),
				];
		}

		public static function comboTypes( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			return $options + [
					Transaction::TYPE_RECEIPT => __( 'Receipts' ),
					Transaction::TYPE_FIXED   => __( 'Fixed Expenses' ),
					Transaction::TYPE_VAR     => __( 'Variable Expenses' ),
					Transaction::TYPE_PEOPLE  => __( 'People' ),
					Transaction::TYPE_TAX     => __( 'Taxes' ),
					Transaction::TYPE_TRANS   => __( 'Transferences' ),
				];
		}

		public static function comboFromTos( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			FromTo::query()
				->where( 'user_id', Auth::id() )
				->get()
				->each( static function ( $row ) use ( &$options ) {
					$options[ $row->id ] = $row->name;
				} );

			return $options;
		}

		public static function comboCategories( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			Category::query()
				->where( 'user_id', Auth::id() )
				->get()
				->each( static function ( $row ) use ( &$options ) {
					$options[ $row->id ] = $row->name;
				} );

			return $options;
		}

		public static function comboPaymentTypes( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			PaymentType::query()
				->where( 'user_id', Auth::id() )
				->get()
				->each( static function ( $row ) use ( &$options ) {
					$options[ $row->id ] = $row->name;
				} );

			return $options;
		}

	}

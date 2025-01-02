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

			if ( $request->has( 'f_date' ) ) {
				$f_date = $request->get( 'f_date' );
			} else {
				$f_date = Carbon::now()->subMonths( 2 )->format( 'Y-m' );
			}

			if ( $request->has( 'f_acc' ) ) {
				$f_acc = is_array($request->get('f_acc'))? implode(',', $request->get( 'f_acc' )) : $request->get( 'f_acc' );
			} else {
				$f_acc = 'all';
			}

			if ( $request->has( 'f_pay' ) ) {
				$f_pay = $request->get( 'f_pay' );
			} else {
				$f_pay = 'all';
			}

			$comboAccounts = self::comboAccounts( true, true, $f_date );
			$comboPaid     = self::comboPaid( true );

			$model = new $this->modelClass();

			$monthTotals = $model->getMonthTotals( $f_date );

			$monthBalance     = $model->getFullBalance( $f_date, $f_acc, 'all' );
			$finalBalance     = $model->getFullBalance( $f_date, $f_acc, 1, true );
			$estimatedBalance = $model->getFullBalance( $f_date, $f_acc, 'all', true );

			$items = $this->parseCollectionData( $model->getAmounts( $f_date, $f_acc, $f_pay ) );

			$f_acc = explode( ',', $f_acc );

			return Inertia::render( $this->listPath, compact(
				'viewAttributes',
				'comboAccounts',
				'comboPaid',
				'f_date',
				'f_acc',
				'f_pay',
				'items',
				'monthTotals',
				'monthBalance',
				'finalBalance',
				'estimatedBalance'
			) );
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
			$comboAccounts     = self::comboAccounts();
			$comboPaid         = self::comboPaid();
			$comboTypes        = self::comboTypes();
			$comboFromTos      = self::comboFromTos();
			$comboCategories   = self::comboCategories();
			$comboPaymentTypes = self::comboPaymentTypes();

			$viewAttributes = $this->viewAttributes();
			$item           = new $this->modelClass();
			$item->_token   = csrf_token();
			$item->_uri     = "/$this->defaultPath";

			return response()->json(
				"$this->defaultPath.edit",
				compact(
					'item',
					'viewAttributes',
					'comboAccounts',
					'comboPaid',
					'comboTypes',
					'comboFromTos',
					'comboCategories',
					'comboPaymentTypes'
				)
			);
		}

		public function edit( $id ): JsonResponse
		{
			$comboAccounts     = self::comboAccounts();
			$comboPaid         = self::comboPaid();
			$comboTypes        = self::comboTypes();
			$comboFromTos      = self::comboFromTos();
			$comboCategories   = self::comboCategories();
			$comboPaymentTypes = self::comboPaymentTypes();

			$viewAttributes = $this->viewAttributes();
			$item           = $this->modelClass::query()->findOrFail( $id );
			$item->_token   = csrf_token();
			$item->_method  = 'PATCH';
			$item->_uri     = "/$this->defaultPath/$item->id";

			return response()->json(
				"$this->defaultPath.edit",
				compact(
					'item',
					'viewAttributes',
					'comboAccounts',
					'comboPaid',
					'comboTypes',
					'comboFromTos',
					'comboCategories',
					'comboPaymentTypes'
				)
			);
		}

		public function store( Request $request ): RedirectResponse
		{
			$columns              = $request->validate( ( new $this->formRequest )->rules() );
			$columns[ 'user_id' ] = Auth::id();

			$itemFrom = new $this->modelClass;

			if ( $request->post( 'transaction_type' ) === 'TRANS' ) {
				$itemTo = new $this->modelClass;

				$columns[ 'status' ]       = 1;
				$columns[ 'date_payment' ] = $columns[ 'date_due' ];

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

			return redirect( "/$this->defaultPath/$itemFrom->id/edit" );
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

			$item->update( $columns );

			if ( $item->wasChanged( 'status' ) && $item->status ) {
				$item->date_payment = \Carbon\Carbon::now();
				$item->save();
			}

			return redirect( "/$this->defaultPath/$item->id/edit" );
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
			if ( $withBalance ) {
				$balance = Transaction::getFullBalance( $date, 'all', '1', true )->final_balance_transfer;
				$balance = ( new NumberFormatter( app()->getLocale(), NumberFormatter::CURRENCY ) )->format( $balance );
			}

			$all[ 'all' ] = [ 'id' => 'all', 'name' => __('All'), 'balance' => $balance ];

			$options = $withAll ? $all : [];

			Account::all()
				   ->where( 'user_id', Auth::id() )
				   ->each( static function ( $row ) use ( $withBalance, $date, &$options ) {

					   $label = $row->name;

					   if ( $withBalance ) {
						   $balance = Transaction::getFullBalance( $date, $row->id, '1', true )->final_balance_transfer;
						   $balance = ( new NumberFormatter( app()->getLocale(), NumberFormatter::CURRENCY ) )->format( $balance );
					   }

					   $options[ $row->id ] = [ 'id' => $row->id, 'name' => $row->name, 'label' => $label, 'balance' => $balance ];
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
					'RECEI' => __( 'Receipts' ),
					'FIXEX' => __( 'Fixed Expenses' ),
					'VAREX' => __( 'Variable Expenses' ),
					'PEOPL' => __( 'People' ),
					'TAXES' => __( 'Taxes' ),
					'TRANS' => __( 'Transferences' ),
				];
		}

		public static function comboFromTos( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			FromTo::all()
				  ->where( 'user_id', Auth::id() )
				  ->each( static function ( $row ) use ( &$options ) {
					  $options[ $row->id ] = $row->name;
				  } );

			return $options;
		}

		public static function comboCategories( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			Category::all()
					->where( 'user_id', Auth::id() )
					->each( static function ( $row ) use ( &$options ) {
						$options[ $row->id ] = $row->name;
					} );

			return $options;
		}

		public static function comboPaymentTypes( $withAll = false ): array
		{
			$options = $withAll ? [ 'all' => __('All') ] : [];
			PaymentType::all()
					   ->where( 'user_id', Auth::id() )
					   ->each( static function ( $row ) use ( &$options ) {
						   $options[ $row->id ] = $row->name;
					   } );

			return $options;
		}

	}

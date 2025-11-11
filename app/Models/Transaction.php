<?php

	namespace App\Models;

	use App\Models\ModelBase\TransactionBase;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;

	class Transaction extends TransactionBase
	{
		// Transaction type constants
		public const TYPE_RECEIPT = 'RECEI';
		public const TYPE_FIXED   = 'FIXEX';
		public const TYPE_VAR     = 'VAREX';
		public const TYPE_PEOPLE  = 'PEOPL';
		public const TYPE_TAX     = 'TAXES';
		public const TYPE_TRANS   = 'TRANS';

		// Query scopes centralizing filters
		public function scopeOfType( Builder $q, string $type ): Builder
		{
			return $q->where( 'transaction_type', $type );
		}

		public function scopeForUser( Builder $q, int $userId ): Builder
		{
			return $q->where( 'user_id', $userId );
		}

		public function scopeForMonth( Builder $q, string $ym ): Builder
		{
			$dt = \Carbon\Carbon::createFromFormat( 'Y-m-d', $ym . '-01' );
			return $q->whereBetween( 'date_due', [ $dt->format( 'Y-m-d' ), $dt->lastOfMonth()->format( 'Y-m-d' ) ] );
		}

		public function scopeForAccounts( Builder $q, string $accountFilter ): Builder
		{
			return $q->when( $accountFilter !== 'all', function ( $qq ) use ( $accountFilter ) {
				$ids = explode( ',', $accountFilter );
				$qq->whereIn( 'account_id', $ids );
			} );
		}

		public function scopeForPaid( Builder $q, string $paidFilter ): Builder
		{
			return $q->when( $paidFilter !== 'all', function ( $qq ) use ( $paidFilter ) {
				$qq->where( 'status', $paidFilter );
			} );
		}

		public function getReceipts(): Builder
		{
			return self::where( 'transaction_type', self::TYPE_RECEIPT );
		}

		public function getFixedExpenses(): Builder
		{
			return self::where( 'transaction_type', self::TYPE_FIXED );
		}

		public function getVariableExpenses(): Builder
		{
			return self::where( 'transaction_type', self::TYPE_VAR );
		}

		public function getPeople(): Builder
		{
			return self::where( 'transaction_type', self::TYPE_PEOPLE );
		}

		public function getTaxes(): Builder
		{
			return self::where( 'transaction_type', self::TYPE_TAX );
		}

		public function getTransfers(): Builder
		{
			return self::where( 'transaction_type', self::TYPE_TRANS );
		}

		/**
		 * Group transfer pairs into single records
		 * Transfers are stored as two transactions: one negative (outgoing) and one positive (incoming)
		 * This method groups them by matching absolute amount, description, and date_due
		 */
		private function groupTransfers( $transfers )
		{
			$grouped   = [];
			$processed = [];

			foreach ($transfers as $index => $transfer) {
				if ( isset( $processed[ $index ] ) ) {
					continue;
				}

				// Find matching pair (same description, same absolute amount, same date_due, opposite sign)
				$matched      = null;
				$matchedIndex = null;

				foreach ($transfers as $otherIndex => $other) {
					if ( $otherIndex === $index || isset( $processed[ $otherIndex ] ) ) {
						continue;
					}

					// Match criteria: same user, description, absolute amount, date_due, opposite amounts
					// Normalize dates to Y-m-d format for comparison (in case of timestamp differences)
					$transferDate = \Carbon\Carbon::parse( $transfer->date_due )->format( 'Y-m-d' );
					$otherDate    = \Carbon\Carbon::parse( $other->date_due )->format( 'Y-m-d' );

					if (
						$transfer->user_id === $other->user_id &&
						$transfer->description === $other->description &&
						abs( $transfer->amount ) === abs( $other->amount ) &&
						$transferDate === $otherDate &&
						( $transfer->amount * $other->amount ) < 0 // opposite signs
					) {
						$matched      = $other;
						$matchedIndex = $otherIndex;
						break;
					}
				}

				// Create grouped record
				$outgoing = $transfer->amount < 0 ? $transfer : $matched;
				$incoming = $transfer->amount > 0 ? $transfer : $matched;

				// If no match found, still include the transfer (might be orphaned)
				if ( !$matched ) {
					$outgoing = $transfer->amount < 0 ? $transfer : null;
					$incoming = $transfer->amount > 0 ? $transfer : null;
				}

				// Use outgoing transaction as base, or incoming if no outgoing
				$base          = $outgoing ?: $incoming;
				$groupedRecord = clone $base;

				// Store account information for both directions
				$groupedRecord->account_id_from = $outgoing ? $outgoing->account_id : null;
				$groupedRecord->account_id_to   = $incoming ? $incoming->account_id : null;

				// Load account relationships if needed
				if ( $outgoing && $outgoing->relationLoaded( 'account' ) ) {
					$groupedRecord->account_from = $outgoing->account;
				}
				if ( $incoming && $incoming->relationLoaded( 'account' ) ) {
					$groupedRecord->account_to = $incoming->account;
				}

				// Use absolute amount
				$groupedRecord->amount = abs( $base->amount );

				// Store both transaction IDs for reference
				$groupedRecord->transfer_ids = [
					$outgoing ? $outgoing->id : null,
					$incoming ? $incoming->id : null
				];

				// Use the outgoing ID as primary, or incoming if no outgoing
				$groupedRecord->id = $outgoing ? $outgoing->id : ( $incoming ? $incoming->id : $transfer->id );

				// Update account_id to the from account for display purposes
				$groupedRecord->account_id = $outgoing ? $outgoing->account_id : ( $incoming ? $incoming->account_id : $transfer->account_id );

				$grouped[] = $groupedRecord;

				// Mark both as processed
				$processed[ $index ] = true;
				if ( $matchedIndex !== null ) {
					$processed[ $matchedIndex ] = true;
				}
			}

			return collect( $grouped );
		}

		private function baseSelect( Builder $q ): Builder
		{
			return $q->select( [
				'id',
				'user_id',
				'account_id',
				'from_to_id',
				'category_id',
				'payment_type_id',
				'transaction_type',
				'description',
				'amount',
				'status',
				'date_due',
				'date_payment',
			] );
		}

		public function getAmounts( $dateFilter, $accountFilter, $paidFilter ): array
		{
			$items[ self::TYPE_RECEIPT ] = $this->baseSelect( $this->applyFilters( $this->getReceipts(), $dateFilter, $accountFilter, $paidFilter ) )->get();
			$items[ self::TYPE_FIXED ]   = $this->baseSelect( $this->applyFilters( $this->getFixedExpenses(), $dateFilter, $accountFilter, $paidFilter ) )->get();
			$items[ self::TYPE_VAR ]     = $this->baseSelect( $this->applyFilters( $this->getVariableExpenses(), $dateFilter, $accountFilter, $paidFilter ) )->get();
			$items[ self::TYPE_PEOPLE ]  = $this->baseSelect( $this->applyFilters( $this->getPeople(), $dateFilter, $accountFilter, $paidFilter ) )->get();
			$items[ self::TYPE_TAX ]     = $this->baseSelect( $this->applyFilters( $this->getTaxes(), $dateFilter, $accountFilter, $paidFilter ) )->get();
			$items[ self::TYPE_TRANS ]   = $this->baseSelect( $this->applyFilters( $this->getTransfers(), $dateFilter, $accountFilter, $paidFilter ) )->get();

			return $items;
		}

		public function getAmountsPaginated( $dateFilter, $accountFilter, $paidFilter, int $perPage = 10 ): array
		{
			$items                       = [];
			$items[ self::TYPE_RECEIPT ] = $this->baseSelect( $this->applyFilters( $this->getReceipts(), $dateFilter, $accountFilter, $paidFilter ) )->paginate( $perPage, [ '*' ], 'page_' . self::TYPE_RECEIPT );
			$items[ self::TYPE_FIXED ]   = $this->baseSelect( $this->applyFilters( $this->getFixedExpenses(), $dateFilter, $accountFilter, $paidFilter ) )->paginate( $perPage, [ '*' ], 'page_' . self::TYPE_FIXED );
			$items[ self::TYPE_VAR ]     = $this->baseSelect( $this->applyFilters( $this->getVariableExpenses(), $dateFilter, $accountFilter, $paidFilter ) )->paginate( $perPage, [ '*' ], 'page_' . self::TYPE_VAR );
			$items[ self::TYPE_PEOPLE ]  = $this->baseSelect( $this->applyFilters( $this->getPeople(), $dateFilter, $accountFilter, $paidFilter ) )->paginate( $perPage, [ '*' ], 'page_' . self::TYPE_PEOPLE );
			$items[ self::TYPE_TAX ]     = $this->baseSelect( $this->applyFilters( $this->getTaxes(), $dateFilter, $accountFilter, $paidFilter ) )->paginate( $perPage, [ '*' ], 'page_' . self::TYPE_TAX );

			// Handle transfers specially: fetch all, group pairs, then paginate manually
			$transfersQuery   = $this->baseSelect( $this->applyFilters( $this->getTransfers(), $dateFilter, $accountFilter, $paidFilter ) );
			$allTransfers     = $transfersQuery->with( 'account' )->get();
			$groupedTransfers = $this->groupTransfers( $allTransfers );

			// Load account relationships for grouped transfers to avoid N+1 queries
			$accountIds = [];
			foreach ($groupedTransfers as $transfer) {
				if ( $transfer->account_id_from ) {
					$accountIds[] = $transfer->account_id_from;
				}
				if ( $transfer->account_id_to ) {
					$accountIds[] = $transfer->account_id_to;
				}
			}

			if ( !empty( $accountIds ) ) {
				$accounts = \App\Models\Account::whereIn( 'id', array_unique( $accountIds ) )->get()->keyBy( 'id' );
				foreach ($groupedTransfers as $transfer) {
					if ( $transfer->account_id_from && isset( $accounts[ $transfer->account_id_from ] ) ) {
						$transfer->account_from = $accounts[ $transfer->account_id_from ];
					}
					if ( $transfer->account_id_to && isset( $accounts[ $transfer->account_id_to ] ) ) {
						$transfer->account_to = $accounts[ $transfer->account_id_to ];
					}
				}
			}

			// Get current page from request
			$page          = request()->get( 'page_' . self::TYPE_TRANS, 1 );
			$offset        = ( $page - 1 ) * $perPage;
			$paginatedData = $groupedTransfers->slice( $offset, $perPage )->values();

			// Create a LengthAwarePaginator manually
			$items[ self::TYPE_TRANS ] = new \Illuminate\Pagination\LengthAwarePaginator(
				$paginatedData,
				$groupedTransfers->count(),
				$perPage,
				$page,
				[
					'path'     => request()->url(),
					'pageName' => 'page_' . self::TYPE_TRANS,
				]
			);

			return $items;
		}

		public function getMonthTotals( $dateFilter ): array
		{
			$userId = Auth::id();
			$dt     = \Carbon\Carbon::createFromFormat( 'Y-m-d', $dateFilter . '-01' );
			$start  = $dt->format( 'Y-m-d' );
			$end    = $dt->lastOfMonth()->format( 'Y-m-d' );

			$row = self::query()
					   ->forUser( $userId )
					   ->forMonth( $dateFilter )
					   ->selectRaw( '
					SUM(CASE WHEN transaction_type = ? AND status = 1 THEN amount ELSE 0 END) AS recei_paid,
					SUM(CASE WHEN transaction_type = ? THEN amount ELSE 0 END) AS recei_total,
					SUM(CASE WHEN transaction_type IN (?, ?, ?, ?) AND status = 1 THEN amount ELSE 0 END) AS expen_paid,
					SUM(CASE WHEN transaction_type IN (?, ?, ?, ?) THEN amount ELSE 0 END) AS expen_total
				', [
						   self::TYPE_RECEIPT,
						   self::TYPE_RECEIPT,
						   self::TYPE_FIXED, self::TYPE_VAR, self::TYPE_PEOPLE, self::TYPE_TAX,
						   self::TYPE_FIXED, self::TYPE_VAR, self::TYPE_PEOPLE, self::TYPE_TAX,
					   ] )
					   ->first();

			$receiPaid  = (float)( $row->recei_paid ?? 0 );
			$receiTotal = (float)( $row->recei_total ?? 0 );
			$expenPaid  = (float)( $row->expen_paid ?? 0 );
			$expenTotal = (float)( $row->expen_total ?? 0 );

			$receiPerc = ( $receiPaid === 0.0 && $receiTotal === 0.0 ) ? 100 : (int)number_format( ( $receiPaid / max( 1e-9, $receiTotal ) * 100 ) );
			$expenPerc = ( $expenPaid === 0.0 && $expenTotal === 0.0 ) ? 100 : (int)number_format( ( $expenPaid / max( 1e-9, $expenTotal ) * 100 ) );

			return [
				'RECEI' => [ 'PAID' => $receiPaid, 'TO_PAY' => $receiTotal, 'PERC' => $receiPerc ],
				'EXPEN' => [ 'PAID' => $expenPaid, 'TO_PAY' => $expenTotal, 'PERC' => $expenPerc ],
			];
		}

		public function applyFilters( Builder $query, string $dateFilter, string $accountFilter, string $paidFilter ): Builder
		{
			return $query
				->forUser( Auth::id() )
				->forMonth( $dateFilter )
				->forAccounts( $accountFilter )
				->forPaid( $paidFilter )
				->orderBy( 'date_due' );
		}

		public static function getFullBalance( string $dateFilter, string $accountFilter, string $paidFilter, $backwardsBalance = false )
		{
			$where         = 'WHERE user_id = ' . Auth::id();
			$whereDate     = '';
			$whereAccount  = '';
			$whereAccount2 = '';
			$wherePaid     = '';

			$date = \Carbon\Carbon::createFromFormat( 'Y-m-d', $dateFilter . '-01' );

			if ( $dateFilter !== '' ) {
				$whereDate = ' AND date_due BETWEEN \'' . $date->format( 'Y-m-d' ) . '\' AND \'' . $date->lastOfMonth()->format( 'Y-m-d' ) . '\'';

				if ( $backwardsBalance ) {
					$whereDate = ' AND date_due <= \'' . $date->lastOfMonth()->format( 'Y-m-d' ) . '\'';
				}
			}

			if ( $accountFilter !== 'all' ) {
				$whereAccount  = ' AND account_id IN (' . $accountFilter . ')';
				$whereAccount2 = ' AND id IN (' . $accountFilter . ')';
			}

			if ( $paidFilter !== 'all' ) {
				$wherePaid = ' AND status = ' . $paidFilter;
			}

			$where2 = $where . $whereAccount2;

			$where .= $whereDate . $whereAccount . $wherePaid;

			$sql = "
			SELECT
				SUM(income) - SUM(expenses) as month_balance,
				SUM(income) + SUM(balance) - SUM(expenses) as final_balance,
				SUM(income) + SUM(balance) - SUM(expenses) + SUM(transfer) as final_balance_transfer

			FROM (
					SELECT
						SUM(amount) / POWER(10,2) AS income,
						0 AS expenses,
						0 AS balance,
						0 AS transfer
					FROM
						balance_incoming
					$where

					UNION

					SELECT
						0 AS income,
						SUM(amount) / POWER(10,2) AS expenses,
						0 AS balance,
						0 AS transfer
					FROM
						balance_outgoing
					$where

					UNION

					SELECT
						0 AS income,
						0 AS expenses,
						SUM(initial_balance) / POWER(10,2) AS balance,
						0 AS transfer
					FROM
						accounts
					$where2

					UNION

					SELECT
						0 AS income,
						0 AS expenses,
						0 AS balance,
						SUM(amount) / POWER(10,2) AS transfer
					FROM
						balance_transfer
					$where

				)
				AS view_balance";

			return DB::selectOne( DB::raw( $sql )->getValue( DB::connection()->getQueryGrammar() ) );
		}

	}

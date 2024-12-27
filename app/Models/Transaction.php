<?php

	namespace App\Models;

	use App\Models\ModelBase\TransactionBase;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;

	class Transaction extends TransactionBase
	{
		public function getReceipts(): Builder
		{
			return self::where( 'transaction_type', 'RECEI' );
		}

		public function getFixedExpenses(): Builder
		{
			return self::where( 'transaction_type', 'FIXEX' );
		}

		public function getVariableExpenses(): Builder
		{
			return self::where( 'transaction_type', 'VAREX' );
		}

		public function getPeople(): Builder
		{
			return self::where( 'transaction_type', 'PEOPL' );
		}

		public function getTaxes(): Builder
		{
			return self::where( 'transaction_type', 'TAXES' );
		}

		public function getTransfers(): Builder
		{
			return self::where( 'transaction_type', 'TRANS' );
		}

		public function getAmounts( $dateFilter, $accountFilter, $paidFilter ): array
		{
			$items[ 'RECEI' ] = $this->applyFilters( $this->getReceipts(), $dateFilter, $accountFilter, $paidFilter )->get();
			$items[ 'FIXEX' ] = $this->applyFilters( $this->getFixedExpenses(), $dateFilter, $accountFilter, $paidFilter )->get();
			$items[ 'VAREX' ] = $this->applyFilters( $this->getVariableExpenses(), $dateFilter, $accountFilter, $paidFilter )->get();
			$items[ 'PEOPL' ] = $this->applyFilters( $this->getPeople(), $dateFilter, $accountFilter, $paidFilter )->get();
			$items[ 'TAXES' ] = $this->applyFilters( $this->getTaxes(), $dateFilter, $accountFilter, $paidFilter )->get();
			$items[ 'TRANS' ] = $this->applyFilters( $this->getTransfers(), $dateFilter, $accountFilter, $paidFilter )->get();

			return $items;
		}

		public function getMonthTotals( $dateFilter ): array
		{
			$totals = [
				'RECEI' => [
					'PAID'   => 0,
					'TO_PAY' => 0,
					'PERC'   => 0,
				],
				'EXPEN' => [
					'PAID'   => 0,
					'TO_PAY' => 0,
					'PERC'   => 0,
				]
			];

			$this->applyFilters( $this->getReceipts(), $dateFilter, 'all', 1 )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'RECEI' ][ 'PAID' ] += $row->amount;
			} );

			$this->applyFilters( $this->getReceipts(), $dateFilter, 'all', 'all' )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'RECEI' ][ 'TO_PAY' ] += $row->amount;
			} );


			$this->applyFilters( $this->getFixedExpenses(), $dateFilter, 'all', 1 )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'PAID' ] += $row->amount;
			} );

			$this->applyFilters( $this->getVariableExpenses(), $dateFilter, 'all', 1 )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'PAID' ] += $row->amount;
			} );

			$this->applyFilters( $this->getPeople(), $dateFilter, 'all', 1 )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'PAID' ] += $row->amount;
			} );

			$this->applyFilters( $this->getTaxes(), $dateFilter, 'all', 1 )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'PAID' ] += $row->amount;
			} );


			$this->applyFilters( $this->getFixedExpenses(), $dateFilter, 'all', 'all' )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'TO_PAY' ] += $row->amount;
			} );
			$this->applyFilters( $this->getVariableExpenses(), $dateFilter, 'all', 'all' )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'TO_PAY' ] += $row->amount;
			} );
			$this->applyFilters( $this->getPeople(), $dateFilter, 'all', 'all' )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'TO_PAY' ] += $row->amount;
			} );
			$this->applyFilters( $this->getTaxes(), $dateFilter, 'all', 'all' )->each( static function ( $row ) use ( &$totals ) {
				$totals[ 'EXPEN' ][ 'TO_PAY' ] += $row->amount;
			} );


			if ( $totals[ 'RECEI' ][ 'PAID' ] === 0 && $totals[ 'RECEI' ][ 'TO_PAY' ] === 0 ) {
				$totals[ 'RECEI' ][ 'PERC' ] = 100;
			}else{
				$totals[ 'RECEI' ][ 'PERC' ] = number_format( ( $totals[ 'RECEI' ][ 'PAID' ] / $totals[ 'RECEI' ][ 'TO_PAY' ] * 100 ) );
			}

			if ( $totals[ 'EXPEN' ][ 'PAID' ] === 0 && $totals[ 'EXPEN' ][ 'TO_PAY' ] === 0 ) {
				$totals[ 'EXPEN' ][ 'PERC' ] = 100;
			}else{
				$totals[ 'EXPEN' ][ 'PERC' ] = number_format( ( $totals[ 'EXPEN' ][ 'PAID' ] / $totals[ 'EXPEN' ][ 'TO_PAY' ] * 100 ) );
			}

			return $totals;
		}

		public function applyFilters( Builder $query, string $dateFilter, string $accountFilter, string $paidFilter ): Builder
		{
			$date = \Carbon\Carbon::createFromFormat( 'Y-m-d', $dateFilter . '-01' );

			return $query->where( 'user_id', Auth::id() )
						 ->whereBetween( 'date_due', [ $date->format( 'Y-m-d' ), $date->lastOfMonth()->format( 'Y-m-d' ) ] )
						 ->when( $accountFilter !== 'all', static function ( $filter ) use ( $accountFilter ) {
							 $filter->whereIn( 'account_id', explode( ',', $accountFilter ) );
						 } )
						 ->when( $paidFilter !== 'all', static function ( $filter ) use ( $paidFilter ) {
							 $filter->where( 'status', $paidFilter );
						 } )
						 ->OrderBy( 'date_due' );
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

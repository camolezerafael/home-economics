@props(['monthTotals', 'monthBalance', 'finalBalance', 'estimatedBalance', 'f_date', 'f_acc', 'f_pay', 'comboAccounts', 'comboPaid'])

<div class="container-fluid">

	<div class="row">

		<h4 class="m-0 p-0">{{__('Summary')}}</h4>

		<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pe-lg-2 pb-lg-3">
			<x-pages.transaction.part.month-totals
				color="success"
				:title="__('Month Receipts')"
				:line1="__('Received:')"
				:line2="__('Estimated')"
				:percent="$monthTotals['RECEI']['PERC']"
				:paid="$monthTotals['RECEI']['PAID']"
				:estimated="$monthTotals['RECEI']['TO_PAY']"
			/>
		</div>

		<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pe-lg-2 pb-lg-3">
			<x-pages.transaction.part.month-totals
				color="danger"
				:title="__('Month Expenses')"
				:line1="__('Paid:')"
				:line2="__('Estimated')"
				:percent="$monthTotals['EXPEN']['PERC']"
				:paid="$monthTotals['EXPEN']['PAID']"
				:estimated="$monthTotals['EXPEN']['TO_PAY']"
			/>
		</div>

		<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pe-lg-2 pb-lg-3">
			<div class="card card-body bg-gradient-faded-light border-warning border-2">
				<div class="flex-fill">
					<div class="text-bold lh-sm">Estimated Month Balance</div>
					<div class="text-end">
						@php
							$class = 'success';

                            if($monthBalance->month_balance < 0){
								$class = 'danger';
                            }
						@endphp
						<span class="text-{{$class}} text-bold text-lg">@formatMoney($monthBalance->month_balance)</span>
					</div>
				</div>
				<div>
					<div class="lh-sm pt-4">Overall Balance</div>
					<div class="text-end">
						@php
							$class = 'success';

                            if($finalBalance->final_balance < 0){
								$class = 'danger';
                            }
						@endphp
						<span class="text-bold text-{{$class}}">@formatMoney($finalBalance->final_balance)</span>
					</div>
				</div>
				<div>
					<div class="lh-sm pt-4">Estimated Balance</div>
					<div class="text-end">
						<span class="text-muted">@formatMoney($estimatedBalance->final_balance)</span>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pb-lg-3">
			<x-pages.transaction.part.filters :$f_date :$f_acc :$f_pay :$comboAccounts :$comboPaid/>
		</div>

	</div>

</div>

@props(['monthTotals', 'monthBalance', 'finalBalance', 'estimatedBalance', 'f_date', 'f_acc', 'f_pay', 'comboAccounts', 'comboPaid'])

<div class="container-fluid">

	<div class="row">

		<h4 class="m-0 p-0 pb-4">{{__('Summary')}}</h4>

		<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-2 pb-4 p-sm-1 p-lg-0 pe-lg-2 pb-lg-3">
			<x-pages.transaction.part.month-totals
				shadow="shadow-success"
				color="bg-gradient-success"
				icon="money-bill-trend-up"
				:title="__('Month Incoming')"
				:line1="__('Received:')"
				:line2="__('Estimated')"
				:percent="$monthTotals['RECEI']['PERC']"
				:paid="$monthTotals['RECEI']['PAID']"
				:estimated="$monthTotals['RECEI']['TO_PAY']"
			/>
		</div>

		<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pe-lg-2 pb-lg-3">
			<x-pages.transaction.part.month-totals
				shadow="shadow-danger"
				color="bg-gradient-danger"
				icon="file-invoice-dollar"
				:title="__('Month Expenses')"
				:line1="__('Paid:')"
				:line2="__('Estimated')"
				:percent="$monthTotals['EXPEN']['PERC']"
				:paid="$monthTotals['EXPEN']['PAID']"
				:estimated="$monthTotals['EXPEN']['TO_PAY']"
			/>
		</div>

		<div class="col-12 col-sm-6 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pe-lg-2 pb-lg-3">
			<div class="card shadow-warning border-2">
				<div class="card-header mb-0 pb-0">
					<div
						class="icon icon-lg icon-shape bg-gradient-warning shadow-info text-center border-radius-xl mt-n5 position-absolute">
						<i class="fa-solid fa-balance-scale opacity-10"></i>
					</div>
					<div class="text-end pt-1">
						<p class="text-sm mb-0 text-capitalize">{{__('Estimated Month Balance')}}</p>
					</div>
				</div>
				<div class="card-footer mt-0 pt-0">
					<div class="flex-fill">
						<div class="text-end">
							@php
								$class = 'text-success';

								if($monthBalance->month_balance < 0){
									$class = 'text-danger';
								}
							@endphp
							<span class="{{$class}} text-bold text-lg">@formatMoney($monthBalance->month_balance)</span>
						</div>
					</div>
					<div>
						<div class="text-sm lh-sm pt-4">{{__('Overall Balance')}}</div>
						<div class="text-end">
							@php
								$class = 'text-success';

								if($finalBalance->final_balance < 0){
									$class = 'text-danger';
								}
							@endphp
							<span class="text-sm text-bold {{$class}}">@formatMoney($finalBalance->final_balance)</span>
						</div>
					</div>
					<div>
						<div class="text-sm lh-sm pt-4">{{__('Estimated Balance')}}</div>
						<div class="text-end">
							<span class="text-sm text-muted">@formatMoney($estimatedBalance->final_balance)</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-md-4 col-lg-3 p-2 p-sm-1 p-lg-0 pb-lg-3">
			<x-pages.transaction.part.filters :$f_date :$f_acc :$f_pay :$comboAccounts :$comboPaid/>
		</div>

	</div>

</div>

@props(['monthTotals'])

<div class="row mx-0">

	<h3>{{__('Summary')}}</h3>
	<div class="d-flex">
		<div class="card card-body me-2 mb-1 col-2">
			<div>{{__('Receipts')}}</div>
			<div>{{$monthTotals['RECEI']['PAID']}}</div>
			<div>{{$monthTotals['RECEI']['TO_PAY']}}</div>
		</div>

		<div class="card card-body me-2 mb-1 col-2">
			<div>{{__('Expenses')}}</div>
			<div>{{$monthTotals['EXPEN']['PAID']}}</div>
			<div>{{$monthTotals['EXPEN']['TO_PAY']}}</div>
		</div>

		<div class="col-10">

		</div>

	</div>

</div>

<x-pages.default-card :$viewAttributes :$item type="form">

	<x-pages.part.combo :options="$comboTypes" name="transaction_type"
						:default="old('transaction_type', $item->transaction_type)" label="Type"/>

	<x-pages.part.combo :options="$comboAccounts" name="account_id" :default="old('account_id', $item->account_id)"
						label="Account"/>

	<x-pages.part.combo :options="$comboAccounts" name="account_id_to" :default="old('account_id', $item->account_id)"
						label="Account To" hidden="true"/>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Description')}}</label>
		<input type="text" name="description" class="form-control" value='{{ old('description', $item->description) }}'>
		@error('description')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<x-pages.part.combo :options="$comboFromTos" name="from_to_id" :default="old('from_to_id', $item->from_to_id)"
						label="From / To"/>

	<x-pages.part.combo :options="$comboCategories" name="category_id" :default="old('category_id', $item->category_id)"
						label="Category"/>

	<x-pages.part.combo :options="$comboPaymentTypes" name="payment_type_id"
						:default="old('payment_type_id', $item->payment_type_id)" label="Payment Type"/>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Amount')}}</label>
		<input type="number" step="0.01" name="amount" class="form-control" value='{{ old('amount', $item->amount) }}'>
		@error('amount')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<x-pages.part.combo :options="$comboPaid" name="status" :default="old('status', $item->status)" label="Paid?"/>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Due Date')}}</label>
		<input type="date" name="date_due" class="form-control" value='{{ old('date_due', $item->date_due) }}'>
		@error('date_due')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	@if($item->date_payment)
		<div class="input-group input-group-static mb-3 col-md-6">
			<label>{{__('Payment Date')}}</label>
			<input type="date" name="date_payment" class="form-control" disabled readonly
				   value='{{ old('date_payment', $item->date_payment) }}'>
			@error('date_payment')
			<p class='text-danger inputerror'>{{ $message }} </p>
			@enderror
		</div>
	@endif

</x-pages.default-card>

<script>
	document.addEventListener("DOMContentLoaded", function () {

		document.getElementById('transaction_type').addEventListener('change', function () {
			formatForm()
		})

		formatForm()

		function formatForm() {
			const type = document.getElementById('transaction_type')

			const accFrom = document.getElementById('account_id')
			const accTo = document.getElementById('account_id_to')
			const fromTo = document.getElementById('from_to_id')
			const category = document.getElementById('category_id')
			const payType = document.getElementById('payment_type_id')

			if (type.value === 'TRANS') {
				accFrom.previousElementSibling.innerText = 'Account From'
				accTo.parentElement.setAttribute('style', 'display:flex;')

				fromTo.parentElement.setAttribute('style', 'display:none;')
				category.parentElement.setAttribute('style', 'display:none;')
				payType.parentElement.setAttribute('style', 'display:none;')
			} else {
				accFrom.previousElementSibling.innerText = 'Account'
				accTo.parentElement.setAttribute('style', 'display:none;')

				fromTo.parentElement.setAttribute('style', 'display:flex;')
				category.parentElement.setAttribute('style', 'display:flex;')
				payType.parentElement.setAttribute('style', 'display:flex;')
			}
		}
	});
</script>

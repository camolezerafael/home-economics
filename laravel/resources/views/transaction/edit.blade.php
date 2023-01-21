<x-pages.default-card :$viewAttributes :$item type="form">


	<x-pages.part.combo :options="$comboAccounts" name="account_id" :default="old('account_id', $item->account_id)" label="Accounts" />

	<x-pages.part.combo :options="$comboTypes" name="transaction_type" :default="old('transaction_type', $item->transaction_type)" label="Type" />

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Description')}}</label>
		<input type="text" name="description" class="form-control" value='{{ old('description', $item->description) }}'>
		@error('description')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<x-pages.part.combo :options="$comboFromTos" name="from_to_id" :default="old('from_to_id', $item->from_to_id)" label="From / To" />

	<x-pages.part.combo :options="$comboCategories" name="category_id" :default="old('category_id', $item->category_id)" label="Category" />

	<x-pages.part.combo :options="$comboPaymentTypes" name="payment_type_id" :default="old('payment_type_id', $item->payment_type_id)" label="Payment Type" />

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Value')}}</label>
		<input type="text" name="value" class="form-control" value='{{ old('value', $item->value) }}'>
		@error('value')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<x-pages.part.combo :options="$comboPaid" name="status" :default="old('status', $item->status)" label="Paid?" />

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Due Date')}}</label>
		<input type="date" name="date_due" class="form-control" value='{{ old('date_due', $item->date_due) }}'>
		@error('date_due')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Payment Date')}}</label>
		<input type="date" name="date_payment" class="form-control" disabled readonly value='{{ old('date_payment', $item->date_payment) }}'>
		@error('date_payment')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

</x-pages.default-card>

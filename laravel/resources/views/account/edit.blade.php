<x-pages.default-card :$viewAttributes :$item type="form">

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Name')}}</label>
		<input type="text" name="name" class="form-control" value='{{ old('name', $item->name) }}'>
		@error('name')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<div class="input-group input-group-static mb-3 col-md-12">
		<label for="description">{{__('Description')}}</label>
		<textarea class="form-control"
				  placeholder=" Say something about this {{$viewAttributes['singularItem']}}"
				  name="description"
				  rows="2" cols="50">{{ old('description', $item->description) }}</textarea>
		@error('description')
		<p class='text-danger inputerror'>{{ $message }}</p>
		@enderror
	</div>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Initial Balance')}}</label>
		<input type="number" name="initial_balance" class="form-control"
			   value='{{ old('initial_balance', $item->initial_balance) }}'>
		@error('name')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Decimal Precision')}}</label>
		<input type="number" name="decimal_precision" class="form-control"
			   value='{{ old('decimal_precision', $item->decimal_precision) }}'>
		@error('name')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Type')}}</label>
		<input type="text" name="type_id" class="form-control" value='{{ old('type_id', $item->type_id) }}'>
		@error('name')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

</x-pages.default-card>

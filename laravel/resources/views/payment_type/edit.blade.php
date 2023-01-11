<x-pages.form-part :viewAttributes="$viewAttributes" :item="$item">

	<div class="input-group input-group-static mb-3 col-md-6">
		<label>{{__('Name')}}</label>
		<input type="text" name="name" class="form-control" value='{{ old('name', $item->name) }}'>
		@error('name')
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

</x-pages.form-part>

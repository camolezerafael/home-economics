<x-pages.form-part :viewAttributes="$viewAttributes" :item="$item">

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
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</div>

</x-pages.form-part>

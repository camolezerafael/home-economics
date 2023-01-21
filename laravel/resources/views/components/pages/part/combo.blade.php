@props(['options', 'default', 'name', 'label'])

<div class="input-group input-group-static mb-3 col-md-6">
	<label class="w-100">{{__($label)}}
		<select class="form-control px-3" name="{{$name}}" id="{{$name}}" style="height: 2.7em">
			@foreach($options as $k => $v)
				<option value="{{$k}}" {{ $default == $k ? ' selected="selected"' : '' }}>{{$v}}</option>
			@endforeach
		</select>
		@error($name)
		<p class='text-danger inputerror'>{{ $message }} </p>
		@enderror
	</label>
</div>

@props(['options', 'default', 'name', 'label'])

<div class="input-group input-group-outline ">
	<label class="w-100">{{__($label)}}
		<select class="form-select form-select-sm px-3 text-sm" name="{{$name}}" id="{{$name}}" style="height: 2.7em">
			@foreach($options as $k => $v)
				<option value="{{$k}}" {{ $default == $k ? ' selected="selected"' : '' }}>{{$v}}</option>
			@endforeach
		</select>
	</label>
</div>

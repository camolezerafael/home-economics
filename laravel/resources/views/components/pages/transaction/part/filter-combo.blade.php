@props(['options', 'default', 'name', 'label'])

<div class="d-inline-flex">
	<div class="input-group input-group-outline ">
		<label>{{__($label)}}
			<select class="form-select form-select-sm px-3" name="{{$name}}" id="{{$name}}" style="height: 2.7em">
				@foreach($options as $k => $v)
					<option value="{{$k}}" {{ $default == $k ? ' selected="selected"' : '' }}>{{$v}}</option>
				@endforeach
			</select>
		</label>
	</div>
</div>

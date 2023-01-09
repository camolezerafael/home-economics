@extends('layouts.app')

@section('content')
	<table class="table">
		<thead>
		<tr>
			<th>{{__('Name')}}</th>
			<th>{{__('Created At')}}</th>
		</tr>
		</thead>
		<tbody>

		@foreach($list as $item)
			<tr>
				<td>{{ $item->name }}</td>
				<td>{{ $item->created_at }}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@endsection

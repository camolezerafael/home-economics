<x-pages.crud-card :viewAttributes="$viewAttributes" header="true">
	<div class="card-body p-3">
		@if (session('status'))
			<div class="row">
				<div class="alert alert-success alert-dismissible text-white" role="alert">
					<span class="text-sm">{{ Session::get('status') }}</span>
					<button type="button" class="btn-close text-lg py-3 opacity-10"
							data-bs-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
		@endif
		<form action="{{ $item->_uri }}" method="POST">
			<input type="hidden" name="_uri" value="{{ $item->_uri }}">
			<input type="hidden" name="_method" value="{{ $item->_method }}">
			@csrf
			<div class="row">

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
			</div>
			<div class="text-end">
				<a href="/{{$viewAttributes['homePage']}}" class="btn btn-info text-end">{{__('Back')}}</a>
				<button type="submit" class="btn bg-gradient-success text-end">{{__('Save')}}</button>
			</div>
		</form>

	</div>

</x-pages.crud-card>

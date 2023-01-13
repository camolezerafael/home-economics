@props(['viewAttributes', 'item'])

<x-pages.crud-card :$viewAttributes header="true">
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
			{{ $slot }}
			</div>
			<div class="text-end">
				<a href="/{{$viewAttributes['homePage']}}" class="btn btn-info text-end">{{__('Back')}}</a>
				<button type="submit" class="btn bg-gradient-success text-end">{{__('Save')}}</button>
			</div>
		</form>

	</div>
</x-pages.crud-card>

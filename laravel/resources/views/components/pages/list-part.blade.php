@props(['viewAttributes', 'items'])

<x-pages.crud-card :$viewAttributes addButton="true" :$f_date :$f_acc>

	<div class="card-body p-0 py-2">
		<div class="table-responsive p-0">
			<table class="table table-striped table-hover align-items-center mb-0">
				<thead>
				<tr>
					@foreach($items[0]->getFillable() as $fieldName)
						<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-2 col-sm-12">
							@if(count($items[0]->labels()) && array_key_exists($fieldName, $items[0]->labels()))
								{{__(\Illuminate\Support\Str::of($items[0]->labels()[$fieldName])->remove('_id')->headline()->toString())}}
							@else
								{{__(\Illuminate\Support\Str::of($fieldName)->remove('_id')->headline()->toString())}}
							@endif
						</th>
					@endforeach
					<th class="text-uppercase text-dark text-sm text-center font-weight-bolder opacity-7 col-md-1 col-sm-12">
						{{__('Actions')}}
					</th>
				</tr>
				</thead>
				<tbody>

				@foreach($items as $item)
					<tr>
						@foreach($item->getFillable() as $fieldName)
							<td class="align-middle text-start">
								<span class="text-secondary text-md font-weight-normal px-3">
									@if(\Illuminate\Support\Str::contains($fieldName,'_id'))
										{{ $item->{Str::of($fieldName)->remove('_id')->toString()}->name }}
									@else
										{{ $item->{Str::of($fieldName)->toString()} }}
									@endif
								</span>
							</td>
						@endforeach
						<td class="align-middle text-center px-3">
							<div class="btn-group-sm">
								<a href="/{{$viewAttributes['routePath']}}/{{ $item->id  }}/edit"
								   class="btn btn-secondary mb-0"
								   data-bs-toggle="tooltip" data-bs-placement="top"
								   title="{{ __('Edit ') . $viewAttributes['singularItem'] }}">
									<i class="fa fa-pencil text-sm"></i>
								</a>

								<a href="javascript:;"
								   class="btn btn-danger mb-0"
								   data-bs-toggle="tooltip" data-bs-placement="top"
								   title="{{ __('Delete ') . $viewAttributes['singularItem'] }}">
									<i class="fa fa-times text-sm"></i>
								</a>
							</div>
						</td>
					</tr>
				@endforeach

				</tbody>
			</table>

			<p>
				{{  $items->links('components.pagination.default') }}
			</p>
		</div>
	</div>


</x-pages.crud-card>

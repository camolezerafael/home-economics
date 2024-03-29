@props(['viewAttributes', 'items'])

<x-navbars.pages.list :$viewAttributes/>

<div class="card-body p-0 py-2">
	<div class="table-responsive p-0">
		<table class="table table-striped table-hover align-items-center mb-0">
			<thead>
			<tr>
				@php
					$fields = array_diff($items[0]->getFillable(),$items[0]->getHidden());
				@endphp
				@foreach($fields as $fieldName)
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
					@php
						$fields = array_diff($item->getFillable(),$item->getHidden());
					@endphp
					@foreach($fields as $fieldName)
						<td class="align-middle text-start">
							<span class="text-secondary text-md font-weight-normal px-3">

								@if(\Illuminate\Support\Str::contains($fieldName,'_id'))
									{{ $item->{Str::of($fieldName)->remove('_id')->toString()}->name }}

								@elseif(isset($item->getCasts()[Str::of($fieldName)->toString()]) && Str::of($item->getCasts()[Str::of($fieldName)->toString()])->contains(['decimal', 'double', 'float']) )
									@formatMoney( $item->{Str::of($fieldName)->toString()} )
								@else
									{{ $item->{Str::of($fieldName)->toString()} }}
								@endif
							</span>
						</td>
					@endforeach

					<td class="align-middle text-end p-1">
						<a href="/{{$viewAttributes['routePath']}}/{{ $item->id  }}/edit"
						   class="btn btn-sm btn-secondary mb-0"
						   data-bs-toggle="tooltip" data-bs-placement="left"
						   title="{{ __('Edit ') . $viewAttributes['singularItem'] }}">
							<i class="fa fa-pencil text-sm"></i>
						</a>

						<span data-bs-toggle="tooltip" data-bs-placement="left"
							  title="{{ __('Delete ') . $viewAttributes['singularItem'] }}">
							<a href="javascript:;" class="btn btn-sm btn-danger mb-0" data-bs-toggle="modal"
							   data-bs-target="#modal-delete" onclick="callDelete({{$item->id}})">
								<i class="fa fa-times text-lg" style="margin-left: 1px; margin-top:1px;"></i>
							</a>
						</span>
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


<x-pages.part.modal-delete route="{{$viewAttributes['routePath']}}"/>

<x-pages.crud-card :viewAttributes="$viewAttributes" addButton="true">

	<div class="card-body p-0 py-2">
		<div class="table-responsive p-0">
			<table class="table table-striped table-hover align-items-center mb-0">
				<thead>
				<tr>
					<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-3 col-sm-12">
						{{__('Name')}}
					</th>
					<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-4 col-sm-12">
						{{__('Description')}}
					</th>
					<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-2 col-sm-12">
						{{__('Initial Balance')}}
					</th>
					<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
						{{__('Decimal Precision')}}
					</th>
					<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
						{{__('Type')}}
					</th>
					<th class="text-uppercase text-dark text-sm text-center font-weight-bolder opacity-7 col-md-1 col-sm-12">
						{{__('Actions')}}
					</th>
				</tr>
				</thead>
				<tbody>
				@foreach($items as $item)
					<tr>
						<td class="align-middle text-start">
							<span class="text-secondary text-md font-weight-normal px-3">{{ $item->name }}</span>
						</td>
						<td class="align-middle text-start">
							<span class="text-secondary text-md font-weight-normal px-3">{{ $item->description }}</span>
						</td>
						<td class="align-middle text-start">
							<span class="text-secondary text-md font-weight-normal px-3">{{ $item->initial_balance }}</span>
						</td>
						<td class="align-middle text-start">
							<span class="text-secondary text-md font-weight-normal px-3">{{ $item->decimal_precision }}</span>
						</td>
						<td class="align-middle text-start">
							<span class="text-secondary text-md font-weight-normal px-3">{{ $item->account_type->name }}</span>
						</td>
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
		</div>
	</div>

	{{  $items->links('components.pagination.default') }}

</x-pages.crud-card>

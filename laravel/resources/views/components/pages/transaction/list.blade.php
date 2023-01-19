@props(['viewAttributes', 'items', 'labelFromTo', 'labelTotal'])

<div class="card-body p-0 py-2">
	<p class="table-responsive p-0">
	<table class="table table-striped table-hover align-items-center mb-0">
		<thead>
		<tr>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
				{{__('Due Date')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-4 col-sm-12">
				{{__('Description')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
				{{__($labelFromTo)}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-2 col-sm-12">
				{{__('Value')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
				{{__('Category')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
				{{__('Payment')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-1 col-sm-12">
				{{__('Paid')}}
			</th>
			<th class="text-uppercase text-dark text-sm text-center font-weight-bolder opacity-7 col-md-1 col-sm-12">
				{{__('Actions')}}
			</th>
		</tr>
		</thead>
		<tbody>

		@php
			$paid = 0;
            $unpaid = 0;
		@endphp

		@foreach($items as $item)
			@php
				$paid += ($item->status) ? $item->value : 0;
				$unpaid += (!$item->status) ? $item->value : 0;

                $dateDue = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date_due);

                $border = '';
                $class = '';
                if($dateDue->copy()->lt(\Carbon\Carbon::now())){
                    $border = 'border-warning border-3 border-top-0 border-start-0 border-end-0';
                    $class = 'text-danger font-weight-bold';
                }

			@endphp
			<tr class="{{$border}}">

				<td class="align-middle text-start">
					<span class="text-secondary text-sm font-weight-normal px-3 {{$class}}">
						{{ $dateDue->format('d/m') }}
					</span>
				</td>

				<td class="align-middle text-start">
					<p class="text-md font-weight-normal mb-0 px-3">
						{{ $item->description }}
					</p>
					<p class="text-xs text-secondary font-weight-normal px-3 mb-0">
						{{ $item->account->name }}
					</p>
				</td>

				<td class="align-middle text-start">
					<span class="text-secondary text-sm font-weight-normal px-3">
						{{ $item->from_to->name }}
					</span>
				</td>

				<td class="align-middle text-start">
					<span class="text-secondary text-sm font-weight-bold px-3">
						@formatMoney( $item->value )
					</span>
				</td>

				<td class="align-middle text-start">
					<span class="text-secondary text-sm font-weight-normal px-3">
						{{ $item->category->name }}
					</span>
				</td>

				<td class="align-middle text-start">
					<span class="text-secondary text-sm font-weight-normal px-3">
						{{ $item->payment_type->name }}
					</span>
				</td>

				<td class="align-middle text-center">
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" {{ $item->status ? 'checked' : '' }} />
					</div>
				</td>

				<td class="align-middle text-center p-1">
					<p class="text-sm p-0 m-1">
						<a href="/{{$viewAttributes['routePath']}}/{{ $item->id  }}/edit"
						   class="btn btn-sm btn-secondary mb-0"
						   data-bs-toggle="tooltip" data-bs-placement="top"
						   title="{{ __('Edit ') . $viewAttributes['singularItem'] }}">
							<i class="fa fa-pencil text-sm"></i>
						</a>
					</p>
					<p class="text-sm p-0 m-1">
						<a href="javascript:;"
						   class="btn btn-sm btn-danger mb-0"
						   data-bs-toggle="tooltip" data-bs-placement="top"
						   title="{{ __('Delete ') . $viewAttributes['singularItem'] }}">
							<i class="fa fa-times text-md"></i>
						</a>
					</p>
				</td>

			</tr>
		@endforeach

		</tbody>
	</table>

</div>

<x-pages.transaction.total :$paid :$unpaid :$labelTotal />

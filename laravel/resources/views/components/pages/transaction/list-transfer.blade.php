@props(['viewAttributes', 'items', 'labelFromTo', 'labelTotal'])

<div class="card-body p-0 py-2">
	<p class="table-responsive p-0">
	<table class="table table-striped table-bordered table-hover align-items-center mb-0">
		<thead>
		<tr class="">
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-12 col-sm-1">
				{{__('Due Date')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-12 col-sm-4">
				{{__('Description')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-12 col-sm-2">
				{{__('Amount')}}
			</th>
			<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-12 col-sm-1">
				{{__('Paid')}}
			</th>
			<th class="text-uppercase text-dark text-sm text-center font-weight-bolder opacity-7 col-12 col-sm-1">
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
				$paid += ($item->status) ? $item->amount : 0;
				$unpaid += (!$item->status) ? $item->amount : 0;

                $dateDue = \Carbon\Carbon::createFromFormat('Y-m-d', $item->date_due);

                $border = '';
                $class = '';
                if(!$item->status && $dateDue->lt(\Carbon\Carbon::now())){
                    $border = 'table-danger';
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
					<span class="text-secondary text-sm font-weight-bold px-3">
						@formatMoney( $item->amount )
					</span>
				</td>

				<td class="align-middle text-center">
					<div class="w-100 d-flex justify-content-center">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" onclick="changeStatus({{$item->id}})" {{ $item->status ? 'checked' : '' }} />
						</div>
					</div>
				</td>

				<td class="align-middle text-end p-1">
					<a href="/{{$viewAttributes['routePath']}}/{{ $item->id  }}/edit"
					   class="btn btn-sm btn-secondary mb-0"
					   data-bs-toggle="tooltip" data-bs-placement="left"
					   title="{{ __('Edit ') . $viewAttributes['singularItem'] }}">
						<i class="fa fa-pencil text-sm"></i>
					</a>

					<span data-bs-toggle="tooltip" data-bs-placement="left" title="{{ __('Delete ') . $viewAttributes['singularItem'] }}">
						<a href="javascript:;" class="btn btn-sm btn-danger mb-0" data-bs-toggle="modal" data-bs-target="#modal-delete" onclick="callDelete({{$item->id}})">
							<i class="fa fa-times text-lg" style="margin-left: 1px; margin-top:1px;"></i>
						</a>
					</span>
				</td>

			</tr>
		@endforeach

		</tbody>
	</table>

</div>

<x-pages.transaction.total :$paid :$unpaid :$labelTotal/>

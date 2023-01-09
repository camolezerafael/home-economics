<x-layout bodyClass="g-sidenav-show  bg-gray-200">
	<x-navbars.sidebar activePage="account_types"></x-navbars.sidebar>

	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
		<!-- Navbar -->
		<x-navbars.navs.auth titlePage="{{ 'Account Types' }}" basePage="{{ 'Configurations' }}"></x-navbars.navs.auth>
		<!-- End Navbar -->
		<div class="container-fluid py-4 pt-1">
			<div class="row">
				<div class="card col-12">
					<div class="card-body p-0 py-2">
						<div class="table-responsive p-0">
							<table class="table align-items-center mb-0">
								<thead>
								<tr>
									<th class="text-uppercase text-dark text-sm  font-weight-bolder opacity-7 col-md-3 col-sm-12">
										{{__('Name')}}</th>
									<th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 col-md-8 col-sm-12">
										{{__('Description')}}
									</th>
									<th class="text-uppercase text-dark text-sm text-center font-weight-bolder opacity-7 col-md-1 col-sm-12">
										{{__('Actions')}}
									</th>
								</tr>
								</thead>
								<tbody>
								@foreach($items as $type)
									<tr>
										<td class="align-middle text-start">
											<span
												class="text-secondary text-md font-weight-normal px-3">{{ $type->name }}</span>
										</td>
										<td class="align-middle text-start">
											<span
												class="text-secondary text-md font-weight-normal px-3">{{ $type->description }}</span>
										</td>
										<td class="align-middle text-center px-3">
											<div class="btn-group-sm">
												<a href="/account_type/{{ $type->id  }}/edit"
												   class="btn btn-secondary mb-0"
												   data-bs-toggle="tooltip" data-bs-placement="top"
												   title="{{ __('Edit Type') }}">
													<i class="fa fa-pencil text-sm"></i>
												</a>

												<a href="javascript:;"
												   class="btn btn-danger mb-0"
												   data-bs-toggle="tooltip" data-bs-placement="top"
												   title="{{ __('Delete Type') }}">
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

				</div>
			</div>

			<x-footers.auth></x-footers.auth>
		</div>
	</main>
	<x-plugins></x-plugins>

</x-layout>

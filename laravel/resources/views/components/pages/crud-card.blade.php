@props(['viewAttributes', 'addButton', 'header'])

<x-layout bodyClass="g-sidenav-show bg-gray-200">
	<x-navbars.sidebar activePage="{{$viewAttributes['routePath']}}"></x-navbars.sidebar>

	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
		<!-- Navbar -->
		<x-navbars.navs.auth :viewAttributes="$viewAttributes"></x-navbars.navs.auth>
		<!-- End Navbar -->
		<div class="container-fluid py-4 pt-1 pb-0">
			<div class="row">
				<div class="card col-12">
					@if(isset($addButton) && $addButton)
					<div class=" me-3 my-3 text-end">
						<a class="btn btn-info mb-0" href="/{{$viewAttributes['routePath']}}/create"><i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{__('Add
							New')}}
							{{$viewAttributes['singularItem']}}</a>
					</div>
					@endif

					@if(isset($header) && $header)
					<div class="card-header pb-0 p-3">
						<div class="row">
							<div class="col-md-8 d-flex align-items-center">
								<h6 class="mb-3">{{$viewAttributes['singularItem']}}</h6>
							</div>
						</div>
					</div>
					@endif

					{{$slot}}

				</div>
			</div>

			<x-footers.auth></x-footers.auth>
		</div>
	</main>
	<x-plugins></x-plugins>

</x-layout>

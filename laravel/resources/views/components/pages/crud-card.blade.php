@props(['viewAttributes','f_date', 'f_acc', 'addButton'])

<x-layout bodyClass="g-sidenav-show bg-gray-200">
	<x-navbars.sidebar activePage="{{$viewAttributes['routePath']}}"></x-navbars.sidebar>

	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
		<!-- Navbar -->
		<x-navbars.navs.auth :$viewAttributes></x-navbars.navs.auth>
		<!-- End Navbar -->
		<div class="container-fluid py-4 pt-1 pb-0">
			<div class="row">
				<div class="card col-12">

					<div class="d-inline-flex justify-content-between">
						<div class="me-3 my-3 text-start">
							@if(isset($f_date) && strlen($f_date))
								<form method="GET" id="date-filter">
									<div class="input-group input-group-dynamic">
										<button type="button" class="btn btn-secondary btn-sm" id="btn-date-dec"><i class="fas fa-chevron-left"></i></button>
										<input class="form-control form-control-sm px-2 col-2" style="height: 2.7em" name="f_date" id="f_date" type="month" min="2020-01" value="{{ $f_date }}" aria-label="Date filter to show moviments" onchange="document.getElementById('date-filter').submit()">
										<button type="button" class="btn btn-secondary btn-sm" id="btn-date-inc"><i class="fas fa-chevron-right"></i></button>
									</div>
								</form>
							@endif

							@if(isset($f_acc) && strlen($f_acc))
								<form method="GET" id="acc-filter">
									<div class="input-group input-group-static mx-2">
										<input class="form-control" name="f_date" type="month" min="2020-01" value="{{ $f_date }}" aria-label="Date filter to show moviments" onchange="document.getElementById('date-filter').submit()">
									</div>
								</form>
							@endif
						</div>

						@if(isset($addButton) && $addButton)
							<div class=" me-3 my-3 text-end">
								<a class="btn btn-info mb-0" href="/{{$viewAttributes['routePath']}}/create"><i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{__('Add
									New')}}
									{{$viewAttributes['singularItem']}}</a>
							</div>
						@endif
					</div>

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

<script>
	document.getElementById('btn-date-dec').addEventListener(
		"click",
		() => {
			changeDateFilter(calcDateChange('DEC'))
		},
	);

	document.getElementById('btn-date-inc').addEventListener(
		"click",
		() => {
			changeDateFilter(calcDateChange('INC'))
		},
	);

	function calcDateChange(type){
		const dt = document.getElementById('f_date').value.split('-')

		let month = parseInt(dt[1])
		let year = parseInt(dt[0])

		if(type === 'INC')
		{
			month += 1
		}else if(type === 'DEC')
		{
			month -= 1
		}

		if (month === 13)
		{
			month = 1
			year += 1
		}
		else if (month === 0)
		{
			month = 12
			year -= 1
		}

		return year.toString() + '-' + month.toString().padStart(2, '0')
	}

	function changeDateFilter(date){
		document.getElementById('f_date').value = date
		document.getElementById('f_date').dispatchEvent(new Event("change"))
	}
</script>


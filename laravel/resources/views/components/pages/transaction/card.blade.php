@props(['viewAttributes','f_date', 'f_acc', 'f_pay', 'comboAccounts', 'comboPaid'])

<div class="container-fluid ">
	<div class="row">
		<div class="card col-12">

			<div class="d-inline-flex justify-content-between">

				<x-pages.transaction.part.filters :$viewAttributes :$f_date :$f_acc :$f_pay :$comboAccounts :$comboPaid/>

				<div class=" me-3 my-3 text-end">
					<a class="btn btn-info mb-0" href="/{{$viewAttributes['routePath']}}/create">
						<i class="material-icons text-sm">add</i>
						&nbsp;&nbsp;{{__('Add New')}}
						{{$viewAttributes['singularItem']}}
					</a>
				</div>
			</div>

			{{$slot}}

		</div>
	</div>

	<x-footers.auth/>
</div>

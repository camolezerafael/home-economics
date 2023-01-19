@props(['viewAttributes'])

<div class="container-fluid ">
	<div class="row">
		<div class="card col-12">

			{{$slot}}

		</div>
	</div>

	<x-footers.auth/>
</div>

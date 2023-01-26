<div class="nav-wrapper position-relative end-0 mx-2 mt-1">

	<ul class="nav nav-pills nav-fill p-1" id="transaction-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="pill" href="#transactions-tab-receipts"
			   role="tab" aria-controls="receipts" aria-selected="true">
				<i class="fa-solid fa-money-check-dollar me-2 fs-5"></i>
				{{__('Incoming')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-fix-expenses" role="tab"
			   aria-controls="fix-expenses" aria-selected="false">
				<i class="fa-solid fa-file-invoice me-2 fs-5"></i>
				{{__('Fixed Expenses')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-var-expenses" role="tab"
			   aria-controls="var-expenses" aria-selected="false">
				<i class="fa-solid fa-credit-card me-2 fs-5"></i>
				{{__('Variable Expenses')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-people" role="tab"
			   aria-controls="people" aria-selected="false">
				<i class="fa-solid fa-people-group me-2 fs-5"></i>
				{{__('People')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-taxes" role="tab"
			   aria-controls="taxes" aria-selected="false">
				<i class="fa-solid fa-receipt me-2 fs-5"></i>
				{{__('Taxes')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-transfers" role="tab"
			   aria-controls="transfers" aria-selected="false">
				<i class="fa-solid fa-money-bill-transfer me-2 fs-5"></i>
				{{__('Transfers')}}
			</a>
		</li>
	</ul>

</div>

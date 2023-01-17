<div class="nav-wrapper position-relative end-0 mx-2 mt-1">

	<ul class="nav nav-pills nav-fill p-1" id="transaction-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="pill" href="#transactions-tab-receipts"
			   role="tab" aria-controls="receipts" aria-selected="true">
				<span class="material-icons align-middle mb-1">receipt</span>
				{{__('Receipts')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-fix-expenses" role="tab"
			   aria-controls="fix-expenses" aria-selected="false">
				<span class="material-icons align-middle mb-1">paid</span>
				{{__('Fixed Expenses')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-var-expenses" role="tab"
			   aria-controls="var-expenses" aria-selected="false">
				<span class="material-icons align-middle mb-1">currency_exchange</span>
				{{__('Variable Expenses')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-people" role="tab"
			   aria-controls="people" aria-selected="false">
				<span class="material-icons align-middle mb-1">groups</span>
				{{__('People')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-taxes" role="tab"
			   aria-controls="taxes" aria-selected="false">
				<span class="material-icons align-middle mb-1">request_quote</span>
				{{__('Taxes')}}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link mb-0 px-0 py-1" data-bs-toggle="pill" href="#transactions-tab-transfers" role="tab"
			   aria-controls="transfers" aria-selected="false">
				<span class="material-icons align-middle mb-1">sync</span>
				{{__('Transfers')}}
			</a>
		</li>
	</ul>

</div>

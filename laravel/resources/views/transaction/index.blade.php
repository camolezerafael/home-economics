<x-pages.crud-card :$viewAttributes addButton="true" :$f_date>

	<div class="nav-wrapper position-relative end-0">

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

	<div class="tab-content" id="transaction-tabsContent">

		<div class="tab-pane fade show active" id="transactions-tab-receipts" role="tabpanel" aria-labelledby="transactions-tab-receipts">
			<x-pages.transactions-list :viewAttributes="$viewAttributes" :items="$items['RECEI']" labelFromTo="{{__('From')}}" labelTotal="{{__('To Receipt')}}"/>
		</div>

		<div class="tab-pane fade" id="transactions-tab-fix-expenses" role="tabpanel" aria-labelledby="transactions-tab-fix-expenses">
			<x-pages.transactions-list :viewAttributes="$viewAttributes" :items="$items['FIEX']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
		</div>

		<div class="tab-pane fade" id="transactions-tab-var-expenses" role="tabpanel" aria-labelledby="transactions-tab-var-expenses">
			<x-pages.transactions-list :viewAttributes="$viewAttributes" :items="$items['VAREX']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
		</div>

		<div class="tab-pane fade" id="transactions-tab-people" role="tabpanel" aria-labelledby="transactions-tab-people">
			<x-pages.transactions-list :viewAttributes="$viewAttributes" :items="$items['PEOP']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
		</div>

		<div class="tab-pane fade" id="transactions-tab-taxes" role="tabpanel" aria-labelledby="transactions-tab-taxes">
			<x-pages.transactions-list :viewAttributes="$viewAttributes" :items="$items['TAXES']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
		</div>

		<div class="tab-pane fade" id="transactions-tab-transfers" role="tabpanel" aria-labelledby="transactions-tab-transfers">
			<x-pages.transactions-list :viewAttributes="$viewAttributes" :items="$items['TRANS']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
		</div>
	</div>


</x-pages.crud-card>

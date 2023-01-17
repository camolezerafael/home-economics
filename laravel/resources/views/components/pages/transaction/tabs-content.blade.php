@props(['viewAttributes', 'items'])

<div class="tab-content" id="transaction-tabsContent">

	<div class="tab-pane fade show active" id="transactions-tab-receipts" role="tabpanel" aria-labelledby="transactions-tab-receipts">
		<x-pages.transaction.list :$viewAttributes :items="$items['RECEI']" labelFromTo="{{__('From')}}" labelTotal="{{__('To Receipt')}}"/>
	</div>

	<div class="tab-pane fade" id="transactions-tab-fix-expenses" role="tabpanel" aria-labelledby="transactions-tab-fix-expenses">
		<x-pages.transaction.list :$viewAttributes :items="$items['FIXEX']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
	</div>

	<div class="tab-pane fade" id="transactions-tab-var-expenses" role="tabpanel" aria-labelledby="transactions-tab-var-expenses">
		<x-pages.transaction.list :$viewAttributes :items="$items['VAREX']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
	</div>

	<div class="tab-pane fade" id="transactions-tab-people" role="tabpanel" aria-labelledby="transactions-tab-people">
		<x-pages.transaction.list :$viewAttributes :items="$items['PEOPL']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
	</div>

	<div class="tab-pane fade" id="transactions-tab-taxes" role="tabpanel" aria-labelledby="transactions-tab-taxes">
		<x-pages.transaction.list :$viewAttributes :items="$items['TAXES']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
	</div>

	<div class="tab-pane fade" id="transactions-tab-transfers" role="tabpanel" aria-labelledby="transactions-tab-transfers">
		<x-pages.transaction.list :$viewAttributes :items="$items['TRANS']" labelFromTo="{{__('To')}}" labelTotal="{{__('To Pay')}}"/>
	</div>
</div>

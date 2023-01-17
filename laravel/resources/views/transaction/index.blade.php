<x-layout bodyClass="g-sidenav-show bg-gray-200">

	<x-navbars.sidebar activePage="{{$viewAttributes['routePath']}}"></x-navbars.sidebar>

	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
		<!-- Navbar -->
		<x-navbars.navs.auth :$viewAttributes></x-navbars.navs.auth>
		<!-- End Navbar -->

		<x-pages.transaction.part.summary :$monthTotals/>

		<x-pages.transaction.tabs />

		<x-pages.transaction.card :$viewAttributes :$f_date :$f_acc :$f_pay :$comboAccounts :$comboPaid >
			<x-pages.transaction.tabs-content :$viewAttributes :$items/>
		</x-pages.transaction.card>

	</main>
	<x-plugins />

</x-layout>

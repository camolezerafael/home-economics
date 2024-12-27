<script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>

<x-layout bodyClass="g-sidenav-show  bg-gray-200">

	<x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>

	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">

            <div class="row">

				<x-pages.dashboard.card-total
					class="bg-gradient-info shadow-info"
					icon="fas fa-balance-scale"
					title="{{__('Month Balance')}}"
					:amount="$totals['balance']"
					:percentage="$totals['balance_percentage']"
					type="in"
				/>

				<x-pages.dashboard.card-total
					class="bg-gradient-success shadow-success"
					icon="fas fa-money-bill-wave"
					title="{{__('Month Incoming')}}"
					:amount="$totals['incoming']"
					:percentage="$totals['incoming_percentage']"
					type="in"
				/>

				<x-pages.dashboard.card-total
					class="bg-gradient-danger shadow-danger"
					icon="fas fa-file-invoice"
					title="{{__('Month Outgoing (Fixed)')}}"
					:amount="$totals['fixed']"
					:percentage="$totals['fixed_percentage']"
					type="out"
				/>

				<x-pages.dashboard.card-total
					class="bg-gradient-danger shadow-danger"
					icon="fas fa-credit-card"
					title="{{__('Month Outgoing (Variable)')}}"
					:amount="$totals['variable']"
					:percentage="$totals['variable_percentage']"
					type="out"
				/>

            </div>

            <div class="row mt-4">

				<x-pages.dashboard.card-chart
					color="bg-gradient-info shadow-info"
					name="chart-balances"
					type="bar"
					legend="{{__('Monthly Balances')}}"
					dataLabel="Balance"
					:labels="$graphBalance['labels']"
					:data="$graphBalance['values']"
				>
					<x-slot:sublegend>
						<p class="text-sm">{{__('Last 6 Months Comparison')}}</p>
					</x-slot:sublegend>
				</x-pages.dashboard.card-chart>

				<x-pages.dashboard.card-chart
					color="bg-gradient-success shadow-success"
					name="chart-incoming"
					type="line"
					legend="{{__('Monthly Incoming')}}"
					dataLabel="Balance"
					:labels="$graphIncoming['labels']"
					:data="$graphIncoming['values']"
				>
					<x-slot:sublegend>
					<p class="text-sm">
						{{__('Last 6 Months Comparison')}}
					</p>
					</x-slot:sublegend>
				</x-pages.dashboard.card-chart>

				<x-pages.dashboard.card-chart
					color="bg-gradient-danger shadow-danger"
					name="chart-outgoing"
					type="line"
					legend="{{__('Monthly Outgoing')}}"
					dataLabel="Balance"
					:labels="$graphOutgoing['labels']"
					:data="$graphOutgoing['values']"
				>
					<x-slot:sublegend>
						<p class="text-sm">
							{{__('Last 6 Months Comparison')}}
						</p>
					</x-slot:sublegend>
				</x-pages.dashboard.card-chart>

            </div>

            <div class="row mb-4">




            </div>
            <x-footers.auth/>
        </div>
    </main>
    <x-plugins></x-plugins>

</x-layout>

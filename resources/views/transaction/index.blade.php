<x-layout bodyClass="g-sidenav-show bg-gray-200">

	<x-navbars.sidebar activePage="{{$viewAttributes['routePath']}}"></x-navbars.sidebar>

	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
		<!-- Navbar -->
		<x-navbars.navs.auth :$viewAttributes></x-navbars.navs.auth>
		<!-- End Navbar -->

		<x-pages.transaction.part.summary
			:$monthTotals
			:$monthBalance
			:$finalBalance
			:$estimatedBalance
			:$f_date
			:$f_acc
			:$f_pay
			:$comboAccounts
			:$comboPaid
		/>

		<x-pages.transaction.tabs />

		<x-pages.transaction.card :$viewAttributes>
			<x-pages.transaction.tabs-content :$viewAttributes :$items/>
		</x-pages.transaction.card>

	</main>

	<x-pages.part.modal-delete route="transaction" />

	<x-plugins />

</x-layout>

<script>
	function changeStatus(id) {
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'POST',
			url: actionRoute + id,
			dataType: 'json',
			success: function () {
				window.location.href = window.location.pathname + window.location.search + $('.nav-pills a.nav-link.active').attr('href');
				window.location.reload()
			},
			error: function () {
				alert('Não foi atualizado o status')
			}
		});
	}

	$(document).ready(function(){
		const urlHash = window.location.hash;

		if(urlHash !== undefined && urlHash.length > 0)
		{
			selectTab()
		}

		function selectTab(){
			const tab = document.querySelector('.nav-pills a.nav-link[href="'+urlHash+'"]');
			(new bootstrap.Tab(tab)).show();
		}
	})

</script>

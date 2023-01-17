@props(['viewAttributes', 'items', 'item', 'type'])

<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="{{$viewAttributes['routePath']}}"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth :$viewAttributes></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4 pt-1 pb-0">
            <div class="row">
                <div class="card col-12">
                    @if($type === 'list')
                        <x-pages.part.list :$viewAttributes :$items/>
                    @endif

                    @if($type === 'form')
                        <x-pages.part.form :$viewAttributes :$item>
                            {{$slot}}
                        </x-pages.part.form>
                    @endif

				</div>
			</div>

			<x-footers.auth></x-footers.auth>
		</div>
	</main>
	<x-plugins></x-plugins>

</x-layout>

@props(['activePage'])

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-2 fixed-start ms-2 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href=" {{ route('dashboard') }} ">
			<div class="text-white text-center me-0 d-flex align-items-center justify-content-center align-middle" >
				<i class="material-icons opacity-10 text-4xl">paid</i>
			</div>
            <span class="ms-2 font-weight-bold text-white text-lg">{{__('Home Economics')}}</span>
        </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">

			<li class="nav-item">
				<a class="nav-link text-white {{ $activePage == 'dashboard' ? ' active bg-gradient-info' : '' }} "
				   href="{{ route('dashboard') }}">
					<div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i class="material-icons opacity-10">dashboard</i>
					</div>
					<span class="nav-link-text ms-1">Dashboard</span>
				</a>
			</li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'transaction' ? 'active bg-gradient-info' : '' }} "
                    href="/transactions">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i class="material-icons opacity-10">receipt_long</i>
                    </div>
                    <span class="nav-link-text ms-1">Transactions</span>
                </a>
            </li>

			<li class="nav-item mt-3">
				<h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Configurations</h6>
			</li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'account' ? ' active bg-gradient-info' : '' }} "
                    href="/accounts">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i style="font-size: 1.2rem;" class="fas fa-landmark text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Accounts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'account_type' ? ' active bg-gradient-info' : '' }} "
                    href="/account_types">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i style="font-size: 1rem;" class="fas fa-tags text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Account Types</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'category' ? ' active bg-gradient-info' : '' }}  "
                    href="/categories">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i style="font-size: 1.2rem;" class="fas fa-stream text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'from_to' ? ' active bg-gradient-info' : '' }}  "
                    href="/from_tos">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i style="font-size: 1.1rem;" class="fas fa-people-arrows text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">From & To</span>
                </a>
            </li>
			<li class="nav-item">
				<a class="nav-link text-white {{ $activePage == 'payment_type' ? ' active bg-gradient-info' : '' }}  "
				   href="/payment_types">
					<div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
						<i style="font-size: 1rem;" class="fas fa-money-bill-wave text-center"></i>
					</div>
					<span class="nav-link-text ms-1">Payment Types</span>
				</a>
			</li>

			<li class="nav-item mt-3">
				<h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Profile</h6>
			</li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'user' ? ' active bg-gradient-info' : '' }}  "
                    href="/profile">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <span class="nav-link-text ms-1">Users</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

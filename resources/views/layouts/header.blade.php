 <!-- begin::header -->
 <div class="header">

 	<div>
 		<ul class="navbar-nav">

 			<li class="nav-item dropdown">
 				<a href="#" class="nav-link header-title" title="User menu" data-toggle="dropdown" aria-expanded="false">
 					<h1>{{ env('APP_TITLE', env('APP_NAME', 'SYSTEM')) }}</h1>
 				</a>
             </li>

			 @if(Session::get('navigation') && env('BREADCRUMB_ENABLED', false))
			 @forelse (collect(Session::get('navigation'))->take(6) as $breadcrumb)
			 <li class="nav-item" style="margin-right: 10px">
				 <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
					<a href="{{ $breadcrumb['url'] }}" class="nav-link btn btn-primary btn-sm" title="Hide navigation">
						{{ $breadcrumb['menu_name'] }}
					</a>
					<a class="btn btn-dark" href="{{ route('delete_url', ['code' => $breadcrumb['menu_action']]) }}">x</a>
				</div>
			 </li>
			 @empty
			 @endforelse

			@endif

 			<li class="nav-item navigation-toggler mobile-toggler">
 				<a href="#" class="nav-link" title="Show navigation">
 					<i class="bi bi-sliders"></i>
				 </a>
 			</li>

			<li class="nav-item profile">
				<a onclick="showDropdown()" class="nav-link dropdown-toggle" data-toggle="dropdown">[ {{ Auth::user()->name }} ]</a>
				<div class="dropdown-content">
					<a data-turbo-prefetch="false" href="{{ route('getProfile') }}" class="dropdown-item">My Profile</a>
					<a data-turbo-prefetch="false" target="_blank" href="{{ route('cms') }}" class="dropdown-item">Login CMS</a>
					<a data-turbo-prefetch="false" href="{{ route('signout') }}" class="dropdown-item">Logout</a>
				</div>
			</li>

 		</ul>

 	</div>

 </div>
 <!-- end::header -->
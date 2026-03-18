
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                    <i class="fa fa-bars"></i>
                </a>
            </li>
            <li>
                <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none">
                    <i class="fas fa-search"></i>
                </a>
            </li>
        </ul>
    </form>

    <ul class="navbar-nav navbar-right">
        <!-- Front End Button -->
        <li class="nav-link">
            <a href="{{ route('home') }}" class="btn btn-warning">Front End</a>
        </li>

        <!-- User Dropdown -->
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                @if(Auth::guard('customer')->check())
                    @php $user = Auth::guard('customer')->user(); @endphp

                    @if(empty($user->photo))
                        <img alt="image" src="{{ asset('uploads/default.png') }}" class="rounded-circle mr-1">
                    @else
                        <img src="{{ asset('uploads/' . $user->photo) }}" alt="image" class="rounded-circle mr-1">
                    @endif

                    <div class="d-sm-none d-lg-inline-block">
                        {{ $user->name }}
                    </div>
                @else
                    <!-- Shown when no customer is logged in -->
                    <img alt="image" src="{{ asset('uploads/default.png') }}" class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">Guest</div>
                @endif
            </a>

            <!-- Dropdown Menu (only for logged-in customers) -->
            @if(Auth::guard('customer')->check())
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('customer_profile') }}" class="dropdown-item has-icon">
                        <i class="fa fa-user"></i> Edit Profile
                    </a>
                    <a href="{{ route('customer_logout') }}" class="dropdown-item has-icon text-danger">
                        <i class="fa fa-sign-out"></i> Logout
                    </a>
                </div>
            @endif
        </li>
    </ul>
</nav>


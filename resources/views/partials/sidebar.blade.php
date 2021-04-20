<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{route('order.index')}}">{{config('app.name')}}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{route('order.index')}}">DG</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Menu</li>
            @can('isAdmin')
            <li class="nav-item dropdown {{ (Request::is('orders') || Request::is('orders/*')) ? 'active' : '' }}">
                <a href="{{ route('order.index') }}" class="nav-link has-dropdown"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('orders') ? 'active' : '' }}"><a class="nav-link" href="{{ route('order.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                </ul>
            </li>
                <li class="nav-item dropdown {{ (Request::is('delivery-charge-rules') || Request::is('delivery-charge-rules/*')) ? 'active' : '' }}">
                    <a href="{{ route('delivery.charge.rules.index') }}" class="nav-link has-dropdown"><i class="fas fa-truck"></i> <span>Delivery Charge Rule</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('delivery-charge-rules') ? 'active' : '' }}"><a class="nav-link" href="{{ route('delivery.charge.rules.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                        <li class="{{ Request::is('delivery-charge-rules/create') ? 'active' : '' }}"><a class="nav-link" href="{{ route('delivery.charge.rules.create') }}"><i class="fas fa-plus-square"></i> <span>Create</span></a></li>
                    </ul>
                </li>
            <li class="nav-item dropdown {{ (Request::is('locations') || Request::is('locations/*')) ? 'active' : '' }}">
                <a href="{{ route('location.index') }}" class="nav-link has-dropdown"><i class="fa fa-location-arrow"></i> <span>Locations</span></a>
                <ul class="dropdown-menu">
                    <!-- <li class="{{ Request::is('locations/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('location.create') }}"><i class="fa fa-plus"></i> <span>New</span></a>
                    </li> -->
                    <li class="{{ Request::is('locations') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('location.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                    <li class="{{ Request::is('locations/sync') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('location.sync') }}"><i class="fas fa-sync-alt"></i> <span>Sync Locations</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('areas') || Request::is('areas/*')) ? 'active' : '' }}">
                <a href="{{ route('area.index') }}" class="nav-link has-dropdown"><i class="fa fa-globe"></i> <span>Areas</span></a>
                <ul class="dropdown-menu">
                    <!-- <li class="{{ Request::is('areas/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('area.create') }}"><i class="fa fa-plus"></i> <span>New</span></a>
                    </li> -->
                    <li class="{{ Request::is('areas') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('area.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                    <li class="{{ Request::is('areas/sync') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('area.sync') }}"><i class="fas fa-sync-alt"></i> <span>Sync Areas</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('brands') || Request::is('brands/*')) ? 'active' : '' }}">
                <a href="{{ route('brands.index') }}" class="nav-link has-dropdown"><i class="fas fa-tags"></i> <span>Brands</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('brands') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('brands.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                    <li class="{{ Request::is('brands/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('brands.create') }}"><i class="fas fa-plus"></i> <span>Create</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('products') || Request::is('products/*')) ? 'active' : '' }}">
                <a href="{{ route('product.index') }}" class="nav-link has-dropdown"><i class="fas fa-box-open"></i> <span>Products</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('products') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                    <li class="{{ Request::is('products/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product.create') }}"><i class="fas fa-user-plus"></i> <span>Create</span></a>
                    </li>
                    @can('isAdmin')
                    <li class="{{ Request::is('products/export-import') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product.export-import') }}"><i class="fas fa-user-plus"></i> <span>Export/Import</span></a>
                    </li>
                    @endcan
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('distributors') || Request::is('distributors/*')) ? 'active' : '' }}">
                <a href="{{ route('distributors.index') }}" class="nav-link has-dropdown"><i class="fas fa-users"></i> <span>Distributors</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('distributors/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('distributors.create') }}"><i class="fa fa-plus"></i> <span>New</span></a>
                    </li>
                    <li class="{{ Request::is('distributors') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('distributors.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('sr') || Request::is('sr/*')) ? 'active' : '' }}">
                <a href="{{ route('sr.index') }}" class="nav-link has-dropdown"><i class="fas fa-briefcase"></i> <span>Sales Officers</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('sr/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sr.create') }}"><i class="fa fa-plus"></i> <span>New</span></a>
                    </li>
                    <li class="{{ Request::is('sr') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sr.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('customers') || Request::is('customers/*')) ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}" class="nav-link has-dropdown"><i class="fas fa-asterisk"></i> <span>Retailer</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('customers') ? 'active' : '' }}"><a class="nav-link" href="{{ route('customers.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                </ul>
            </li>
            @endcan
            @can('isDistributor')
            <li class="nav-item dropdown {{ (Request::is('orders') || Request::is('orders/*')) ? 'active' : '' }}">
                <a href="{{ route('order.index') }}" class="nav-link has-dropdown"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('orders') ? 'active' : '' }}"><a class="nav-link" href="{{ route('order.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('distributors') || Request::is('distributors/*')) ? 'active' : '' }}">
                <a href="{{ route('distributors.index') }}" class="nav-link has-dropdown"><i class="fas fa-users"></i> <span>Distributors</span></a>
                <ul class="dropdown-menu">
                    <!-- <li class="{{ Request::is('distributors/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('distributors.create') }}"><i class="fa fa-plus"></i> <span>New</span></a>
                    </li> -->
                    <li class="{{ Request::is('distributors') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('distributors.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('sr') || Request::is('sr/*')) ? 'active' : '' }}">
                <a href="{{ route('sr.index') }}" class="nav-link has-dropdown"><i class="fas fa-briefcase"></i> <span>Sales Officers</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('sr/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sr.create') }}"><i class="fa fa-plus"></i> <span>New</span></a>
                    </li>
                    <li class="{{ Request::is('sr') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sr.index') }}"><i class="fas fa-list"></i> <span>All</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('customers') || Request::is('customers/*')) ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}" class="nav-link has-dropdown"><i class="fas fa-asterisk"></i> <span>Retailer</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('customers') ? 'active' : '' }}"><a class="nav-link" href="{{ route('customers.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                </ul>
            </li>
            @endcan
            @can('isSalesRepresentative')
            <li class="nav-item dropdown {{ (Request::is('orders') || Request::is('orders/*')) ? 'active' : '' }}">
                <a href="{{ route('order.index') }}" class="nav-link has-dropdown"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('orders') ? 'active' : '' }}"><a class="nav-link" href="{{ route('order.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ (Request::is('customers') || Request::is('customers/*')) ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}" class="nav-link has-dropdown"><i class="fas fa-asterisk"></i> <span>Retailer</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('customers') ? 'active' : '' }}"><a class="nav-link" href="{{ route('customers.index') }}"><i class="fas fa-list"></i> <span>All</span></a></li>
                </ul>
            </li>
            @endcan
        </ul>
    </aside>
</div>

<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>


<!-- Users, Roles, Permissions -->
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('order') }}'><i class='nav-icon la la-question'></i> Orders</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('detail') }}'><i class='nav-icon la la-question'></i> Details</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('payment') }}'><i class='nav-icon la la-question'></i> Payments</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('account') }}'><i class='nav-icon la la-question'></i> Accounts</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('amount') }}'><i class='nav-icon la la-question'></i> Amounts</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('balance') }}'><i class='nav-icon la la-question'></i> Balances</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('action') }}'><i class='nav-icon la la-question'></i> Actions</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('cart') }}'><i class='nav-icon la la-question'></i> Carts</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('supplier') }}'><i class='nav-icon la la-question'></i> Suppliers</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('shop') }}'><i class='nav-icon la la-question'></i> Shops</a></li>
<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->

                    @php($restaurant_logo = \App\Model\BusinessSetting::where(['key' => 'logo'])->first()->value)
                    <a class="navbar-brand" href="{{ route('admin.dashboard') }}" aria-label="Front">
                        <img class="navbar-brand-logo"
                            onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                            src="{{ asset('storage/app/public/ecommerce/' . $restaurant_logo) }}" alt="Logo">
                        <img class="navbar-brand-logo-mini"
                            onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                            src="{{ asset('storage/app/public/ecommerce/' . $restaurant_logo) }}" alt="Logo">
                    </a>

                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                        class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.dashboard') }}"
                                title="{{ translate('Dashboards') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('dashboard') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->

                        <!-- POS Section -->
                        @if (auth('admin')->user()->hasRole('super-admin'))
                            <li class="nav-item">
                                <small class="nav-subtitle">{{ \App\CentralLogics\translate('pos') }}
                                    {{ \App\CentralLogics\translate('system') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/pos*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-shopping nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('POS') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/pos/*') ? 'block' : 'none' }}">
                                    <li class="nav-item {{ Request::is('admin/pos/') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.pos.index') }}"
                                            title="{{ \App\CentralLogics\translate('pos') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('pos') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{ Request::is('admin/pos/orders') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.pos.orders') }}"
                                            title="{{ \App\CentralLogics\translate('orders') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ \App\CentralLogics\translate('orders') }}
                                                <span class="badge badge-info badge-pill ml-1">
                                                    {{ \App\Model\Order::Pos()->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <!-- End POS -->


                        <li class="nav-item">
                            <small class="nav-subtitle">{{ \App\CentralLogics\translate('order') }}
                                {{ \App\CentralLogics\translate('section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/orders*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('order') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/order*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/orders/list/all') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.orders.list', ['all']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('all') }}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{ \App\Model\Order::notPos()->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/pending') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['pending']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('pending') }}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'pending'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/confirmed') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['confirmed']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('confirmed') }}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'confirmed'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/processing') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['processing']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('processing') }}
                                            <span class="badge badge-warning badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'processing'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/orders/list/out_for_delivery') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['out_for_delivery']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('out_for_delivery') }}
                                            <span class="badge badge-warning badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'out_for_delivery'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/delivered') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['delivered']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('delivered') }}
                                            <span class="badge badge-success badge-pill ml-1">
                                                {{ \App\Model\Order::notPos()->where(['order_status' => 'delivered'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/returned') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['returned']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('returned') }}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'returned'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/orders/list/failed') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['failed']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('failed') }}
                                            <span class="badge badge-danger badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'failed'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/orders/list/canceled') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.orders.list', ['canceled']) }}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ \App\CentralLogics\translate('canceled') }}
                                            <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{ \App\Model\Order::where(['order_status' => 'canceled'])->count() }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End Pages -->


                        <li class="nav-item">
                            <small class="nav-subtitle">{{ \App\CentralLogics\translate('product') }}
                                {{ \App\CentralLogics\translate('section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/category*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-category nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('category') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/category*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/category/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.category.add') }}"
                                        title="{{ translate('add new category') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ \App\CentralLogics\translate('category') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/category/add-sub-category') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.category.add-sub-category') }}"
                                        title="{{ translate('add new sub category') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ \App\CentralLogics\translate('sub_category') }}</span>
                                    </a>
                                </li>

                                {{-- <li class="nav-item {{Request::is('admin/category/add-sub-sub-category')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.category.add-sub-sub-category')}}"
                                       title="add new sub sub category">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Sub-Sub-Category</span>
                                    </a>
                                </li> --}}
                            </ul>
                        </li>
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/attribute*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.attribute.add-new') }}">
                                <i class="tio-apps nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('attribute') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/addon*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{ route('admin.addon.index') }}">
                                <i class="tio-apps nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('addons') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/product*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('product') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/product*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/product/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.product.add-new') }}"
                                        title="{{ translate('add new product') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('add') }}
                                            {{ \App\CentralLogics\translate('new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/product/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.product.list') }}"
                                        title="{{ translate('product list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('list') }}</span>
                                    </a>
                                </li>

                                @if (auth('admin')->user()->hasRole('super-admin'))
                                    <li
                                        class="nav-item {{ Request::is('admin/product/bulk-import') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.product.bulk-import') }}"
                                            title="{{ translate('bulk import') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('bulk_import') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/product/bulk-export') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.product.bulk-export') }}"
                                            title="{{ translate('bulk export') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('bulk_export') }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/banner*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-image nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('banner') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/banner*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/banner/add-new') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.banner.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('add') }}
                                            {{ \App\CentralLogics\translate('new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/banner/list') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.banner.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End Pages -->

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{ translate('Business Section') }}">{{ \App\CentralLogics\translate('business') }}
                                {{ \App\CentralLogics\translate('section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/branch*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.branch.add-new') }}">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('branch') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/message*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.message.list') }}">
                                <i class="tio-messages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('messages') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/reviews*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.reviews.list') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('product') }}
                                    {{ \App\CentralLogics\translate('reviews') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Pages -->


                        <!-- Pages -->
                        @if (auth('admin')->user()->hasRole('super-admin'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/notification*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.notification.add-new') }}">
                                    <i class="tio-notifications nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ \App\CentralLogics\translate('send') }}
                                        {{ \App\CentralLogics\translate('notification') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <!-- End Pages -->

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/coupon*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.coupon.add-new') }}">
                                <i class="tio-gift nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('coupon') }}</span>
                            </a>
                        </li>
                        <!-- End Pages -->

                        {{-- Delivery Companies (Now it's only for admins) --}}
                        @if (auth('admin')->user()->hasRole('super-admin'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-companies*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                   href="{{ route('admin.delivery-company.index') }}">
                                    <i class="tio-user nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('delivery_companies') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Users (Now it's only for admins) --}}
                        @if (auth('admin')->user()->hasRole('super-admin'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/user*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.users.list') }}">
                                    <i class="tio-user nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('users') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (auth('admin')->user()->hasRole('super-admin'))
                            {{--
                            TODO:
                                1. Prevent access to links belows and their functions
                                2. Create a page, so super-admin can add "admins"
                                3. Prevent access to the other things azzam want
                        --}}
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-settings nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('settings') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/business-settings*') ? 'block' : 'none' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/ecom-setup') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.ecom-setup') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('ecommerce') }}
                                                {{ \App\CentralLogics\translate('setup') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/db*') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.business-settings.db-index') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ \App\CentralLogics\translate('clean') }}
                                                {{ \App\CentralLogics\translate('database') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/firebase-message-config') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.firebase_message_config_index') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('Firebase Message Config') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/location-setup') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.location-setup') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('location') }}
                                                {{ \App\CentralLogics\translate('setup') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/mail-config') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.mail-config') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ \App\CentralLogics\translate('mail') }}
                                                {{ \App\CentralLogics\translate('config') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/sms-module') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.sms-module') }}"
                                            title="{{ translate('sms module') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ \App\CentralLogics\translate('sms') }}
                                                {{ \App\CentralLogics\translate('module') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/payment-method') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.payment-method') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ \App\CentralLogics\translate('payment') }}
                                                {{ \App\CentralLogics\translate('methods') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/fcm-index') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.business-settings.fcm-index') }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ \App\CentralLogics\translate('push') }}
                                                {{ \App\CentralLogics\translate('notification') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/recaptcha*') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.recaptcha_index') }}"
                                            title="{{ \App\CentralLogics\translate('languages') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('reCaptcha') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/app-setting') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.app_setting') }}"
                                            title="{{ \App\CentralLogics\translate('App Setting') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('App Setting') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/map-api-settings') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.map_api_settings') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('map_api_setting') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/social-media') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.social-media') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('Social Media') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/return-page*') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.return_page_index') }}"
                                            title="{{ \App\CentralLogics\translate('Return Policy') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('Return Policy') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/refund-page*') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.refund_page_index') }}"
                                            title="{{ \App\CentralLogics\translate('Refund Policy') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('Refund Policy') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/cancellation-page*') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.cancellation_page_index') }}"
                                            title="{{ \App\CentralLogics\translate('Cancellation Policy') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('Cancellation Policy') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/terms-and-conditions') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.terms-and-conditions') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('terms_and_condition') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/privacy-policy') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.business-settings.privacy-policy') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('privacy_policy') }}</span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/about-us') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.business-settings.about-us') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ \App\CentralLogics\translate('about_us') }}</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                        @endif
                        <!-- End Pages -->

                        @if (auth('admin')->user()->hasRole('super-admin'))
                            <li class="nav-item">
                                <small class="nav-subtitle"
                                    title="{{ translate('deliveryman section') }}">{{ \App\CentralLogics\translate('deliveryman') }}
                                    {{ \App\CentralLogics\translate('section') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- Pages -->
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man/add') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.delivery-man.add') }}">
                                    <i class="tio-running nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ \App\CentralLogics\translate('register') }}
                                    </span>
                                </a>
                            </li>
                            <!-- End Pages -->

                            <!-- Pages -->
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man/list') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.delivery-man.list') }}">
                                    <i class="tio-filter-list nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ \App\CentralLogics\translate('list') }}
                                    </span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man/reviews/list') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.delivery-man.reviews.list') }}">
                                    <i class="tio-star-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ \App\CentralLogics\translate('reviews') }}
                                    </span>
                                </a>
                            </li>
                            <!-- End Pages -->
                        @endif

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{ translate('customer section') }}">{{ \App\CentralLogics\translate('customer') }}
                                {{ \App\CentralLogics\translate('section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/customer*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.customer.list') }}">
                                <i class="tio-poi-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('customer') }}
                                    {{ \App\CentralLogics\translate('list') }}
                                </span>
                            </a>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/customer/subscribed-email*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.customer.subscribed_emails') }}">
                                <i class="tio-email-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ \App\CentralLogics\translate('Subscribed Emails') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Pages -->

                        <li class="nav-item">
                            <div class="nav-divider"></div>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{ translate('report_and_analytics') }}">{{ \App\CentralLogics\translate('report_and_analytics') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/report*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-report-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ \App\CentralLogics\translate('reports') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/report*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/report/earning') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.earning') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('earning') }}
                                            {{ \App\CentralLogics\translate('report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/report/order') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.order') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('order') }}
                                            {{ \App\CentralLogics\translate('report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/report/driver-report') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.driver-report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('driver') }}
                                            {{ \App\CentralLogics\translate('report') }}</span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/report/product-report') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.product-report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('product') }}
                                            {{ \App\CentralLogics\translate('report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/report/sale-report') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.report.sale-report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ \App\CentralLogics\translate('sale') }}
                                            {{ \App\CentralLogics\translate('report') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End Pages -->

                        <li class="nav-item" style="padding-top: 100px">
                            <div class="nav-divider"></div>
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


{{-- <script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script> --}}

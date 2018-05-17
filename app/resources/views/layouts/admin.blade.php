<!DOCTYPE html>
<html lang="en">
<head>
	<title>
		@yield('title')
	</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<script src="{{ asset('js/webfont.js') }}"></script>
	<script>
		WebFont.load({
			google: { "families": ["Montserrat:300,400,500,600,700", "Roboto:300,400,500,600,700"] },
			active: function () {	
				sessionStorage.fonts = true;
			}
		});
	</script>
	
	<link href="{{ asset('css/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
	@yield('css')

	<!-- TO DO  -->
	<!-- <link rel="shortcut icon" href="../../../assets/demo/default/media/img/logo/favicon.ico" /> -->
</head>
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
	<!-- begin:: Page -->
	<div class="m-grid m-grid--hor m-grid--root m-page">
		<!-- BEGIN: Header -->
		<header class="m-grid__item m-header" data-minimize-offset="200" data-minimize-mobile-offset="200">
			<div class="m-container m-container--fluid m-container--full-height">
				<div class="m-stack m-stack--ver m-stack--desktop">
					<!-- BEGIN: Brand -->
					<div class="m-stack__item m-brand  m-brand--skin-dark ">
						<div class="m-stack m-stack--ver m-stack--general">
							<div class="m-stack__item m-stack__item--middle m-stack__item--center m-brand__logo">
								<a href="{{ route('dashboard.index') }}" class="m-brand__logo-wrapper">
									<img alt="logo" src="/img/logo/logo.png" />
								</a>
							</div>
							<div class="m-stack__item m-stack__item--middle m-brand__tools">
								<!-- BEGIN: Responsive Aside Left Menu Toggler -->
								<a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
									<span></span>
								</a>
								<!-- END -->
								<!-- BEGIN: Topbar Toggler -->
								<a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
									<i class="flaticon-more"></i>
								</a>
								<!-- BEGIN: Topbar Toggler -->
							</div>
						</div>
					</div>
					<!-- END: Brand -->
					<div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
						<!-- BEGIN: Topbar -->
						<div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
							<div class="m-stack__item m-topbar__nav-wrapper">
								<ul class="m-topbar__nav m-nav m-nav--inline">
									<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
									 data-dropdown-toggle="click">
										<a href="#" class="m-nav__link m-dropdown__toggle">
											<span class="m-topbar__welcome">
												Hello,&nbsp;
											</span>
											<span class="m-topbar__username">
												{{ Auth::user()->name }}
											</span>
										</a>
										<div class="m-dropdown__wrapper">
											<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
											<div class="m-dropdown__inner">
												<div class="m-dropdown__header m--align-center">
													<div class="m-card-user m-card-user--skin-dark">
														<div class="m-card-user__details">
															<span class="m-card-user__name m--font-weight-500">
																{{ Auth::user()->name }}
															</span>
															<span class="m-card-user__email m--font-weight-300 m-link">
																{{ Auth::user()->email }}
															</span>
														</div>
													</div>
												</div>
												<div class="m-dropdown__body">
													<div class="m-dropdown__content">
														<ul class="m-nav m-nav--skin-light">
															<li class="m-nav__section m--hide">
																<span class="m-nav__section-text">
																	Section
																</span>
															</li>
															<li class="m-nav__item">
																<a href="{{ route('profile.index') }}" class="m-nav__link">
																	<i class="m-nav__link-icon flaticon-profile-1"></i>
																	<span class="m-nav__link-title">
																		<span class="m-nav__link-wrap">
																			<span class="m-nav__link-text">
																				My Profile
																			</span>
																		</span>
																	</span>
																</a>
															</li>
															<li class="m-nav__separator m-nav__separator--fit"></li>
															<li class="m-nav__item">
																<form action="{{ route('auth.logout.post') }}" method="POST">
																	<input type="submit" value="Logout" class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">
																	{{ csrf_field() }}
																</form>
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<!-- END: Topbar -->
					</div>
				</div>
			</div>
		</header>
		<!-- END: Header -->
		<!-- begin::Body -->
		<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
			<!-- BEGIN: Left Aside -->
			<button class="m-aside-left-close m-aside-left-close--skin-dark" id="m_aside_left_close_btn">
				<i class="la la-close"></i>
			</button>
			<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
				<!-- BEGIN: Aside Menu -->
				<div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark m-aside-menu--dropdown "
				 data-menu-vertical="true" data-menu-dropdown="true" data-menu-scrollable="true" data-menu-dropdown-timeout="500">
					<ul class="m-menu__nav m-menu__nav--dropdown-submenu-arrow ">
						@if (Auth::user()->child()->first() instanceof \App\Admin || Auth::user()->child()->first() instanceof \App\Owner)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'dashboard.') === 0 ?'m-menu__item--active' : '' }}">
								<a href="{{ route('dashboard.index') }}" class="m-menu__link ">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-line-graph"></i>
									<span class="m-menu__link-text">
										Dashboard
									</span>
								</a>
							</li>
						@endif
						@if (Auth::user()->child()->first() instanceof \App\Admin)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'owner.') === 0 ?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-users"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Owner
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('owner.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Owner List
												</span>
											</a>
										</li>
										<li class="m-menu__item " aria-haspopup="true" data-redirect="true">
											<a href="{{ route('owner.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Owner
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
						@endif
						@if (Auth::user()->child()->first() instanceof \App\Admin || Auth::user()->child()->first() instanceof \App\Owner)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'store.') === 0 
							|| strpos(Route::current()->getName(), 'cost.') === 0
							|| strpos(Route::current()->getName(), 'staff.') === 0
							|| strpos(Route::current()->getName(), 'store_.product.') === 0
							?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-business"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Store
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('store.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Store List
												</span>
											</a>
										</li>
										<li class="m-menu__item " aria-haspopup="true" data-redirect="true">
											<a href="{{ route('store.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Store
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'product.') === 0 ?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-gift"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Product
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('product.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Product List
												</span>
											</a>
										</li>
										<li class="m-menu__item " aria-haspopup="true" data-redirect="true">
											<a href="{{ route('product.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Product
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
						@endif
						@if (Auth::user()->child()->first() instanceof \App\Owner)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'transaction.') === 0 ?'m-menu__item--active' : '' }}">
								<a href="{{ route('transaction.index') }}" class="m-menu__link ">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-time-3"></i>
									<span class="m-menu__link-text">
										Transaction
									</span>
								</a>
							</li>
						@else
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'transaction.') === 0 ||
							strpos(Route::current()->getName(), 'transaction_.product') === 0
							?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-time-3"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Transaction
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('transaction.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Transaction List
												</span>
											</a>
										</li>
										<li class="m-menu__item" aria-haspopup="true" data-redirect="true">
											<a href="{{ route('transaction.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Transaction
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
						@endif
						@if (Auth::user()->child()->first() instanceof \App\Admin || Auth::user()->child()->first() instanceof \App\Owner)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'category.') === 0 ||
							strpos(Route::current()->getName(), 'sub-category.') === 0
							?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-squares"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Category
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('category.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Category List
												</span>
											</a>
										</li>
										<li class="m-menu__item " aria-haspopup="true" data-redirect="true">
											<a href="{{ route('category.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Category
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
						@endif
						@if (Auth::user()->child()->first() instanceof \App\Admin)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'subs-plan.') === 0 ?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-time-3"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Subscription Plan
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('subs-plan.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Subscription Plan List
												</span>
											</a>
										</li>
										<li class="m-menu__item " aria-haspopup="true" data-redirect="true">
											<a href="{{ route('subs-plan.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Subscription Plan
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'subs-trans.') === 0 ?'m-menu__item--active' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="click">
								<a href="#" class="m-menu__link m-menu__toggle">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-piggy-bank"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">
												Subscription Transaction
											</span>
										</span>
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item" aria-haspopup="true">
											<a href="{{ route('subs-trans.index') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-list-3"></i>
												<span class="m-menu__link-text">
													Subscription Transaction List
												</span>
											</a>
										</li>
										<li class="m-menu__item " aria-haspopup="true" data-redirect="true">
											<a href="{{ route('subs-trans.create') }}" class="m-menu__link ">
												<i class="m-menu__link-icon flaticon-add"></i>
												<span class="m-menu__link-text">
													Create Subscription Transaction
												</span>
											</a>
										</li>
									</ul>
								</div>
							</li>
						@elseif (Auth::user()->child()->first() instanceof \App\Owner)
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'subs-plan.') === 0 ?'m-menu__item--active' : '' }}">
								<a href="{{ route('subs-plan.index') }}" class="m-menu__link ">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-time-3"></i>
									<span class="m-menu__link-text">
										Subscription Plan
									</span>
								</a>
							</li>
							<li class="m-menu__item {{ strpos(Route::current()->getName(), 'subs-trans.') === 0 ?'m-menu__item--active' : '' }}">
								<a href="{{ route('subs-trans.index') }}" class="m-menu__link ">
									<span class="m-menu__item-here"></span>
									<i class="m-menu__link-icon flaticon-piggy-bank"></i>
									<span class="m-menu__link-text">
										Subscription Transaction
									</span>
								</a>
							</li>
						@endif
					</ul>
				</div>
				<!-- END: Aside Menu -->
			</div>
			<!-- END: Left Aside -->
			<div class="m-grid__item m-grid__item--fluid m-wrapper">
				<div class="m-subheader" {!!(Route::current()->getName()=='dashboard.index')?'style="display:none;"':''!!}>
					<div class="d-flex align-items-center">
						<div class="mr-auto">
							<h3 class="m-subheader__title m-subheader__title--separator">
								@yield('title')
								@if ((strpos(Route::current()->getName(), 'product.') === 0) && \Auth::user()->child()->first() instanceof \App\Owner)
								<span style="font-weight: 400;font-size: 1.1rem;padding-left: 1rem;"> (Produk yang ditambahkan pada halaman ini adalah produk katalog. Agar dapat dijual, harap produk ditambahkan pada halaman store)</span>
								@endif
							</h3>
							<ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
								@if (strpos(Route::current()->getName(), 'dashboard.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
								@elseif (strpos(Route::current()->getName(), 'profile.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('profile.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Profile
											</span>
										</a>
									</li>
								@elseif (strpos(Route::current()->getName(), 'owner.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('owner.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Owner List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'owner.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('owner.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Owner
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'owner.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('owner.edit', Request::route('owner')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Owner
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'store.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Store List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'store.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('store.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Store
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'store.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('store.edit', Request::route('store')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Store
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'cost.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Store List
											</span>
										</a>
									</li>
									<li class="m-nav__separator">
											-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.edit', Request::route('store')) }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Edit Store
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'cost.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('cost.create', Request::route('store')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Cost
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'cost.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('cost.edit', [Request::route('store'), Request::route('cost')]) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Cost
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'staff.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Store List
											</span>
										</a>
									</li>
									<li class="m-nav__separator">
											-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.edit', Request::route('store')) }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Edit Store
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'staff.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('staff.create', Request::route('store')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Staff
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'staff.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('staff.edit', [Request::route('store'), Request::route('staff')]) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Staff
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'store_.product.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Store List
											</span>
										</a>
									</li>
									<li class="m-nav__separator">
											-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('store.edit', Request::route('store')) }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Edit Store
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'store_.product.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('store_.product.create', Request::route('store')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Product
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'store_.product.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('store_.product.edit', [Request::route('store'), Request::route('product')]) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Product
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'product.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('product.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Product List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'product.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('product.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Product
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'product.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('product.edit', Request::route('product')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Product
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'category.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('category.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Category List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'category.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('category.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Category
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'category.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('category.edit', Request::route('category')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Category
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'sub-category.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('category.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Category List
											</span>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('category.show', Request::route('category')) }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Edit Category
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'sub-category.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('sub-category.create', Request::route('category')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Sub Category
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'sub-category.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('sub-category.edit', [Request::route('category'), Request::route('sub_category')]) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Sub Category
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'subs-plan.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('subs-plan.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Subscription Plan List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'subs-plan.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('subs-plan.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Subscription Plan
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'subs-plan.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('subs-plan.edit', Request::route('subs_plan')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Subscription Plan
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'subs-trans.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('subs-trans.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Subscription Transaction List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'subs-trans.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('subs-trans.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Subscription Transaction
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'subs-trans.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('subs-trans.edit', Request::route('subs_trans')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Subscription Transaction
												</span>
											</a>
										</li>
									@endif
								@elseif (strpos(Route::current()->getName(), 'transaction.') === 0)
									<li class="m-nav__item m-nav__item--home">
										<a href="{{ route('dashboard.index') }}" class="m-nav__link m-nav__link--icon">
											<i class="m-nav__link-icon la la-home"></i>
										</a>
									</li>
									<li class="m-nav__separator">
										-
									</li>
									<li class="m-nav__item">
										<a href="{{ route('transaction.index') }}" class="m-nav__link">
											<span class="m-nav__link-text">
												Transaction List
											</span>
										</a>
									</li>
									@if (strpos(Route::current()->getName(), 'transaction.create') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('transaction.create') }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Create Transaction
												</span>
											</a>
										</li>
									@elseif (strpos(Route::current()->getName(), 'transaction.edit') === 0)
										<li class="m-nav__separator">
											-
										</li>
										<li class="m-nav__item">
											<a href="{{ route('transaction.edit', Request::route('transaction')) }}" class="m-nav__link">
												<span class="m-nav__link-text">
													Edit Transaction
												</span>
											</a>
										</li>
									@endif
								@endif
							</ul>
						</div>
					</div>
				</div>
				<div class="m-content">
					@yield('content')
				</div>
			</div>
		</div>
        <!-- end:: Body -->
		<!-- begin::Footer -->
		<footer class="m-grid__item	m-footer ">
			<div class="m-container m-container--fluid m-container--full-height m-page__container">
				<div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
					<div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
						<span class="m-footer__copyright">
							2017 &copy; TEPOS designed by
							<a href="http://www.tepos.web.id" class="m-link">
								TEPOS
							</a>
						</span>
					</div>
				</div>
			</div>
		</footer>
		<!-- end::Footer -->
	</div>
	<!-- end:: Page -->
	<!-- begin::Scroll Top -->
	<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
		<i class="la la-arrow-up"></i>
	</div>
	<!-- end::Scroll Top -->

	@if (session('success'))
		<div id="session-success" style="display: none;">{{ session('success') }}</div>
	@elseif (session('error'))
		<div id="session-error" style="display: none;">{{ session('error') }}</div>
	@endif
	
	<script src="{{ asset('js/vendors.bundle.js') }}" type="text/javascript"></script>
	<script src="{{ asset('js/scripts.bundle.js') }}" type="text/javascript"></script>
	<script src="{{ asset('js/toastr.js') }}" type="text/javascript"></script>
	@yield('js')
</body>
</html>
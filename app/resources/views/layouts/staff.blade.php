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
	<link href="{{ asset('css/style.bundle-staff.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
	@yield('css')

	<!-- TO DO  -->
	<!-- <link rel="shortcut icon" href="../../../assets/demo/default/media/img/logo/favicon.ico" /> -->
</head>
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

    <div class="m-grid m-grid--hor m-grid--root m-page">
		<!-- BEGIN: Header -->
		<header class="m-grid__item    m-header " data-minimize-offset="200" data-minimize-mobile-offset="200">
			<div class="m-container m-container--fluid m-container--full-height">
				<div class="m-stack m-stack--ver m-stack--desktop">
					<!-- BEGIN: Brand -->
					<div class="m-stack__item m-brand  m-brand--skin-dark ">
						<div class="m-stack m-stack--ver m-stack--general">
							<div class="m-stack__item m-stack__item--middle m-stack__item--center m-brand__logo">
								<a href="index.html" class="m-brand__logo-wrapper">
									<img alt="asdasdsa" src="/img/logo/logo.png" />
								</a>
							</div>
							<div class="m-stack__item m-stack__item--middle m-brand__tools">
								<!-- BEGIN: Left Aside Minimize Toggle -->
								<a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block">
									<span></span>
								</a>
								<!-- END -->
								<!-- BEGIN: Responsive Aside Left Menu Toggler -->
								<a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
									<span></span>
								</a>
								<!-- END -->
								<!-- BEGIN: Responsive Header Menu Toggler -->
								<a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
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
						<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
							<ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
								<li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
									<a href="#" class="m-menu__link m-menu__toggle">
										<i class="m-menu__link-icon flaticon-list-3"></i>
										<span class="m-menu__link-text">
											Transactions Info
										</span>
										<i class="m-menu__hor-arrow la la-angle-down"></i>
										<i class="m-menu__ver-arrow la la-angle-right"></i>
									</a>
									<div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
										<span class="m-menu__arrow m-menu__arrow--adjust"></span>
										<ul class="m-menu__subnav">
											@if (strpos(Route::current()->getName(), 'transaction.edit') === 0)
												<li class="m-menu__item" aria-haspopup="true" style="margin-bottom: 15px">
													<a class="m-menu__link" style="cursor: default">
														<i class="m-menu__item-icon flaticon-file-1" style="margin-right: 3px"></i>
														<span class="m-menu__item-text">
															Invoice
														</span>
													</a>
													<a>
														<div class="col-md">
															<input type="text" class="form-control m-input--solid" value="{{ $transaction->invoice }}" readonly>
														</div>
													</a>
												</li>
												<li class="m-menu__item" aria-haspopup="true" style="margin-bottom: 15px">
													<a class="m-menu__link" style="cursor: default">
														<i class="m-menu__item-icon flaticon-time" style="margin-right: 3px"></i>
														<span class="m-menu__item-text">
															Date
														</span>
													</a>
													<a>
														<div class="col-md">
															<input type="text" class="form-control m-input--solid" value="{{ explode(' ', $transaction->date)[0] }}" readonly>
														</div>
													</a>
												</li>
											@endif
											<li class="m-menu__item" aria-haspopup="true" style="margin-bottom: 15px">
												<a class="m-menu__link" style="cursor: default">
													<i class="m-menu__item-icon flaticon-edit-1" style="margin-right: 3px"></i>
													<span class="m-menu__item-text">
														Note
													</span>
												</a>
												<a>
													<div class="col-md">
														<input type="text" id="m_note" class="form-control" value="{{ strpos(Route::current()->getName(), 'transaction.edit') === 0 ? (old('note') ? old('note') : $transaction->note) : old('note') }}">
													</div>
												</a>
											</li>
										</ul>
									</div>
								</li>
							</ul>
						</div>
						<!-- BEGIN: Topbar -->
						<div id="m_header_topbar" class="m-topbar m-stack m-stack--ver m-stack--general">
							<div class="m-stack__item m-topbar__nav-wrapper">
								<ul class="m-topbar__nav m-nav m-nav--inline">
									<li class="m-nav__item">
										<div class="m-topbar__save-btn-outer">
											<div class="m-topbar__save-btn">
												<a class="btn btn-secondary m-btn--bolder m-btn--uppercase" style="max-height: 32px" href="{{ route('transaction.index') }}">Cancel</a>
											</div>
										</div>
									</li>
									<li class="m-nav__item">
										<div class="m-topbar__save-btn-outer">
											<div class="m-topbar__save-btn">
												<form method="post" id="m_save_form" action="{{ strpos(Route::current()->getName(), 'transaction.edit') === 0 ? route('transaction.update', $transaction->id) : route('transaction.store') }}" style="margin: 0">
													<input type="hidden" name="note" id="m_save_form_note" value="">
													@if (strpos(Route::current()->getName(), 'transaction.edit') === 0)
														{{ method_field('put') }}
													@endif
													{{ csrf_field() }}
													<input type="submit" class="btn btn-success m-btn--bolder m-btn--uppercase" style="max-height: 32px" id="m_save" value="Save">
												</form>
											</div>
										</div>
									</li>
									@if (strpos(Route::current()->getName(), 'transaction.edit') === 0)
										<li class="m-nav__item">
											<div class="m-topbar__save-btn-outer">
												<div class="m-topbar__save-btn">
												<a class="btn btn-warning m-btn--bolder m-btn--uppercase" style="max-height: 32px" href="{{ route('transaction.checkout', $transaction->id) }}">
														Checkout
													</a>
												</div>
											</div>
										</li>
										<li class="m-nav__item">
											<div class="m-topbar__save-btn-outer">
												<div class="m-topbar__save-btn">
													<button class="btn btn-brand m-btn--bolder m-btn--uppercase" style="max-height: 32px">
														Print
													</button>
												</div>
											</div>
										</li>
									@endif
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>

		<!-- END: Header -->
		<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
			<!-- BEGIN: Left Aside -->
			<button class="m-aside-left-close m-aside-left-close--skin-dark" id="m_aside_left_close_btn">
				<i class="la la-close"></i>
			</button>
			<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
				<!-- BEGIN: Aside Menu -->
				<div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark " data-menu-vertical="true"
				 data-menu-scrollable="true" data-menu-dropdown-timeout="500">
					<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
						@foreach ($categories as $category)
							<li class="m-menu__item m-menu__item--submenu m-menu__item--expanded {{ $category->subCategories()->where('id', Request::input('sub_category'))->first() ? 'm-menu__item--open' : '' }}" aria-haspopup="true" data-menu-submenu-toggle="hover">
								<a href="#" class="m-menu__link m-menu__toggle">
									<i class="m-menu__link-icon flaticon-layers"></i>
									<span class="m-menu__link-text">
										{{ $category->name }}
									</span>
									<i class="m-menu__ver-arrow la la-angle-right"></i>
								</a>
								<div class="m-menu__submenu" style="display: none;">
									<span class="m-menu__arrow"></span>
									<ul class="m-menu__subnav">
										<li class="m-menu__item  m-menu__item--parent" aria-haspopup="true">
											<a href="#" class="m-menu__link ">
												<span class="m-menu__link-text">
													{{ $category->name }}
												</span>
											</a>
										</li>
										@foreach ($category->subCategories()->get() as $subCategory)
											<li class="m-menu__item {{ Request::input('sub_category') == $subCategory->id ? 'm-menu__item--active' : ''}}" aria-haspopup="true">
												<a href="{{ strpos(Route::current()->getName(), 'transaction.edit') === 0 ? route('transaction.edit', ['id' => $transaction->id, 'category' => $category->id, 'sub_category' => $subCategory->id]) : route('transaction.create', ['category' => $category->id, 'sub_category' => $subCategory->id]) }}" class="m-menu__link ">
													<i class="m-menu__link-bullet m-menu__link-bullet--dot">
														<span></span>
													</i>
													<span class="m-menu__link-text">
														{{ $subCategory->name }}
													</span>
												</a>
											</li>
										@endforeach
									</ul>
								</div>
							</li>
						@endforeach
					</ul>
				</div>
				<!-- END: Aside Menu -->
			</div>
			<!-- END: Left Aside -->
			<div class="m-grid__item m-grid__item--fluid m-wrapper">
				<!-- MAIN_BODY START -->
				<div class="m-content">
                    @yield('content')
				</div>
				<!-- MAIN_BODY END -->
			</div>
		</div>

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
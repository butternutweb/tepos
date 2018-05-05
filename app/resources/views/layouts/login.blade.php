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
			google: { "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"] },
			active: function () {	
				sessionStorage.fonts = true;
			}
		});
	</script>
	
	<link href="{{ asset('css/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/style.bundle-login.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
	@yield('css')

	<!-- TO DO  -->
	<!-- <link rel="shortcut icon" href="../../../assets/demo/default/media/img/logo/favicon.ico" /> -->
</head>
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
	@yield('content')

	@if (session('success'))
		<div id="session-success" style="display: none;">{{ session('success') }}</div>
	@elseif (session('error'))
		<div id="session-error" style="display: none;">{{ session('error') }}</div>
	@endif
	
	<script src="{{ asset('js/vendors.bundle.js') }}" type="text/javascript"></script>
	<script src="{{ asset('js/scripts.bundle.js') }}" type="text/javascript"></script>
	<script src="{{ asset('js/form-validation.js') }}" type="text/javascript"></script>
	@yield('js')
</body>
</html>
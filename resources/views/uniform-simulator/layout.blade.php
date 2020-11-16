<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/png" href="/favicon.ico"/>
	<title>@yield('title')</title>
	<link rel="stylesheet" href="{{ mix('css/uniform-simulator/app.css') }}">
</head>
<body>
	<div id="app">
		@yield('content')
	</div>
	<script src="{{ mix('js/uniform-simulator/app.js') }}"></script>
</body>
</html>
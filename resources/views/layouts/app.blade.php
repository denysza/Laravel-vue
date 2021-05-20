<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="format-detection" content="telephone=no">
  <!-- CSRF Token-->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- meta title description -->
  <title>Hello</title>



  <!-- Styles-->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">
  <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
<div id="app">
  @if (Auth::guard('admin')->check())
  @include('layouts.headers.admin')
  @elseif (Auth::guard('painter')->check())
  @include('layouts.headers.painter')
  @elseif (Auth::guard('user')->check())
  @include('layouts.headers.user')
  @else
  @include('layouts.headers.guest')
  @endif

  <div class="mb-5">
    @yield('content')
  </div>

  @if (Auth::guard('admin')->check())
  @include('layouts.footers.admin')
  @elseif (Auth::guard('painter')->check())
  @include('layouts.footers.painter')
  @elseif (Auth::guard('user')->check())
  @include('layouts.footers.user')
  @else
  <!-- @include('layouts.footers.guest') -->
  @endif
</div>
  <!-- Scripts -->
  <script src="{{ mix('js/app.js') }}"></script>
 
</body>

</html>
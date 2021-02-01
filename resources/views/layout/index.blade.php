<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', '🐓🥚🥚')</title>
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="{{ asset('images/icon/icon-57.jpg') }}">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ asset('images/icon/icon-72.jpg') }}">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ asset('images/icon/icon-114.jpg') }}">
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('images/icon/icon-144.jpg') }}">
  @section('custom_header')
  @show
</head>
<body>
<div id="egg">
  @yield('body')
</div>

{{--底部JS代码--}}
@env('local')
<script src="https://cdn.bootcdn.net/ajax/libs/vConsole/3.3.4/vconsole.min.js"></script>
<script>var vConsole = new VConsole();</script>
@endenv
<script src="{{ mix('js/app.js') }}"></script>
@section('custom_footer')
  <script>
    if (document.getElementById('egg')) {
      var app = new Vue({el: '#egg'});
    }
  </script>
@show
@include('layout._footer')
</body>
</html>

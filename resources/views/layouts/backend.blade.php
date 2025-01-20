<!DOCTYPE html>
<html lang="en" class="dark-style layout-navbar-fixed layout-menu-fixed " dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template-dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu @yield('title')</title>

    @include('rsc.styles')

</head>
<body>
    <x-menu />

    @include('rsc.scripts')

    @yield('script')

</body>
</html>
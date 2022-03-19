<!-- BEGIN: Vendor CSS-->
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp
@if (app()->getLocale()==='ar')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors-rtl.min.css')) }}"/>
@else
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}"/>
@endif

@yield('vendor-style')
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" href="{{ asset(mix($css_path . '/core.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix($css_path . '/base/themes/dark-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix($css_path . '/base/themes/bordered-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix($css_path . '/base/themes/semi-dark-layout.css')) }}"/>

@php $configData = Helper::applClasses(); @endphp

<!-- BEGIN: Page CSS-->
@if ($configData['mainLayoutType'] === 'horizontal')
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/core/menu/menu-types/horizontal-menu.css')) }}"/>
@else
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/core/menu/menu-types/vertical-menu.css')) }}"/>
@endif

{{-- Page Styles --}}
@yield('page-style')

<!-- laravel style -->
<link rel="stylesheet" href="{{ asset(mix($css_path .'/overrides.css')) }}"/>

<!-- BEGIN: Custom CSS-->

@if (app()->getLocale()==='ar')
    <link rel="stylesheet" href="{{ asset(mix('css-rtl/custom-rtl.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(mix('css-rtl/style-rtl.css')) }}"/>

@else
    {{-- user custom styles --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path .'/style.css')) }}"/>
@endif

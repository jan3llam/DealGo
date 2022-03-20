@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

    <!DOCTYPE html>
@php
    $configData = Helper::applClasses();
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

<html class="loading {{ ($configData['theme'] === 'light') ? '' : $configData['layoutTheme']}}"
      lang="@if(session()->has('locale')){{session()->get('locale')}}@else{{$configData['defaultLanguage']}}@endif"
      data-textdirection="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      @if($configData['theme'] === 'dark') data-layout="dark-layout" @endif>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(session()->has('api_token'))
        <meta name="api-token" content="{{ session()->get('api_token') }}">
    @endif
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="VEGASDS">
    <title>@yield('title') - {{env('APP_NAME')}}</title>
    <link rel="apple-touch-icon" href="{{asset('images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/png" href="{{asset('images/logo/logo.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
          rel="stylesheet">

    {{-- Include core + vendor Styles --}}
    @include('panels/styles')
    <style>
        .kv-file-remove {
            display: none;
        }
    </style>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
@isset($configData["mainLayoutType"])
    @extends((( $configData["mainLayoutType"] === 'horizontal') ? 'layouts.horizontalLayoutMaster' :
    'layouts.verticalLayoutMaster' ))
@endisset

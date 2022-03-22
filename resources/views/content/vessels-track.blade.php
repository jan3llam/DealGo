@extends('layouts.contentLayoutMaster')

@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Track vessels'))

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="vessels-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <div class="mb-1">
                    <div class="input-group">
                        <select class="form-control dt-full-name vessels-select2">
                            <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                            @foreach($vessels as $vessel)
                                <option @if($vessel_id && $vessel_id == $vessel->id) selected
                                        @endif value="{{$vessel->id}}">{{$vessel->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="latitude" id="latitude">
                <label class="form-label" for="map">{{__('locale.LocationOnMap')}}</label>
                <div id="map" style="min-height: 350px"></div>
            </div>
        </div>
        <!-- list and filter end -->
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script>

        var latElement = $('#latitude').val() ? $('#latitude').val() : '24.7241504';
        var lngElement = $('#longitude').val() ? $('#longitude').val() : '46.2620616';
        const myLatLng = {lat: latElement, lng: lngElement};
        let map, marker, info;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: new google.maps.LatLng(latElement, lngElement),
                zoom: 8,
            });
        }
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&callback=initMap&libraries=&v=weekly"
        async></script>
    <script src="{{ asset(mix('js/scripts/pages/vessels-track.js')) }}"></script>
    @if($vessel_id)
        <script>
            $(document).ready(function () {
                $('.vessels-select2').trigger('change.select2');
            })
        </script>
    @endif
@endsection

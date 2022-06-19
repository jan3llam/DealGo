@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Ports'))

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="ports-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="ports-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Name')}}</th>
                        <th>{{__('locale.Country')}}</th>
                        <th>{{__('locale.City')}}</th>
                        <th>{{__('locale.UNLocode')}}</th>
                        <th>{{__('locale.Status')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-port-modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-port modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.Add')}} {{__('locale.Port')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <ul class="nav nav-tabs wrap-border" role="tablist">
                                @foreach($languages as $language)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif"
                                           id="language-{{$language->code}}"
                                           data-bs-toggle="tab" href="#" data-language="{{$language->code}}"
                                           role="tab" aria-selected="true">{{strtoupper($language->code)}}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mb-1">
                                <div class="tap-content">
                                    @foreach($languages as $language)
                                        <div class="tab-pane @if($loop->first) active @else hidden @endif"
                                             id="name-tab-{{$language->code}}"
                                             aria-labelledby="language-{{$language->code}}" role="tabpanel">
                                            <label class="form-label" for="name">{{__('locale.Name')}}</label>
                                            <input type="text" class="form-control dt-full-name"
                                                   placeholder="{{__('locale.Name')}}"
                                                   name="name[{{$language->code}}]"/>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="country">{{__('locale.Country')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="country"
                                        name="country">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input type="hidden" value="" id="city_id">
                                <label class="form-label" for="city">{{__('locale.City')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="city"
                                        name="city">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <input type="text" class="form-control" readonly name="longitude" id="longitude">
                                <input type="text" class="form-control" readonly name="latitude" id="latitude">
                                <label class="form-label" for="map">{{__('locale.LocationOnMap')}}</label>
                                <input type="text" class="form-control dt-full-name" id="google-link"
                                       placeholder="{{__('locale.GoogleLink')}}" name="google-link"/>
                                <div id="map" style="min-height: 350px"></div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="unlocode">{{__('locale.UNLocode')}}</label>
                                <input type="text" class="form-control dt-full-name" id="unlocode"
                                       placeholder="{{__('locale.UNLocode')}}" name="unlocode"/>
                            </div>
                            <button type="submit" class="btn btn-primary me-1 data-submit">
                                {{__('locale.Submit')}}
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                {{__('locale.Cancel')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal modal-slide-in view-port-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.View')}} {{__('locale.Crew')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Name')}}:</span>
                                        <span id="view-name"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Country')}}:</span>
                                        <span id="view-country"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.City')}}:</span>
                                        <span id="view-city"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.LocationOnMap')}}:</span>
                                        <div id="view-map" style="min-height: 350px"></div>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.UNLocode')}}:</span>
                                        <span id="view-unlocode"></span>
                                    </li>

                                </ul>
                                <div class="d-flex justify-content-center pt-2">
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        {{__('locale.Cancel')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal to add new user Ends-->
        </div>
        <!-- list and filter end -->
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}

    <script>
        $('#country').on("change.select2", function () {
            var $element = $(this);
            var target = $element.parents('form').find('select#city');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/api/admin/cities/list/' + $element.find("option:selected").val(),
                type: 'GET',
                cache: false,
                contentType: 'application/json',
                dataType: "json",
                success: function (result) {
                    var dbSelect = target;
                    dbSelect.empty();
                    for (var i = 0; i < result.data.length; i++) {
                        dbSelect.append($('<option/>', {
                            value: result.data[i].id,
                            text: result.data[i].name
                        }));
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });

            if ($('#city_id').val()) {
                target.val($('#city_id').val());
                target.trigger('change');
            }
        });
    </script>
    <script>

        var latElement = $('#latitude').val() ? $('#latitude').val() : '24.7241504';
        var lngElement = $('#longitude').val() ? $('#longitude').val() : '46.2620616';
        const myLatLng = {lat: latElement, lng: lngElement};
        let map, marker, mapView, markerView;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: new google.maps.LatLng(latElement, lngElement),
                zoom: 8,
            });

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(latElement, lngElement),
                map,
                draggable: true,
                title: "Port",
            });
            google.maps.event.addListener(marker, "dragend", function (evt) {
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
            });
            initMapView();
        }

        function initMapView() {
            mapView = new google.maps.Map(document.getElementById("view-map"), {
                center: new google.maps.LatLng(latElement, lngElement),
                zoom: 8,
            });

            markerView = new google.maps.Marker({
                position: new google.maps.LatLng(latElement, lngElement),
                mapView,
                title: "Port",
            });
        }

        $(document).on('change', '#google-link', function () {
            var element = $(this);
            var link = element.val();
            var coordinates = (link.split('@'))[1].split(',');
            var latlng = new google.maps.LatLng(coordinates[0], coordinates[1]);
            document.getElementById("latitude").value = coordinates[0];
            document.getElementById("longitude").value = coordinates[1];
            marker.setMap(map);
            marker.setPosition(latlng);
            map.setCenter(latlng);
        });
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&callback=initMap&libraries=&v=weekly"
        async></script>
    <script src="{{ asset(mix('js/scripts/pages/ports-list.js')) }}"></script>
@endsection

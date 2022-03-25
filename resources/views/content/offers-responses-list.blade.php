@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.OffersResponses'))

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
    <style>
        #routes_container .select2-container--default {
            flex: 1 1 auto;
            width: auto !important;
        }
    </style>
@endsection
@section('content')
    <!-- users list start -->
    <section class="offers-list">
        <input type="hidden" id="offer_id" value="{{$offer ? $offer->id : null}}">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="responses-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Tenants')}}</th>
                        <th>{{__('locale.ShipmentOrigin')}}</th>
                        <th>{{__('locale.ShipmentDestination')}}</th>
                        <th>{{__('locale.Date')}}</th>
                        <th>{{__('locale.Value')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-response-modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-response modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        @if($offer)
                            <input type="hidden" value="{{$offer->id}}" name="offer" id="offer">
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.Add')}} {{__('locale.OfferResponse')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">{{__('locale.Title')}}</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="{{__('locale.Title')}}" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="tenant">{{__('locale.Tenant')}}</label>
                                <select class="form-control dt-full-name select2" id="tenant"
                                        name="tenant">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{$tenant->userable->id}}">{{$tenant->contact_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="port_from">{{__('locale.ShipmentOrigin')}}</label>
                                <select class="form-control dt-full-name select2" id="port_from"
                                        name="port_from">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->id}}">{{$port->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="port_to">{{__('locale.ShipmentDestination')}}</label>
                                <select class="form-control dt-full-name select2" id="port_to"
                                        name="port_to">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->id}}">{{$port->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="contract">{{__('locale.Type')}}</label>
                                <select class="form-control dt-full-name select2" id="contract" name="contract">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    <option value="1">{{__('locale.Voyage')}}</option>
                                    <option value="2">{{__('locale.Time')}}</option>
                                    <option value="3">{{__('locale.Bareboat')}}</option>
                                    <option value="4">{{__('locale.COA')}}</option>
                                </select>
                            </div>
                            <div id="routes_container" style="display: none">
                                <div data-repeater-list="routes">
                                    <label class="form-label" for="route">{{__('locale.Routes')}}</label>
                                    <div data-repeater-item>
                                        <div class="mb-1">
                                            <div class="input-group">
                                                <select class="form-control dt-full-name routes-select2"
                                                        name="route">
                                                    <option value="" disabled
                                                            selected>{{__('locale.KindlyChoose')}}</option>
                                                    @foreach($ports as $port)
                                                        <option value="{{$port->id}}">{{$port->name}}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-icon btn-danger" type="button"
                                                            data-repeater-delete>
                                                        <i data-feather="trash" class="me-25"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-icon btn-success" type="button" data-repeater-create>
                                    <i data-feather="plus" class="me-25"></i>
                                    <span>{{__('locale.Add')}} {{__('locale.Route')}}</span>
                                </button>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="date_from">{{__('locale.FromDate')}}</label>
                                <input type="date" class="form-control dt-full-name" id="date_from"
                                       placeholder="{{__('locale.FromDate')}}" name="date_from"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="date_to">{{__('locale.ToDate')}}</label>
                                <input type="date" class="form-control dt-full-name" id="date_to"
                                       placeholder="{{__('locale.ToDate')}}" name="date_to"/>
                            </div>
                            <div id="goods_container">
                                <div data-repeater-list="goods">
                                    <label class="form-label" for="route">{{__('locale.Loads')}}</label>
                                    <div data-repeater-item>
                                        <div class="mb-1">
                                            <label class="form-label" for="gtype">{{__('locale.Goods types')}}</label>
                                            <select type="text" class="form-control dt-full-name goods-select2"
                                                    name="gtype">
                                                @foreach($types as $type)
                                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label" for="weight">{{__('locale.GrossWeight')}}</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control dt-full-name"
                                                       placeholder="{{__('locale.GrossWeight')}}" name="weight"/>
                                                <div class="input-group-append">
                                                    <button class="btn btn-icon btn-danger" type="button"
                                                            data-repeater-delete>
                                                        <i data-feather="trash" class="me-25"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-icon btn-success" type="button" data-repeater-create>
                                    <i data-feather="plus"
                                       class="me-25"></i><span>{{__('locale.Add')}} {{__('locale.AnotherLoad')}}</span>
                                </button>
                            </div>
                            <label class="form-label">{{__('locale.Payments')}}</label>
                            <div class="mb-1 row">
                                <div class="col-6">
                                    <label class="form-label"
                                           for="down_value">{{__('locale.DownPayment')}} {{__('locale.Value')}}</label>
                                    <input type="number" class="form-control dt-full-name"
                                           placeholder="{{__('locale.DownPayment')}} {{__('locale.Value')}}"
                                           name="down_value"/>
                                </div>
                                <div class="col-6">
                                    <label class="form-label"
                                           for="down_description">{{__('locale.Description')}}</label>
                                    <input type="text" class="form-control dt-full-name"
                                           placeholder="{{__('locale.Description')}}" name="down_description"/>
                                </div>
                            </div>
                            <div id="payments_container"
                                 @if(!$offer) style="display: none" @endif>
                                <div data-repeater-list="payments">
                                    <div data-repeater-item>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label" for="value">{{__('locale.Value')}}</label>
                                                <input type="number" class="form-control dt-full-name"
                                                       placeholder="{{__('locale.Value')}}" name="value"/>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label"
                                                       for="date">{{__('locale.NextPaymentDate')}}</label>
                                                <input type="date" class="form-control dt-full-name"
                                                       placeholder="{{__('locale.NextPaymentDate')}}" name="date"/>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label"
                                                       for="description">{{__('locale.Description')}}</label>
                                                <input type="text" class="form-control dt-full-name"
                                                       placeholder="{{__('locale.Description')}}" name="description"/>
                                            </div>
                                            <div class="col-6">
                                                <label for="formFile" class="form-label">{{__('locale.File')}}</label>
                                                <input class="form-control" type="file" name="file"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-icon btn-success" type="button" data-repeater-create>
                                    <i data-feather="plus" class="me-25"></i>
                                    <span>{{__('locale.AddNew')}}</span>
                                </button>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="description">{{__('locale.Description')}}</label>
                                <textarea class="form-control dt-full-name" id="description"
                                          placeholder="{{__('locale.Description')}}" name="description"></textarea>
                            </div>
                            <div class="mb-1">
                                <label for="files" class="form-label">{{__('locale.Attachments')}}</label>
                                <input type="file" name="files" id="files"/>
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
            <!-- Modal to add new user Ends-->
            <div class="modal modal-slide-in view-offer-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.View')}} {{__('locale.OfferResponse')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Title')}}:</span>
                                        <span id="view-name"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Tenant')}}:</span>
                                        <span id="view-tenant"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.ShipmentOrigin')}}:</span>
                                        <span id="view-origin"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.ShipmentDestination')}}:</span>
                                        <span id="view-destination"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.FromDate')}}:</span>
                                        <span id="view-date-from"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.ToDate')}}:</span>
                                        <span id="view-date-to"></span>
                                    </li>
                                    <div id="view-loads-container" style="display: none">
                                        <div class="info-container">
                                            <span class="fw-bolder me-25">{{__('locale.Goods types')}}:</span>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>{{__('locale.GoodType')}}</th>
                                                    <th>{{__('locale.GrossWeight')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="view-loads"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="view-routes-container" style="display: none;">
                                        <div class="info-container">
                                            <span class="fw-bolder me-25">{{__('locale.Routes')}}:</span>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>{{__('locale.Port')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="view-routes"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="view-payments-container" style="display: none">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-75">
                                                    <span class="fw-bolder me-25">{{__('locale.DownPayment')}}:</span>
                                                    <span id="view-down-value"></span>
                                                </li>
                                                <li class="mb-75">
                                                    <span class="fw-bolder me-25">{{__('locale.Description')}}:</span>
                                                    <span id="view-down-description"></span>
                                                </li>
                                            </ul>
                                            <span class="fw-bolder me-25">{{__('locale.Payments')}}:</span>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>{{__('locale.Value')}}</th>
                                                    <th>{{__('locale.Date')}}</th>
                                                    <th>{{__('locale.Description')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="view-payments"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Description')}}:</span>
                                        <span id="view-description"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Attachments')}}:</span>
                                        <span id="view-files"></span>
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
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/offers-responses-list.js')) }}"></script>
@endsection

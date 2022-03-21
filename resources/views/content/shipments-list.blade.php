@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Shipments'))

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="shipments-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="trashed" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="shipments-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Contract')}} ID</th>
                        <th>{{__('locale.Tenant')}}</th>
                        <th>{{__('locale.From')}}</th>
                        <th>{{__('locale.To')}}</th>
                        <th>{{__('locale.Owner')}}</th>
                        <th>{{__('locale.Vessel')}}</th>
                        <th>{{__('locale.ArrivingAt')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to view user starts-->
            <div class="modal modal-slide-in view-shipment-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.View')}} {{__('locale.Shipment')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Contract')}} #:</span>
                                        <span id="view-contract"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Tenant')}}:</span>
                                        <span id="view-tenant"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Vessel')}}:</span>
                                        <span id="view-vessel"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Owner')}}:</span>
                                        <span id="view-owner"></span>
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
                                        <span class="fw-bolder me-25">{{__('locale.Date')}}:</span>
                                        <span id="view-date"></span>
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
            <!-- Modal to view user Ends-->
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
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/pages/shipments-list.js')) }}"></script>
@endsection

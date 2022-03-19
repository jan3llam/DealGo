@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Contracts'))

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
    <section class="contracts-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="trashed" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="ports-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Tenant')}}</th>
                        <th>{{__('locale.Owner')}}</th>
                        <th>{{__('locale.Type')}}</th>
                        <th>{{__('locale.StartAt')}}</th>
                        <th>{{__('locale.EndAt')}}</th>
                        <th>{{__('locale.Shipments')}}</th>
                        <th>{{__('locale.Value')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to view user starts-->
            <div class="modal modal-slide-in view-contract-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">View ship owner</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Type:</span>
                                        <span id="view-type"></span>
                                    </li>
                                </ul>
                            </div>
                            <div id="view-company-container" style="display: none">
                                <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">Full name:</span>
                                            <span id="view-name"></span>
                                        </li>
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">Commercial #:</span>
                                            <span id="view-commercial"></span>
                                        </li>
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">License file:</span>
                                            <span id="view-license"></span>
                                        </li>
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">Company file:</span>
                                            <span id="view-company"></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Contact name:</span>
                                        <span id="view-contact"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Email:</span>
                                        <span id="view-email"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Phone:</span>
                                        <span id="view-phone"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Zip code:</span>
                                        <span id="view-zip"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Country:</span>
                                        <span id="view-country"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">City:</span>
                                        <span id="view-city"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Address 1:</span>
                                        <span id="view-address-1"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Address 2:</span>
                                        <span id="view-address-2"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Legal file (ID, Passport):</span>
                                        <span id="view-legal"></span>
                                    </li>

                                </ul>
                                <div class="d-flex justify-content-center pt-2">
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Cancel
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
    <script src="{{ asset(mix('js/scripts/pages/contracts-list.js')) }}"></script>
@endsection

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
                <table class="contracts-list-table table">
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
            <div class="modal modal-slide-in new-payment-modal fade" data-keyboard="false" data-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog" style="width: 50%">
                    <form class="add-new-payment modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.Add')}} {{__('locale.Payment')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <table class="table table-striped table-bordered mb-1">
                                <thead>
                                <tr>
                                    <th>{{__('locale.DownPayment')}}</th>
                                    <th>{{__('locale.Description')}}</th>
                                    <th>{{__('locale.SubmittedDate')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span id="view-down-value"></span></td>
                                    <td><span id="view-down-description"></span></td>
                                    <td><input id="view-down-submit" type="date" class="form-control dt-full-name"
                                               placeholder="{{__('locale.SubmittedDate')}} "
                                               name="down_date"/>
                                        <span id="view-down-text" style="display: none"></span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <span class="fw-bolder me-25">{{__('locale.Payments')}}:</span>
                            <table class="table table-striped table-bordered mb-1">
                                <thead>
                                <tr>
                                    <th>{{__('locale.Value')}}</th>
                                    <th>{{__('locale.Date')}}</th>
                                    <th>{{__('locale.Description')}}</th>
                                    <th>{{__('locale.Payment')}}</th>
                                    <th>{{__('locale.SubmittedDate')}}</th>
                                </tr>
                                </thead>
                                <tbody id="view-payments"></tbody>
                            </table>
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
            <div class="modal modal-slide-in view-contract-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.View')}} {{__('locale.Contract')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Type')}}:</span>
                                        <span id="view-type"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Tenant')}}:</span>
                                        <span id="view-tenant"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Owner')}}:</span>
                                        <span id="view-owner"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.StartAt')}}:</span>
                                        <span id="view-start"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.EndAt')}}:</span>
                                        <span id="view-end"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Value')}}:</span>
                                        <span id="view-value"></span>
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
    <script src="{{ asset(mix('js/scripts/pages/contracts-list.js')) }}"></script>
@endsection

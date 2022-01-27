@extends('layouts.contentLayoutMaster')

@section('title', 'Shipping requests')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <style>
        #routes_container .select2-container--default {
            flex: 1 1 auto;
            width: auto !important;
        }
    </style>
@endsection
@section('content')
    <!-- users list start -->
    <section class="requests-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="requests-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Tenant</th>
                        <th>From Port</th>
                        <th>To Port</th>
                        <th>Owner</th>
                        <th>Vessel</th>
                        <th>Arrive @</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-request-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-request modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add request</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">Title</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="Title" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="tenant">Charter</label>
                                <select class="form-control dt-full-name select2" id="tenant"
                                        name="tenant">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{$tenant->id}}">{{$tenant->contact_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="port_from">Origin of shipment</label>
                                <select class="form-control dt-full-name select2" id="port_from"
                                        name="port_from">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->id}}">{{$port->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="port_to">Destination of shipment</label>
                                <select class="form-control dt-full-name select2" id="port_to"
                                        name="port_to">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->id}}">{{$port->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="contract">Contract type</label>
                                <select class="form-control dt-full-name select2" id="contract" name="contract">
                                    <option value="" disabled selected>Kindly choose</option>
                                    <option value="1">Voyage</option>
                                    <option value="2">Time</option>
                                    <option value="3">Bareboat</option>
                                    <option value="4">COA</option>
                                </select>
                            </div>
                            <div id="routes_container" style="display: none">
                                <div data-repeater-list="routes">
                                    <label class="form-label" for="route">Routes</label>
                                    <div data-repeater-item>
                                        <div class="mb-1">
                                            <div class="input-group">
                                                <select class="form-control dt-full-name routes-select2"
                                                        name="route">
                                                    <option value="" disabled selected>Kindly choose</option>
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
                                    <span>Add New</span>
                                </button>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="date_from">From date</label>
                                <input type="date" class="form-control dt-full-name" id="date_from"
                                       placeholder="From date" name="date_from"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="date_to">To date</label>
                                <input type="date" class="form-control dt-full-name" id="date_to"
                                       placeholder="To date" name="date_to"/>
                            </div>
                            <div id="goods_container">
                                <div data-repeater-list="goods">
                                    <div data-repeater-item>
                                        <div class="mb-1">
                                            <label class="form-label" for="gtype">Goods type</label>
                                            <select type="text" class="form-control dt-full-name goods-select2"
                                                    name="gtype">
                                                @foreach($types as $type)
                                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label" for="weight">Gross weight, kg</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control dt-full-name"
                                                       placeholder="Gross weight, kg" name="weight"/>
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
                                    <span>Add New</span>
                                </button>
                            </div>
                            <div id="owners_container" style="display: none">
                                <div class="mb-1">
                                    <label class="form-label" for="owner">Ship owner</label>
                                    <select type="text" class="form-control dt-full-name select2" id="owner"
                                            name="owner">
                                        @foreach($owners as $owner)
                                            <option value="{{$owner->id}}">{{$owner->contact_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control dt-full-name" id="description"
                                          placeholder="Description" name="description"></textarea>
                            </div>
                            <div class=mb-1>
                                <label for="files" class="form-label">Files</label>
                                <div class="dropzone" id="files">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary me-1 data-submit">
                                Submit
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </form>
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
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/file-uploaders/dropzone.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/requests-list.js')) }}"></script>
@endsection

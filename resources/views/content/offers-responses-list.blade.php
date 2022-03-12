@extends('layouts.contentLayoutMaster')

@section('title', 'Offers Responses')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css">
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
    <section class="offers-list">
        <input type="hidden" id="offer_id" value="{{$offer ? $offer->id : null}}">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
                <input type="hidden" id="trashed" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="responses-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Charterer</th>
                        <th>Origin of shipment</th>
                        <th>Destination of shipment</th>
                        <th>Date</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-response-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-response modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        @if($offer)
                            <input type="hidden" value="{{$offer->id}}" name="offer" id="offer">
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add response</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">Title</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="Title" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="tenant">Charterer</label>
                                <select class="form-control dt-full-name select2" id="tenant"
                                        name="tenant">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{$tenant->userable->id}}">{{$tenant->contact_name}}</option>
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
                                    <span>Add Route</span>
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
                                    <label class="form-label" for="route">Loads</label>
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
                                    <i data-feather="plus" class="me-25"></i><span>Add another load</span>
                                </button>
                            </div>
                            <label class="form-label">Payments</label>
                            <div class="mb-1 row">
                                <div class="col-6">
                                    <label class="form-label" for="down_value">Down payment value</label>
                                    <input type="number" class="form-control dt-full-name"
                                           placeholder="Down payment value" name="down_value"/>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="down_description">Details</label>
                                    <input type="text" class="form-control dt-full-name"
                                           placeholder="Details" name="down_description"/>
                                </div>
                            </div>
                            <div id="payments_container"
                                 @if(!$offer) style="display: none" @endif>
                                <div data-repeater-list="payments">
                                    <div data-repeater-item>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label" for="value">Value</label>
                                                <input type="number" class="form-control dt-full-name"
                                                       placeholder="Value" name="value"/>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" for="date">Next payment</label>
                                                <input type="date" class="form-control dt-full-name"
                                                       placeholder="Date" name="date"/>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label" for="description">Details</label>
                                                <input type="text" class="form-control dt-full-name"
                                                       placeholder="Details" name="description"/>
                                            </div>
                                            <div class="col-6">
                                                <label for="formFile" class="form-label">File</label>
                                                <input class="form-control" type="file" name="file"/>
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
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control dt-full-name" id="description"
                                          placeholder="Description" name="description"></textarea>
                            </div>
                            <div class="mb-1">
                                <label for="files" class="form-label">Attachments</label>
                                <input type="file" name="files" id="files"/>
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
            <div class="modal modal-slide-in view-offer-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">View response</h5>
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
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/offers-responses-list.js')) }}"></script>
@endsection

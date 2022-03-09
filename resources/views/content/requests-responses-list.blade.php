@extends('layouts.contentLayoutMaster')

@section('title', 'Requests Responses')

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
        <input type="hidden" id="request_id" value="{{$request ? $request->id : null}}">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
                <input type="hidden" id="trashed" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="offers-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Shipowner</th>
                        <th>Value</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-offer-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-offer modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        @if($request)
                            <input type="hidden" value="{{$request->id}}" name="request" id="request">
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add response</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="owner">Owner</label>
                                <select type="text" class="form-control dt-full-name select2" id="owner"
                                        name="owner">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($owners as $owner)
                                        <option
                                            value="{{$owner->userable->id}}">{{$owner->userable->contact_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="date">Date</label>
                                <input type="date" class="form-control dt-full-name" id="date"
                                       placeholder="Date" name="date"/>
                            </div>
                            <div id="routes_container"
                                 @isset($request) @if ($request->contract !== 1)style="display: none" @endif @endisset>
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
                            <hr>
                            @isset($request)
                                @foreach($request->goods_types as $index => $item)
                                    <div class="mb-1 row">
                                        <div class="col-7">
                                            <label class="form-label">Goods type</label>
                                            <label class="form-label"><b>{{$item->name}}</b></label>
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label">Gross weight, kg</label>
                                            <label
                                                class="form-label"><b>{{$item->pivot->weight}}</b></label>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="vessel">Vessels</label>
                                        <select type="text" class="form-control dt-full-name vessels-select2"
                                                id="vessel"
                                                name="vessel">
                                            <option value="" disabled selected>Kindly choose</option>
                                            @foreach($vessels->whereIn('type_id',$item->vessels_types->pluck('id')) as $vessel)
                                                <option value="{{$vessel->id}}">{{$vessel->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <hr>
                                @endforeach
                            @endisset
                            <label class="form-label">Payments</label>
                            <div class="mb-1 row">
                                <div class="col-12">
                                    <label class="form-label" for="total">Total value</label>
                                    <input type="text" id="total" disabled readonly class="form-control dt-full-name"
                                           placeholder="Total value"/>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-6">
                                    <label class="form-label" for="down_value">Down payment value</label>
                                    <input type="number" class="form-control calculate-value dt-full-name"
                                           placeholder="Down payment value" name="down_value"/>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="down_description">Details</label>
                                    <input type="text" class="form-control dt-full-name"
                                           placeholder="Details" name="down_description"/>
                                </div>
                            </div>
                            <div id="payments_container"
                                 @if(!$request) style="display: none" @endif>
                                <div data-repeater-list="payments">
                                    <div data-repeater-item>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label" for="value">Value</label>
                                                <input type="number" class="form-control calculate-value dt-full-name"
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
    <script src="{{ asset(mix('js/scripts/pages/requests-responses-list.js')) }}"></script>
@endsection

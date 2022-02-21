@extends('layouts.contentLayoutMaster')

@section('title', 'Shipping offers')

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
                        <th>Vessel</th>
                        <th>Shipowner</th>
                        <th>Date</th>
                        <th>Origin of shipment</th>
                        <th>Destination of shipment</th>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add request</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="owner">Shipowner</label>
                                <select type="text" class="form-control dt-full-name select2" id="owner"
                                        name="owner">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($owners as $owner)
                                        <option value="{{$owner->id}}">{{$owner->contact_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="vessel">Vessel</label>
                                <select type="text" class="form-control dt-full-name select2" id="vessel"
                                        name="vessel">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($vessels as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="weight">Max weight</label>
                                <input type="number" class="form-control dt-full-name" id="weight"
                                       placeholder="Max weight" name="weight"/>
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
            <div class="modal modal-slide-in view-request-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">View request</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Title:</span>
                                        <span id="view-name"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Shipowner:</span>
                                        <span id="view-tenant"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Origin of shipment:</span>
                                        <span id="view-origin"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Destination of shipment:</span>
                                        <span id="view-destination"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Date to:</span>
                                        <span id="view-date-to"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Date from:</span>
                                        <span id="view-date-from"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Description:</span>
                                        <span id="view-description"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Attachments:</span>
                                        <span id="view-files"></span>
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
    <script src="{{ asset(mix('js/scripts/pages/offers-list.js')) }}"></script>
@endsection

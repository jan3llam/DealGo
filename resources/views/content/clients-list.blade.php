@extends('layouts.contentLayoutMaster')

@section('title', 'Clients')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="clients-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="clients-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-client-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-client modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add client</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="url">URL</label>
                                <input type="text" class="form-control dt-full-name" id="url"
                                       placeholder="URL" name="url"/>
                            </div>
                            <div class="mb-1">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="file" id="file" accept="image/*"/>
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
            <div class="modal modal-slide-in view-client-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">View client</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">URL:</span>
                                        <span id="view-name"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Image:</span>
                                        <span id="view-image"></span>
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
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/clients-list.js')) }}"></script>
@endsection

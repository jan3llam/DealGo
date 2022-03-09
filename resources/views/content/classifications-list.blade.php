@extends('layouts.contentLayoutMaster')

@section('title', 'Blog classifications')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
    <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="classifications-list">
        <input type="hidden" id="classification_id" value="{{$classification ? $classification->id : null}}">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="classifications-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>Title</th>
                        <th>Parent classification</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-classification-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-classification modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add classification</h5>
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
                                @foreach($languages as $language)
                                    <div class="tab-pane @if($loop->first) active @else hidden @endif"
                                         id="name-tab-{{$language->code}}"
                                         aria-labelledby="language-{{$language->code}}" role="tabpanel">
                                        <label class="form-label" for="name">Title</label>
                                        <input type="text" class="form-control dt-full-name"
                                               placeholder="Title" name="name[{{$language->code}}]"/>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="classification">Parent classification</label>
                                <select type="text" class="form-control dt-full-name select2" id="classification"
                                        name="classification">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($classifications as $item)
                                        <option @if($classification && $classification->id === $item->id) selected
                                                @endif value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="description">Description</label>
                                <div class="editor" id="description"></div>
                                <input type="hidden" name="description" id="hiddenDescription"/>
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
            <div class="modal modal-slide-in view-classification-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">View classification</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Title:</span>
                                        <span id="view-title"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Parent Classification:</span>
                                        <span id="view-classification"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">Description:</span>
                                        <span id="view-description"></span>
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
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/classifications-list.js')) }}"></script>
@endsection

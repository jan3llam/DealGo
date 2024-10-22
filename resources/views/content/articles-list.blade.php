@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Articles'))

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
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-quill-editor.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="articles-list">
        <input type="hidden" id="category_id" value="{{$category ? $category->id : null}}">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="articles-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Title')}}</th>
                        <th>{{__('locale.Category')}}</th>
                        <th>{{__('locale.CreatedAt')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-article-modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-article modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.Add')}} {{__('locale.Article')}}</h5>
                            <input type="hidden" id="edit___label" value="{{__('locale.Edit')}} {{__('locale.Article')}}">

                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="category">{{__('locale.Category')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="category"
                                        name="category">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($categories as $item)
                                        <option @if($category && $category->id === $item->id) selected
                                                @endif value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="name">{{__('locale.Title')}}</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="{{__('locale.Title')}}" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="created_at">{{__('locale.Date')}}</label>
                                <input type="date" class="form-control dt-full-name" id="created_at"
                                       placeholder="{{__('locale.Date')}}" name="created_at"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="updated_at">{{__('locale.UpdateDate')}}</label>
                                <input type="date" class="form-control dt-full-name" id="updated_at"
                                       placeholder="{{__('locale.UpdateDate')}}" name="updated_at"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="description">{{__('locale.Description')}}</label>
                                <div class="editor"></div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="related">{{__('locale.RelatedArticles')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="related"
                                        name="related">
                                    @foreach($articles as $article)
                                        <option value="{{$article->id}}">{{$article->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="loading-btn" class="btn btn-primary me-1 waves-effect" type="button" disabled=""
                                    style="display: none">
                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                <span class="ms-25 align-middle">{{__('locale.Loading')}}</span>
                            </button>
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
            <div class="modal modal-slide-in view-article-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.View')}} {{__('locale.Article')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Title')}}:</span>
                                        <span id="view-title"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Category')}}:</span>
                                        <span id="view-category"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.CreatedAt')}}:</span>
                                        <span id="view-created"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.UpdatedAt')}}:</span>
                                        <span id="view-updated"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Description')}}:</span>
                                        <span id="view-description"></span>
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
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/articles-list.js')) }}"></script>
@endsection

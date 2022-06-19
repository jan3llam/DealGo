@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Vessels types'))

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="vessels-types-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="vessels-types-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Name')}}</th>
                        <th>{{__('locale.Parent')}}</th>
                        <th>{{__('locale.DWT')}}</th>
                        <th>{{__('locale.Draught')}}</th>
                        <th>{{__('locale.LOA')}}</th>
                        <th>{{__('locale.Geared')}}</th>
                        <th>{{__('locale.Holds')}}</th>
                        <th>{{__('locale.VesselsCount')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-vessels-type-modal fade" data-bs-keyboard="false"
                 data-bs-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-vessels-type modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.Add')}} {{__('locale.VesselType')}}</h5>
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
                                <div class="tap-content">
                                    @foreach($languages as $language)
                                        <div class="tab-pane @if($loop->first) active @else hidden @endif"
                                             id="name-tab-{{$language->code}}"
                                             aria-labelledby="language-{{$language->code}}" role="tabpanel">
                                            <label class="form-label" for="name">{{__('locale.Name')}}</label>
                                            <input type="text" class="form-control dt-full-name"
                                                   placeholder="{{__('locale.Name')}}"
                                                   name="name[{{$language->code}}]"/>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-1">
                                <input type="hidden" value="" id="parent_id">
                                <label class="form-label" for="parent">{{__('locale.Parent')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="parent"
                                        name="parent">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="dwt">{{__('locale.DWT')}}</label>
                                <input type="text" class="form-control dt-full-name" id="dwt"
                                       placeholder="{{__('locale.DWT')}}" name="dwt"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="draught">{{__('locale.Draught')}}</label>
                                <input type="text" class="form-control dt-full-name" id="draught"
                                       placeholder="{{__('locale.Draught')}}" name="draught"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="loa">{{__('locale.LOA')}}</label>
                                <input type="text" class="form-control dt-full-name" id="loa"
                                       placeholder="{{__('locale.LOA')}}" name="loa"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="holds">{{__('locale.Holds')}}</label>
                                <input type="number" class="form-control dt-full-name" id="holds"
                                       placeholder="{{__('locale.Holds')}}" name="holds"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="geared">{{__('locale.Geared')}}</label>
                                <select class="form-control dt-full-name" id="geared" name="geared">
                                    <option value="0">{{__('locale.No')}}</option>
                                    <option value="1">{{__('locale.Yes')}}</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                @foreach($languages as $language)
                                    <div class="tab-pane @if($loop->first) active @else hidden @endif"
                                         id="description-tab-{{$language->code}}"
                                         aria-labelledby="language-{{$language->code}}" role="tabpanel">
                                        <label class="form-label" for="description">{{__('locale.Description')}}</label>
                                        <textarea class="form-control dt-full-name"
                                                  placeholder="{{__('locale.Description')}}"
                                                  name="description[{{$language->code}}]"></textarea>
                                    </div>
                                @endforeach
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

            <div class="modal modal-slide-in view-vessels-type-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.View')}} {{__('locale.VesselType')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Name')}}:</span>
                                        <span id="view-name"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Parent')}}:</span>
                                        <span id="view-parent"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.DWT')}}:</span>
                                        <span id="view-dwt"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Draught')}}:</span>
                                        <span id="view-draught"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.LOA')}}:</span>
                                        <span id="view-loa"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Holds')}}:</span>
                                        <span id="view-holds"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Geared')}}:</span>
                                        <span id="view-geared"></span>
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
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/vessels_types-list.js')) }}"></script>
@endsection

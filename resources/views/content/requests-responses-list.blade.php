@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Requests Responses'))

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
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
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="offers-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.Owner')}}</th>
                        <th>{{__('locale.Value')}}</th>
                        <th>{{__('locale.Date')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-offer-modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-offer modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        @if($request)
                            <input type="hidden" value="{{$request->id}}" name="request" id="request">
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.Add')}} {{__('locale.Requests Responses')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="owner">{{__('locale.Owner')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="owner"
                                        name="owner">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($owners as $owner)
                                        <option
                                            value="{{$owner->userable->id}}">{{$owner->contact_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="date">{{__('locale.Date')}}</label>
                                <input type="date" class="form-control dt-full-name" id="date"
                                       @if($request)
                                           min="{{\Illuminate\Support\Carbon::parse($request->date_from)->toDateString()}}"
                                       @endif
                                       placeholder="{{__('locale.Date')}}" name="date"/>
                            </div>
                            <div id="routes_container"
                                 @isset($request) @if ($request->contract !== 1)style="display: none" @endif @endisset>
                                <div data-repeater-list="routes">
                                    <label class="form-label" for="route">{{__('locale.Routes')}}</label>
                                    <div data-repeater-item>
                                        <div class="mb-1">
                                            <div class="input-group">
                                                <select class="form-control dt-full-name routes-select2"
                                                        name="route">
                                                    <option value="" disabled
                                                            selected>{{__('locale.KindlyChoose')}}</option>
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
                                    <span>{{__('locale.Add')}} {{__('locale.Route')}}</span>
                                </button>
                            </div>
                            <hr>
                            @isset($request)
                                @foreach($request->goods_types as $index => $item)
                                    <div class="mb-1 row">
                                        <div class="col-7">
                                            <label class="form-label">{{__('locale.GoodType')}}</label>
                                            <label class="form-label"><b>{{$item->name}}</b></label>
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label">{{__('locale.GrossWeight')}}</label>
                                            <label class="form-label">
                                                <b>{{$item->pivot->weight}}</b>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="vessel">{{__('locale.Vessel')}}</label>
                                        <select type="text" class="form-control dt-full-name vessels-select2"
                                                name="vessel[{{$item->pivot->id}}]">
                                            <option value="" disabled
                                                    selected>{{__('locale.KindlyChoose')}}</option>
                                            @foreach($vessels->whereIn('type_id',$item->vessels_types->pluck('id')) as $vessel)
                                                <option value="{{$vessel->id}}">{{$vessel->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <hr>
                                @endforeach
                            @endisset
                            <label class="form-label">{{__('locale.Payments')}}</label>
                            <div class="mb-1 row">
                                <div class="col-12">
                                    <label class="form-label" for="total">{{__('locale.Total')}}</label>
                                    <input type="text" id="total" disabled readonly class="form-control dt-full-name"
                                           placeholder="{{__('locale.Total')}}"/>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <div class="col-6">
                                    <label class="form-label" for="down_value">{{__('locale.DownPayment')}}</label>
                                    <input type="number" id="down_value"
                                           class="form-control calculate-value dt-full-name"
                                           placeholder="{{__('locale.DownPayment')}}" name="down_value"/>
                                </div>
                                <div class="col-6">
                                    <label class="form-label"
                                           for="down_description">{{__('locale.Description')}}</label>
                                    <input type="text" id="down_description" class="form-control dt-full-name"
                                           placeholder="{{__('locale.Description')}}" name="down_description"/>
                                </div>
                            </div>
                            <div id="payments_container"
                                 @if(!$request) style="display: none" @endif>
                                <div data-repeater-list="payments">
                                    <div data-repeater-item>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label" for="value">{{__('locale.Value')}}</label>
                                                <input type="number" class="form-control calculate-value dt-full-name"
                                                       placeholder="{{__('locale.Value')}}" name="value"/>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label"
                                                       for="date">{{__('locale.NextPaymentDate')}}</label>
                                                <input type="date" class="form-control dt-full-name"
                                                       placeholder="{{__('locale.NextPaymentDate')}}" name="date"/>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-6">
                                                <label class="form-label"
                                                       for="description">{{__('locale.Description')}}</label>
                                                <input type="text" class="form-control dt-full-name"
                                                       placeholder="{{__('locale.Description')}}" name="description"/>
                                            </div>
                                            <div class="col-6">
                                                <label for="formFile" class="form-label">{{__('locale.File')}}</label>
                                                <input class="form-control" type="file" name="file"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-icon btn-success" type="button" data-repeater-create>
                                    <i data-feather="plus" class="me-25"></i>
                                    <span>{{__('locale.AddNew')}}</span>
                                </button>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="description">{{__('locale.Description')}}</label>
                                <textarea class="form-control dt-full-name" id="description"
                                          placeholder="{{__('locale.Description')}}" name="description"></textarea>
                            </div>
                            <div class="mb-1">
                                <label for="files" class="form-label">{{__('locale.Attachments')}}</label>
                                <input type="file" name="files" id="files"/>
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
                                        <span class="fw-bolder me-25">{{__('locale.Owner')}}:</span>
                                        <span id="view-owner"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Date')}} #:</span>
                                        <span id="view-date"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Description')}}:</span>
                                        <span id="view-description"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span
                                            class="fw-bolder me-25">{{__('locale.Payments')}} {{__('locale.Total')}}:</span>
                                        <span id="view-total"></span>
                                    </li>
                                    <div id="view-vessels-container" style="display: none">
                                        <div class="info-container">
                                            <span class="fw-bolder me-25">{{__('locale.Vessels')}}:</span>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>{{__('locale.GoodType')}}</th>
                                                    <th>{{__('locale.GrossWeight')}}</th>
                                                    <th>{{__('locale.Vessel')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="view-vessels"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="view-payments-container" style="display: none">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-75">
                                                    <span class="fw-bolder me-25">{{__('locale.DownPayment')}}:</span>
                                                    <span id="view-down-value"></span>
                                                </li>
                                                <li class="mb-75">
                                                    <span class="fw-bolder me-25">{{__('locale.Description')}}:</span>
                                                    <span id="view-down-description"></span>
                                                </li>
                                            </ul>
                                            <span class="fw-bolder me-25">{{__('locale.Payments')}}:</span>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>{{__('locale.Value')}}</th>
                                                    <th>{{__('locale.Date')}}</th>
                                                    <th>{{__('locale.Description')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="view-payments"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Attachments')}}:</span>
                                        <span id="view-files"></span>
                                    </li>
                                </ul>
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
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/requests-responses-list.js')) }}"></script>
    <script>
        $('#owner').on("change.select2", function () {
            var $element = $(this);
            var target = $element.parents('form').find('select.vessels-select2');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content')
                },
                url: '/api/admin/vessels/list?owner=' + $element.find("option:selected").val(),
                type: 'GET',
                cache: false,
                contentType: 'application/json',
                dataType: "json",
                success: function (result) {
                    target.each((index, dbSelect) => {
                        $(dbSelect).empty();
                        for (var i = 0; i < result.data.data.length; i++) {
                            dbSelect.append($('<option/>', {
                                value: result.data.data[i].id,
                                text: result.data.data[i].name
                            }));
                        }
                    })
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        })
    </script>
@endsection

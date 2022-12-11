@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Owners'))

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/css/intlTelInput.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="owners-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="owners-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>{{__('locale.BusinessLegalName')}}</th>
                        <th>{{__('locale.City')}}</th>
                        <th>{{__('locale.ContactName')}}</th>
                        <th>{{__('locale.Phone')}}</th>
                        <th>{{__('locale.Email')}}</th>
                        <th>{{__('locale.VesselsCount')}}</th>
                        <th>{{__('locale.Status')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-owner-modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
                 id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-owner modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.Add')}} {{__('locale.Owner')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="type">{{__('locale.Type')}}</label>
                                <select type="text" class="form-control dt-full-name" id="type"
                                        name="type">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    <option value="1">{{__('locale.Company')}}</option>
                                    <option value="2">{{__('locale.Individual')}}</option>
                                </select>
                            </div>
                            <div id="company-container" style="display: none">
                                <div class="mb-1">
                                    <label class="form-label" for="name">{{__('locale.BusinessLegalName')}}</label>
                                    <input type="text" class="form-control dt-full-name" id="name"
                                           placeholder="{{__('locale.BusinessLegalName')}}" name="name"/>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="commercial">{{__('locale.Commercial')}} #</label>
                                    <input type="text" class="form-control dt-full-name" id="commercial"
                                           placeholder="{{__('locale.Commercial')}} #" name="commercial"/>
                                </div>
                                <div class=mb-1>
                                    <label for="license" class="form-label">{{__('locale.BusinessLicense')}}</label>
                                    <span id="edit-license"></span>
                                    <input type="file" name="license" id="license">
                                </div>
                                <div class=mb-1>
                                    <label for="company" class="form-label">{{__('locale.CompanyFile')}}</label>
                                    <span id="edit-company"></span>
                                    <input type="file" name="company" id="company">
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="contact">{{__('locale.ContactName')}}</label>
                                <input type="text" class="form-control dt-full-name" id="contact"
                                       placeholder="{{__('locale.ContactName')}}" name="contact"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="email">{{__('locale.Email')}}</label>
                                <input type="email" class="form-control dt-full-name" id="email"
                                       placeholder="{{__('locale.Email')}}" name="email"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="phone">{{__('locale.Phone')}}</label>
                                <input type="tel" class="form-control dt-full-name" id="phone"
                                       placeholder="{{__('locale.Phone')}}" name="phone"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="zip">{{__('locale.Zipcode')}}</label>
                                <input type="text" class="form-control dt-full-name" id="zip"
                                       placeholder="{{__('locale.Zipcode')}}" name="zip"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="pass">{{__('locale.Password')}}</label>
                                <div class="input-group input-group-merge form-password-toggle">
                                    <input type="password" class="form-control dt-full-name" id="pass"
                                           placeholder="{{__('locale.Password')}}" name="password"/>
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label"
                                       for="confirm_password">{{__('locale.PasswordConfirmation')}}</label>
                                <div class="input-group input-group-merge form-password-toggle">
                                    <input type="password" class="form-control dt-full-name" id="confirm_password"
                                           equalTo="#pass" placeholder="{{__('locale.PasswordConfirmation')}}"/>
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="country">{{__('locale.Country')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="country"
                                        name="country">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-1">
                                <input type="hidden" value="" id="province_id">
                                <label class="form-label" for="province">{{__('locale.Province')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="province"
                                        name="province">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <input type="hidden" value="" id="city_id">
                                <label class="form-label" for="city">{{__('locale.City')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="city"
                                        name="city">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="address_1">{{__('locale.Address')}} 1</label>
                                <input type="text" class="form-control dt-full-name" id="address_1"
                                       placeholder="{{__('locale.Address')}} 1" name="address_1"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="address_2">{{__('locale.Address')}} 2</label>
                                <input type="text" class="form-control dt-full-name" id="address_2"
                                       placeholder="{{__('locale.Address')}} 2" name="address_2"/>
                            </div>
                            <div class=mb-1>
                                <label for="legal" class="form-label">{{__('locale.LegalFile')}}</label>
                                <span id="edit-legal"></span>
                                <input type="file" name="legal" id="legal"/>
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

            <!-- Modal to view user starts-->
            <div class="modal modal-slide-in view-owner-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">{{__('locale.View')}} {{__('locale.Owner')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Type')}}:</span>
                                        <span id="view-type"></span>
                                    </li>
                                </ul>
                            </div>
                            <div id="view-company-container" style="display: none">
                                <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">{{__('locale.BusinessLegalName')}}:</span>
                                            <span id="view-name"></span>
                                        </li>
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">{{__('locale.Commercial')}} #:</span>
                                            <span id="view-commercial"></span>
                                        </li>
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">{{__('locale.BusinessLicense')}}:</span>
                                            <span id="view-license"></span>
                                        </li>
                                        <li class="mb-75">
                                            <span class="fw-bolder me-25">{{__('locale.CompanyFile')}}:</span>
                                            <span id="view-company"></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="info-container">
                                <ul class="list-unstyled">
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.ContactName')}}:</span>
                                        <span id="view-contact"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Email')}}:</span>
                                        <span id="view-email"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Phone')}}:</span>
                                        <span id="view-phone"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Zipcode')}}:</span>
                                        <span id="view-zip"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Country')}}:</span>
                                        <span id="view-country"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.City')}}:</span>
                                        <span id="view-city"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Province')}}:</span>
                                        <span id="view-province"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Address')}} 1:</span>
                                        <span id="view-address-1"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.Address')}} 2:</span>
                                        <span id="view-address-2"></span>
                                    </li>
                                    <li class="mb-75">
                                        <span class="fw-bolder me-25">{{__('locale.LegalFile')}}:</span>
                                        <span id="view-legal"></span>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/owners-list.js')) }}"></script>
    <script>
        $('#country').on("change.select2", function () {
            var $element = $(this);
            var target = $element.parents('form').find('select#province');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/api/admin/states/list/' + $element.find("option:selected").val(),
                type: 'GET',
                cache: false,
                contentType: 'application/json',
                dataType: "json",
                success: function (result) {
                    var dbSelect = target;
                    dbSelect.empty();
                    for (var i = 0; i < result.data.length; i++) {
                        dbSelect.append($('<option/>', {
                            value: result.data[i].id,
                            text: result.data[i].name
                        }));
                    }

                    if ($('#province_id').val()) {
                        target.val(target.find('option[text="' + $('#province_id').val() + '"]').val());
                        target.trigger('change');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        });
        $('#province').on("change.select2", function () {
            var $element = $(this);
            var target = $element.parents('form').find('select#city');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/api/admin/cities/list/' + $element.find("option:selected").val(),
                type: 'GET',
                cache: false,
                contentType: 'application/json',
                dataType: "json",
                success: function (result) {
                    var dbSelect = target;
                    dbSelect.empty();
                    for (var i = 0; i < result.data.length; i++) {
                        dbSelect.append($('<option/>', {
                            value: result.data[i].id,
                            text: result.data[i].name
                        }));
                    }

                    if ($('#city_id').val()) {
                        target.val($('#city_id').val());
                        target.trigger('change');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        });
    </script>
@endsection

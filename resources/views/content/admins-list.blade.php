@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Administrators'))

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
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{asset(mix($css_path . '/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- admins list start -->
    <section class="admins-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">{{__('locale.SearchAndFilter')}}</h4>
                <input type="hidden" id="status_filter" value="1">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="admins-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>DealGo ID</th>
                        <th>{{__('locale.ContactName')}}</th>
                        <th>{{__('locale.Phone')}}</th>
                        <th>{{__('locale.Email')}}</th>
                        <th>{{__('locale.Role')}}</th>
                        <th>{{__('locale.Status')}}</th>
                        <th>{{__('locale.Actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <div class="modal modal-slide-in new-admin-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-admin modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title"
                                id="modal-label">{{__('locale.Add')}} {{__('locale.Administrator')}}</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="role">{{__('locale.Role')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="role"
                                        name="role">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="name">{{__('locale.ContactName')}}</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="{{__('locale.ContactName')}}" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="dealgo_id">DealGo ID</label>
                                <input type="text" class="form-control dt-full-name" id="dealgo_id"
                                       placeholder="DealGo ID" name="dealgo_id"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="name">{{__('locale.Email')}}</label>
                                <input type="email" class="form-control dt-full-name" id="email"
                                       placeholder="{{__('locale.Email')}}" name="email"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="phone">{{__('locale.Phone')}}</label>
                                <input type="tel" class="form-control dt-full-name" id="phone"
                                       placeholder="{{__('locale.Phone')}}" name="phone"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="password">{{__('locale.Password')}}</label>
                                <div class="input-group input-group-merge form-password-toggle">
                                    <input type="password" class="form-control dt-full-name" id="password"
                                           placeholder="{{__('locale.Password')}}" name="password"/>
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
                                <input type="hidden" value="" id="city_id">
                                <label class="form-label" for="city">{{__('locale.City')}}</label>
                                <select type="text" class="form-control dt-full-name select2" id="city"
                                        name="city">
                                    <option value="" disabled selected>{{__('locale.KindlyChoose')}}</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="address">{{__('locale.Address')}}</label>
                                <input type="text" class="form-control dt-full-name" id="address"
                                       placeholder="{{__('locale.Address')}}" name="address"/>
                            </div>
                            <div class="mb-1">
                                <label for="files" class="form-label">{{__('locale.Attachments')}}</label>
                                <input type="file" name="files" id="files"/>
                            </div>

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
        </div>
        <!-- list and filter end -->
    </section>
    <!-- admins list ends -->
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
    <script src="{{ asset(mix('js/scripts/pages/admins-list.js')) }}"></script>
    <script>
        $('#country').on("change.select2", function () {
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
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });

            if ($('#city_id').val()) {
                target.val($('#city_id').val());
                target.trigger('change');
            }
        });
    </script>
@endsection

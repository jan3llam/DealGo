@extends('layouts.contentLayoutMaster')

@section('title', 'Charters')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/css/intlTelInput.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/css/fileinput.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="tenants-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
                <input type="hidden" id="trashed" value="0">
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="tenants-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Business legal name</th>
                        <th>City</th>
                        <th>Contact name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-tenant-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-tenant modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add charter</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="type">Type</label>
                                <select type="text" class="form-control dt-full-name" id="type"
                                        name="type">
                                    <option value="" disabled selected>Kindly choose</option>
                                    <option value="1">Company</option>
                                    <option value="2">Individual</option>
                                </select>
                            </div>
                            <div id="company-container" style="display: none">
                                <div class="mb-1">
                                    <label class="form-label" for="name">Full name</label>
                                    <input type="text" class="form-control dt-full-name" id="name"
                                           placeholder="Full name" name="name"/>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="commercial">Commercial #</label>
                                    <input type="text" class="form-control dt-full-name" id="commercial"
                                           placeholder="Commercial #" name="commercial"/>
                                </div>
                                <div class=mb-1>
                                    <label for="license" class="form-label">License file</label>
                                    <input type="file" name="license" id="license">
                                </div>
                                <div class=mb-1>
                                    <label for="company" class="form-label">Company file</label>
                                    <input type="file" name="company" id="company">
                                </div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="contact">Contact name</label>
                                <input type="text" class="form-control dt-full-name" id="contact"
                                       placeholder="Contact name" name="contact"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control dt-full-name" id="email"
                                       placeholder="Email" name="email"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="tel" class="form-control dt-full-name" id="phone"
                                       placeholder="Phone" name="phone"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="zip">Zip code</label>
                                <input type="text" class="form-control dt-full-name" id="zip"
                                       placeholder="Zip code" name="zip"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control dt-full-name" id="password"
                                       placeholder="Password" name="password"/>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" for="country">Country</label>
                                <select type="text" class="form-control dt-full-name select2" id="country"
                                        name="country">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input type="hidden" value="" id="city_id">
                                <label class="form-label" for="city">Cities</label>
                                <select type="text" class="form-control dt-full-name select2" id="city"
                                        name="city">
                                    <option value="" disabled selected>Kindly choose</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="address_1">Address 1</label>
                                <input type="text" class="form-control dt-full-name" id="address_1"
                                       placeholder="Address 1" name="address_1"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="address_2">Address 2</label>
                                <input type="text" class="form-control dt-full-name" id="address_2"
                                       placeholder="Address 2" name="address_2"/>
                            </div>
                            <div class=mb-1>
                                <label for="legal" class="form-label">Legal file (ID, Passport)</label>
                                <input type="file" name="legal" id="legal"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="gtype">Goods types</label>
                                <select type="text" class="form-control dt-full-name select2" id="gtype"
                                        name="gtype" multiple="multiple">
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
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
            <div class="modal modal-slide-in view-tenant-modal fade">
                <div class="modal-dialog">
                    <div class="modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">View charter</h5>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/plugins/piexif.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/fileinput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/tenants-list.js')) }}"></script>
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

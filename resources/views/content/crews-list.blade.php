@extends('layouts.contentLayoutMaster')

@section('title', 'Crews')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/css/intlTelInput.css">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="crews-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="crews-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Full name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Job title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-crew-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-crew modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add crew</h5>
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
                            <div class="mb-1">
                                <label class="form-label" for="first_name">First name</label>
                                <input type="text" class="form-control dt-full-name" id="first_name"
                                       placeholder="First name" name="first_name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="last_name">Last name</label>
                                <input type="text" class="form-control dt-full-name" id="last_name"
                                       placeholder="Last name" name="last_name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="job">Job title</label>
                                <input type="text" class="form-control dt-full-name" id="job"
                                       placeholder="Job title" name="job"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="birth">DOB</label>
                                <input type="date" class="form-control dt-full-name" id="birth"
                                       placeholder="DOB" name="birth"/>
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
                                <label class="form-label" for="address">Address</label>
                                <input type="text" class="form-control dt-full-name" id="address"
                                       placeholder="Address" name="address"/>
                            </div>
                            <div class=mb-1>
                                <label for="file" class="form-label">File</label>
                                <div class="dropzone" id="file">
                                </div>
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
    <script src="{{ asset(mix('vendors/js/file-uploaders/dropzone.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/intlTelInput.min.js"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/crews-list.js')) }}"></script>
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

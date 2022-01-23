@extends('layouts.contentLayoutMaster')

@section('title', 'Shipping requests')

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
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="requests-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="requests-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Full legal name</th>
                        <th>Vessels count</th>
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
            <div class="modal modal-slide-in new-request-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-request modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add request</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">Title</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="Title" name="name"/>
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
                                <label class="form-label" for="commercial">Commercial #</label>
                                <input type="text" class="form-control dt-full-name" id="commercial"
                                       placeholder="Commercial #" name="commercial"/>
                            </div>
                            <div class=mb-1>
                                <label for="file" class="form-label">License file</label>
                                <div class="dropzone" id="license">
                                    <div class="dz-message">Drop files here or click to upload.</div>
                                </div>
                            </div>
                            <div class=mb-1>
                                <label for="file" class="form-label">Company file</label>
                                <div class="dropzone" id="company">
                                    <div class="dz-message">Drop files here or click to upload.</div>
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
                                <label for="file" class="form-label">Legal file (ID, Passport)</label>
                                <div class="dropzone" id="legal">
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
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/requests-list.js')) }}"></script>
@endsection

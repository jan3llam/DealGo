@extends('layouts.contentLayoutMaster')

@section('title', 'Administrators')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- admins list start -->
    <section class="admins-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="admins-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>DealGo ID</th>
                        <th>Contact name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <div class="modal modal-slide-in new-admin-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-admin modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add administrator</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">Contact name</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="Contact name" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="dealgo_id">DealGo ID</label>
                                <input type="text" class="form-control dt-full-name" id="dealgo_id"
                                       placeholder="DealGo ID" name="dealgo_id"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="name">Email</label>
                                <input type="email" class="form-control dt-full-name" id="email"
                                       placeholder="Email" name="email"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="tel" class="form-control dt-full-name" id="phone"
                                       placeholder="Phone" name="phone"/>
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
                                <label class="form-label" for="address">Address</label>
                                <input type="text" class="form-control dt-full-name" id="address"
                                       placeholder="Address" name="address"/>
                            </div>
                            <div class=mb-1>
                                <label for="files" class="form-label">Files</label>
                                <div class="dropzone" id="files">
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
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/admins-list.js')) }}"></script>
@endsection

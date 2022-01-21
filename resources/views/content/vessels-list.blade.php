@extends('layouts.contentLayoutMaster')

@section('title', 'Vessels')

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
    <section class="vessels-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-body border-bottom">
                <h4 class="card-title">Search & Filter</h4>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="vessels-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>IMO</th>
                        <th>Owner name</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal to add new user starts-->
            <div class="modal modal-slide-in new-vessel-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-vessel modal-content pt-0">
                        <input type="hidden" value="1" id="form_status">
                        <input type="hidden" value="" id="object_id">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">Add vessel</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="Name" name="name"/>
                            </div>
                            <div class="mb-1">
                                <input type="hidden" value="" id="type_id">
                                <label class="form-label" for="type">Type</label>
                                <select type="text" class="form-control dt-full-name select2" id="type"
                                        name="type">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input type="hidden" value="" id="owner_id">
                                <label class="form-label" for="owner">Owner</label>
                                <select type="text" class="form-control dt-full-name select2" id="owner"
                                        name="owner">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @foreach($owners as $owner)
                                        <option value="{{$owner->id}}">{{$owner->contact_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="imo">IMO</label>
                                <input type="text" class="form-control dt-full-name" id="imo"
                                       placeholder="IMO" name="imo"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="mmsi">MMSI</label>
                                <input type="text" class="form-control dt-full-name" id="mmsi"
                                       placeholder="MMSI" name="mmsi"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="capacity">Capacity</label>
                                <input type="text" class="form-control dt-full-name" id="capacity"
                                       placeholder="Capacity" name="capacity"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="build">Build year</label>
                                <select type="text" class="form-control dt-full-name" id="build" name="build">
                                    <option value="" disabled selected>Kindly choose</option>
                                    @for($i = 1910; $i<=intval(date('Y'));$i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
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
                            <div class=mb-1>
                                <label for="file" class="form-label">Image</label>
                                <div class="dropzone" id="image">
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
    <script src="{{ asset(mix('js/scripts/pages/vessels-list.js')) }}"></script>
@endsection

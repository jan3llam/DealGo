@extends('layouts.contentLayoutMaster')

@section('title', 'قائمة المدراء')

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
                <h4 class="card-title">البحث والتصفية</h4>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="admins-list-table table">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الحالة</th>
                        <th>النوع</th>
                        <th>أُنشى @</th>
                        <th>حُدث @</th>
                        <th>حُذف @</th>
                        <th>الخيارات</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <div class="modal modal-slide-in new-admin-modal fade" id="modals-slide-in">
                <div class="modal-dialog">
                    <form class="add-new-admin modal-content pt-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                        <div class="modal-header mb-1">
                            <h5 class="modal-title" id="modal-label">إضافة مدير</h5>
                        </div>
                        <div class="modal-body flex-grow-1">
                            <div class="mb-1">
                                <label class="form-label" for="name">اسم المدير</label>
                                <input type="text" class="form-control dt-full-name" id="name"
                                       placeholder="اسم المدير" name="name"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="name">البريد الإلكتروني</label>
                                <input type="email" class="form-control dt-full-name" id="email"
                                       placeholder="البريد الإلكتروني" name="email"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="name">كلمة المرور</label>
                                <input type="password" class="form-control dt-full-name" id="password"
                                       placeholder="كلمة المرور" name="password"/>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="price">النوع</label>
                                <select type="text" class="form-control dt-full-name" id="type"
                                        name="type">
                                    <option value="1">مدير عام</option>
                                    <option value="2" selected>مدير عادي</option>
                                </select>
                                <p class="help-block">
                                    المدير العادي : يستطيع التحكم بكل شيء عدا المدراء والإعدادات<br>
                                    المدير العام : يستطيع التحكم بكل شيء
                                </p>
                            </div>
                            <button type="submit" class="btn btn-primary me-1 data-submit">إرسال</button>
                            <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                تراجع
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

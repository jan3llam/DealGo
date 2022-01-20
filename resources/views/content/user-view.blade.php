@extends('layouts/contentLayoutMaster')

@section('title', 'استعراض المستخدم')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('content')
    <section class="app-user-view-account">
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="user-info text-center">
                                    <h4>{{$object->name}}</h4>
                                </div>
                            </div>
                        </div>
                        <h4 class="fw-bolder border-bottom pb-50 mb-1">التفاصيل</h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">اسم المستخدم:</span>
                                    <span>{{$object->username}}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">الإيميل:</span>
                                    <span>{{$object->email}}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">الحالة:</span>
                                    <span
                                        class="badge bg-light-success">{{$object->active ? 'فعال' : 'غير فعال'}}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">رقم الموبايل:</span>
                                    <span>{{$object->gsm}}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">تاريخ الانضمام:</span>
                                    <span>{{$object->created_at}}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">رقم العضوية:</span>
                                    <span>{{$object->id}}</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center pt-2">
                                <a href="javascript:;" class="btn btn-primary me-1" data-bs-target="#editUser"
                                   data-bs-toggle="modal">
                                    تعديل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
            <!--/ User Sidebar -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <!-- Invoice table -->
                <div class="card">
                    <div class="card-header">الاشتراكات</div>
                    <table class="invoice-table table text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>الباقة</th>
                            <th>رقم الإثبات</th>
                            <th>أُنشى @</th>
                            <th>ينتهي @</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($object->subscriptions as $subscription)
                            <tr>
                                <td>{{$subscription->id}}</td>
                                <td>{{$subscription->package->name}}</td>
                                <td>{{$subscription->number}}</td>
                                <td>{{$subscription->created_at}}</td>
                                <td>{{$subscription->end_at}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="card">
                    <div class="card-header">التذاكر</div>
                    <table class="invoice-table table text-nowrap">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>النص</th>
                            <th>الحالة</th>
                            <th>أُنشى @</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($object->tickets as $ticket)
                            <tr>
                                <td>{{$ticket->id}}</td>
                                <td>{{$ticket->title}}</td>
                                <td>{{$ticket->text}}</td>
                                <td>{{$ticket->status ? 'مفتوح' : 'مغلق'}}</td>
                                <td>{{$ticket->id}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /Invoice table -->
            </div>
        </div>
    </section>

    @include('content/_partials/modal-edit-user')
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    {{-- data table --}}
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

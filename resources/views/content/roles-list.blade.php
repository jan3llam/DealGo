@extends('layouts.contentLayoutMaster')
@php
    $css_path = app()->getLocale()==='ar' ? 'css-rtl' : 'css';
@endphp

@section('title', __('locale.Roles'))

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix($css_path . '/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')

    <div class="row">
        @foreach($roles as $role)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <span>{{__('locale.Total')}} {{$role->users_count}} {{__('locale.Administrators')}}</span>
                            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                @foreach($role->users->take(5) as $admin)
                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                        title="{{$admin->name}}" class="avatar avatar-sm pull-up">
                                        <img class="rounded-circle" src="{{asset('images/avatars/blank.png')}}"
                                             alt="Avatar"/>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="d-flex justify-content-between align-items-end mt-1 pt-25">
                            <div class="role-heading">
                                <h4 class="fw-bolder">{{$role->name}}</h4>
                                <p class="lead">{{$role->description}}</p>
                                <a href="javascript:;" data-permissions="{{$role->permissions->pluck('id')}}"
                                   data-name="{{$role->name}}" data-id="{{$role->id}}"
                                   data-description="{{$role->description}}"
                                   class="role-edit-modal" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                    <small class="fw-bolder">{{__('Edit')}} {{__('Role')}}</small>
                                </a>
                            </div>
                            @if ($role->id !== 1)
                                <a href="javascript:void(0);" class="text-body item-delete" data-id="{{$role->id}}">
                                    <i data-feather="trash" class="font-medium-5"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="d-flex align-items-end justify-content-center h-100">
                            <img src="{{asset('images/illustration/faq-illustrations.svg')}}"
                                 class="img-fluid mt-2" alt="Image" width="85"/>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="card-body text-sm-end text-center ps-sm-0">
                            <a href="javascript:void(0)" data-bs-target="#addRoleModal" data-bs-toggle="modal"
                               class="stretched-link text-nowrap add-new-role">
                                <span class="btn btn-primary mb-1">{{__('locale.AddNew')}} {{__('locale.Role')}}</span>
                            </a>
                            <p class="mb-0">{{__('locale.AddNewRoleMsg')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('content/_partials/modal-add-role')

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/modal-add-role.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/roles-list.js')) }}"></script>
@endsection

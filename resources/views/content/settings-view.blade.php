@extends('layouts.contentLayoutMaster')

@section('title', 'الإعدادات')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.settings')}}" method="POST">
                            @csrf
                            <div class="row">
                                @foreach($settings as $setting)
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label"
                                                   for="settings-{{$setting->key}}">{{$setting->key}}</label>
                                            <input type="text" class="form-control" name="{{$setting->key}}"
                                                   id="settings-{{$setting->key}}" value="{{$setting->value}}"
                                                   placeholder="Enter {{$setting->key}}" required>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-primary waves-effect waves-float waves-light" type="submit">
                                إرسال
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection


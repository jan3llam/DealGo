@extends('layouts.contentLayoutMaster')

@section('title', 'إرسال إشعار')

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
                        <form action="{{route('admin.notification')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="title_ar">العنوان</label>
                                        <input type="text" class="form-control" name="title_ar"
                                               id="title_ar" placeholder="أدخل العنوان" required>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="title_ar">النص</label>
                                        <textarea class="form-control" name="text_ar" id="text_ar"
                                                  placeholder="أدخل النص" required></textarea>
                                    </div>
                                </div>
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


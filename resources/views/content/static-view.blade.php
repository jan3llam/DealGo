@extends('layouts.contentLayoutMaster')

@section('title', 'الإعدادات')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <section class="full-editor">
        <form action="{{route('admin.content')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-12">
                    @foreach($content as $item)
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{$item->identifier}}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <textarea name="{{$item->identifier}}" class="text-area-val"
                                                  style="display:none"
                                                  id="{{$item->identifier}}"></textarea>
                                        <div id="{{$item->identifier}}-editor">
                                            <div class="full-container">
                                                <div class="editor">{!! $item->content !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="card">
                        <button class="btn btn-primary waves-effect waves-float waves-light" type="submit">
                            إرسال
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/settings-view.js')) }}"></script>
    <script>
        $("form").on("submit", function () {
            $("#privacy-policy").val($(".ql-editor").html());
        })
    </script>
@endsection

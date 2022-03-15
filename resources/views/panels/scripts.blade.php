<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
<!-- BEGIN Vendor JS-->
<!-- BEGIN: Page Vendor JS-->
<script src="{{asset(mix('vendors/js/ui/jquery.sticky.js'))}}"></script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script>
    var LANG = {!! json_encode(\Illuminate\Support\Facades\Lang::get('locale')) !!};
</script>
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>

<!-- custome scripts file for user -->
<script src="{{ asset(mix('js/core/scripts.js')) }}"></script>

@if($configData['blankPage'] === false)
    <script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>
@endif
<!-- END: Theme JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
@if(app()->getLocale() === 'ar')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_ar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.2.5/js/locales/ar.js"></script>
@endif
<!-- END: Page JS-->
@if(\Illuminate\Support\Facades\Session::has('success'))
    <script>
        toastr['success']('{{\Illuminate\Support\Facades\Session::get('success')}}');
    </script>
@elseif(\Illuminate\Support\Facades\Session::has('error'))
    <script>
        toastr['error']('{{\Illuminate\Support\Facades\Session::get('error')}}');
    </script>
@endif

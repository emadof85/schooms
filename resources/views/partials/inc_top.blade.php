<link rel="icon" href="{{ asset('global_assets/images/favicon.png') }}">

{{--<!-- Global stylesheets -->--}}
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('global_assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    {{-- LTR & RTL Stylesheet --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet"  type="text/css">
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('assets/css/bootstrap-rtl.min.css') }}" rel="stylesheet"  type="text/css">
        <link href=" {{ asset('assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
        <link href=" {{ asset('assets/css/layout-rtl.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href=" {{ asset('assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
        <link href=" {{ asset('assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    @endif
    
    

{{--DatePickers--}}
<!-- <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker.min.css') }}" type="text/css"> -->
<link rel="stylesheet" href="{{ asset('global_assets/css/pickeadate/theme/classic.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('global_assets/css/pickeadate/theme/classic.date.css') }}" type="text/css">
@if(app()->getLocale() == 'ar') 
	<link href=" {{ asset('global_assets/css/pickeadate/theme/rtl.css') }}" rel="stylesheet" type="text/css">
@endif

{{-- Custom App CSS--}}
<link href=" {{ asset('assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
<link href=" {{ asset('assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
<link href=" {{ asset('assets/css/qs.css') }}" rel="stylesheet" type="text/css">
<link href=" {{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->
@if(app()->getLocale() == 'ar') 
	<link href=" {{ asset('assets/css/rtl.css') }}" rel="stylesheet" type="text/css">
@endif

{{--   Core JS files --}}
    <script src="{{ asset('global_assets/js/main/jquery.min.js') }} "></script>
    <script src="{{ asset('global_assets/js/main/bootstrap.bundle.min.js') }} "></script>
    <script src="{{ asset('global_assets/js/plugins/loaders/blockui.min.js') }} "></script>


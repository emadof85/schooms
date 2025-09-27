<!-- Theme JS files -->
<script src="{{ asset('global_assets/js/plugins/extensions/jquery_ui/interactions.min.js') }} "></script>
<script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }} "></script>

{{--Forms--}}
<script src="{{ asset('global_assets/js/plugins/forms/wizards/steps.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/inputs/inputmask.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/validation/validate.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/extensions/cookie.js') }}"></script>

{{--Notify--}}
<script type="text/javascript" src="{{ asset('global_assets/js/plugins/notifications/sweet_alert2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('global_assets/js/plugins/notifications/pnotify.min.js') }}"></script>

{{--DataTables--}}
<script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/buttons.min.js') }}"></script>

{{--Date Pickers--}}
<script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/pickers/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/pickers/pickadate/legacy.js') }}"></script>

{{--Uploaders--}}
<script src="{{ asset('global_assets/js/plugins/uploaders/fileinput/fileinput.min.js') }}"></script>

{{--Calendar--}}
<script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/fullcalendar.min.js') }}"></script>
{{-- FullCalendar Locales --}}

@if(app()->getLocale() != 'en')
    <script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/lang/'.app()->getLocale().'.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/validation/localization/messages_'.str_replace('-', '_', app()->getLocale()).'.js') }}"></script>
@endif


<script src=" {{ asset('assets/js/app.js') }} "></script>
<script src="{{ asset('global_assets/js/demo_pages/form_wizard.js') }}"></script>
<script src="{{ asset('global_assets/js/demo_pages/form_select2.js') }}"></script>
<script src="{{ asset('global_assets/js/demo_pages/datatables_extension_buttons_html5.js') }}"></script>
<script src="{{ asset('global_assets/js/demo_pages/uploader_bootstrap.js') }}"></script>
<script src="{{ asset('global_assets/js/demo_pages/fullcalendar_basic.js') }}"></script>

<!-- /theme JS files -->

<script src=" {{ asset('assets/js/custom.js') }} "></script>

<script>
    // Set DataTables language file URL
    @if(app()->getLocale() != 'en')
        window.dtLanguageUrl = "{{ asset('global_assets/js/plugins/internationalization/' . app()->getLocale() . '.json') }}";
    @else
        window.dtLanguageUrl = null;
    @endif

    // Set DataTables custom button translations
    @php
        $dtButtonTranslations = [
            'copy' => __('msg.copy'),
            'excel' => __('msg.excel'),
            'pdf' => __('msg.pdf'),
            'visibility' => __('msg.visibility'),
        ];
    @endphp
    window.dtButtonTranslations = {!! json_encode($dtButtonTranslations, JSON_UNESCAPED_UNICODE) !!};
</script>

@include('partials.js.custom_js')
<div id="page-header" class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-plus-circle2 mr-2"></i> <span class="font-weight-semibold">@yield('page_title')</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
   {{--             <a href="#" class="btn btn-link btn-float text-default"><i class="icon-bars-alt text-primary"></i><span>{{ __('msg.statistics') }}</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>{{ __('msg.invoices') }}</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>{{ __('msg.schedule') }}</span></a>--}}
                <a href="{{ Qs::userIsSuperAdmin() ? route('settings') : '' }}" class="btn btn-link btn-float text-default"><i class="icon-arrow-down7 text-primary"></i> <span class="font-weight-semibold">{{__('msg.current_session')}}: {{ Qs::getSetting('current_session') }}</span></a>
            </div>
        </div>
    </div>

    {{--Breadcrumbs--}}
    {{--<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> {{ __('msg.home') }}</a>
                <a href="form_select2.html" class="breadcrumb-item">{{ __('msg.forms') }}</a>
                <span class="breadcrumb-item active">{{ __('msg.select2_selects') }}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="breadcrumb justify-content-center">
                <a href="#" class="breadcrumb-elements-item">
                    <i class="icon-comment-discussion mr-2"></i>
                    {{ __('msg.support') }}
                </a>

                <div class="breadcrumb-elements-item dropdown p-0">
                    <a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-gear mr-2"></i>
                        {{ __('msg.settings') }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item"><i class="icon-user-lock"></i> {{ __('msg.account_security') }}</a>
                        <a href="#" class="dropdown-item"><i class="icon-statistics"></i> {{ __('msg.analytics') }}</a>
                        <a href="#" class="dropdown-item"><i class="icon-accessibility"></i> {{ __('msg.accessibility') }}</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item"><i class="icon-gear"></i> {{ __('msg.all_settings') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}
</div>

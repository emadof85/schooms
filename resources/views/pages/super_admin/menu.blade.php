{{--Manage Settings--}}
<li class="nav-item">
    <a href="{{ route('settings') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['settings',]) ? 'active' : '' }}"><i class="icon-gear"></i> <span>{{ __('msg.settings') }}</span></a>
</li>

{{--Pins--}}
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['pins.create', 'pins.index']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-lock2"></i> <span> {{ __('msg.pins') }}</span></a>

    <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.manage_pins') }}">
        {{--Generate Pins--}}
            <li class="nav-item">
                <a href="{{ route('pins.create') }}"
                   class="nav-link {{ (Route::is('pins.create')) ? 'active' : '' }}">{{ __('msg.generate_pins') }}</a>
            </li>

        {{--    Valid/Invalid Pins  --}}
        <li class="nav-item">
            <a href="{{ route('pins.index') }}"
               class="nav-link {{ (Route::is('pins.index')) ? 'active' : '' }}">{{ __('msg.view_pins') }}</a>
        </li>
    </ul>
</li>
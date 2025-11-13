<div class="navbar navbar-expand-md navbar-dark">
    <div class="mt-2 mr-5 d-flex align-items-center">
        <a href="{{ route('dashboard') }}" class="d-inline-block mr-3">
            <img src="{{ Qs::getSetting('logo') ?: asset('global_assets/images/logo_light.png') }}" alt="Logo" style="height: 40px;">
        </a>
        <a href="{{ route('dashboard') }}" class="d-inline-block">
            <h4 class="text-bold text-white mb-0">{{ Qs::getSystemName() }}</h4>
        </a>
    </div>

    <div class="d-md-none">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            <i class="icon-tree5"></i>
        </button>
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-paragraph-justify3"></i>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>


        </ul>

			<span class="navbar-text ml-md-3 mr-md-auto"></span>
		<ul class="navbar-nav ml-auto">
        	<li class="nav-item dropdown">
                <a class="navbar-nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ strtoupper(app()->getLocale()) }}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="/language/en">{{ __('msg.en') }}</a>
                    <a class="dropdown-item" href="/language/fr">{{ __('msg.fr') }}</a>
                    <a class="dropdown-item" href="/language/ar">{{ __('msg.ar') }}</a>
                    <a class="dropdown-item" href="/language/ru">{{ __('msg.ru') }}</a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav">

            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown">
                    <img style="width: 38px; height:38px;" src="{{ Auth::user()->photo }}" class="rounded-circle" alt="{{ __('msg.photo_5ae0') }}">
                    <span>{{ Auth::user()->name }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ Qs::userIsStudent() ? route('students.show', Qs::hash(Qs::findStudentRecord(Auth::user()->id)->id)) : route('users.show', Qs::hash(Auth::user()->id)) }}" class="dropdown-item"><i class="icon-user-plus"></i> {{ __('msg.my_profile') }}</a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('my_account') }}" class="dropdown-item"><i class="icon-cog5"></i> {{ __('msg.account_settings') }}</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
          document.getElementById('logout-form').submit();" class="dropdown-item"><i class="icon-switch2"></i> {{ __('msg.logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>

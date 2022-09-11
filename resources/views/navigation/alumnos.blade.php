<!-- Authentication Links -->
@guest('alumnos')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('alumnos.login') }}">{{ __('Alumnos Login') }}</a>
    </li>
@else
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ Auth::guard('alumnos')->inscripcion()->alumno_id }} 
            <span class="caret"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route('alumnos.logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('alumnos.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </li>
@endguest
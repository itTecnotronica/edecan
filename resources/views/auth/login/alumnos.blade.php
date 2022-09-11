@extends('layouts.login_layout')

<link href="{{ asset('css/login.css') }}" rel="stylesheet">

<!-- @section('navigation')
    @include('navigation.alumnos')
@endsection -->

@section('content')


<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="jumbotron">
                <h2>Bienvenido a tu espacio!</h2>
                <p> Desde aca vas a poder ver todas tus lecciones, modificar tus datos, contactarnos por Whatsapp y mucho mas!</p>
            </div>
        </div>
    </div>


    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                {{ csrf_field() }}    

                <br />
                <br />

                <div class="card-body">
                    <form method="POST" action="{{ route('alumnos.login') }}">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label for="id" class="col-sm-4 col-form-label text-md-right">Incripcion ID</label>

                            <div class="col-md-6">
                                <input id="id" type="text" class="form-control{{ $errors->has('id') ? ' is-invalid' : '' }}" name="id" value="{{ old('id') }}" required autofocus>

                                @if ($errors->has('id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="correo_o_cel" class="col-sm-4 col-form-label text-md-right">Correo o Celular</label>

                            <div class="col-md-6">
                                <input id="correo_o_cel" type="text" class="form-control{{ $errors->has('correo_o_cel') ? ' is-invalid' : '' }}" name="correo_o_cel" value="{{ old('corre-tel') }}" required autofocus>

                                @if ($errors->has('id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('correo_o_cel') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                      <!--   <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" >

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
 -->
                        @if($errors->any())
                            @foreach($errors->getMessages() as $this_error)
                                <p style="color: red;">{{$this_error[0]}}</p>
                            @endforeach
                        @endif 
                        

                        <div class="row mensaje-error">
                            @if ($errors->has('usuario-no-encontrado'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{  "Error!" }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app_gnosis')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo __('Error') ?></div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ __('Página no encontrada') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
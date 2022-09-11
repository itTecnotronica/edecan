@extends('layouts.app_gnosis')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Error</div>
                <div class="card-body">
                    @if ($error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
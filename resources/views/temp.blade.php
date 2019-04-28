@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="alert alert-success" role="alert">{{ $weatherEntity->temp > 0 ? '+' : '-'}} {{ $weatherEntity->temp }} &deg;C</div>
        </div>
    </div>
@endsection
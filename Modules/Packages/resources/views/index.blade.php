@extends('packages::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('packages.name') !!}</p>
@endsection

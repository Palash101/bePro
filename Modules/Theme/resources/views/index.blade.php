@extends('theme::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('theme.name') !!}</p>
@endsection

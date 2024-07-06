@extends('creator::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('creator.name') !!}</p>
@endsection

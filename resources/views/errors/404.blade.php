@extends('errors.minimal')

@section('title', 'Page Not Found')
@section('code', '404')
@section('message', 'The page you are looking for could not be found. Please check the URL and try again.')

@section('action')
    <a href="{{ url('/' . app()->getLocale()) }}" class="btn">Back to Home</a>
@endsection

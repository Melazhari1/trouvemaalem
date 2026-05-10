@extends('errors.minimal')

@section('title', 'Server Error')
@section('code', '500')
@section('message', 'Something went wrong on our end. Please try again later. If the problem persists, contact support.')

@section('action')
    <a href="{{ url('/' . app()->getLocale()) }}" class="btn">Back to Home</a>
@endsection

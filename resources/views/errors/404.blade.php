@extends('layouts.error')
@section('title', 'Page not found')
@section('eyebrow', 'Error 404 &middot; Page not found')
@section('code', '404')
@section('heading', 'This page has wandered off.')
@section('message', 'The link may be out of date, or the page may have moved. Let us get you back to something beautiful.')
@section('actions')
    <a class="button primary" href="{{ route('home') }}">Back to home</a>
    <a class="button" href="{{ route('category') }}">Browse the shop</a>
@endsection

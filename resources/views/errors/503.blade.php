@extends('layouts.error')
@section('title', 'We will be right back')
@section('eyebrow', 'Error 503 &middot; Briefly unavailable')
@section('code', '503')
@section('heading', 'A little care is underway.')
@section('message', 'We are making a few improvements behind the scenes. Please check back shortly; the shop will be ready for you again soon.')
@section('actions')
    <button class="button primary" type="button" onclick="window.location.reload()">Check again</button>
    <a class="button" href="{{ route('home') }}">Back to home</a>
@endsection

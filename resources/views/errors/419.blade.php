@extends('layouts.error')
@section('title', 'Session expired')
@section('eyebrow', 'Error 419 &middot; Session expired')
@section('code', '419')
@section('heading', 'Your session took a little break.')
@section('message', 'For your security, this page has timed out. Return to the previous page and try your action once more.')
@section('actions')
    <button class="button primary" type="button" onclick="window.history.back()">Go back</button>
    <a class="button" href="{{ route('home') }}">Back to home</a>
@endsection

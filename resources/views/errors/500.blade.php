@extends('layouts.error')
@section('title', 'Something went wrong')
@section('eyebrow', 'Error 500 &middot; A temporary problem')
@section('code', '500')
@section('heading', 'We hit a snag on our side.')
@section('message', 'Something did not load as expected. Our team is looking into it; please give it another moment, then try again.')
@section('actions')
    <button class="button primary" type="button" onclick="window.location.reload()">Try again</button>
    <a class="button" href="{{ route('home') }}">Back to home</a>
@endsection

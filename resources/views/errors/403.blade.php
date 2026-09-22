@extends('layouts.error')
@section('title', 'Access restricted')
@section('eyebrow', 'Error 403 &middot; Access restricted')
@section('code', '403')
@section('heading', 'This corner is private.')
@section('message', 'Your account does not have access to this page. If that does not seem right, sign in with a different account or head back to the shop.')
@section('actions')
    <a class="button primary" href="{{ route('home') }}">Back to home</a>
    <button class="button" type="button" onclick="window.history.back()">Go back</button>
@endsection

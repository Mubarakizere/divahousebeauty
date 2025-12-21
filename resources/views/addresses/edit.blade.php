@extends('layouts.dashboard')
@section('title', isset($address) ? 'Edit Address' : 'Add Address')

@section('content')
<h4 class="fw-bold mb-4">{{ isset($address) ? '✏️ Edit Address' : '➕ Add Address' }}</h4>

<form action="{{ isset($address) ? route('address.update', $address) : route('address.store') }}" method="POST">
    @csrf
    @if(isset($address)) @method('PUT') @endif

    <div class="mb-3">
        <label>Address Line *</label>
        <input type="text" name="address_line" class="form-control" required value="{{ old('address_line', $address->address_line ?? '') }}">
    </div>

    <div class="mb-3">
        <label>City *</label>
        <input type="text" name="city" class="form-control" required value="{{ old('city', $address->city ?? '') }}">
    </div>

    <div class="mb-3">
        <label>District</label>
        <input type="text" name="district" class="form-control" value="{{ old('district', $address->district ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Phone *</label>
        <input type="text" name="phone" class="form-control" required value="{{ old('phone', $address->phone ?? '') }}">
    </div>

    <button class="btn btn-success">Save</button>
    <a href="{{ route('address.index') }}" class="btn btn-secondary">Back</a>
</form>
@endsection

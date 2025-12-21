@extends('layouts.public')

@section('content')
@if (!empty($data->image->guid))
    <section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
        <div class="container relative z-2">
            <div class="row g-4">
                <div class="col-lg-12 text-center">
                    <div class="spacer-double"></div>
                    <h1 class="mb-0">User Profile</h1>
                    <div class="spacer-double"></div>
                </div>
            </div>
        </div>
        <div class="sw-overlay op-8"></div>
        <div class="gradient-edge-bottom"></div>
    </section>
@endif

<section>
    <div class="container">

        <!-- Profile Update Form -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Update Profile</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('userprofileupdate') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                           id="username" name="username" value="{{ old('username', auth()->user()->username ? auth()->user()->username : '' ) }}">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="birthday" class="form-label">Birthday</label>
                                    <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                                           id="birthday" name="birthday" value="{{ old('birthday', auth()->user()->birthday ? auth()->user()->birthday->format('Y-m-d') : '') }}">
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Change Password</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                           id="current_password" name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                           id="new_password" name="new_password">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                             <div class="form-group mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control"
                                        id="new_password_confirmation" name="new_password_confirmation">
                            </div>
                            </div>

                        </div>

                        <div class="text-end">
                            <button type="submit">
                                <i class="fa fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        </div>

    </div>

@endsection
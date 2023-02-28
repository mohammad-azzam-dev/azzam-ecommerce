@extends('layouts.admin.app')

@section('title', isset($user) ? translate('Update User') : translate('Create User'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize">
                        <i class="tio-edit"></i>
                        {{ translate('user') }} {{ translate(isset($user) ? 'update' : 'create') }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ isset($user) ? route('admin.users.update', [$user->id]) : route('admin.users.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('first_name') }}</label>
                                <input type="text" name="f_name"
                                    value="{{ old('f_name', '') !== '' ? old('f_name') : (isset($user) ? $user->f_name : '') }}"
                                    class="form-control" placeholder="{{ translate('first_name') }}" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('last_name') }}</label>
                                <input type="text" name="l_name" value="{{ old('l_name', '') !== '' ? old('l_name') : (isset($user) ? $user->l_name : '') }}"
                                    class="form-control" placeholder="{{ translate('last_name') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('email') }}</label>
                                <input type="email" name="email" value="{{ old('email', '') !== '' ? old('email') : (isset($user) ? $user->email : '') }}"
                                    class="form-control" placeholder="{{ translate('email') }}" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('phone') }}</label>
                                <input type="text" name="phone" value="{{ old('phone', '') !== '' ? old('phone') : (isset($user) ? $user->phone : '') }}"
                                    class="form-control" placeholder="{{ translate('phone') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('password') }}</label>
                                <input type="password" name="password" value="" class="form-control"
                                    {{ isset($user) ? '' : 'required' }}>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('password_confirmation') }}</label>
                                <input type="password" name="password_confirmation" value="" class="form-control"
                                    {{ isset($user) ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('role') }}</label>
                                @php
                                    $roleName = old('role', '') !== '' ? old('role') : (isset($user) ? $user->roles()->first()->name ?? '' : '');
                                @endphp
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>

                                    @if (auth('admin')->user()->hasRole('super-admin'))
                                        <option value="super-admin" {{ $roleName == 'super-admin' ? 'selected' : '' }}>
                                            Super Admin</option>
                                    @endif

                                    <option value="admin" {{ $roleName == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ translate('image') }}</label><small style="color: red">(
                            {{ translate('ratio') }} 1:1 )</small>
                        <div class="custom-file">
                            <input type="file" name="image" id="user-image" class="custom-file-input"
                                value="{{ isset($user) && $user->image ? $user->image : '' }}"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="user-image">{{ translate('Choose File') }}</label>
                        </div>
                        <div class="text-center mt-2">
                            <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                src="{{ isset($user) && $user->image ? asset('storage/app/public/users/' . $user->image) : '' }}"
                                onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                alt="user image" />
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-primary">{{ isset($user) ? translate('update') : translate('create') }}</button>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#user-image").change(function() {
            readURL(this);
        });
    </script>
@endpush

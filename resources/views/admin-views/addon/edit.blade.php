@extends('layouts.admin.app')

@section('title', translate('Update Addon'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CentralLogics\translate('addon')}} {{\App\CentralLogics\translate('update')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.addon.update',[$addon['id']])}}" method="post">
                    @csrf

                    <div class="row">

                        {{-- Name --}}
                        <div class="col-6">
                            <div class="form-group lang_form" id="form">
                                <label class="input-label" for="exampleFormControlInput1">{{\App\CentralLogics\translate('name')}}</label>
                                <input type="text" name="name" value="{{ $addon['name'] }}" class="form-control" placeholder="Name">
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{\App\CentralLogics\translate('price')}}</label>
                                <input type="text" value="{{ $addon['price'] }}" name="price"
                                       class="form-control"
                                       placeholder="Price" required>
                            </div>
                        </div>


                    </div>

                    <button type="submit" class="btn btn-primary">{{\App\CentralLogics\translate('update')}}</button>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')

@endpush

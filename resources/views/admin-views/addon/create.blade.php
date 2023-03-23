@extends('layouts.admin.app')

@section('title', translate('Add new addon'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                            class="tio-add-circle-outlined"></i> {{\App\CentralLogics\translate('add')}} {{\App\CentralLogics\translate('new')}} {{\App\CentralLogics\translate('addon')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.addon.store')}}" method="post" id="addon_form"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <div class="col-6">
                            <div class="form-group" id="form">
                                <label class="input-label" for="exampleFormControlInput1">{{\App\CentralLogics\translate('name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="New Addon">
                            </div>
                        </div>

                        {{-- Phone Number--}}
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{\App\CentralLogics\translate('price')}}</label>
                                <input type="text" value="" name="price"
                                       class="form-control"
                                       placeholder="Price" required>
                            </div>
                        </div>

                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">{{\App\CentralLogics\translate('submit')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')

    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

@endpush



@extends('layouts.admin.app')

@section('title', translate('Update Delivery Company'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CentralLogics\translate('delivery_company')}} {{\App\CentralLogics\translate('update')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.delivery-company.update',[$deliveryCompany['id']])}}" method="post">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group lang_form" id="form">
                                <label class="input-label" for="exampleFormControlInput1">{{\App\CentralLogics\translate('name')}}</label>
                                <input type="text" name="name" value="{{ $deliveryCompany['name'] }}" class="form-control" placeholder="New Delivery Company">
                            </div>
                        </div>
                    </div>
                    <div id="from_part_2">
                        <div class="row">

                            {{-- Phone Number--}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{\App\CentralLogics\translate('phone_number')}}</label>
                                    <input type="text" value="{{ $deliveryCompany['phone_number'] }}" name="phone_number"
                                           class="form-control"
                                           placeholder="Phone Number" required>
                                </div>
                            </div>

                            {{-- Provinces --}}
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{\App\CentralLogics\translate('province')}}</label>
                                    <select name="provinces[]" class="form-control js-select2-custom" multiple>
                                        @foreach($provinces as $key => $province)
                                            <option value="{{ $province->id }}" {{ in_array( $province->id, $deliveryCompany->provinces->pluck('province_id')->toArray() ) ? 'selected' : '' }}>{{ $province->province }}</option>
                                        @endforeach
                                    </select>
                                </div>
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

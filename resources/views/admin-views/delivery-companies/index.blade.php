@extends('layouts.admin.app')

@section('title', translate('Delivery Company List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                            class="tio-filter-list"></i> {{\App\CentralLogics\translate('delivery_companies')}} {{\App\CentralLogics\translate('list')}}
                        <span
                            class="badge badge-soft-dark ml-2">{{$deliveryCompanies->total()}}</span></h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="ml-3">
                                <form action="{{url()->current()}}" method="GET">
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search"
                                               class="form-control"
                                               placeholder="{{translate('Search')}}" aria-label="Search"
                                               value="{{$search}}" required autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text"><i class="tio-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <a href="{{route('admin.delivery-company.create')}}" class="btn btn-primary float-right"><i
                                        class="tio-add-circle"></i> {{\App\CentralLogics\translate('add')}} {{\App\CentralLogics\translate('new')}} {{\App\CentralLogics\translate('delivery_company')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{\App\CentralLogics\translate('#')}}</th>
                                <th style="width: 30%">{{\App\CentralLogics\translate('name')}}</th>
                                <th style="width: 25%">{{\App\CentralLogics\translate('phone_number')}}</th>
                                <th>{{\App\CentralLogics\translate('provinces')}}</th>
{{--                                <th>{{\App\CentralLogics\translate('status')}}</th>--}}
                                <th>{{\App\CentralLogics\translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($deliveryCompanies as $key => $deliveryCompany)
                                <tr>
                                    <td>{{$deliveryCompanies->firstitem()+$key}}</td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$deliveryCompany['name']}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$deliveryCompany['phone_number']}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            @php
                                                $provinces = $deliveryCompany->countryProvinces->pluck('province')->toArray();
                                            @endphp
                                            {{implode(', ', $provinces)}}
                                        </span>
                                    </td>
{{--                                    <td>--}}
{{--                                        @if($deliveryCompany['status'] == 1)--}}
{{--                                            <div style="padding: 10px;border: 1px solid;cursor: pointer"--}}
{{--                                                 onclick="location.href='{{route('admin.product.status',[$deliveryCompany['id'],0])}}'">--}}
{{--                                                <span--}}
{{--                                                    class="legend-indicator bg-success"></span>{{\App\CentralLogics\translate('active')}}--}}
{{--                                            </div>--}}
{{--                                        @else--}}
{{--                                            <div style="padding: 10px;border: 1px solid;cursor: pointer"--}}
{{--                                                 onclick="location.href='{{route('admin.product.status',[$deliveryCompany['id'],1])}}'">--}}
{{--                                                <span--}}
{{--                                                    class="legend-indicator bg-danger"></span>{{\App\CentralLogics\translate('disabled')}}--}}
{{--                                            </div>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"
                                                   href="{{route('admin.delivery-company.edit',[$deliveryCompany['id']])}}">{{\App\CentralLogics\translate('edit')}}</a>
                                                <a class="dropdown-item" href="javascript:"
                                                   onclick="form_alert('delivery-company-{{$deliveryCompany['id']}}','{{\App\CentralLogics\translate('Want to delete this item ?')}}')">{{\App\CentralLogics\translate('delete')}}</a>
                                                <form action="{{route('admin.delivery-company.delete',[$deliveryCompany['id']])}}"
                                                      method="post" id="delivery-company-{{$deliveryCompany['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div class="page-area">
                            <table>
                                <tfoot class="border-top">
                                {!! $deliveryCompanies->links() !!}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush

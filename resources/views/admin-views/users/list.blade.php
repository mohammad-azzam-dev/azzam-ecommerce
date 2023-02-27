@extends('layouts.admin.app')

@section('title', translate('Users List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i> {{ \App\CentralLogics\translate('users') }}
                        {{ \App\CentralLogics\translate('list') }}
                        <span class="badge badge-soft-dark ml-2">{{ $users->total() }}</span>
                    </h1>
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
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ translate('Search') }}" aria-label="Search"
                                            value="{{ $search }}" required autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text"><i class="tio-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @if (auth('admin')->user()->hasRole('super-admin'))
                                <div class="col-md-6">
                                    <a href="{{ route('admin.users.add-new') }}" class="btn btn-primary float-right"><i
                                            class="tio-add-circle"></i> {{ \App\CentralLogics\translate('add') }}
                                        {{ \App\CentralLogics\translate('new') }}
                                        {{ \App\CentralLogics\translate('user') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ \App\CentralLogics\translate('#') }}</th>
                                    <th style="width: 25%">{{ \App\CentralLogics\translate('image') }}</th>
                                    <th>{{ \App\CentralLogics\translate('first_name') }}</th>
                                    <th>{{ \App\CentralLogics\translate('last_name') }}</th>
                                    <th>{{ \App\CentralLogics\translate('email') }}</th>
                                    <th>{{ \App\CentralLogics\translate('phone') }}</th>
                                    <th>{{ \App\CentralLogics\translate('role') }}</th>
                                    <th>{{ \App\CentralLogics\translate('action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @include('admin-views.users.partials._table', $users)
                            </tbody>
                        </table>
                        <hr>
                        <div class="page-area">
                            <table>
                                <tfoot class="border-top">
                                    {!! $users->links() !!}
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
        $('#search-form').on('submit', function() {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.users.search') }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function() {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush

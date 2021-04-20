@extends('layouts.master')

@section('title', 'Sales Representatives List')

@section('heading', 'Sales Representatives List')

@section('breadcrumbs', Breadcrumbs::render('sr.index'))

@section('contents')
    <form action="{{route('sr.index')}}" enctype="multipart/form-data" method="GET">
    @can('isAdmin')
    <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
        <div class="card card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Select Status</option>
                            @foreach(trans('sr.status') as $key => $status)
                                <option value="{{ $key }}" @if(array_get($input, 'status') == $key) selected @endif>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 30px">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        @if(!empty(array_filter($input)))
                            <a href="{{ route('sr.index') }}" class="btn btn-light">Remove Filters</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    <div class="card">
        <div class="card-header">
            @can('isAdmin')        
            <h4>All SR</h4>
            @endcan
            <div class="card-header-form">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ $input['search'] ?? '' }}">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>        
        <div class="table-wrapper">
            <table class="table-responsive card-list-table">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Status</th>
                    <th scope="col">Distributor</th>
                    <th scope="col">Distributor Contact</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($salesRepresentatives as $salesRepresentative)
                <tr>
                    <td data-title="Name">{{ $salesRepresentative->user->name }}</td>
                    <td data-title="Mobile">{{ $salesRepresentative->mobile }}</td>
                    <td data-title="Status">{{ $salesRepresentative->status ? trans('distributor.status.'.$salesRepresentative->status) : ''}}</td>
                    <td data-title="Distributor">{{ $salesRepresentative->distributor->name }}</td>
                    <td data-title="Contact">{{ $salesRepresentative->distributor->mobile }}</td>
                    <td data-title="Created">{{ $salesRepresentative->created_at->format('d M Y h:m a') }}</td>
                    <td data-title="Actions">
                        <a href="{{route('sr.edit', [$salesRepresentative->id])}}" class="btn btn-sm btn-info">Edit</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>            
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="dataTables_info" id="table-1_info" role="status" aria-live="polite">
                        Showing {{ $salesRepresentatives->firstItem() }} to {{ $salesRepresentatives->lastItem() }} of {{ $salesRepresentatives->total() }} entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="float-right">
                        {{ $salesRepresentatives->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection

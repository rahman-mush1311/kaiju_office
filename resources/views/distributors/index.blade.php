@extends('layouts.master')

@section('title', 'Distributor List')

@section('heading', 'Distributor List')

@section('breadcrumbs', Breadcrumbs::render('distributors.index'))

@section('contents')
    <form action="{{route('distributors.index')}}" enctype="multipart/form-data" method="GET">
    @can('isAdmin')
    <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
        <div class="card card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Select Status</option>
                            @foreach(trans('distributor.status') as $key => $status)
                                <option value="{{ $key }}" @if(array_get($input, 'status') == $key) selected @endif>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 30px">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        @if(!empty(array_filter($input)))
                            <a href="{{ route('distributors.index') }}" class="btn btn-light">Remove Filters</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h4>My Distributors</h4>
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
                    @can('isAdmin')
                    <th scope="col">Created At</th>                        
                    @endcan                            
                    <th scope="col">Actions</th>                        
                </tr>
                </thead>
                <tbody>
                @php $index = $distributors->firstItem(); @endphp
                @foreach($distributors as $distributor)
                <tr>
                    <td data-title="Name">{{str_replace('"', '', $distributor->name_en)}}</td>
                    <td data-title="Mobile">{{$distributor->mobile}}</td>
                    <td data-title="Status">{{$distributor->status ? trans('distributor.status.'.$distributor->status) : ''}}</td>
                    @can('isAdmin')
                    <td data-title="Created">{{$distributor->created_at}}</td>  
                    @endcan                                                  
                    <td data-title="Actions">
                        <a href="{{route('distributors.assign-product', [$distributor->id])}}" class="btn btn-sm btn-primary">Assign Product</a>
                        @can('isAdmin')
                        <a href="{{route('distributors.edit', [$distributor->id])}}" class="btn btn-sm btn-info">Edit</a>
                        <a href="{{route('distributors.export-products', [$distributor->id])}}" class="btn btn-sm btn-outline-primary"
                            title="Export Product"><i class="fa fa-file-download"></i> Product Export</a>
                        <a href="{{route('distributors.import-products', [$distributor->id])}}" class="btn btn-sm btn-outline-primary"
                            title="Import Product" target="_blank"><i class="fa fa-file-upload"></i> Product Import</a>
                        @endcan                            
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
                        Showing {{ $distributors->firstItem() }} to {{ $distributors->lastItem() }} of {{ $distributors->total() }} entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="float-right">
                        {{ $distributors->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection

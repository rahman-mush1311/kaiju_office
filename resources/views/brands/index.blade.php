@extends('layouts.master')

@section('title', 'Brand List')

@section('heading', 'Brand List')

@section('breadcrumbs', Breadcrumbs::render('brands.index'))

@section('contents')
    <form action="{{route('brands.index')}}" enctype="multipart/form-data" method="GET">
        <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Select Status</option>
                                @foreach(trans('brand.status') as $key => $status)
                                    <option value="{{ $key }}"
                                            @if(array_get($input, 'status') == $key) selected @endif>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="margin-top: 30px">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            @if(!empty(array_filter($input)))
                                <a href="{{ route('brands.index') }}" class="btn btn-light">Remove Filters</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>All Brands</h4>
                <div class="card-header-form">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search"
                               value="{{ $input['search'] ?? '' }}">
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
                        <th scope="col">ID</th>
                        <th scope="col">Image</th>
                        <th scope="col">Name</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($brands as $brand)

                        <tr>
                            <td scope="row"  data-title="#">{{$brand->id}}</td>
                            <td  data-title="Image">
                                <img src="{{$brand->image}}" alt="{{$brand->name_en}}" width="50" height="50">
                            </td>
                            <td  data-title="Name">{{$brand->name_en}}</td>
                            <td  data-title="Status">{{$brand->status == 2 ? 'Inactive' : 'Active'}}</td>
                            <td  data-title="Created">{{$brand->created_at}}</td>
                            <td  data-title="Actions">
                                <a href="{{route('brands.edit', [$brand->id])}}" class="btn btn-sm btn-info">Edit</a>
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
                            Showing {{ $brands->firstItem() }} to {{ $brands->lastItem() }} of {{ $brands->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="float-right">
                            {{ $brands->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

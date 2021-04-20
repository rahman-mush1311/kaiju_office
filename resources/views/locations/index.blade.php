@extends('layouts.master')

@section('title', 'Location List')

@section('heading', 'Location List')

@section('breadcrumbs', Breadcrumbs::render('location.index'))

@section('contents')
    <form action="{{route('location.index')}}" enctype="multipart/form-data" method="GET">
        <div class="card">
            <div class="card-header">
                <h4>All Locations</h4>
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
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Created At</th>
                        <!-- <th scope="col">Actions</th> -->
                    </tr>
                    </thead>
                    <tbody>
                    @php $index = $locations->firstItem(); @endphp
                    @foreach($locations as $location)
                        <tr>
                            <td scope="row"  data-title="#">{{ $index++ }}</td>
                            <td data-title="Name">{{ $location->name }}</td>
                            <td data-title="Created">{{ $location->created_at }}</td>
                            <!-- <td>
                                <a href="{{ route('location.edit', [$location->id]) }}" class="btn btn-sm btn-info">Edit</a>
                            </td> -->
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>                
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="dataTables_info" id="table-1_info" role="status" aria-live="polite">
                            Showing {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ $locations->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="float-right">
                            {{ $locations->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

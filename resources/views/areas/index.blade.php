@extends('layouts.master')

@section('title', 'Area List')

@section('heading', 'Area List')

@section('breadcrumbs', Breadcrumbs::render('area.index'))

@section('contents')
    <form action="{{route('area.index')}}" enctype="multipart/form-data" method="GET">
        <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="location">Location</label>
                            <select id="location" name="location_id" class="form-control">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}"
                                            @if(array_get($input, 'location_id') == $location->id) selected @endif>{{ str_replace('"', '', $location->name_en) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3" style="margin-top: 30px">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            @if(!empty(array_filter($input)))
                                <a href="{{ route('area.index') }}" class="btn btn-light">Remove Filters</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>All Areas</h4>
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
                        <th scope="col">Location</th>
                        <th scope="col">Latitude</th>
                        <th scope="col">Longitude</th>
                        <th scope="col">Created At</th>
                        <!-- <th scope="col">Actions</th> -->
                    </tr>
                    </thead>
                    <tbody>
                    @php $index = $areas->firstItem(); @endphp
                    @foreach($areas as $area)
                        <tr>
                            <td scope="row"  data-title="#">{{ $index++ }}</td>
                            <td  data-title="Name">{{ $area->name }}</td>
                            <td data-title="Location">{{ $area->location->name }}</td>
                            <td data-title="Latitude">{{ $area->lat }}</td>
                            <td data-title="Longitude">{{ $area->long }}</td>
                            <td data-title="Created">{{ $area->created_at }}</td>
                            <!-- <td>
                                <a href="{{ route('area.edit', [$area->id]) }}" class="btn btn-sm btn-info">Edit</a>
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
                            Showing {{ $areas->firstItem() }} to {{ $areas->lastItem() }} of {{ $areas->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="float-right">
                            {{ $areas->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

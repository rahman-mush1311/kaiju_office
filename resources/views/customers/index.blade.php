@extends('layouts.master')

@section('title', 'Retailer List')

@section('heading', 'Retailer List')

@section('breadcrumbs', Breadcrumbs::render('customers.index'))

@section('contents')
    <form action="{{route('customers.index')}}">
        @can('isAdmin')
        <div class="col-12 col-md-12 col-lg-12" style="padding: 0px">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Select Status</option>
                                @foreach(trans('customer.status') as $key => $status)
                                    <option value="{{ $key }}" @if(array_get($input, 'status') == $key) selected @endif>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        @can('isAdmin')
                        <div class="form-group col-md-3">
                            <label for="location_id">Location</label>
                            <select id="location_id" name="location_id" class="form-control select2" data-placeholder="Select Location">
                                @if(!blank($location))
                                    <option value="{{$location->id}}">{{$location->name}}</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="area_id">Area</label>
                            <select id="area_id" name="area_id" class="form-control select2" data-placeholder="Select Area">
                                @if(!blank($area))
                                    <option value="{{$area->id}}">{{$area->name}}</option>
                                @endif
                            </select>
                        </div>
                        @endcan

                        <div class="col-md-3" style="margin-top: 30px">
                            <button type="submit" class="btn btn-primary" style="margin-right: 5px">Apply Filters</button>
                            @if(!empty(array_filter($input)))
                                <a href="{{ route('customers.index') }}" class="btn btn-light">Remove Filters</a>
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
                <h4>All Retailers</h4>
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
                        @can('isAdmin')
                        <th scope="col">Created At</th>                            
                        <th scope="col">Actions</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td data-title="Name">{{$customer->name}}</td>
                            <td data-title="Mobile">{{$customer->mobile}}</td>
                            <td data-title="Status">{{ trans('customer.status.'.$customer->status) }}</td>
                            @can('isAdmin')
                            <td data-title="Created">{{$customer->created_at}}</td>                                
                            <td data-title="Actions">
                                <a href="{{route('customers.edit', [$customer->id])}}" class="btn btn-sm btn-info">Edit</a>
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>                
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="dataTables_info" id="table-1_info" role="status" aria-live="polite">
                            Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="float-right">
                            {{ $customers->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection


@push('stack_js')
    <script>
        $(document).ready(function(){
            @can('isAdmin')
            $('#location_id').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('locations.search.select2') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    }
                }
            });
            
            function areaSelect(location_id){
                var searchUrl = '/areas/search/select2/' + location_id;
                $('#area_id').select2({
                    ajax: {
                        delay: 250,
                        url: searchUrl,
                        dataType: 'json',
                        data: function (params) {
                            return {
                                search: params.term,
                            }
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            }
                        }
                    }
                });
            }

            areaSelect($('#location_id').val());
        
            $('#location_id').on("select2:selecting", function(e) { 
                var location_id = e.params.args.data.id;
                $('#area_id').empty();
                areaSelect(location_id);
            });
            @endcan 
        });
    </script>
@endpush

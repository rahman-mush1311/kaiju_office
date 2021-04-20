@extends('layouts.master')

@section('title', 'Delivery Charge Rule List')

@section('heading', 'Delivery Charge Rule List')

@section('breadcrumbs', Breadcrumbs::render('delivery.charge.rule.index'))

@section('contents')
    <form action="{{route('delivery.charge.rules.index')}}" enctype="multipart/form-data" method="GET">
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
                                <a href="{{ route('delivery.charge.rules.index') }}" class="btn btn-light">Remove Filters</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>All Delivery Charge Rules</h4>
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
                        <th scope="col">Min Basket Size</th>
                        <th scope="col">Max Basket Size</th>
                        <th scope="col">Delivery Charge</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $index = $rules->firstItem(); @endphp
                    @foreach($rules as $rule)

                        <tr>
                            <td scope="row"  data-title="#">{{$index++}}</td>
                            <td  data-title="Name">{{$rule->name}}</td>
                            <td  data-title="Min Basket Size">{{$rule->min_basket_size}}</td>
                            <td  data-title="Max Basket Size">{{$rule->max_basket_size}}</td>
                            <td  data-title="Delivery Charge">{{$rule->delivery_charge}}</td>
                            <td  data-title="Status">{{$rule->status == 2 ? 'Inactive' : 'Active'}}</td>
                            <td  data-title="Created">{{$rule->created_at}}</td>
                            <td  data-title="Actions">
                                <a href="{{route('delivery.charge.rules.edit', [$rule->id])}}" class="btn btn-sm btn-info">Edit</a>
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
                            Showing {{ $rules->firstItem() }} to {{ $rules->lastItem() }} of {{ $rules->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="float-right">
                            {{ $rules->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

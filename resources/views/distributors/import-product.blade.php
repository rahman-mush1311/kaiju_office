@extends('layouts.master')

@section('title', 'Import Distributor Product')

@section('heading', 'Import Distributor Product')

@section('breadcrumbs', Breadcrumbs::render('distributors.import-products'))

@section('contents')
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-12">
                        <form id="distributorProductForm" action="{{ route('distributors.import-products') }}" method="post"
                              enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-sm-6">
                                    @if(!empty($distributor))
                                        <input type="hidden" name="distributor_id" value="{{ $distributor->id }}">
                                        <label>Distributor</label>
                                        <input type="text" class="form-control" value="{{ str_replace('"', '', $distributor->name_en) }}" disabled>
                                    @else
                                        <label>Select Distributor</label>
                                        <select name="distributor_id" class="form-control select2" id="distributor-search" required></select>
                                    @endif
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Products</label>
                                    <input type="file" class="form-control" name="products" required>
                                </div>
                            </div>
                            @csrf
                            <button type="submit" class="btn btn-success">Import</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('stack_js')
    <script>
        $(document).ready(function () {
            $('#distributor-search').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('distributors.search.select2') }}',
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

            $('#distributor-search').on('select2:select', function(e){
                $('#selected-distributor-id').val(e.params.data.id);
            });
        });
    </script>
@endpush

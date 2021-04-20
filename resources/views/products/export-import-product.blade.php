@extends('layouts.master')

@section('title', 'Export / Import Distributor Product')

@section('heading', 'Export / Import Distributor Product')

@section('breadcrumbs', Breadcrumbs::render('distributors.import-products'))

@section('contents')
    <div class="card">
        <div class="card-body">
            <form id="distributorProductForm" action="{{ route('product.import.save') }}" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="export_import_options" id="radio-export" value="export" @if(old('export_import_options') != 'import') checked @endif>
                        <label class="form-check-label" for="radio-export">Export</label>
                    </div>
                    <div class="form-group form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="export_import_options" id="radio-import" value="import" @if(old('export_import_options') == 'import') checked @endif>
                        <label class="form-check-label" for="radio-import">Import</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" id="export-container">
                    <div class="col-md-6">
                        <a href="{{ route('product.export') }}" class="btn btn-success">Export</a>
                    </div>
                </div>

                <div class="col-sm-12 d-none" id="import-container">
                        <div class="form-row col-md-6">
                            <div class="form-group col-sm-6">
                                <label>Products</label>
                                <input type="file" class="form-control" name="products" required>
                            </div>
                        </div>
                        @csrf
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success">Import</button>
                        </div>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@push('stack_js')
    <script>
        $(document).ready(function () {
            function showForm(){
                let input = $('input[name="export_import_options"]:checked');
                if (input.val() == 'import') {
                    $('#import-container').removeClass('d-none');
                    $('#export-container').addClass('d-none');
                } else if(input.val() == 'export') {
                    $('#import-container').addClass('d-none');
                    $('#export-container').removeClass('d-none');
                }
            }
            $('input:radio[name="export_import_options"]').change(function(){
                showForm();
            });
            showForm();
        });
    </script>
@endpush

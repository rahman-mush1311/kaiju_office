@extends('layouts.master')

@section('title', 'Edit Distributor')

@section('heading', 'Edit Distributor')

@section('heading_buttons')
    <a href="{{route('distributors.index')}}" class="btn btn-primary">All Distributors</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('distributors.edit'))

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}">
@endpush


@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('sr.update', [$sr->id])}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ $sr->user_id }}">

                @include('sr.form')

                <button type="submit" class="btn btn-success">Save</button>
                @method('PUT')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            if(jQuery().timepicker && $(".timepicker").length) {
                $(".timepicker").timepicker({
                    icons: {
                        up: 'fas fa-chevron-up',
                        down: 'fas fa-chevron-down'
                    }
                });
            }
        });
    </script>
@endpush
@push('stack_js')
    <script>
        $(document).ready(function () {
            $('.select2multiple').each(function(){
                $(this).select2({
                    multiple: true,
                });
            });

            @if(!empty($selectedLocations))
                $('#location').val(@json($selectedLocations)).trigger('change');
            @endif

            @if(!empty($selectedAreas))
                $('#area').val(@json($selectedAreas)).trigger('change');
            @endif


            $('#area').select2({
                ajax: {
                    delay: 250,
                    url: '{{ route('area.search') }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            locations: $('#location').val()
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    }
                }
            });
        });
    </script>
@endpush

@extends('layouts.master')

@section('title', 'New Sales Representative')

@section('heading', 'New Sales Representative')

@section('heading_buttons')
    <a href="{{route('sr.index')}}" class="btn btn-primary">All Sales Representatives</a>
@endsection

@section('breadcrumbs', Breadcrumbs::render('distributors.create'))

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}">
@endpush


@section('contents')
    <div class="card">
        <div class="card-body">
            <form action="{{route('sr.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                @include('sr.form')

                <button type="submit" class="btn btn-success">Save</button>
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

<div class="form-row">
    <div class="form-group col-md-12">
        <label for="distributor_id">Distributor</label>
        <select id="distributor_id" name="distributor_id" class="form-control select2" data-placeholder="Select Distributor">
            @if(filled($sr ?? null))
                <option value="{{ $sr->distributor_id }}">{{ $sr->distributor->name }}</option>
            @endif
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>SR Name</label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $sr->user->name ?? '') }}">
    </div>

    <div class="form-group col-md-6">
        <label>Mobile</label>
        <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $sr->mobile ?? '') }}">
    </div>
</div>


<div class="form-row">

    <div class="form-group col-md-6">
        <label>Password</label>
        <input type="password" class="form-control" name="password">
    </div>

    <div class="form-group col-md-6">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control">
            @foreach(trans('distributor.status') as $value => $item)
                <option value="{{ $value }}" @if(old('status', $sr->status ?? '') == $value) selected @endif>{{ $item }}</option>
            @endforeach
        </select>
    </div>
</div>


@push('stack_js')
    <script>
        $(document).ready(function(){

            $('#distributor_id').select2({
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
        });
    </script>
@endpush

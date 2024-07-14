@extends('layouts.appOwner')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Orders</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order List</h3>
                    </div>
                    <div class="card-body">
                        <form id="filter-form" method="GET" action="{{ route('orders.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="customer">Customer</label>
                                        <input type="text" name="customer" id="customer" class="form-control" value="{{ request('customer') }}" placeholder="Search by customer">
                                    </div>
                                </div>
                                <div class="col-md-3 align-self-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>

                        <div id="order-table">
                            @include('owner.order_table', ['orders' => $orders])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#date').change(function() {
            var date = $(this).val();
            $.ajax({
                url: "{{ route('orders.index') }}",
                data: {date: date},
                success: function(data) {
                    $('#order-table').html(data);
                }
            });
        });
    });
</script>
@endsection
@endsection

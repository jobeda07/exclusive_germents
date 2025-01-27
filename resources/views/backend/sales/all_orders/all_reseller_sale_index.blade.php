@extends('admin.admin_master')
@section('admin')

<style type="text/css">
    table, tbody, tfoot, thead, tr, th, td{
        border: 1px solid #dee2e6 !important;
    }
    th{
        font-weight: bolder !important;
    }
</style>

<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Reseller Order List</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <!-- card-header end// -->
                <div class="card-body">
                    <form class="" action="" method="GET">
                    <div class="form-group row mb-3">
                        <div class="col-md-2">
                            <label class="col-form-label"><span>All Orders :</span></label>
                        </div>
                        <div class="col-md-2 mt-2">
                            <div class="custom_select">
                                <select class="form-select d-inline-block select-active select-nice mb-lg-0 mr-5 mw-200" name="delivery_status" id="delivery_status">
                                    <option value="" selected="">Delivery Status</option>
                                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>Pending</option>
                                    <option value="holding" @if ($delivery_status == 'holding') selected @endif>Holding</option>
                                    <option value="processing" @if ($delivery_status == 'processing') selected @endif>Processing</option>
                                    <option value="shipped" @if ($delivery_status =='shipped') selected @endif>Shipped</option>
                                    <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>Delivered</option>
                                    <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <div class="custom_select">
                               <select class=" select-active select-nice form-select d-inline-block mb-lg-0 mr-5 mw-200" name="payment_status" id="payment_status">
                                    <option value="" selected="">Payment Status</option>
                                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>Unpaid</option>
                                    <option value="paid" @if ($payment_status == 'paid') selected @endif>Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <div class="custom_select">
                                <input type="text"   id="reportrange" class="form-control"  name="date" placeholder="Filter by date" data-format="DD-MM-Y" value="Filter by date" data-separator=" - " autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </div>
                    <div class="row mb-3 pack_print">
                        <div class="col-sm-3 col-6">
                            <button type="button" class="btn btn-sm" id="courierSend">Courier Send</button>
                            <button type="button" class="btn   btn-sm" id="all_print" target="blank">Print</button>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-bordered table-hover" id="example" width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select_all_ids"></th>
                                    <th>Order Code</th>
                                    <th>Reseller name</th>
                                    <th>Collectable Amount</th>
                                    <th>Order Amount</th>
                                    <th>Profit</th>
                                    <th>Shipping</th>
                                    <th>Delivery Status</th>
                                    <th>Payment Status</th>
                                    <th class="text-end">Options</th>
                                </tr>
                            </thead>
                            @php
                                $sum = 0;
                                $sum1 = 0;
                                $sum2 = 0;
                            @endphp
                            <tbody>
                                @foreach ($orders as $key => $order)
                                    <tr id="order_ids{{ $order->id }}">
                                        @if (in_array($order->delivery_status, ['cancelled', 'shipped', 'delivered']))
                                            <td><input type="checkbox" disabled></td>
                                        @else
                                            <td><input type="checkbox" class="check_ids" name="ids"
                                                    value="{{ $order->id }}"></td>
                                        @endif
                                        <td>{{ $order->invoice_no }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->collectable_amount }}</td>
                                        <td>{{ $order->grand_total }}</td>
                                        <td>{{ $order->collectable_amount - $order->grand_total }}</td>
                                        <td>{{ $order->shipping_charge }}</td>
                                         <td class="text-center">
                                                @if($order->delivery_status == 'cancelled')
                                                    <span class="badge rounded-pill alert-danger">Cancelled</span>
                                                @elseif($order->delivery_status == 'delivered')
                                                    <span class="badge rounded-pill alert-success">Delivered</span>
                                                @elseif($order->delivery_status == 'shipped')
                                                    <span class="badge rounded-pill alert-dark">Shipped</span>
                                                @elseif($order->delivery_status == 'pending')
                                                    <span class="badge rounded-pill alert-warning">Pending</span>
                                                @elseif($order->delivery_status == 'processing')
                                                    <span class="badge rounded-pill alert-primary">Processing</span>
                                                @elseif($order->delivery_status == 'holding')
                                                    <span class="badge rounded-pill alert-primary">Holding</span>
                                                @else
                                                    <span class="badge rounded-pill alert-info">{{$order->delivery_status}}</span>
                                                @endif
                                            </td>

                                        <td>
                                            @php
                                                $status = $order->payment_status;
                                                if ($order->payment_status == 'unpaid') {
                                                    $status =
                                                        '<span class="badge rounded-pill alert-danger">Unpaid</span>';
                                                } elseif ($order->payment_status == 'paid') {
                                                    $status =
                                                        '<span class="badge rounded-pill alert-success">Paid</span>';
                                                }
                                            @endphp
                                            {!! $status !!}
                                        </td>

                                        <td class="text-end">
                                            <a class="btn btn-primary btn-icon btn-circle btn-sm btn-xs"
                                                href="{{ route('all_orders.reseller_show', $order->id) }}">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a class="btn btn-primary btn-icon btn-circle btn-sm btn-xs"
                                                target="blank"
                                                href="{{ route('reseller.print.invoice.download', $order->id) }}">
                                                <i class="icon material-icons md-print"></i>
                                            </a>
                                            <a  class="btn btn-danger btn-icon btn-circle btn-sm btn-xs" href="{{route('delete.orders',$order->id) }}" id="delete" style="background-color: #DD1D21;">
    			                                <i class="fa-solid fa-trash"></i>
    			                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $sum += $order->collectable_amount;
                                        $sum1 += $order->grand_total;
                                        $sum2 += $order->collectable_amount - $order->grand_total;

                                    @endphp
                                @endforeach
                            </tbody>
                            
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-center"></td>
                                    <td>Sum : {{ $sum }}</td>
                                    <td>Sum : {{ $sum1 }}</td>
                                    <td>Sum : {{ $sum2 }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                        {{-- <div class="pagination-area mt-25 mb-50">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-end">
                                    {{ $orders->links() }}
                                </ul>
                            </nav>
                        </div> --}}
                    </div>
                    </form>
                    <!-- table-responsive //end -->
                </div>
                <!-- card-body end// -->
            </div>
            <!-- card end// -->
        </div>
    </div>
</section>

@push('footer-script')
<script type="text/javascript">
    $(function() {
        var start = moment();
        var end = moment();

        $('input[name="date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        function cb(start, end) {
            $('#reportrange').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    });
</script>
<script>
    $(function() {
        // Function to update "Select All" checkbox based on individual checkboxes
        function updateSelectAll() {
            var allChecked = $('.check_ids:checked').length === $('.check_ids').length;
            $('#select_all_ids').prop('checked', allChecked);
        }

        // Click event for individual checkboxes
        $('.check_ids').change(function() {
            updateSelectAll();
        });

        // Click event for "Select All" checkbox
        $('#select_all_ids').change(function() {
            $('.check_ids').prop('checked', $(this).prop('checked'));
        });
    });
</script>
<script>
    $(function(e) {
        $("#all_package").click(function(e) {
            e.preventDefault();
            var all_ids = [];
            $('input:checkbox[name=ids]:checked').each(function() {
                all_ids.push($(this).val());
            });
            $.ajax({
                url: "{{ route('order.product.packaged') }}",
                type: "GET",
                data: {
                    ids: all_ids,
                    _token: "{{csrf_token()}}"
                },
                success: function(response) {
                    if (response.status == 'success') {
                        toastr.success(response.message, 'message');
                        $.each(all_ids, function(key, val) {
                            $('#order_ids' + val).remove();
                        });
                        window.location.reload(true);
                        }
                    else {
                        toastr.error(response.error, 'Error');
                    }
                }
            });
        });
    });
</script>
<script>
    $(function(e) {
        $("#all_print").click(function(e) {
            e.preventDefault();
            var all_ids = [];
            $('input:checkbox[name=ids]:checked').each(function() {
                all_ids.push($(this).val());
            });
            $.ajax({
                url: "{{ route('order.product.Print') }}",
                type: "GET",
                data: {
                    ids: all_ids,
                    _token: "{{csrf_token()}}"
                },
                success: function(response) {
                    window.location.href = response.redirect_url;
                    $.each(all_ids, function(key, val) {
                            $('#order_ids' + val).remove();
                        });
                }
            });
        });
    });
</script>

<script>
    $(function(e) {
        $("#courierSend").click(function(e) {
            e.preventDefault();
            var all_ids = [];
            $('input:checkbox[name=ids]:checked').each(function() {
                all_ids.push($(this).val());
            });
            $.ajax({
                url: "{{ route('order.product.courierSend') }}",
                type: "GET",
                data: {
                    ids: all_ids,
                    _token: "{{csrf_token()}}"
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success') {
                        toastr.success(response.message, 'message');
                        $.each(all_ids, function(key, val) {
                            $('#order_ids' + val).remove();
                        });
                        //window.location.reload(true);
                         setTimeout(function() {
                                window.location.reload(true);
                            }, 1000);
                        }
                    else {
                        toastr.error(response.error, 'Error');
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
@extends('admin.barbar.layout')



@section('mainContent')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-6 ">
                                    <h4>Invoice No.</h4>
                                    <h4 class="text-navy">HWC-000{{ $order->id }}-00</h4>
                                    <p>
                                        <span><strong>Invoice Date:</strong>
                                            {{ $order->created_at->format('d-m-Y') }}</span><br />
                                    </p>
                                    <span>To:</span>
                                    @if ($order->contact == 0)
                                        <address>
                                            <strong>{{ $products[0]->customer->name }}</strong><br>
                                            {{ $products[0]->customer ? $products[0]->customer->email : '' }}<br>
                                            <i class="fa fa-phone"></i>
                                            {{ $products[0]->customer ? $products[0]->customer->contact : '' }}
                                        </address>
                                    @else
                                        <address>
                                            {{-- <strong>{{ $products->customer ? $products->customer->name : '' }}</strong><br> --}}
                                            {{ $order->address }}<br>
                                            <i class="fa fa-phone"></i> {{ $order->contact }}
                                        </address>
                                    @endif



                                </div>
                            </div>

                            <div class="table-responsive m-t">
                                <table class="table invoice-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $net = 0; @endphp

                                        @foreach ($products as $key => $pro)
                                            <tr>
                                                <td>
                                                    <div><strong>{{ $pro->product->product_name }}</strong></div>
                                                </td>
                                                <td>{{ $pro->quatity }}</td>
                                                <td><i class="fa fa-gbp"></i> {{ $pro->price }}</td>

                                                @php
                                                    $total = $pro->quatity * $pro->price;
                                                    $net += $total;
                                                @endphp
                                                <td><i class="fa fa-gbp"></i> {{ $total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><!-- /table-responsive -->

                            <table class="table invoice-total">
                                <tbody>
                                    <tr>
                                        <td><strong>Net Total :</strong></td>
                                        <td><i class="fa fa-gbp"></i> {{ $net }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="{{ route('confirm', [$data->inv_id, $data->order_id]) }}"
                                class="btn btn-info">Confirm</a>

                            <!-- </div> -->


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

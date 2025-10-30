@extends('admin.barbar.layout')



@section('mainContent')


    <div class="row">
        <div class="col-lg-12">
            <br>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Stock List </h5>
                    <div class="ibox-tools">

                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#">Config option 1</a>
                            </li>
                            <li><a href="#">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table
                            class="table table-striped table-bordered table-hover dataTables-example toggle-arrow-tiny footable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Stock</th>
                                    <th>Sold Stock</th>
                                    <th>Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barberstocks as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->product->product_name }}</td>

                                        @php $cur = 0; @endphp

                                        @if ($item->product->barberproduct->isNotEmpty())
                                            @php $cur = $item->product->barberproduct[0]->stock @endphp
                                        @endif

                                        <td>{{ $cur }}</td>


                                        @if ($item->product->soldproduct->isNotEmpty())
                                            @php $sold = $item->product->soldproduct[0]->soldstock @endphp
                                        @else
                                            @php $sold = 0 @endphp
                                        @endif
                                        <td>{{ $sold }}</td>
                                        <td>{{ $cur - $sold }}</td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script_code')

@endsection

@extends('layouts.adminapp')




@section('Main-content')

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
                                    <h4>Product No.</h4>
                                    <h4 class="text-navy">{{ $show->id }}</h4>
                                    <p>
                                        <span><strong>Created Date:</strong>
                                            {{ $show->created_at->format('d-m-Y') }}</span><br />
                                    </p>
                                    <span>Creater:</span>
                           
                                        <address>
                                            <strong>{{ $show->job_creater }}</strong><br>
                                          <span>Brand:</span>
                                            {{ $show->brand }}<br>
                                            <span>Status:</span>
                                            {{ $show->status }}<br>
                                        </address>
                                



                                </div>
                                <div class="col-sm-6">
                                    <img src="{{asset($show->image)}}" alt="" style="width:150px; height:140px;">  
                                </div>
                            </div>

                            <div class="table-responsive m-t">
                                <table class="table invoice-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Discounted Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                  

                                      
                                            <tr>
                                                <td>
                                                    <div><strong>{{ $show->product_name }}</strong></div>
                                                </td>
                                                <td>{{ $show->category }}</td>
                                                <td><i class="fa fa-gbp"></i> {{ $show->price }}</td>
                                                <td><i class="fa fa-gbp"></i> {{ $show->discountprice }}</td>
                                            </tr>
                                    </tbody>
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align: left;">Short Description</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="text-align: left;">{{$show->short_description}}</td>
                                                </tr>
                                            </tbody>
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align: left;">Specifications</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="text-align: left;">{{$show->specification}}</td>
                                                </tr>
                                            </tbody>
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align: left;">Description</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="text-align: left;">{{$show->detail_description}}</td>
                                                </tr>
                                            </tbody>

                                 
                                    </tbody>
                                </table>
                            </div><!-- /table-responsive -->

                            <table class="table invoice-total">
                                <tbody>
                                    <tr>
                                        <a href="{{route('marketplaceproductadmin')}}" class="btn btn-primary my-2 mx-2">Back</a>
                                    </tr>
                                </tbody>
                            </table>
                            {{-- <a href="{{ route('adminproductorder') }}" class="btn btn-info">Back</a> --}}
                              
                            <!-- </div> -->


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

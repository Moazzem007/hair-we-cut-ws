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
                                    <h4>Salon No.</h4>
                                    <h4 class="text-navy">{{ $show->id }}</h4>
                                    <p>
                                        <span><strong>Created Date:</strong>
                                            {{ $show->created_at->format('d-m-Y') }}</span><br />
                                    </p>
                                    <span>Creater:</span>
                           
                                        <address>
                                            <strong>{{ $show->job_creater }}</strong><br>
                                         
                                        </address>
                                        <span>Status:</span>
                                        <address>
                                            <strong>{{ $show->status }}</strong>
                                         
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
                                            <th>Salon Name</th>
                                            <th>Adress</th>
                                            <th>Barber available</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                  

                                      
                                            <tr>
                                                <td>
                                                    <div><strong>{{ $show->salon_name }}</strong></div>
                                                </td>
                                                <td>{{ $show->address }}</td>
                                                <td> {{ $show->barber_available }}</td>
                                                <td>{{ $show->price }}</td>
                                            </tr></tbody>

                                
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align: left;">Description</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="text-align: left;">{{$show->description}}</td>
                                                </tr>
                                            </tbody>
                                        

                                 
                                    </tbody>
                                </table>
                            </div><!-- /table-responsive -->

                            <table class="table invoice-total">
                                <tbody>
                                    <tr>
                                        <a href="{{route('marketplacesalonadmin')}}" class="btn btn-primary my-2 mx-2">Back</a>
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

@extends('admin.barbar.layout')



@section('mainContent')
    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Market Sell Salon</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Market place</a>
                </li>
                <li class="active">
                    <strong>Market All Sell Salon</strong>
                </li>
            </ol>
        </div>
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
        <!-- Button trigger modal -->

        <a href="{{ route('marketsalonsell') }}" type="button" class="btn btn-primary">
            My Sell Salon
        </a>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
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
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Creater</th>
                                    <th>Salon/ Business Name</th>
                                    <th>Location</th>
                                    <th>Barbar Available</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>


                                </thead>
                                <tbody>
                                    @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <img src="{{ asset($view->image) }}" alt=""
                                                    style="width:80px; height:80px;">
                                            </td>
                                            <td>{{ $view->job_creater }}</td>
                                            <td>{{ $view->salon_name }}</td>
                                            <td>{{ $view->address }}</td>
                                            <td>{{ $view->barber_available }}</td>
                                            <td>{{ $view->price }}</td>
                                            <td>{{ $view->description }}</td>
                                            <td>{{ $view->status }}</td>
                                            <td><div class="btn-group">
        
                                                <a href="{{route('marketsalon_view',$view->id)}}"
                                                    class="btn btn-xs btn-outline btn-success"><i
                                                        class="fa fa-print"></i></a>
    
    
                                            </div></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>


    </div>





    {{-- Main Body End --}}
@endsection


@section('script')
    <script>
        @if (Session::has('status'))
            toastr.success("{{ Session::get('status') }}")
        @endif
    </script>
@endsection

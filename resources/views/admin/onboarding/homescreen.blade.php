@extends('layouts.adminapp')




@section('Main-content')

  {{-- Page Header --}}
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>On Boarding Screens</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('adminDashboard') }}">Home</a>
            </li>
            <li>
                <a> On Boarding</a>
            </li>
            <li class="active">
                <strong>Starting Screen</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

{{-- Page Header  End --}}


{{-- Main Body --}}

<div class="wrapper wrapper-content animated fadeInRight">
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
                    <a href="{{route('boarding_screen')}}" class="btn btn-primary my-2 mx-2">Starting Screen</a>
               <a href="{{route('boarding_home_screen')}}" class="btn btn-primary my-2 mx-2">Home Screen</a>
              

             


                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($images as $key => $item)
                                <tr>
                                    <th>{{ $key + 1 }}</th>
                                    <td><img src="{{ $item->image }}" alt="" style="width:100px; height:100px;"></td>
                                    <td>
                                        <div class="btn-group">

                                            <a href="{{ route('deleteonboardingstart', $item->id) }}"
                                                class="btn btn-xs btn-outline btn-success"><i class="fa fa-trash"></i></a>


                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                    
                    <a href="" class="btn btn-primary" data-toggle="modal" data-target="#modal-form" style="float: right; margin-right:5px;">Add Image</a>
                
                </div>

                </div>
            </div>
        </div>
    </div>


</div>





{{-- Main Body End --}}

    
    {{-- Modal Form --}}
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-10 col-md-offset-1">
                            <h3 class="m-t-none m-b">Add Service</h3>
                            <form action="{{ route('storeonboardingimage')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Starting Screen Image</label>
                                               <input type="file" name="image" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <button class="btn btn-sm btn-primary btn-block" type="submit"><strong>Save</strong></button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Form --}}




@endsection
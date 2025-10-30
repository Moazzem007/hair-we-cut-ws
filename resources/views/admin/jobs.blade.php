@extends('layouts.adminapp')




@section('Main-content')

  {{-- Page Header --}}
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Barber Job List</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('adminDashboard') }}">Home</a>
            </li>
            <li>
                <a> Jobs</a>
            </li>
            <li class="active">
                <strong> Job List</strong>
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


                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Creater</th>
                                    <th>Title</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Experience</th>
                                    <th>Salary</th>
                                    <th>City</th>
                                    <th>Gender</th>
                                    <th>Employee Type</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>



                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($show as $key => $item)
                                <tr>
                                    <th>{{ $key + 1 }}</th>
                                    <td>{{ $item->job_creater }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->companyname }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->experience }}</td>
                                    <td>{{ $item->salary }}</td>
                                    <td>{{ $item->city }}</td>
                                    <td>{{ $item->gender }}</td>
                                    <td>{{ $item->employee_type }}</td>
                                    <td>{{ $item->role }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>
                                        <div class="btn-group">

                                            <a href="{{route('jobview',$item->id)}}"
                                                class="btn btn-xs btn-outline btn-success"><i
                                                    class="fa fa-print"></i></a>
                                                     <a href="{{route('Admin.Job.Delete',$item->id)}}"
                                                        class="btn btn-xs btn-outline btn-danger py-2"><i class="fa fa-trash"></i>
                                                    </a>


                                        </div>
                                    </td>


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

@section('script_code')
<script>

@if(Session::has('status'))
toastr.success("{{ Session::get('status') }}")
@endif

</script>
@endsection
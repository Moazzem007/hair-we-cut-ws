@extends('admin.barbar.layout')



@section('mainContent')






{{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Jobs List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Job Provider</a>
                </li>
                <li class="active">
                    <strong>List all Jobs</strong>
                </li>
            </ol>
        </div>
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
    <!-- Button trigger modal -->
    <div class="row">
<a href="{{route('infojobapply')}}" type="button" class="btn btn-primary float-right">
  Back
</a>
<a href="{{route('getappliedjobs')}}" class="btn btn-primary float-right">Apply Job History</a>
</div>
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
                                    <th>Title</th>
                                    <th>Position</th>
                                    <th>Salary</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                   

                                </thead>
                                <tbody>
                                    @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $view->job_info->title }}</td>
                                            <td>{{ $view->job_info->role }}</td>
                                            <td>{{ $view->job_info->salary }}</td>

                                            <td>{{ $view->created_at }}</td>
                                          
                                            <td>
                                                <div class="btn-group">

                                                    <a href="{{ route('job_view_barber', $view->id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-print"></i></a>


                                                </div>
                                                <div class="btn-group">

                                                    <!-- <a href="{{ route('orderInvoiceViewToBarber', $view->id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i class="fa fa-trash"></i></a> -->


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



@endsection

@section('script')

<script>
  @if(Session::has('status'))
    toastr.success("{{ Session::get('status') }}")
@endif
</script>


@endsection
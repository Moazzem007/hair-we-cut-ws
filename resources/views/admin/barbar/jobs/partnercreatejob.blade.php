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
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Add/Create Job
</button>
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
                                    <th>Job creater</th>
                                    <th>Title</th>
                                    <th>Company/Shop</th>
                                    <th>Email</th>
                                    <th>Contect_no</th>
                                    <th>Experience</th>
                                    <th>Salary</th>
                                    <th>Gate_no</th>
                                    <th>City</th>
                                    <th>Gender</th>
                                    <th>Employee_type</th>
                                    <th>Role</th>
                                    <th>Vacancies</th>
                                    <th>Description</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                    @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $view->job_creater }}</td>
                                            <td>{{ $view->title }}</td>
                                            <td>{{ $view->companyname }}</td>
                                            <td>{{ $view->email }}</td>
                                            <td>{{ $view->contactno }}</td>
                                            <td>{{ $view->experience }}</td>
                                            <td>{{ $view->salary }}</td>
                                            <td>{{ $view->gate_no }}</td>
                                            <td>{{ $view->city }}</td>
                                            <td>{{ $view->gender }}</td>
                                            <td>{{ $view->employee_type }}</td>
                                            <td>{{ $view->role }}</td>
                                            <td>{{ $view->vacancies}}</td>

                                            <td>{{ $view->job_description }}</td>
                                            <td>
                                                <div class="btn-group">

                                                    <a href="{{ route('job_view_barber', $view->id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-print"></i></a>


                                                </div>
                                                <div class="btn-group">

                                                    <a href="{{ route('orderInvoiceViewToBarber', $view->id) }}"
                                                        class="btn btn-xs btn-outline btn-success"><i class="fa fa-trash"></i></a>


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




         <!-- Add  Modal -->
         <div class="modal fade"id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add/Create Job</h1>
       
        </div>
         <div class="modal-body">
        <form action="{{route('storepartnerjob')}}" method="POST">
            @csrf
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="title" class="form-label">Title</label>
                      <input class="form-control" type="text"
                          name="title" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="companyname" class="form-label">Company/shop Name</label>
                      <input class="form-control" type="text" name="companyname" />
                  </div>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="email" class="form-label">Email Address</label>
                      <input class="form-control" type="text"
                          name="email" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="contact" class="form-label">Contact NO</label>
                      <input class="form-control" type="text" name="contact" />
                  </div>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="experience" class="form-label">Experience</label>
                      <input class="form-control" type="text"
                          name="experience" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="salary" class="form-label">Salary</label>
                      <input class="form-control" type="text" name="salary" />
                  </div>
              </div><div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="gateno" class="form-label">Gate_no</label>
                      <input class="form-control" type="text"
                          name="gateno" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="city" class="form-label">City</label>
                      <input class="form-control" type="text" name="city" />
                  </div>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="gender" class="form-label">Gender</label>
                      <select name="gender" class="form-control" autofocus>
                       <option value="male">Male</option>
                       <option value="female">Female</option>
                       </select>
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="employee" class="form-label">Employee_type</label>
                      <select name="employee" class="form-control" autofocus>
                       <option value="full time">Full time</option>
                       <option value="part time">Part time</option>
                       </select>
                  </div>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="role" class="form-label">Job Role</label>
                      <select name="role" class="form-control" autofocus>
                       <option value="Barber">Barber</option>
                       <option value="Hairdresser">Hairdresser</option>
                       <option value="Makeup Artist">Makeup Artist</option>

                       </select>
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="vacancies" class="form-label">Vacancies</label>
                      <input class="form-control" type="number" name="vacancies" />
                  </div>
              </div>
              <div class="row">
                 
                  <div class="mb-3 col-md-12">
                      <label for="description" class="form-label">Description</label>
                      <textarea name="description" class="form-control" autofocus cols="60" rows="7"></textarea>
                  </div>
              </div>
              <div class="mt-2">
                  <button type="submit" class="btn btn-primary me-2">Save changes</button>
                  <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                  <button type="reset" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>
              </div>
          </form>
        </div>
       <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                            </div>
                             </div>
                            </div>


    {{-- Main Body End --}}
@endsection

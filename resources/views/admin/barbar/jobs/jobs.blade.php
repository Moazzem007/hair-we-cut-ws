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
<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#exampleModal">
  Personal Info
</button>
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
                                    <th>Contect_no</th>
                                    <th>Experience</th>
                                    <th>Salary</th>
                                    <th>Gate_no</th>
                                    <th>City</th>
                                    <th>Gender</th>
                                    <th>Employee_type</th>
                                    <th>Description</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                    @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $view->title }}</td>
                                            <td>{{ $view->contactno }}</td>
                                            <td>{{ $view->experience }}</td>
                                            <td>{{ $view->salary }}</td>
                                            <td>{{ $view->gate_no }}</td>
                                            <td>{{ $view->city }}</td>
                                            <td>{{ $view->gender }}</td>
                                            <td>{{ $view->employee_type }}</td>
                                            <td>{{ $view->job_description }}</td>
                                            <td>
                                              @if($view->applied)
                                                <div class="btn-group">

                                                    <a href=""
                                                        class="btn btn-xs btn-outline btn-success" type="button" data-toggle="modal" data-target="#appliedmodel" onclick="getdata({{$view->id}})"><i
                                                            class="fa fa-print"></i></a>


                                                </div>
                                                @else
                                                <div class="btn-group">

                                           <a href=""
                                              class="btn btn-xs btn-outline btn-success" type="button" data-toggle="modal" data-target="#detailmodal" onclick="getdata({{$view->id}})"><i
                                            class="fa fa-print"></i></a>


                                             </div>
                                                @endif
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
        <div class="modal-header bg-primary">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Personal Information</h1>
       
        </div>
         <div class="modal-body">
    
  <div class="container h-auto">
    <div class="row d-flex justify-content-center align-items-center h-auto">
      <div class="col col-lg-6 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-md-4 gradient-custom text-left"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img src="{{asset('../admin/img/user-picture.png')}}"
                alt="Avatar" class="img-fluid my-5" style="width: 80px;" />
              
                <h2>Personal Information</h2>
                <hr class="mt-0">
                <div class="row pt-1">
                  <div class="col-12">
                    <h4 style="color:black; font-weight:bold;">Name</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->name}}</p>
                  </div>
                  <div class="col-12">
                    <h4 style="color:black; font-weight:bold;">Email</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->email}}</p>
                  </div>
                  <div class="col-12 mb-3">
                    <h4 style="color:black; font-weight:bold;">Contect</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->contect}}</p>
                  </div> <div class="col-12">
                    <h4 style="color:black; font-weight:bold;">Address</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->gateno}}</p>
                  </div> 
                  <div class="col-12">
                    <h4 style="color:black; font-weight:bold;">City</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->city}}</p>
                  </div>
                  <div class="col-12">
                    <h4 style="color:black; font-weight:bold;">Postal Code</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->postal_code}}</p>
                  </div>
                </div>
            </div>
            <div class="col-md-8">
             
                <h2>Experience</h2>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Experience as Barber</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->experiencebarber}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Previeus Salon Name</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->previewsalonname}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Previeus Salon Address</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->presalonaddress}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">From Date</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->fromdate}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">To Date</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->todate}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Position Role</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->position_role}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Reason For Leaving</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->reasonforleaving}}</p>
                  </div>
                </div>

                <h2>Education</h2>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Barber Licence No</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->barber_licence_no}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Institute Name</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->institute_name}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Institute Address</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->institute_address}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Certificate/Training Name</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->certificate_training}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Skill</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->skill}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Available</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->available}}</p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Status</h4>
                    <p class="text-muted" style="font-size:16px;">{{$check->status}}</p>
                  </div>
                </div>
              
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 <div class="modal-footer">
 <button type="button" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>

    <a href="{{route('editpersonalinfo')}}" type="button" class="btn btn-primary">Edit Info</a>
                        </div>
                  
        </div>
       <!-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
                        </div> -->
                            </div>
                             </div>
                            </div>

        

        <!-- get job details  Modal -->
        <div class="modal fade"id="detailmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-primary">
        <!-- <h1 class="modal-title fs-5 text-center" id="exampleModalLabel">Job Details</h1> -->
       
        </div>
         <div class="modal-body">
    
  <div class="container h-auto">
    <div class="row d-flex justify-content-center align-items-center h-auto">
      <div class="col col-lg-6 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-12 gradient-custom text-center"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img src="{{asset('../admin/img/user-picture.png')}}"
                alt="Avatar" class="img-fluid my-5" style="height:30vh;" />
              
                <!-- <h2>Job Details</h2> -->
                <hr class="mt-0">
                
            </div>
            <div class="col-12">
             
                <h2>Job Details</h2>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Title</h4>
                    <p class="text-muted title" style="font-size:16px;" ></p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Company /Shop Name</h4>
                    <p class="text-muted companyname" style="font-size:16px;"></p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Email Address</h4>
                    <p class="text-muted email" style="font-size:16px;"></p>
                  </div>
                  <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Contact No</h4>
                    <p class="text-muted contactno" style="font-size:16px;"></p>
                    
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Experience Require</h4>
                    <p class="text-muted experience" style="font-size:16px;"></p>
                  </div>
                  </div>
                  <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Gate_No</h4>
                    <p class="text-muted gateno" style="font-size:16px;"></p>
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Salary</h4>
                    <p class="text-muted salary" style="font-size:16px;"></p>
                  </div>
                
                    </div>

                  <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">City</h4>
                    <p class="text-muted city" style="font-size:16px;"></p>
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Gender</h4>
                    <p class="text-muted gender" style="font-size:16px;"></p>
                  </div>
                 </div>
                 <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Employee Type</h4>
                    <p class="text-muted employeetype" style="font-size:16px;"></p>
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Job Role</h4>
                    <p class="text-muted role" style="font-size:16px;"></p>
                  </div>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Vacancies</h4>
                    <p class="text-muted vacancies" style="font-size:16px;"></p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Description</h4>
                    <p class="text-muted description" style="font-size:16px;"></p>
                  </div>
                 
                </div>
              
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 <div class="modal-footer">
  <form action="{{route('jobapplynow')}}" method="post">
    @csrf
    <input type="hidden" name="jobcreater" id="jobcreater">
    <input type="hidden" name="jobid" id="jobid">
    <button type="submit" class="btn btn-primary">Easy Apply</button>
    <button type="button" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>
  </form>

                        </div>
                  
        </div>
       <!-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
                        </div> -->
                            </div>
                             </div>
                            </div>





    

    <!-- get job details  Modal -->
    <div class="modal fade"id="appliedmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-primary">
        <!-- <h1 class="modal-title fs-5 text-center" id="exampleModalLabel">Job Details</h1> -->
       
        </div>
         <div class="modal-body">
    
  <div class="container h-auto">
    <div class="row d-flex justify-content-center align-items-center h-auto">
      <div class="col col-lg-6 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-12 gradient-custom text-center"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img src="{{asset('logo.png')}}"
                alt="Avatar" class="img-fluid my-5" style="height:30vh;" />
              
                <!-- <h2>Job Details</h2> -->
                <hr class="mt-0">
                
            </div>
            <div class="col-12">
             
                <h2>Job Details</h2>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Title</h4>
                    <p class="text-muted title" style="font-size:16px;" ></p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Company /Shop Name</h4>
                    <p class="text-muted companyname" style="font-size:16px;"></p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Email Address</h4>
                    <p class="text-muted email" style="font-size:16px;"></p>
                  </div>
                  <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Contact No</h4>
                    <p class="text-muted contactno" style="font-size:16px;"></p>
                    
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Experience Require</h4>
                    <p class="text-muted experience" style="font-size:16px;"></p>
                  </div>
                  </div>
                  <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Gate_No</h4>
                    <p class="text-muted gateno" style="font-size:16px;"></p>
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Salary</h4>
                    <p class="text-muted salary" style="font-size:16px;"></p>
                  </div>
                
                    </div>

                  <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">City</h4>
                    <p class="text-muted city" style="font-size:16px;"></p>
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Gender</h4>
                    <p class="text-muted gender" style="font-size:16px;"></p>
                  </div>
                 </div>
                 <div class="row px-3">
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Employee Type</h4>
                    <p class="text-muted employeetype" style="font-size:16px;"></p>
                  </div>
                  <div class="col-sm-6 col-12 mb-3">
                  <h4 style="color:black; font-weight:bold;">Job Role</h4>
                    <p class="text-muted role" style="font-size:16px;"></p>
                  </div>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Vacancies</h4>
                    <p class="text-muted vacancies" style="font-size:16px;"></p>
                  </div>
                  <div class="col-6 mb-3">
                  <h4 style="color:black; font-weight:bold;">Description</h4>
                    <p class="text-muted description" style="font-size:16px;"></p>
                  </div>
                  <p class="text-muted" style="font-size:16px; color:green;">Already applied </p>
                </div>
              
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 <div class="modal-footer">
  
   
    <button type="button" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>

                        </div>
                  
        </div>
       <!-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
                        </div> -->
                            </div>
                             </div>
                            </div>




@endsection

@section('script')

<script>

function getdata(id){
  console.log(id);
    $.ajax({
 type:'get',
 url:'/getjobdata/'+id,

 success:function(response){
    console.log(response);
  $('#jobcreater').val(response.show.job_creater);
  $('#jobid').val(response.show.id);
  $('.title').html(response.show.title);
   $('.companyname').html(response.show.companyname);
   $('.email').html(response.show.email);
   $('.contactno').html(response.show.contactno);
   $('.experience').html(response.show.experience);
   $('.salary').html(response.show.salary);
   $('.gateno').html(response.show.gate_no);
   $('.city').html(response.show.city);
   $('.gender').html(response.show.gender);
   $('.employeetype').html(response.show.employee_type);
   $('.role').html(response.show.role);
   $('.vacancies').html(response.show.vacancies);
   $('.description').html(response.show.job_description);





}

});
 }  

  @if(Session::has('status'))
    toastr.success("{{ Session::get('status') }}")
@endif
</script>


@endsection
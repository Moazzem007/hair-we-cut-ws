@extends('admin.barbar.layout')



@section('mainContent')

<div class="container-fluid">
    <div class="row">
    <form action="{{route('storeeditpersonalinfo')}}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$check->id}}">
            <div class="row py-4">
                 <h1 class="text-center pl-3">Personal Information</h1>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="name" class="form-label">Full Name</label>
                      <input class="form-control" type="text"
                          name="name" value="{{$check->name}}" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="contact" class="form-label">Contact NO</label>
                      <input class="form-control" type="text" name="contact" value="{{$check->contect}}" />
                  </div>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="email" class="form-label">Email</label>
                      <input class="form-control" type="text"
                          name="email" value="{{$check->email}}" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="gateno" class="form-label">Complete Address</label>
                      <input class="form-control" type="text" name="gateno" value="{{$check->gateno}}" />
                  </div>
                </div>
                <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="city" class="form-label">City</label>
                      <input class="form-control" type="text" name="city" value="{{$check->city}}"  />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="postal_code" class="form-label">Postal Code</label>
                      <input class="form-control" type="text"
                          name="postal_code" value="{{$check->postal_code}}"  autofocus />
                  </div>
              </div>
              <div class="row py-4">
                 <h1 class="text-center pl-3">Experience Information</h1>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="experiencebarber" class="form-label">Experience As Barbar</label>
                      <!-- <input class="form-control" type="text" name="experiencebarber" /> -->
                      <select name="experiencebarber" class="form-control" id="" value="{{$check->experiencebarber}}" >
                        <option value="{{$check->experiencebarber}}" >{{$check->experiencebarber}}</option>
                        <option value="2 years">2 years</option>
                        <option value="3 years">3 years</option>
                        <option value="3 - 4 years">3 - 4 years</option>
                        <option value="5+ years">5+ years</option>


                      </select>
                  </div>
              </div>
              
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="previewsalonname" class="form-label">Previeus Salon Name</label>
                      <input class="form-control" type="text"
                          name="previewsalonname" value="{{$check->previewsalonname}}" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="presalonaddress" class="form-label">Previeus Salon Address</label>
                      <input class="form-control" type="text" name="presalonaddress" value="{{$check->presalonaddress}}" />
                  </div>
                
              </div>

              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="fromdate" class="form-label">From Date</label>
                      <input class="form-control" type="date" name="fromdate" value="{{$check->fromdate}}" />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="todate" class="form-label">To Date</label>
                      <input class="form-control" type="date"
                          name="todate" value="{{$check->todate}}" autofocus />
                  </div>
              </div>  

              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="position_role" class="form-label">Position/Role</label>
                      <input class="form-control" type="text" name="position_role" value="{{$check->position_role}}" />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="reasonforleaving" class="form-label">Reason For Leaving</label>
                      <input class="form-control" type="text"
                          name="reasonforleaving" value="{{$check->reasonforleaving}}" autofocus />
                  </div>
              </div>  
              <div class="row py-4">
                 <h1 class="text-center pl-3">Education Information</h1>
              </div>
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="barber_licence_no" class="form-label">Barbar Licence No </label>
                      <input class="form-control" type="text" name="barber_licence_no"  value="{{$check->barber_licence_no}}"/>
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="institute_name" class="form-label">Institute / Organization Name</label>
                      <input class="form-control" type="text"
                          name="institute_name" value="{{$check->institute_name}}" autofocus />
                  </div>
              </div>  

              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="institute_address" class="form-label">Institute / Organization Address </label>
                      <input class="form-control" type="text" name="institute_address" value="{{$check->institute_address}}" />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="certificate_training" class="form-label">Certificate/Training Name</label>
                      <input class="form-control" type="text"
                          name="certificate_training" value="{{$check->certificate_training}}" autofocus />
                  </div>
              </div>  

              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="skill" class="form-label">Skills </label>
                      <input class="form-control" type="text" name="skill" value="{{$check->skill}}" />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="available" class="form-label">Available</label>
                      <!-- <input class="form-control" type="text"
                          name="available" autofocus /> -->
                          <select class="form-control"
                          name="available" id=""  autofocus>
                          <option value="{{$check->available}}">{{$check->available}}</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>

                        </select>
                  </div>
              </div>
              <div class="mt-2">
                  <button type="submit" class="btn btn-primary me-2">Update changes</button>
                  <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                  <button type="reset" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>
              </div>
          </form>
    </div>
</div>


@endsection
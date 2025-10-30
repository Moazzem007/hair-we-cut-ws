@extends('layouts.adminapp')




@section('Main-content')

  {{-- Page Header --}}
  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Market Place Rent List</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('adminDashboard') }}">Home</a>
            </li>
            <li>
                <a> Market Place</a>
            </li>
            <li class="active">
                <strong> Rent List</strong>
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
               <a href="{{route('marketplacerentadmin')}}" class="btn btn-primary my-2 mx-2">Market Place Rent</a>
               <a href="{{route('marketplacesalonadmin')}}" class="btn btn-primary my-2 mx-2">Market Place Salon</a>
               <a href="{{route('marketplaceproductadmin')}}" class="btn btn-primary my-2 mx-2">Market Place Product</a>


                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Creater</th>
                                    <th>Business</th>
                                    <th>Category</th>
                                    <th>Address</th>
                                    <th>Chairs</th>
                                    <th>Price</th>
                                    <th>Available From</th>
                                    <th>Contact Name</th>
                                    <th>Contact Number</th>
                                    <th>Contact Email</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($show as $key => $item)
                                <tr>
                                    <th>{{ $key + 1 }}</th>
                                    <td>{{ $item->job_creater }}</td>
                                    <td>{{ $item->business }}</td>
                                    <td>{{ $item->category }}</td>
                                    <td>{{ $item->address }}</td>
                                    <td>{{ $item->chairs }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->availablefrom }}</td>
                                    <td>{{ $item->contactname }}</td>
                                    <td>{{ $item->contactnumbar }}</td>
                                    <td>{{ $item->contactemail }}</td>
                                    <td>
                                        <img src="{{asset($item->image) }}" alt="" style="width:100px; height:100px;">
                                    </td>
                                    <td>{{ $item->status }}</td>
                                    <td>
                                        <div class="btn-group">

                                            <a href="{{route('marketplace_rent_view',$item->id)}}"
                                                class="btn btn-xs btn-outline btn-success"><i
                                                    class="fa fa-print"></i></a>


                                        </div>
                                        {{-- <div class="btn-group">

                                            <a href="{{ route('deletemarketproduct', $view->id) }}"
                                                class="btn btn-xs btn-outline btn-success"><i class="fa fa-trash"></i></a>


                                        </div> --}}
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


 <!-- edit  Modal -->
 <div class="modal fade"id="updatemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header bg-primary">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Rent A Business</h1>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
     <div class="modal-body">
    <form>
          <div class="row">
              <div class="mb-3 col-md-6">
                  <label for="business" class="form-label">Business Name</label>
                  <input class="form-control" type="text"
                      name="business" id="business" autofocus  readonly/>
              </div>
              <!-- <div class="mb-3 col-md-6">
                  <label for="address" class="form-label" >Category</label>
                  <select class="form-control" name="category" id="category">
                  <option value=""></option>
                    <option value="tools">Tools</option>
                    <option value="support">Support</option>
                    <option value="cover">Cover</option>

                </select>
              </div> -->
            
          </div>
          <div class="row">
          <div class="mb-3 col-md-6">
                  <label for="address" class="form-label">Location</label>
                  <input class="form-control" type="text" name="address" id="address" readonly/>
              </div>
              <div class="mb-3 col-md-6">
                  <label for="chairs" class="form-label">Chairs Available</label>
                  <input class="form-control" type="text"
                      name="chairs" id="chairs" autofocus readonly/>
              </div>
            
          </div>
          <div class="row">
          <div class="mb-3 col-md-6">
                  <label for="price" class="form-label">Price</label>
                  <input class="form-control" type="text" name="price" id="price"  readonly/>
              </div>
              <div class="mb-3 col-md-6">
              <img src="" alt="" id="image" style="width:100px; height:100px;">
                  <label for="image" class="form-label">Image</label>
                 
              </div>
          </div>
          <div class="row">
          <div class="mb-3 col-md-6">
                  <label for="available" class="form-label">Available From</label>
                  <input class="form-control" type="date" name="availablefrom" id="availablefrom" readonly/>
              </div>
              <div class="mb-3 col-md-6">
                  <label for="contactname" class="form-label">Saller Name</label>
                  <input class="form-control" type="text"
                      name="contactname" id="contactname" autofocus readonly/>
              </div>
          </div>
          <div class="row">
          <div class="mb-3 col-md-6">
                  <label for="contactnumbar" class="form-label">Contact Numbar</label>
                  <input class="form-control" type="text" name="contactnumbar" id="contactnumbar" readonly/>
              </div>
              <div class="mb-3 col-md-6">
                  <label for="contactemail" class="form-label">Email</label>
                  <input class="form-control" type="email"
                      name="contactemail" id="contactemail" autofocus readonly/>
              </div>
          </div>
          <div class="row">
             
              <div class="mb-3 col-md-12">
                  <label for="description" class="form-label">Description</label>
                  <textarea name="description" class="form-control" id="description" autofocus cols="60" rows="7" readonly></textarea>
              </div>
          </div>
          <div class="mt-2">
           
              <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
              <button type="reset" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>
          </div>
      </form>
    </div>
   <div class="modal-footer">
<!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
<!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                        </div>
                         </div>
                        </div>




@endsection


@section('script_code')
<script>
     function edit(id){
  console.log(id);
    $.ajax({
 type:'get',
 url:'editmarketrent/'+id,

 success:function(response){
    console.log(response);
  $('#id').val(id);
   $('#business').val(response.show.business);
   $('#category').val(response.show.category);

   $('#address').val(response.show.address);
   $('#chairs').val(response.show.chairs);
   $('#price').val(response.show.price);
   $('#description').val(response.show.description);
   $('#availablefrom').val(response.show.availablefrom);
   $('#contactname').val(response.show.contactname);
   $('#contactnumbar').val(response.show.contactnumbar);
   $('#contactemail').val(response.show.contactemail);

//    $('#address').val(response.show.address);

   document.getElementById('image').src=response.show.image;
}

});
 }  

 @if(Session::has('status'))
    toastr.success("{{ Session::get('status') }}")
@endif
</script>


@endsection

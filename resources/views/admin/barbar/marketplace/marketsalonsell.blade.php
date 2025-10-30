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
                    <strong>Market Sell Salon</strong>
                </li>
            </ol>
        </div>
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
    <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Add Sell Salon
</button>

<a href="{{route('marketallsalonsell')}}" type="button" class="btn btn-primary" >
    All Sell Salon
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
                                    <th>Creater</th>
                                    <th>Salon/ Business Name</th>
                                    <th>Location</th>
                                    <th>Barbar Available</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $view->job_creater }}</td>
                                            <td>{{ $view->salon_name }}</td>
                                            <td>{{ $view->address }}</td>
                                            <td>{{ $view->barber_available }}</td>
                                            <td>{{ $view->price }}</td>
                                            <td>{{ $view->description }}</td>
                                            <td>
                                            <img src="{{asset($view->image)}}" alt="" style="width:80px; height:80px;">    
                                            </td>
                                            <td>{{ $view->status }}</td>
                                            
                                            <td>
                                                <div class="btn-group">

                                                    <a href="#"
                                                        class="btn btn-xs btn-outline btn-success" data-toggle="modal" data-target="#updatemodel" onclick="edit({{$view->id}})"><i
                                                            class="fa fa-edit"></i></a>


                                                </div>
                                                <div class="btn-group">
        
                                                    <a href="{{route('marketsalon_view',$view->id)}}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-print"></i></a>
        
        
                                                </div>
                                                <div class="btn-group">

                                                    <a href="{{ route('deletesalonsell', $view->id) }}"
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
        <div class="modal-header bg-primary">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Sell Salon</h1>
    
        </div>
         <div class="modal-body">
        <form action="{{route('storemarketsalon')}}" method="POST" enctype="multipart/form-data">
            @csrf
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="salon_name" class="form-label">Salon/ Business Name</label>
                      <input class="form-control" type="text"
                          name="salon_name" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="address" class="form-label">Location</label>
                      <input class="form-control" type="text" name="address" />
                  </div>
              </div>
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="barber_available" class="form-label">No of Barbers Available</label>
                      <input class="form-control" type="text"
                          name="barber_available" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="price" class="form-label">Price</label>
                      <input class="form-control" type="text" name="price" />
                  </div>
              </div>
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="image" class="form-label">Image</label>
                      <input class="form-control" type="file"
                          name="image" autofocus />
                  </div>
                
              </div>
             
              <div class="row">
                 
                  <div class="mb-3 col-md-12">
                      <label for="description" class="form-label">Description/Details</label>
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
    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                            </div>
                             </div>
                            </div>


 <!-- edit  Modal -->
 <div class="modal fade"id="updatemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-primary">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Sell Salon</h1>
      
        </div>
         <div class="modal-body">
         <form action="{{route('storemarketsalonupdated')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="id" name="id">
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="salon_name" class="form-label">Salon/ Business Name</label>
                      <input class="form-control" type="text"
                          name="salon_name" id="salon_name" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="address" class="form-label">Location</label>
                      <input class="form-control" id="address" type="text" name="address" />
                  </div>
              </div>
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="barber_available" class="form-label">No of Barbers Available</label>
                      <input class="form-control" type="text"
                          name="barber_available" id="barber_available" autofocus />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="price" class="form-label">Price</label>
                      <input class="form-control" type="text" id="price"  name="price" />
                  </div>
              </div>
              <div class="row">
              <div class="mb-3 col-md-6">
                <img src="" id="image" alt="" style="width:100px; height:80px;">
                      <label for="image" class="form-label">Image</label>
                      <input class="form-control" type="file"
                          name="image" autofocus />
                  </div>
                 
              </div>
             
              <div class="row">
                 
                  <div class="mb-3 col-md-12">
                      <label for="description" class="form-label">Description/Details</label>
                      <textarea name="description"  id="description" class="form-control" autofocus cols="60" rows="7"></textarea>
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
    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                            </div>
                             </div>
                            </div>




    {{-- Main Body End --}}
@endsection


@section('script')
<script>
     function edit(id){
  console.log(id);
    $.ajax({
 type:'get',
 url:'editmarketsalon/'+id,

 success:function(response){
    console.log(response);
  $('#id').val(id);
   $('#salon_name').val(response.show.salon_name);
   $('#address').val(response.show.address);
   $('#barber_available').val(response.show.barber_available);
   $('#price').val(response.show.price);
   $('#description').val(response.show.description);
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

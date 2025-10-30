@extends('admin.barbar.layout')



@section('mainContent')






    {{-- Page Header --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Sell Products List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('adminDashboard') }}">Home</a>
                </li>
                <li>
                    <a>Market place</a>
                </li>
                <li class="active">
                    <strong>Market Sell Products</strong>
                </li>
            </ol>
        </div>
    </div>

    {{-- Page Header  End --}}


    {{-- Main Body --}}

    <div class="wrapper wrapper-content animated fadeInRight">
    <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Add Products
</button>
<a href="{{route('marketallproducts')}}" type="button" class="btn btn-primary" >
    All Products
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
                                    <th>Product Name</th>
                                    <!-- <th>Category</th> -->
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Dis_Price</th>
                                    <th>Shift_Cost</th>
                                    <!-- <th>Short Desc</th> -->
                                    <th>Description</th>
                                    <th>Image</th>
                                    <!-- <th>Video</th> -->
                                    <th>Specification</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>
                                @foreach ($show as $key => $view)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $view->job_creater }}</td>
                                            <td>{{ $view->product_name }}</td>
                                            <!-- <td>{{ $view->category }}</td> -->
                                            <td>{{ $view->brand }}</td>
                                            <td>{{ $view->price }}</td>
                                            <td>{{ $view->discountprice }}</td>
                                            <td>{{ $view->shift_cost }}</td>
                                            <!-- <td>{{ $view->short_description }}</td> -->
                                            <td>{{ $view->detail_description }}</td>
                                            <td>
                                            <img src="{{asset($view->image)}}" alt="" style="width:80px; height:80px;">    
                                            </td>
                                            <!-- <td>
                                            <video src="{{asset($view->video)}}" alt="" style="width:80px; height:80px;">    
                                            </td> -->
                                            <td>{{ $view->specification }}</td>
                                            <td>{{ $view->status }}</td>
                                            
                                            <td>
                                                <div class="btn-group">

                                                    <a href="#"
                                                        class="btn btn-xs btn-outline btn-success" data-toggle="modal" data-target="#updatemodel" onclick="edit({{$view->id}})"><i
                                                            class="fa fa-edit"></i></a>


                                                </div>
                                                <div class="btn-group">
        
                                                    <a href="{{route('marketproduct_view',$view->id)}}"
                                                        class="btn btn-xs btn-outline btn-success"><i
                                                            class="fa fa-print"></i></a>
        
        
                                                </div>
                                                <div class="btn-group">

                                                    <a href="{{ route('deletemarketproduct', $view->id) }}"
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
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Products</h1>
        </div>
         <div class="modal-body">
        <form action="{{route('storemarketproducts')}}" method="POST" enctype="multipart/form-data">
            @csrf
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="product_name" class="form-label">Product Name</label>
                      <input class="form-control" type="text"
                          name="product_name" autofocus />
                  </div>
                  <!-- <div class="mb-3 col-md-6">
                      <label for="category" class="form-label">Category</label>
                      <select class="form-control" type="text" name="category">
                        <option value="">Select</option>
                        <option value="silver">Silver</option>
                        <option value="cream">Cream</option>

                      </select>
                  </div> -->
              </div>
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="brand" class="form-label">Brand</label>
                      <input type="text" class="form-control" name="brand">
                      <!-- <select class="form-control" type="text" name="brand">
                        <option value="">Select</option>
                        <option value="upper brand">Upper Brand</option>
                        <option value="american">American</option>

                      </select> -->
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="price" class="form-label">Price</label>
                      <input class="form-control" type="text" name="price" />
                  </div>
              </div>
             <div class="row">
             <div class="mb-3 col-md-6">
                      <label for="discountprice" class="form-label">Discounted price</label>
                      <input class="form-control" type="text" name="discountprice" />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="shift_cost" class="form-label">Shipping Charges</label>
                      <input class="form-control" type="text"
                          name="shift_cost" autofocus />
                  </div>
              </div>
              <div class="row">
              <!-- <div class="mb-3 col-md-6">
                      <label for="short_description" class="form-label">Short Description</label>
                      <input class="form-control" type="text" name="short_description" />
                  </div> -->
                  <div class="mb-3 col-md-12">
                      <label for="description" class="form-label">Description</label>
                      <textarea name="detail_description" class="form-control" autofocus cols="60" rows="7"></textarea>
                  </div>
              </div>
             
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="image" class="form-label">Product Image</label>
                      <input class="form-control" type="file"
                          name="image" autofocus />
                  </div>
                  <!-- <div class="mb-3 col-md-6">
                      <label for="video" class="form-label">Product Video</label>
                      <input class="form-control" type="file"
                          name="video" autofocus />
                  </div> -->
                 
              </div>

              <div class="row">
              <div class="mb-3 col-md-12">
                      <label for="specification" class="form-label">Product Details</label>
                      <textarea name="specification" class="form-control" autofocus cols="60" rows="7"></textarea>
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
        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Products</h1>
     
        </div>
         <div class="modal-body">
        <form action="{{route('storeeditproduct')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="id" name="id">
              <div class="row">
                  <div class="mb-3 col-md-6">
                      <label for="product_name" class="form-label">Product Name</label>
                      <input class="form-control" type="text"
                          name="product_name" id="product_name" autofocus />
                  </div>
                  <!-- <div class="mb-3 col-md-6">
                      <label for="category" class="form-label">Category</label>
                      <select class="form-control" type="text" name="category" id="category">
                        <option value="">Select</option>
                        <option value="silver">Silver</option>
                        <option value="cream">Cream</option>

                      </select>
                  </div> -->
              </div>
              <div class="row">
              <div class="mb-3 col-md-6">
                      <label for="brand" class="form-label">Brand</label>
                      <input type="text" class="form-control" name="brand" id="brand">
                      <!-- <select class="form-control" type="text" name="brand" id="brand">
                        <option value="">Select</option>
                        <option value="upper brand">Upper Brand</option>
                        <option value="american">American</option>

                      </select> -->
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="price" class="form-label">Price</label>
                      <input class="form-control" type="text" name="price" id="price" />
                  </div>
              </div>
             <div class="row">
             <div class="mb-3 col-md-6">
                      <label for="discountprice" class="form-label">Discounted price</label>
                      <input class="form-control" type="text" name="discountprice" id="discountprice" />
                  </div>
                  <div class="mb-3 col-md-6">
                      <label for="shift_cost" class="form-label">Shipping Charges</label>
                      <input class="form-control" type="text"
                          name="shift_cost" id="shift_cost" autofocus />
                  </div>
              </div>
              <div class="row">
              <!-- <div class="mb-3 col-md-6">
                      <label for="short_description" class="form-label">Short Description</label>
                      <input class="form-control" type="text" name="short_description" id="short_description" />
                  </div> -->
                  <div class="mb-3 col-md-12">
                      <label for="description" class="form-label">Description</label>
                      <textarea name="detail_description" class="form-control" autofocus cols="60" rows="7" id="description"></textarea>
                  </div>
              </div>
             
              <div class="row">
              <div class="mb-3 col-md-6">
                <img src="" alt="" id="image"  style="width:100px; height:100px;">
                      <label for="image" class="form-label">Product Image</label>
                      <input class="form-control" type="file"
                          name="image" autofocus />
                  </div>
                  <!-- <div class="mb-3 col-md-6">
               
                      <label for="video" class="form-label">Product Video</label>
                      <input class="form-control" type="file"
                          name="video" autofocus />
                  </div> -->
                 
              </div>

              <div class="row">
              <div class="mb-3 col-md-12">
                      <label for="specification" class="form-label">Product Details</label>
                      <textarea name="specification" class="form-control" autofocus cols="60" rows="7" id="specification"></textarea>
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
 url:'editmarketproduct/'+id,

 success:function(response){
    console.log(response);
  $('#id').val(id);
   $('#product_name').val(response.show.product_name);
   $('#category').val(response.show.category);
   $('#brand').val(response.show.brand);
   $('#price').val(response.show.price);
   $('#discountprice').val(response.show.discountprice);
   $('#shift_cost').val(response.show.shift_cost);
   $('#short_description').val(response.show.short_description);
   $('#detail_description').val(response.show.detail_description);

   $('#specification').val(response.show.specification);
//    $('#address').val(response.show.address);

   document.getElementById('image').src=response.show.image;
   document.getElementById('video').src=response.show.video;

}

});
 }  


 @if(Session::has('status'))
    toastr.success("{{ Session::get('status') }}")
@endif
</script>


@endsection








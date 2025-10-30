@extends('layouts.adminapp')

{{-- @section('title','Requests')@endsection --}}


{{-- Main Content --}}

@section('Main-content')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
         <h2>Add Product Quantity</h2>
         <ol class="breadcrumb">
             <li>
                 <a href="index.html">Dashboard</a>
             </li>
             <li>
                 <a>Product</a>
             </li>
             <li class="active">
                 <strong>Add Quantity</strong>
             </li>
         </ol>
     </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="ibox float-e-margins">
                {{--  --}}
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>  
                    <div class="ibox-content">

                        <form action="{{route('stockstore')}}" method="POST" role="form" id="form">
                            {{csrf_field()}}

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Select Category</label>
                                        <select name="category" id="category" class="form-control">
                                            <option >--Select Category--</option>
                                            @foreach ( $categories as $category)
                                                <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Select Product</label>
                                        <select name="product" id="product" class="form-control">
                                           
                                        </select>
                                    </div>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Quantity</label>
                                        <input type="text" name="quantity" class="form-control">
                                    </div>
                                </div>

                                                               
                            </div>                                    
                            <div class="row">
                                
                                <div class="col-md-6 col-md-offset-4">
                                <div class="form-group">
                                    <label for=""></label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                                </div>

                            </div>            
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script_code')

<script>
    document.getElementById('category').addEventListener('change',ajaxProduct);
    function ajaxProduct(){
        var cat = document.getElementById('category').value;
        // 
        $.ajaxSetup({
                    headers:{
                        'X_CSRF_TOKEN':$('meta[name="csrf-token"]').attr('contant')
                    }
                });

                $.ajax({
                    type:"POST",
                    url:'{{url("/categoryProduct")}}',
                    data:{
                        cat_id:cat,
                        _token:'{{ csrf_token() }}'
                    },
                    success:function(data){
                        
                        var html = '';
                        data.forEach(element => {
                            html += '<option value="'+element.id+'">'+element.product_name+'</option>'
                        });

                        document.getElementById('product').innerHTML = html;

                        console.log(data);
                    }
                });

        // Barber Data
        // 
    }
</script>

@endsection
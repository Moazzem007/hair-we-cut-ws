@extends('layouts.adminapp')

{{-- @section('title','Requests')@endsection --}}


{{-- Main Content --}}

@section('Main-content')


<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Categories List</h5>
                <div class="ibox-tools">
                    <a class="btn btn-info btn-outline" data-toggle="modal" data-target="#myModal6">Add Category</a>
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
                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                {{-- <th>Created_at</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $categories as $key => $category)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$category->category_name}}</td>
                                    {{-- <td>{{$category->created_at->diffForHumans()}}</td> --}}
                                    <td>
                                        {{-- <a href="{{route('category.edit',$category->id)}}" class="btn btn-warning btn-xs pull-left btn-outline" style="margin-right:10px;"><i class="glyphicon glyphicon-edit"></i></a> --}}
                                        <a href="{{route('categorydestroy',$category->id)}}" class="btn btn-danger btn-xs pull-left btn-outline" style="margin-right:10px;"><i class="glyphicon glyphicon-trash"></i></a>
                                       
                                    </td>
                                </tr>
                            @endforeach
                    
                        </tbody>
                        {{-- <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Created_at</th>
                                <th>Action</th>
                            </tr>
                        </tfoot> --}}
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>



<div class="modal inmodal fade" id="myModal6" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add Category</h4>
            </div>
            <div class="modal-body">
                <form action="{{route('category.store')}}" method="POST">
                    {{csrf_field()}}
                        <div class="form-group">
                           <label for="">Category Name</label>
                           <input type="text" name="name" class="form-control" required>
                         </div>
             
                     <div class="row">
                       <div class="col-md-12">
                         <div class="form-group">
                           <label for=""></label>
                           <input type="submit" class="btn btn-info  btn-block" name="submit" value="Save">
                         </div>
                       </div>
                     </div>
             
                </form> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@extends('admin.barbar.layout')



@section('mainContent')


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="ibox float-e-margins">
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

                        <form action="{{route('codes.store')}}" method="POST" role="form" id="form"  >
                            {{csrf_field()}}

                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">

                                @if(Session::has('Error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <i class="fa fa-exclamation-triangle"></i> {{Session::get('Error')}}.
                                </div>
                                @endif



                                    <div class="form-group">
                                        <label for="email" class="col-form-label text-md-right">{{ __('E-Mail') }}</label>
                                        <input id="email" type="text" class="form-control" name="email"  value="{{old('email')}}" required >
                                        @if($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                        
                                    </div>


                                    <div class="form-group">
                                        <label for="code" class="col-form-label text-md-right">{{ __('Code') }}</label>
                                        <input id="code" type="text" class="form-control" name="code" value="{{old('code')}}"  required >
                                        @if($errors->has('code'))
                                            <span class="text-danger">{{ $errors->first('code') }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">{{ __('Register') }}</button>
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

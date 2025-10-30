<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HAIR WE CUT</title>

    <link href="{{asset('admin/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('admin/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('admin/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('admin/css/style.css')}}" rel="stylesheet">
    <style>
        body{
            background:url('{{asset("website/img/portfolio-1.jpg")}}');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat
        }
    </style>
</head>

<body class="gray-bg" >

    <div class="loginColumns animated fadeInDown">
        <div class="row" style="background:rgba(255,255,255,0.7);padding:20px;">

            <div class="col-md-6 text-justify">
                <h2 class="font-bold">Welcome to Hair We Cut</h2>

                <p>
                    In <b>HAIR WE CUT</b>, we are experts in the online barber system. We aim to provide you smart and trendy haircuts and treatments. 
                </p>

                <p>
                    We have a team of professionally trained barbers who are experts in multiple hairs and skincare treatments. 
                </p>

                <p>
                    Now you can easily get a professional haircut and skin treatments at your home. 
                </p>

                <p>
                    <small>The barbers of <b>HAIR WE CUT</b> are near your home town. We aim to connect with all the barbers who have a core skill of hair and skin services and want to continue their business.</small>
                </p>

            </div>
            <div class="col-md-6 text-center">
                <img class="wow fadeInUp" data-wow-delay="500ms" src="{{asset('website/img/logo.png')}}" alt="logo" width="100">

                <div class="ibox-content">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="E-Mail Address">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary block full-width m-b">
                            {{ __('Login') }}
                        </button>

                        {{-- <a href="#">
                            <small>Forgot password?</small>
                        </a> --}}

                        <p class="text-muted text-center">
                            <small>Do not have an account?</small>
                        </p>
                        
                        {{-- <a class="btn btn-sm btn-white btn-block" href="{{route('signup.create')}}">Create an account</a> --}}
                    </form>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                Copy right Hair We Cut
            </div>
            <div class="col-md-6 text-right">
               <small>© 2021-2022</small>
            </div>
        </div>
    </div>

</body>

</html>

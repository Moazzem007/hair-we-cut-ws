@extends('admin.barbar.layout')



@section('mainContent')
    <div class="container">
        <div class="row m-b-lg m-t-lg">
            <div class="col-md-6">
                <form action="{{ route('updateprofile', $user->id) }}" method="POST" enctype="multipart/form-data">
                    {{ method_field('PATCH') }}
                    {{ csrf_field() }}


                    <div class="profile-image">
                        @if ($user->img == null)
                            <img src="{{ asset('avatar.png') }}" class="img-circle circle-border m-b-md" alt="profile">
                        @else
                            <img src="{{ asset('barberDoc/' . $user->img) }}" class="img-circle circle-border m-b-md"
                                alt="profile">
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="profile-info">
                        <h2 class="no-margins">
                            {{ $user->name }}
                        </h2>


                        <table class="table small m-b-xs m-t-sm">
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <strong>Email : {{ $user->email }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td> <strong>Contact # : {{ $user->contact }}</strong></td>
                                    <td><input type="text" class="form-control" name="contact"
                                            value="{{ $user->contact }}"></td>
                                </tr>
                                <tr>
                                    <td><strong>Salone : {{ $user->salon }}</strong></td>
                                    <td><input type="text" class="form-control" name="salon"
                                            value="{{ $user->salon }}"></td>
                                </tr>
                                <tr>
                                    <td><strong>Service : {{ $user->barber_type }}</strong></td>
                                    <td>
                                        <select name="type" id="" class="form-control">
                                            @if ($user->barber_type == null)
                                                <option value="">--Select Type --</option>
                                            @else
                                                <option selected value="{{ $user->barber_type }}">
                                                    {{ $user->barber_type }}</option>
                                            @endif

                                            <option value="Shop Barber">Shop Barber</option>
                                            <option value="Mobile Barber">Mobile Barber</option>
                                            <option value="Mobile Barber / Shop Barber">Mobile Barber /Shop Barber
                                            </option>
                                        </select>
                                    </td>

                                </tr>
                                <tr>
                                    <td><strong>Account Title : {{ $user->account_title }}</strong></td>
                                    <td><input type="text" class="form-control" name="accounttitle"
                                            value="{{ $user->account_title }}"></td>
                                </tr>
                                <tr>
                                    <td><strong>Account #: {{ $user->account_no }}</strong></td>
                                    <td><input type="text" class="form-control" name="accountno"
                                            value="{{ $user->account_no }}"></td>

                                </tr>
                                <tr>
                                    <td><strong>Sort Code #: {{ $user->credit_card }}</strong></td>
                                    <td><input type="text" class="form-control" name="creditcard"
                                            value="{{ $user->credit_card }}"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input type="submit" class="btn btn-info btn-outline" value="Update Info">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </form>
            </div>
            <div class="col-md-3">
                <table class="table small m-b-xs">
                    <tbody>
                        <tr>
                            <th>
                                Total Appointments
                            </th>

                            <td>
                                <strong class="text-success">{{ $totalapp }}</strong>
                            </td>

                        </tr>
                        <tr>
                            <th>
                                Canceled Appointments
                            </th>
                            <td>
                                <strong class="text-danger">{{ $canceled }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Completed Appointments
                            </th>
                            <td>
                                <strong class="text-primary">{{ $completed }}</strong>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Slot Details</h3>
                        <table class="table small m-b-xs table-striped">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Barber Name</th>
                                    <th>From Time</th>
                                    <th>To Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                @foreach ($slots as $key => $slot)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $slot->barber ? $slot->barber->name : '' }}</td>
                                        <td> {{ date('g:i A', strtotime($slot->from_time)) }}</td>
                                        <td> {{ date('g:i A', strtotime($slot->to_time)) }}</td>
                                        <td>{{ $slot->status }}</td>
                                        <td><a href="{{ route('deleteslot', $slot->id) }}"
                                                onclick="return confirm('Are you sure you want to Delete ?');"
                                                class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row">
                            <hr>
                            @if (session()->has('time'))
                                <div class="alert alert-warning alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>Warning!</strong> {{ session()->get('time') }}.
                                </div>
                            @endif
                            <div class="col-md-4">
                                <a class="btn btn-info btn-outline" data-toggle="modal" data-target="#myModal5">Add Slot</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content ">
                        <h3>Attach the following documents in the PDF or Image <br> 1. ID "Identification" <br> 2. Proof
                            Adress
                        </h3>
                        <div class="row">
                            @foreach ($docs as $doc)
                                <div class="col-md-6 m-b-xs">
                                    <h5 class="text-uppercase bg-info text-center" style="line-height:20px; padding:5px;">
                                        {{ $doc->title }}</h5>
                                    @if ($doc->type == 'PDF')
                                        <img src="{{ asset('PDF_file_icon.svg') }}" alt="" width="80"> <br><br>
                                        <a href="{{ asset('barberDoc/' . $doc->image) }}" download> <i
                                                class="fa fa-download"></i> Download</a>
                                    @else
                                        <img alt="image" class="img-responsive"
                                            src="{{ asset('barberDoc/' . $doc->image) }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <hr>
                            <div class="col-md-4">
                                <a class="btn btn-info btn-outline" data-toggle="modal" data-target="#myModal6">Add
                                    Document</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Service</h3>
                        <table class="table small m-b-xs table-striped">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Time</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                                @foreach ($services as $key => $service)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $service->title }}</td>
                                        <td>{{ $service->price }}</td>
                                        <td>{{ $service->minut }}</td>
                                        <td>{{ $service->description }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('servicesDelete', $service->id) }}"
                                                    onclick="return confirm('Are you sure you want to Delete ?');"
                                                    class="btn btn-xs btn-outline btn-danger"><i class="fa fa-trash"></i>
                                                    Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-4">
                                <a class="btn btn-info btn-outline" data-toggle="modal" data-target="#modal-services">Add
                                    Service</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-lg-6">

                <div class="ibox">
                    <div class="ibox-content">
                        <h3>Our Barbers</h3>
                        <table class="table small m-b-xs table-striped">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Contact #</th>
                                    <th>Account #</th>
                                    <th>Account Title</th>
                                    <th>Credit Card</th>
                                </tr>
                                @foreach ($businessBarber as $key => $barber)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><img src="{{ asset('barberDoc/' . $barber->img) }}" alt=""
                                                width="60"></td>
                                        <td>{{ $barber->name }}</td>
                                        <td>{{ $barber->contact }}</td>
                                        <td>{{ $barber->account_no }}</td>
                                        <td>{{ $barber->account_title }}</td>
                                        <td>{{ $barber->credit_card }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <a class="btn btn-info btn-outline" data-toggle="modal"
                                        data-target="#barber-model">Add
                                        Barber</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- Model For Documents --}}
    <div class="modal inmodal fade" id="myModal6" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Document</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('add_documetns') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="">Image</label>
                            <input type="file" name="image" accept="*" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="type" id="" class="form-control">
                                <option value="Image">Image</option>
                                <option value="PDF">PDF</option>
                            </select>
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



    {{-- Model For SLots --}}
    <div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Slot</h4>
                </div>
                <div class="modal-body">
                    <p><Strong>Note:</Strong> <br> <b>From time</b> should not be later than <b>To Time</b></p>
                    <form action="{{ route('add_slot') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        @if (session()->has('time'))
                            <div class="alert alert-warning alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>Warning!</strong> {{ session()->get('time') }}.
                            </div>
                        @endif

                        <div class="form-group">
                            <label for=""> Select Barber</label>
                            <select name="slotno" id="" class="form-control">
                                @foreach ($businessBarber as $key => $barber)
                                    <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">From Time</label>
                            <input type="time" class="form-control" name="fromtime">
                        </div>



                        <div class="form-group">
                            <label for="">To Time</label>
                            <input type="time" class="form-control" name="totime">
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


    {{-- Services MOdel --}}
    <div id="modal-services" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-10 col-md-offset-1">
                            <h3 class="m-t-none m-b">Add Service</h3>
                            <form action="{{ route('services.store') }}" method="post">
                                {{ csrf_field() }}
                                <div class="row">

                                    {{-- <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Service Type</label>
                                            <select name="type" id="" class="form-control">
                                                <option value="Hair" selected>Hair Styling</option>
                                                <option value="Shaving">Shaving</option>
                                                <option value="Face">Face Masking</option>
                                            </select>
                                        </div>
                                    </div> --}}



                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" placeholder="Enter Service Title" name="title"
                                                class="form-control">
                                            <input type="hidden" name="minut"value="0" class="form-control">
                                            <input type="hidden" name="type"value="Hair" class="form-control">
                                            <input type="hidden" name="description"value="some test"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Price (In Pounds)</label>
                                            <input type="number" placeholder="" name="price" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">


                                    <button class="btn btn-sm btn-primary btn-block"
                                        type="submit"><strong>Save</strong></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Barber MOdel --}}
    <div id="barber-model" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">Add Barber</h3>
                            <form action="{{ route('addbusinessbarber') }}" method="post"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Profile Image</label>
                                            <input type="file" class="form-control" name="image" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Name</label>
                                            <input type="text" class="form-control" name="name" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Contact #</label>
                                            <input type="text" class="form-control" name="contact" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Account Tilte</label>
                                            <input type="text" class="form-control" name="account_title" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Account #</label>
                                            <input type="text" class="form-control" name="account" />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Credit Card #</label>
                                            <input type="text" class="form-control" name="credit" />
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <button class="btn btn-sm btn-primary btn-block"
                                        type="submit"><strong>Save</strong></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

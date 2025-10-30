<!DOCTYPE html>
<html>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>HAIR-WE-CUT</title>
<link rel="icon" href="{{ asset('public/favicon.ico') }}">
<link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/iCheck/custom.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/chosen/bootstrap-chosen.css') }}" rel="stylesheet">

<!-- <link href="css/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet"> -->

<link href="{{ asset('admin/css/plugins/colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/cropper/cropper.min.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/switchery/switchery.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/nouslider/jquery.nouislider.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/ionRangeSlider/ion.rangeSlider.css') }}" rel="stylesheet">
<link href="{{ asset('admin/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">

<link href="{{ asset('admin/css/plugins/clockpicker/clockpicker.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/select2/select2.min.css" rel="stylesheet') }}">

<link href="{{ asset('admin/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/dualListbox/bootstrap-duallistbox.min.css') }}" rel="stylesheet">

<!-- FooTable -->
<link href="{{ asset('admin/css/plugins/footable/footable.core.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">


</head>

<body class="fixed-navigation">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="image" class="" src="{{ asset('logo.png') }}" width="80" />
                            </span>
                        </div>
                    </li>

                    <li class="active">
                        <a href="{{ route('adminDashboard') }}"><i class="fa fa-th-large"></i> <span
                                class="nav-label">Dashboard</span></a>
                    </li>

                    <li><a href="{{ route('barbers.index') }}"><i class="fa fa-cut"></i> <span
                                class="nav-label">Barbers</span></a></li>


                    <li><a href="{{ route('appointment.index') }}"><i class="fa fa-book"></i> <span
                                class="nav-label">Appointnemts</span></a></li>

                    <li><a href="{{ route('commission.index') }}"><i class="fa fa-list"></i> <span
                                class="nav-label">Commission</span></a></li>


                    <li><a href="{{ route('category.index') }}"><i class="fa fa-tree"></i> <span
                                class="nav-label">Categories</span></a></li>



                    <li><a href="{{ route('productdash.index') }}"><i class="fa fa-cubes"></i> <span
                                class="nav-label">Product</span></a></li>
                    <li><a href="{{ route('adminproductorder') }}"><i class="fa fa-shopping-cart"></i> <span
                                class="nav-label">Product Order</span></a></li>
                    <li><a href="{{ route('barberproductorder') }}"><i class="fa fa-users"></i> <span
                                class="nav-label">Barber Order</span></a></li>
                    <li><a href="{{ url('wallet') }}"><i class="fa fa-google-wallet"></i> <span
                                class="nav-label">Wallet</span></a></li>
                    <li><a href="{{ route('productwallet') }}"><i class="fa fa-google-wallet"></i> <span
                                class="nav-label">Product Wallet</span></a></li>
                    <li><a href="{{ route('listjobsadmin') }}"><i class="fa fa-briefcase"></i>
                            <span class="nav-label">Job Portal</span></a></li>
                    <li><a href="{{ route('marketplacerentadmin') }}"><i class="fa fa-shopping-cart"></i>
                            <span class="nav-label">Market Place</span></a></li>

                    <li><a href="{{ route('boarding_screen') }}"><i class="fa fa-shopping-cart"></i>
                            <span class="nav-label">Onboarding Screens</span></a></li>

                    <li><a href="{{ route('contactus.index') }}"><i class="fa fa-envelope"></i> <span
                                class="nav-label">Contact Us</span></a></li>


                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg sidebar-content">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" action="search_results.html">
                            <div class="form-group">
                                <input type="text" placeholder="Search for something..." class="form-control"
                                    name="top-search" id="top-search">
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message"> Hair We Cut Dashboard</span>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">

                                <i class="fa fa-envelope"></i>

                                <span
                                    class="label label-warning">{{ App\Models\Contactus::where('status', 1)->count() }}</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-bell"></i> <span class="label label-primary">8</span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>
                                    <a href="mailbox.html">
                                        <div>
                                            <i class="fa fa-envelope fa-fw"></i> You have 16 messages
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="profile.html">
                                        <div>
                                            <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                            <span class="pull-right text-muted small">12 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="grid_options.html">
                                        <div>
                                            <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a href="notifications.html">
                                            <strong>See All Alerts</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>

                        </li>




                        {{-- <li>
                            <a href="login.html">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li> --}}
                        <li>
                            <a class="right-sidebar-toggle">
                                <i class="fa fa-tasks"></i>
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>

            @yield('Main-content')

            <div class="footer">
                <div class="pull-right">
                    {{-- 10GB of <strong>250GB</strong> Free. --}}
                </div>
                <div>
                    <strong>Copyright</strong> Hair We Cut &copy; 2021-2022
                </div>
            </div>

        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{ asset('admin/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{ asset('admin/js/inspinia.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/pace/pace.min.js') }}"></script>
    {{-- <script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script> --}}

    <script src="{{ asset('admin/js/plugins/dataTables/datatables.min.js') }}"></script>

    <!-- Chosen -->
    <script src="{{ asset('admin/js/plugins/chosen/chosen.jquery.js') }}"></script>

    <!-- JSKnob -->
    <script src="{{ asset('admin/js/plugins/jsKnob/jquery.knob.js') }}"></script>

    <!-- Input Mask-->
    <script src="{{ asset('admin/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>

    <!-- Data picker -->
    <script src="{{ asset('admin/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>

    <!-- NouSlider -->
    <script src="{{ asset('admin/js/plugins/nouslider/jquery.nouislider.min.js') }}"></script>

    <!-- Switchery -->
    <script src="{{ asset('admin/js/plugins/switchery/switchery.js') }}"></script>

    <!-- IonRangeSlider -->
    <script src="{{ asset('admin/js/plugins/ionRangeSlider/ion.rangeSlider.min.js') }}"></script>

    <!-- iCheck -->
    <script src="{{ asset('admin/js/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- MENU -->
    <script src="{{ asset('admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>

    <!-- Color picker -->
    <script src="{{ asset('admin/js/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <!-- Clock picker -->
    <script src="{{ asset('admin/js/plugins/clockpicker/clockpicker.js') }}"></script>

    <!-- Image cropper -->
    <script src="{{ asset('admin/js/plugins/cropper/cropper.min.js') }}"></script>

    <!-- Date range use moment.js same as full calendar plugin -->
    <script src="{{ asset('admin/js/plugins/fullcalendar/moment.min.js') }}"></script>

    <!-- Date range picker -->
    <script src="{{ asset('admin/js/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('admin/js/plugins/select2/select2.full.min.js') }}"></script>

    <!-- TouchSpin -->
    <script src="{{ asset('admin/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>

    <!-- Tags Input -->
    <script src="{{ asset('admin/js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Dual Listbox -->
    <script src="{{ asset('admin/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js') }}"></script>

    <!-- Flot -->
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.spline.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.symbol.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/flot/jquery.flot.time.js') }}"></script>

    <!-- Peity -->
    <script src="{{ asset('admin/js/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('admin/js/demo/peity-demo.js') }}"></script>


    <!-- jQuery UI -->
    <script src="{{ asset('admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <!-- Jvectormap -->
    <script src="{{ asset('admin/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

    <!-- EayPIE -->
    <script src="{{ asset('admin/js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>

    <!-- Sparkline -->
    <script src="{{ asset('admin/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Sparkline demo data  -->
    <script src="{{ asset('admin/js/demo/sparkline-demo.js') }}"></script>

    <!-- Jquery Validate -->
    <script src="{{ asset('admin/js/plugins/validate/jquery.validate.min.js') }}"></script>

    <!-- FooTable -->
    <script src="{{ asset('admin/js/plugins/footable/footable.all.min.js') }}"></script>

    {{-- FOR All Js Code of Layout Page --}}
    <script src="{{ asset('admin/js/layout.js') }}"></script>

    {{-- For ChartJS --}}
    <script src="{{ asset('admin/js/plugins/chartJs/Chart.min.js') }}"></script>




    <script>
        $(document).ready(function() {

            var lineData = {
                labels: ["January", "February", "March", "April", "May", "June", "July"],
                datasets: [{
                        label: "Example dataset",
                        backgroundColor: "rgba(26,179,148,0.5)",
                        borderColor: "rgba(26,179,148,0.7)",
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: [28, 48, 40, 19, 86, 27, 90]
                    },
                    {
                        label: "Example dataset",
                        backgroundColor: "rgba(220,220,220,0.5)",
                        borderColor: "rgba(220,220,220,1)",
                        pointBackgroundColor: "rgba(220,220,220,1)",
                        pointBorderColor: "#fff",
                        data: [65, 59, 80, 81, 56, 55, 40]
                    }
                ]
            };

            var lineOptions = {
                responsive: true
            };


            var ctx = document.getElementById("lineChart").getContext("2d");
            new Chart(ctx, {
                type: 'line',
                data: lineData,
                options: lineOptions
            });

        });
    </script>


    @yield('script_code')
</body>

</html>

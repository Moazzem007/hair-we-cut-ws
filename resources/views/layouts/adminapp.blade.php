<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hair We Cut | Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="{{ asset('admin/css/bootstrap.min.css') }}?v=2.0" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/font-awesome/css/font-awesome.css') }}?v=2.0" rel="stylesheet" type="text/css">

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

    <link href="{{ asset('admin/css/plugins/select2/select2.min.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">

<link href="{{ asset('admin/css/plugins/dualListbox/bootstrap-duallistbox.min.css') }}" rel="stylesheet">

<!-- FooTable -->
<link href="{{ asset('admin/css/plugins/footable/footable.core.css') }}" rel="stylesheet">

    <link href="{{ asset('admin/css/animate.css') }}?v=2.0" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/css/style.css') }}?v=2.0" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/css/premium_admin.css') }}?v=2.0" rel="stylesheet" type="text/css">


    <style>
        :root {
            --primary: #9b7a5a; /* A brownish-gold accent for buttons based on the image's "MAKE APPOINTMENT" button */
            --primary-dark: #816548;
            --secondary: #2c2f33;
            --secondary-light: #3a3d42;
            --accent: #ff0080;
            --bg-body: #f8fafc;
            --bg-sidebar: #2f3439; /* Dark gray/brown background from the image */
            --glass: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --success: #10b981;
            --info: #3b82f6;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow-sm: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: var(--bg-body) !important;
            color: var(--text-main);
            overflow-x: hidden;
        }

        #wrapper {
            background-color: var(--bg-sidebar);
        }

        /* Sidebar Modernization */
        .navbar-static-side {
            background: var(--bg-sidebar) !important;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        .nav > li > a {
            color: #d1d5db !important; /* Lighter text for the dark background */
            font-weight: 500;
            padding: 12px 25px;
            margin: 4px 12px;
            border-radius: var(--radius-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav > li.active > a, .nav > li > a:hover {
            background: rgba(155, 122, 90, 0.15) !important; /* Soft brown/gold hover */
            color: white !important;
        }

        .nav > li.active > a {
            color: #ffffff !important;
            background: var(--primary) !important; /* Use the new primary accent color */
            box-shadow: 0 4px 12px rgba(155, 122, 90, 0.3); /* Match shadow with new primary */
        }

        /* Global Table & Dropdown Fixes */
        .table-responsive {
            overflow: visible !important;
            padding-bottom: 20px;
        }
        #page-wrapper { overflow-x: visible !important; }
        .ibox, .ibox-content { overflow: visible !important; }

        /* Standardize dropdown menu appearance and positioning */
        .dropdown-menu {
            border-radius: 8px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            border: 1px solid #eee !important;
            padding: 5px 0 !important;
            z-index: 2000 !important;
        }

        .dropdown-menu-right {
            right: 0 !important;
            left: auto !important;
        }

        /* Ensure responsive tables don't clip on small screens while maintaining visibility */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto !important;
            }
        }

        .nav-header {
            background: transparent !important;
            padding: 35px 25px !important;
            text-align: center;
        }

        .img-circle {
            border: 3px solid rgba(255,255,255,0.1);
            padding: 3px;
            transition: transform 0.3s;
        }

        .img-circle:hover {
            transform: scale(1.05);
        }

        /* Glassmorphism Classes */
        .premium-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            transition: transform 0.3s;
        }

        .premium-card:hover {
            transform: translateY(-5px);
        }

        /* Navbar & Content Styling */
        .navbar-default.navbar-static-top {
            background: rgba(248, 250, 252, 0.8) !important;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 10px 0;
        }

        #page-wrapper {
            background: var(--bg-body) !important;
            min-height: 100vh !important;
        }

        .footer {
            background: transparent !important;
            border: none !important;
            padding: 20px 40px !important;
            color: var(--text-muted);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
</head>

<body class="fixed-navigation">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header" style="border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px;">
                        <div class="dropdown profile-element">
                            <span>
                                <img alt="image" class="img-circle" src="{{ asset('logo.png') }}" 
                                     style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 15px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" />
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> 
                                    <span class="block m-t-xs"> 
                                        <strong class="font-bold" style="color: white; font-size: 16px; letter-spacing: 0.5px;">Admin Control</strong>
                                    </span> 
                                    <span class="text-muted text-xs block" style="opacity: 0.6;">Hair We Cut Platform <b class="caret"></b></span>
                                </span> 
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link" style="color: inherit; text-decoration: none; padding: 3px 20px;">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Overview -->
                    <li class="{{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('adminDashboard') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Overview</span></a>
                    </li>

                    <!-- Pending Approvals -->
                    <li class="{{ request('status') == 'Pending' || request('status') == 'Pendding' ? 'active' : '' }}">
                        <a href="{{ route('barbers.index', ['status' => 'Pending']) }}">
                            <i class="fa fa-user-plus"></i> <span class="nav-label">Pending Approvals</span>
                            @php
                                $pendingCount = App\Models\Barber::whereIn('status', ['Pending', 'Pendding'])->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="label label-warning pull-right" style="margin-top: 2px;">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <!-- Operations -->
                    <li class="{{ Request::is('admin/appointment*') || Request::is('admin/service*') || Request::is('admin/commission*') || Request::is('admin/category*') || Request::is('admin/product*') || Request::is('admin/wallet*') ? 'active' : '' }}">
                        <a href="#"><i class="fa fa-calendar"></i> <span class="nav-label">Operations</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse">
                            <li><a href="{{ route('appointment.index') }}">Bookings</a></li>
                            <li><a href="{{ route('services.index') }}">Services</a></li>
                            <li><a href="{{ route('commission.index') }}">Commissions</a></li>
                            <li><a href="{{ route('category.index') }}">Categories</a></li>
                            <li><a href="{{ route('productdash.index') }}">Marketplace</a></li>
                            <li><a href="{{ route('adminproductorder') }}">Orders</a></li>
                            <li><a href="{{ url('wallet') }}">Financial Wallets</a></li>
                            <li><a href="{{ route('admin.payout_requests') }}">Payout Requests</a></li>
                        </ul>
                    </li>

                    <!-- Job Portal -->
                    <li class="{{ Request::is('admin/list-job*') ? 'active' : '' }}">
                        <a href="#"><i class="fa fa-briefcase"></i> <span class="nav-label">Portals</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse">
                            <li><a href="{{ route('listjobsadmin') }}">Job Board</a></li>
                        </ul>
                    </li>

                    <!-- Digital Content removed -->



                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg sidebar-content">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                        <!-- Search bar removed -->
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message"> Hair We Cut Dashboard</span>
                        </li>
                        <!-- Notifications removed -->

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
                    </ul>

                </nav>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible animate-fade-in" style="margin: 20px 20px 0 20px; border-radius: 8px; border-left: 4px solid var(--success);">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <i class="fa fa-check-circle m-r-xs"></i> <strong>Success:</strong> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible animate-fade-in" style="margin: 20px 20px 0 20px; border-radius: 8px; border-left: 4px solid var(--danger);">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <i class="fa fa-exclamation-circle m-r-xs"></i> <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

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
    <script src="{{ asset('admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

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
    <script src="{{ asset('admin/js/plugins/colorpicker/bootstrap-colorpicker.min.css') }}"></script>

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

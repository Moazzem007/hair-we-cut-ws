<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HAIR WE CUT</title>
    <link href="{{ asset('admin/email_templates/styles.css') }}" media="all" rel="stylesheet" type="text/css" />
</head>

<body>

    <table class="body-wrap">
        <tr>
            <td></td>
            <td class="container" width="600">
                <div class="content">
                    <table class="main" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="content-wrap">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" style="background-color:#eee;padding:10px;">
                                            <img class="img-responsive" src="{{ asset('about-logo2.png') }}"
                                                width="300" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <p></p>
                                            <h4> Dear Customer, <strong>{{ $data['name'] }}</strong></h4>
                                            <p></p>
                                        </td>
                                    </tr>
                                    <tr>

                                    </tr>
                                    <tr>
                                        <td class="content-block">
                                            Your account has been created at Hair we cut. <br>
                                            Feel free to avail the reliable services of Hair we cut. <br>
                                            Email : <strong>{{ $data['email'] }}</strong> <br>
                                            Contact Number : <strong>{{ $data['contact'] }}</strong>. <br>
                                            Are updated in your profile. <br>
                                            We're excited to see you here. <br>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block">
                                            TThe login credentials for using the business account are <br>
                                            User Name: <strong> {{ $data['email'] }} </strong> <br>
                                            Password: <strong>{{ $data['password'] }}</strong> <br>
                                            @if ($data['otp'] !== 0)
                                                Your OTP for verification: <strong>{{ $data['otp'] }}</strong> <br>
                                            @endif
                                            Url : www.hairwecut.co.uk/signin
                                            <br>
                                            <br>
                                            <strong>Regards: Team Hair we cut</strong>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer">
                        <table width="100%">
                            <tr>
                                <td class="aligncenter content-block">Follow <a href="#">HAIR WE CUT</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    </table>

</body>

</html>

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
                                            <h4>New Appointment Request</h4>
                                            <p></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block">
                                            Dear Customer, you have received a new appointment request. Here are the details:
                                            <br><br>
                                            Salon: <strong>{{ $data['salon_name'] }}</strong> <br>
                                            From: <strong>{{ \Carbon\Carbon::parse($data['from_time'])->format('g:i A') }}</strong> <br>
                                            To: <strong>{{ \Carbon\Carbon::parse($data['to_time'])->format('g:i A') }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block">
                                            Please log in to your account to view appointment details.
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

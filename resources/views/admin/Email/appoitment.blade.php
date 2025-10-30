<!DOCTYPE html >
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HAIR WE CUT</title>
    <link href="{{asset('admin/email_templates/styles.css')}}" media="all" rel="stylesheet" type="text/css" />
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
                            <table  cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="background-color:#eee;padding:10px;">
                                        <img class="img-responsive" src="{{asset('about-logo2.png')}}" width="300" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="">
                                        {{-- <p></p> --}}
                                        <h4> Dear Customer, {{$data['name']}}</h4>
                                        <p>Your appointment has been created. The booking Date And timing is</p>
                                    </td>
                                </tr>
                                <tr>
                                   
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        Salon : {{$data['salon']}}  <br>
                                        barber : {{$data['barber']}}  <br>
                                        Date : {{$data['date']}}  <br>
                                        Time : {{$data['time']->from_time}}  To  {{$data['time']->to_time}}
                                        appointment Type : {{$data['appType']}}

                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        The customer care will contact you shortly via <strong>{{$data['contact']}}</strong>.
                                    </td>
                                </tr>
                              </table>
                        </td>
                    </tr>
                </table>
                <div class="footer">
                    <table width="100%">
                        <tr>
                            <td class="aligncenter content-block">We'll be happy to see you again and again.
                                Thank you.</td>
                        </tr>
                    </table>
                </div></div>
        </td>
        <td></td>
    </tr>
</table>

</body>
</html>

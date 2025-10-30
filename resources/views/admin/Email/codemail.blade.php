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
        <td class="container" width="600">
            <div class="content">
                <table class="main" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="content-wrap">
                            <table  cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <h1 style="color:#fff;background:#1ab394; padding:20px;"> Product Purchase Code</h1>
                                    </td>
                                </tr>
								 <tr>
                                    <td class="">
                                        <h4>Dear Customer,</h4>
                                    </td>
                                </tr>
                                <tr>
                                   
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <p style="text-align: justify">Your order has just been placed at Hairwecut .
In order to collect your products from nearest Barber please visit the nearest registered Barber shop and show the secret code as identify of your order and collect the products you ordered.
Regards
Team Hairwecut</p>
                                    </td>
                                </tr>
								<tr>
                                    <td class="">
                                        <h1 style="text-align: center">{{$data['code']}}</h1>
                                    </td>
                                </tr>
                              </table>
                        </td>
                    </tr>
                </table>
                <div class="footer">
                    <table width="100%">
                        <tr>
                            <td class="aligncenter content-block">Follow <a href="#">Hair WE Cut</a></td>
                        </tr>
                    </table>
                </div></div>
        </td>
        <td></td>
    </tr>
</table>

</body>
</html>

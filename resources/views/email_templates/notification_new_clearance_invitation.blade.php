<html>

    <head>

        <title>{{ env('APP_COMPANY_NAME') }} New Clearance Invitation</title>

    </head>

    <body>

        <table>

            <tr>

                <td>
                	A new Clearance Invitation was sent to user {{$details->firstname}} {{$details->lastname}} {{$details->email}}<br />
                    Clearance request date: {{$details->crdate}}
                </td>

            </tr>

        </table>

    </body>

</html>

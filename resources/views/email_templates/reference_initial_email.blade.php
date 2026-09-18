<html>

    <head>

        <title>{{ env('APP_COMPANY_NAME') }} Personal Reference Request For {{$details->firstname}} {{$details->lastname}}</title>

    </head>

    <body>

        <table>

            <tr>

                <td>
                    <p>Good {{$details->timeofday}},</p>

                    <p>{{$details->firstname}} {{$details->lastname}} is currently undertaking DBS/BPSS Clearance through us, we will require a personal reference from you in order to continue as you were detailed as such on their forms, please can you fill out the form at the following link: <a href="{{ $details->referenceLink }}">{{ $details->referenceLink }}</a> and make sure to submit this before leaving the page.</p>

                    <p>Many thanks,<br />The Security Vetting team</p>
                </td>

            </tr>

        </table>

    </body>

</html>

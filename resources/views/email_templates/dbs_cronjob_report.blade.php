<html>

    <head>

        <title>{{ env('APP_COMPANY_NAME') }} Cron Job Report</title>

    </head>

    <body>

        <table>

            <tr>

                <td>
                	Dear user,<br />
                    The autorefresh script for DBS status update has just completed succesfully on {{ date("d M Y") }} at {{ date("H:i:s") }}. Total applications processed by DBS since the last autorefresh: {{ $countStatusChange}} <br /><br />
                    
                </td>

            </tr>

        </table>

    </body>

</html>

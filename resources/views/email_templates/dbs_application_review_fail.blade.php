<html>

    <head>

        <title>{{ env('APP_COMPANY_NAME') }} DBS Application Review</title>

    </head>

    <body>

        <table>

            <tr>

                <td>
                	Dear user,<br />
                    Your DBS application was reviewed by one of the administrators and it failed to meet the minimum requirements. As a result you will need to login <a href="{{$loginLink}}" target="_blank">here</a> using the email and password that you have already saved and update your application. <br /><br />
                    If you need further details regarding your application please contact {{ env('APP_COMPANY_NAME') }} at {{ env('APP_CONTACT_EMAIL') }}<br />
                    If you have any issue with your login you can reset your password <a href="{{$loginLink}}forgotPassword" target="_blank">here</a>.
                </td>

            </tr>

        </table>

    </body>

</html>

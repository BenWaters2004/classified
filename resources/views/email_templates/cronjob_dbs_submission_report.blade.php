<html>

    <head>

        <title>{{ env('APP_COMPANY_NAME') }} DBS Submission Report</title>

    </head>

    <body>
        <h2>{{ $dateRangeFrom }}{{ $dateRangeTo }}</h2>
        
        @if(isset($organisationsReport) && count($organisationsReport)>0)
        <table>
            <thead>
                <tr>
                    <th style="border: 1px solid #000;">Organisation</th>
                    <th style="border: 1px solid #000;">Number of Applications</th>
                    <th style="border: 1px solid #000;">Total cost for this period (@&pound;23 / application)</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($organisationsReport as $organisationData)
                <tr>
                  <td style="border: 1px solid #000;">{{$organisationData['organisationName']}}</td>
                  <td style="border: 1px solid #000;">{{$organisationData['numberOfApplications']}}</td>
                  <td style="border: 1px solid #000;">&pound; {{$organisationData['totalCost']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #f00;">No applications have been submitted to DBS for the above date period.</p>
        @endif

        
<br /><p>To view a report for a different date range please login to the online application and select the "DBS Submission Report" in the Organisations category.</p>
    </body>

</html>

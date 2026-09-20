<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333333;
            font-size: 14px;
            line-height: 1.5;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            color: #2C3C64;
        }

        h3 {
            color: #2C3C64;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #dddddd;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f4f4f4;
            color: #2C3C64;
        }

        .success {
            color: #3c763d;
        }

        .warning {
            color: #a94442;
        }

        .summary {
            background: #f7f7f7;
            border-left: 4px solid #C55359;
            padding: 12px 15px;
            margin: 15px 0;
        }

        .footer {
            margin-top: 30px;
            color: #777777;
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Get ClassifIeD Data Retention Purge</h2>

    <p>
        The scheduled candidate data-retention purge ran on
        <strong>
            {{ $runAt->format('d/m/Y \a\t H:i') }}
        </strong>.
    </p>

    <div class="summary">
        <strong>{{ count($purged) }}</strong>
        candidate{{ count($purged) === 1 ? '' : 's' }}
        successfully purged.

        @if(count($failed) > 0)
            <br>

            <strong class="warning">
                {{ count($failed) }}
                candidate{{ count($failed) === 1 ? '' : 's' }}
                could not be purged.
            </strong>
        @endif
    </div>


    @if(count($purged) > 0)

        <h3 class="success">
            Purged candidates
        </h3>

        <table>

            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Email</th>
                    <th>Organisation</th>
                    <th>Completed</th>
                    <th>Retention</th>
                    <th>Retention expired</th>
                </tr>
            </thead>

            <tbody>

                @foreach($purged as $candidate)

                    <tr>
                        <td>
                            {{ $candidate['firstName'] }}
                            {{ $candidate['lastName'] }}
                        </td>

                        <td>
                            {{ $candidate['email'] }}
                        </td>

                        <td>
                            {{ $candidate['organisationName'] }}
                        </td>

                        <td>
                            @if(!empty($candidate['completedDate']))
                                {{ \Carbon\Carbon::parse(
                                    $candidate['completedDate']
                                )->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>

                        <td>
                            {{ $candidate['retentionYears'] }}
                            year{{ $candidate['retentionYears'] === 1 ? '' : 's' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse(
                                $candidate['retentionExpiresAt']
                            )->format('d/m/Y') }}
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif


    @if(count($failed) > 0)

        <h3 class="warning">
            Candidates requiring attention
        </h3>

        <p>
            The following candidates were eligible for deletion,
            but the purge could not be completed. Their data has
            <strong>not been marked as successfully purged</strong>.
            Please check the Laravel application log.
        </p>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Candidate</th>
                    <th>Email</th>
                    <th>Organisation</th>
                </tr>
            </thead>

            <tbody>

                @foreach($failed as $candidate)

                    <tr>
                        <td>
                            {{ $candidate['id'] }}
                        </td>

                        <td>
                            {{ $candidate['firstName'] }}
                            {{ $candidate['lastName'] }}
                        </td>

                        <td>
                            {{ $candidate['email'] }}
                        </td>

                        <td>
                            {{ $candidate['organisationName'] }}
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif


    <div class="footer">
        This email was generated automatically by the
        Get ClassifIeD data-retention process.
    </div>

</div>

</body>
</html>
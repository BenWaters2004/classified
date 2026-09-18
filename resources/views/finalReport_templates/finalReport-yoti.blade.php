@php
    $identityCheck = collect($orderedChecks)->firstWhere('name', 'Digital Identity Verification');
    $state = $yotiReportData['state'] ?? 'N/A';
    $summary = $yotiReportData['identitySummary'] ?? [];
    $documentsData = $yotiReportData['documentsData'] ?? [];
    $watchlistChecks = $yotiReportData['watchlistChecks'] ?? [];
@endphp

<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <bookmark title="Digital Identity Verification" level="0"></bookmark>

    <h3 style="font-size:16px;color:#2C3C64;border-bottom:2px solid #2C3C64;padding-bottom:5px;">
        Digital Identity Verification
    </h3>

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check Status</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ ucfirst($identityCheck['status'] ?? 'N/A') }}</td>
        </tr>
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Session State</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $state }}</td>
        </tr>
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check Completed</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                @if (!empty($identityCheck['updated_at']) && $identityCheck['updated_at'] !== 'N/A')
                    {{ \Carbon\Carbon::parse($identityCheck['updated_at'])->format('d M Y') }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Framework</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $summary['frameworkType'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Scheme</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                {{ $summary['schemeType'] ?? 'N/A' }} - {{ $summary['schemeObjective'] ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Level of Assurance</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $summary['levelOfAssurance'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Requirements Met</td>
            <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                @php $rm = $summary['requirementsMet'] ?? null; @endphp
                {{ $rm === true ? 'Yes' : ($rm === false ? 'No' : 'N/A') }}
            </td>
        </tr>
    </table>

    @if(!empty($documentsData))
        @php $docRows = array_chunk($documentsData, 2); @endphp

        @foreach($docRows as $row)
            <br>
            <table width="100%" style="border-collapse:collapse;table-layout:fixed;">
                <tr>
                    @foreach($row as $docIndex => $doc)
                        <td style="width:50%; vertical-align:top; padding-right:6px; padding-left:6px;">
                            <h4 style="font-size:12px;color:#2C3C64;border-bottom:1px solid #2C3C64;padding-bottom:3px; margin:0 0 6px 0;">
                                {{ $doc['type'] ?? 'ID Document' }} ({{ $doc['issuingCountry'] ?? 'N/A' }})
                            </h4>

                            @php $imgs = $doc['images'] ?? []; @endphp
                            @if(!empty($imgs) && !empty($imgs[0]['src']))
                                <table width="100%" style="border-collapse:collapse;table-layout:fixed;">
                                    <tr>
                                        <td style="padding:2px; vertical-align:top;">
                                            <img src="{{ $imgs[0]['src'] }}" style="width:100%; max-width:85mm; height:auto; border:1px solid #ccc; padding:2px;">
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <p style="font-size:10px;color:#777; margin:4px 0 0 0;">No document page image available.</p>
                            @endif
                        </td>
                    @endforeach

                    {{-- pad odd row with empty right cell --}}
                    @if(count($row) === 1)
                        <td style="width:50%; vertical-align:top; padding-left:6px;"></td>
                    @endif
                </tr>
            </table>
        @endforeach
    @else
        <p style="font-size:11px;color:#777;">No ID documents found for this session.</p>
    @endif

    @if(!empty($watchlistChecks))
        <br><br>
        <h4 style="font-size:13px;color:#2C3C64;border-bottom:1px solid #2C3C64;padding-bottom:3px;">Watchlist Screening</h4>
        <table width="100%" style="border-collapse:collapse;table-layout:fixed;font-size:10px;">
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;"><strong>Status</strong></td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;"><strong>Recommendation</strong></td>
            </tr>
            @foreach($watchlistChecks as $check)
                @php
                    $report = $check->getReport();
                    $rec = ($report && $report->getRecommendation()) ? $report->getRecommendation()->getValue() : 'N/A';
                    $breakdown = $report ? ($report->getBreakdown() ?? []) : [];
                @endphp
                <tr>
                    <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $check->getState() }}</td>
                    <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $rec }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <page_footer>
        <div style="font-size:11px;text-align:center;border-top:1px solid #ccc;padding-top:10px;color:rgba(44,60,100,0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>
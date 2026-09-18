<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <bookmark title="Employment History (5 years)" level="0" ></bookmark>
    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Employment History (5 years)</h3>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check status</td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Complete</td>
            </tr>
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check completed</td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d/m/Y') ?? ' '}}</td>
            </tr>
        </tbody>
    </table>
    <br><br><br>
    @php
    $counter = 0;
    @endphp
    @if (!empty($employment) && count($employment) > 0)
        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
            <tbody>
                @foreach ($employment as $job)
                    @php
                    $counter += 1;
                    @endphp
                    <tr>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555 !important; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">Period Covered [{{$counter}}]</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;"><span style="color: #2C3C64;">Date From: </span>{{ !empty($job->date_from) ? \Carbon\Carbon::parse($job->date_from)->format('d/m/Y') : '' }}</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;"><span style="color: #2C3C64;">Date To: </span>{{ !empty($job->date_to) ? \Carbon\Carbon::parse($job->date_to)->format('d/m/Y') : '' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">Company Name:</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">{{ $job->company_name ?? '' }}</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;"> </td>
                    </tr>
                    <tr>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">Email Address:</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">{{ $job->email_address ?? '' }}</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;"> </td>
                    </tr>
                    <tr>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">Company Address:</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">{{ $job->company_address_line ?? '' }}</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;"> </td>
                    </tr>
                    <tr>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">Town:</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">{{ $job->company_address_town ?? '' }}</td>
                        <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;"><span style="color: #2C3C64;">Postcode: </span>{{ $job->company_address_postcode ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 33.33%; padding-top: 5px; padding-bottom: 5px;">&nbsp;&nbsp;</td>
                        <td style="width: 33.33%; padding-top: 5px; padding-bottom: 5px;">&nbsp;&nbsp;</td>
                        <td style="width: 33.33%; padding-top: 5px; padding-bottom: 5px;">&nbsp;&nbsp;</td>
                    </tr>
                    
                @endforeach
            </tbody>
        </table>
        <p style="color: #595959; font-size: 10px">*All of the above employments, were contacted or confirmed via HRMC/PAYE.</p>

    @else
        <p class="text-muted">No employment listed.</p>
    @endif

    <h3 style="font-size: 14px; color: #2C3C64; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Unemployment period:</h3>
    @if (!empty($unemployment) && count($unemployment) > 0)
        @foreach ($unemployment as $job)
            <div style="color: #595959;"><span style="color: #C55359;">Date From: </span>{{ !empty($job->date_from) ? \Carbon\Carbon::parse($job->date_from)->format('d/m/Y') : '' }}, <span style="color: #C55359;">Date To: </span>{{ !empty($job->date_to) ? \Carbon\Carbon::parse($job->date_to)->format('d/m/Y') : '' }}</div><br>
        @endforeach
    @else
        <p>n/a</p>
    @endif
    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>
@php
    $tickImg = asset('/images/checkbox_checked.png'); // Path to tick image
    $emptyBoxImg = asset('/images/checkbox.png'); // Path to empty box image
@endphp
<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <bookmark title="Academic History (5 years)" level="0" ></bookmark>
    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Academic History (5 years)</h3>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check status</td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Complete</td>
            </tr>
            <tr>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check completed</td>
                <td style="width: 50%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">@if(isset($BPSSApplication->completedDate)){{ \Carbon\Carbon::parse($BPSSApplication->completedDate)->format('d/m/Y') ?? ' '}}@endif</td>
            </tr>
        </tbody>
    </table>
    <br><br><br>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">School @if(isset($BPSSApplication->school_data))<img src="{{ $BPSSApplication->school_data == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox">@endif</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">College @if(isset($BPSSApplication->college_data))<img src="{{ $BPSSApplication->college_data == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox">@endif</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">University @if(isset($BPSSApplication->university_data))<img src="{{ $BPSSApplication->university_data == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox">@endif</td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Name: {{ $BPSSApplication->school_name ?? ' ' }}</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Name: {{ $BPSSApplication->college_name ?? ' ' }}</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Name: {{ $BPSSApplication->university_name ?? ' ' }}</td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Date From: @if(isset($BPSSApplication->school_date_from)){{ $BPSSApplication->school_date_from ? \Carbon\Carbon::parse($BPSSApplication->school_date_from)->format('d M Y') : '' }}@endif</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Date From: @if(isset($BPSSApplication->college_date_from)){{ $BPSSApplication->college_date_from ? \Carbon\Carbon::parse($BPSSApplication->college_date_from)->format('d M Y') : '' }}@endif</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Date From: @if(isset($BPSSApplication->university_date_from)){{ $BPSSApplication->university_date_from ? \Carbon\Carbon::parse($BPSSApplication->university_date_from)->format('d M Y') : '' }}@endif</td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Date To: @if(isset($BPSSApplication->school_date_to)){{ $BPSSApplication->school_date_to ? \Carbon\Carbon::parse($BPSSApplication->school_date_to)->format('d M Y') : '' }}@endif</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Date To: @if(isset($BPSSApplication->college_date_to)){{ $BPSSApplication->college_date_to ? \Carbon\Carbon::parse($BPSSApplication->college_date_to)->format('d M Y') : '' }}@endif</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Date To: @if(isset($BPSSApplication->university_date_to)){{ $BPSSApplication->university_date_to ? \Carbon\Carbon::parse($BPSSApplication->university_date_to)->format('d M Y') : '' }}@endif</td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Contact Name: {{ $BPSSApplication->school_contact_name ?? ' ' }}</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Contact Name: {{ $BPSSApplication->college_contact_name ?? ' ' }}</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Contact Email: {{ $BPSSApplication->university_contact_email ?? ' ' }}</td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Contact Address: {{ $BPSSApplication->school_contact_address ?? ' ' }}</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Contact Address: {{ $BPSSApplication->college_contact_address ?? ' ' }}</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #2C3C64; padding: 5px; border: 1px solid #555555;">Contact Address: {{ $BPSSApplication->university_contact_number ?? ' ' }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p style="color: #595959; font-size: 10px">*All of the above academic contacts, were contacted to confirm the given dates.</p>

    

    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>
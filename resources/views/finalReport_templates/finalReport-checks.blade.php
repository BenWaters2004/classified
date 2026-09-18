@php

if (!function_exists('CheckColorCode')) {
    function CheckColorCode($status) {
        return [
            'complete' => '#4EA72E',
            'Certificate contains no information' => '#4EA72E',
            'pending' => '#E97132',
            'pending review' => '#E97132',
            'pending submission' => '#E97132',
            'submitted to DBS' => '#E97132',
            'DBS processing' => '#E97132',
            'failed' => '#FF0000',
            'review fail' => '#FF0000',
            'DBS fail' => '#FF0000',
            'incomplete' => '#2C3C64',
            'not started' => '#2C3C64'
        ][$status] ?? '#2C3C64';
    }
}

@endphp

<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Checks</h3>

    @if (!empty($orderedChecks) && count($orderedChecks) > 0)
        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
            <tbody>
                <tr>
                    <td style="width: 40%; text-align: center; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;">Check Type</td>
                    <td style="width: 20%; text-align: center; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;">Status</td>
                    <td style="width: 40%; text-align: center; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;">Additional Information</td>
                </tr>
                @foreach ($orderedChecks as $check)
                    <tr>
                        <td style="width: 40%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">{{ $check['name'] }}</td>
                        <td style="width: 20%; text-align: center; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px; color: {{ CheckColorCode($check['status']) }};">{{ ucfirst($check['status']) }}</td>
                        <td style="width: 40%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No checks ordered yet.</p>
    @endif
    <br>
    <h3 style="font-size: 14px; color: #2C3C64; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Additional Information</h3>
    <p>{!! nl2br(e($userDetails->admin_approval_notes ?? ' ')) !!}</p>
    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>
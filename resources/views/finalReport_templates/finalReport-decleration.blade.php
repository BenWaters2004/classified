<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td colspan="3" style="width: 100%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;">
                    Security Declaration
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width: 100%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #C55359;">
                    I declare that the information I have given on this form is true and complete to the best of my knowledge and belief. I understand that any false information or deliberate omission in the information I have given on this form may disqualify me for employment in connection with Government contracts.<br><br>
                    I give permission to Bluescreen IT to confirm factual information from previous/current employer(s), covering the last 5 (permanent employee) / 3 (contractor/temporary worker) years of my employment, that I have disclosed in part 3 of this form. I undertake to notify any material changes in the information I have given to the HR or Security Branch concerned.
                </td>
            </tr>
            <tr>
                <td style="width: 30%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;"><strong>Is Consent Provided:</strong></td>
                <td style="width: 20%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">Yes</td>
                <td style="width: 50%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;"><strong>Consented Responsible Body Email:</strong><br><br>{{$responsibleEmail->settingValue}}</td>
            </tr>
            <tr>
                <td style="width: 30%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;"><strong>Date Signed:</strong></td>
                <td colspan="2" style="width: 70%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">{{$userDetails->dbs_consent_date ?? ' '}}</td>
            </tr>
        </tbody>
    </table>

    <br><br><br>

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td colspan="4" style="width: 100%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #C55359; text-align: center;">
                    I certify that in accordance with Blue Screen IT’s standards: I have personally examined the documents listed above and have satisfactorily established the details listed.
                </td>
            </tr>
            <tr>
                <td style="width: 20%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">Name:</td>
                <td style="width: 30%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">{{ $completedAdmin->title ?? ' ' }} {{ $completedAdmin->firstName ?? ' ' }} {{ $completedAdmin->lastName ?? ' ' }}</td>
                <td style="width: 20%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">Post:</td>
                <td style="width: 30%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">{{ $completedAdmin->position ?? ' ' }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">Date:</td>
                <td style="width: 30%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">{{ !empty($userDetails->completedDate) ? \Carbon\Carbon::parse($userDetails->completedDate)->format('d/m/Y') : '' }}</td>
                <td style="width: 20%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">Telephone Number:</td>
                <td style="width: 30%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">{{ $completedAdmin->phoneNumber ?? ' ' }}</td>
            </tr>
            <tr>
                <td colspan="4" style="width: 100%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #2C3C64;">
                    Document was signed electronically by {{ $completedAdmin->title ?? ' ' }} {{ $completedAdmin->firstName ?? ' ' }} {{ $completedAdmin->lastName ?? ' ' }} on @if(isset($userDetails->completedDate) && date('Y-m-d',strtotime($userDetails->completedDate)) != '1970-01-01'){{\Carbon\Carbon::parse($userDetails->completedDate)->format('d/m/Y')}}@endif 
                    @if(isset($completedAdmin->signature_path))
                        <img src="{{ env('APP_URL') }}/uploads/user_signatures/{{ str_replace(' ', '', strtolower($completedAdmin->signature_path)) }}" style="height: 25px;">
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <br><br>
    <p style="text-align: center; color: #595959; font-size: 11px;">End of report</p>
    @if ($isCollins)
        <page_footer>
            <div style="font-size: 8px;">
                <p>ClassifIeD is trading name of BluescreenIT LTD. All information in this document has been provided to BluescreenIT LTD by a third party and we cannot therefore be held liable for any inaccuracies or omissions contained within it.</p><br>
                <p style="line-height: 18px;" ><span style="font-size: 10px; color: #2C3C64;">BluescreenIT LTD</span><br>
                Plymouth Science Park, 1 Davy Rd, Plymouth, Devon PL6 8BX<br>
                Tel: +44 (0)1752 724 000   Email:<a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a>   Web: <a href="ttps://classified.getclassified.co.uk/">https://classified.getclassified.co.uk/</a><br>
                BluescreenIT LTD is registered in England and Wales with RO number 89045885006<br>
                Copyright © 2025 BluescreenIT LTD. All rights reserved</p><br>
                <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
                    Page {{$pageNumber}}
                </div>
            </div>
        </page_footer>
    @endif
</page>

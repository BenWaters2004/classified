@php
    $tickImg = asset('/images/checkbox_checked.png'); // Path to tick image
    $emptyBoxImg = asset('/images/checkbox.png'); // Path to empty box image
@endphp
<page backtop="20mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <page_header>
        <div style="text-align: right; font-size: 10px; color: #2C3C64;"> {{$userDetails->forename}} {{$userDetails->presentSurname}}</div>
    </page_header>

    <div style="margin-top: 5px; margin-left: 0px; text-align: left;">

    @if (!empty($userDetails->logo))
        <img src="{{ storage_path('app/logos/' . $userDetails->logo) }}" style="width: 500px;" alt="Company Logo">
    @else
        <img src="{{ env('APP_URL') }}images/classified-report.png" style="width: 8cm;" alt="ClassifIeD Logo">
    @endif
</div>


    <h1 style="color: #C55359; text-align: left; margin-top: 30px; font-size: 35px;">Final Report</h1>

    <p style="font-size: 12px; line-height: 18px;"><strong>Approved Access number:</strong> {{$userDetails->applicationCode}} <br>
    <strong>Candidate name:</strong> {{$userDetails->forename}} {{$userDetails->presentSurname}}<br>
    <strong>Client name:</strong> {{$userDetails->organisationName}}</p>

    <div style="border: 2px solid #2C3C64; padding: 2px; width: 184px; margin-top: 15px;">
        <div style="border: 2px solid #2C3C64; padding: 10px; width: 180px;">
            <div style="font-size: 12px; color: #2C3C64; line-height: 18px;"><strong>Issue date:</strong> {{ \Carbon\Carbon::parse($userDetails->completedDate ?? '')->format('d M Y') ?? ' ' }}<br>
            <strong>Expiry:</strong> 
                <strong>Expiry:</strong> 
                @php
                    $isContractor = ($BPSSApplication->employement_type ?? '') === 'contractor';
                    $isCargo = ($collinsApproval->cargo ?? 0) == 1;

                    $expiryYears = ($isContractor || $isCargo) ? $userDetails->contractor_renewal_years : $userDetails->permanent_employee_renewal_years;
                @endphp
                {{ \Carbon\Carbon::parse($userDetails->completedDate)->addYears($expiryYears)->format('d M Y') }}</div>
        </div>
    </div>

    @if ($isCollins)
        <br /><br /><br />
        <table  width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 100%; padding-left: 5px; padding-right: 5px; font-size: 18px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; color: #C55359; text-align: center;"><strong>APPROVAL FOR ACCESS</strong><br>(To be completed by the Site Security Controller)</td>
                </tr>
            </tbody>
        </table>
        <p style="color: #C55359; text-align: center; font-size: 11px;">I certify that in accordance with the requirements of Baseline Security Personnel Standard that I, the undersigned have personally examined the documents provided as evidence and am satisfied meets the BPSS requirements as specified by the cabinet office.</p>

        <!-- Approve / Decline Section -->
        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; margin-bottom: 10px;">
            <tbody>
                <tr>
                    <td style="width: 50%; text-align: center; padding: 10px; font-size: 18px;">
                        <img src="{{ ($collinsApproval->approval ?? '') == 1 ? $tickImg : $emptyBoxImg }}" style="width: 18px;" alt="Checkbox"> 
                        <strong>Approved</strong>
                    </td>
                    <td style="width: 50%; text-align: center; padding: 10px; font-size: 18px;">
                        <img src="{{ ($collinsApproval->approval ?? '') == 0 ? $tickImg : $emptyBoxImg }}" style="width: 18px;" alt="Checkbox"> 
                        <strong>Denied</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
            <tbody>
                <tr>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        Name:
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        {{ $collinsApproval->adminName ?? ''}}
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        Title:
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        {{ $collinsApproval->adminTitle ?? ''}}
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        Signature:
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        Document was signed electronically
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        Date:
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                    {{ !empty($collinsApproval->updated_at) ? \Carbon\Carbon::parse($collinsApproval->updated_at)->format('d/m/Y') : '' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="width: 100%; height: 100px; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555;">
                        <strong>Notes:</strong> {!! nl2br(e($userDetails->admin_approval_notes ?? ' ')) !!}
                    </td>
                </tr>
                <tr>
                    <td style="background: #eee; border: 1px solid #555555; padding: 7px;">
                        <img src="{{ ($BPSSApplication->employement_type ?? '') !== 'contractor' ? $tickImg : $emptyBoxImg }}" style="width: 15px;"> Employee (10 Years)
                    </td>
                    <td style="background: #eee; border: 1px solid #555555; padding: 7px;">
                        <img src="{{ ($BPSSApplication->employement_type ?? '') === 'contractor' ? $tickImg : $emptyBoxImg }}" style="width: 15px;"> Contractor (3 Years)
                    </td>

                    <!-- Initial Clearance / Revalidation Section -->
                    <td style="background: #eee; border: 1px solid #555555; padding: 7px;">
                        <img src="{{ ($userDetails->REVALonsite ?? 0) == 0 && ($userDetails->REVALoffsite ?? 0) == 0 && ($BPSSApplication->form_type ?? '') !== 'revalidation' ? $tickImg : $emptyBoxImg }}" style="width: 15px;"> Initial Clearance
                    </td>
                    <td style="background: #eee; border: 1px solid #555555; padding: 7px;">
                        <img src="{{ ($userDetails->REVALonsite ?? 0) == 1 || ($userDetails->REVALoffsite ?? 0) == 1 || ($BPSSApplication->form_type ?? '') === 'revalidation' ? $tickImg : $emptyBoxImg }}" style="width: 15px;"> Revalidation
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;"> Site:</td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        {{ $userDetails->organisationName ?? '' }}
                    </td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">Known Cargo:</td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">
                        <img src="{{ ($collinsApproval->cargo ?? 0) == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Cargo
                        &nbsp;&nbsp;
                        <img src="{{ ($collinsApproval->cargo ?? 0) == 0 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Non-cargo
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">Contractor Company:</td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">{{ $BPSSApplication->contractor_name_of_company ?? '' }}</td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">Position:</td>
                    <td style="width: 25%; padding-left: 5px; padding-right: 5px; font-size: 11px; word-break: break-word; white-space: normal; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #eee;">{{ $BPSSApplication->contractor_position ?? '' }}</td>
                </tr>
            </tbody>
        </table>
        <page_footer>
            <div style="font-size: 11px; text-align: center; border-top: 1px solid #eee; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
                Page {{$pageNumber}}
            </div>
        </page_footer>
    @else
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
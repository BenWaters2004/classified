@php
    $tickImg = asset('/images/checkbox_checked.png'); // Path to tick image
    $emptyBoxImg = asset('/images/checkbox.png'); // Path to empty box image
@endphp
<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <bookmark title="Candidate Details" level="0" ></bookmark>
    @if ($userDetails->bpssApplication > 0)
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Part 1. Candidate Details</h3>
    @else
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Candidate Details</h3>
    @endif

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Present Surname:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->presentSurname ?? ' '}}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Present Forename:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->forename ?? ' '}}</td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Previous Surname:<br><span style="color: #2C3C64;">If Applicable</span></td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->other_surname ?? ' ' }}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Previous Forename:<br><span style=" color: #2C3C64;">If Applicable</span></td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->other_forename ?? ' ' }}</td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Contact Number:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->contact_number ?? ' '}}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">AA-Number:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->applicationCode ?? ' '}}</td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Address Line 1:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->address_line_1 ?? ' '}}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Date Added:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ !empty($createdOn) ? \Carbon\Carbon::parse($createdOn)->format('d/m/Y') : '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Line 2:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->address_line_2 ?? '' }}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Status:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Complete</td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Line 3:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->address_town ?? ' '}}, {{ $userDetails->address_county ?? ' '}}, {{ $userDetails->address_country ?? ' '}}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Final Report Issued:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ \Carbon\Carbon::parse($userDetails->completedDate ?? '')->format('d M Y') ?? ' ' }}</td>
            </tr>
        </tbody>
    </table>

    @if ($userDetails->bpssApplication > 0)
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; padding-bottom: 5px;">Part 2. Nationality and Immigration Status</h3>

        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
            <tbody>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Date of Birth:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ \Carbon\Carbon::parse($userDetails->dob ?? '')->format('d M Y') ?? ' ' }}</td>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Town of Birth:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->birth_town ?? ' '}}</td>
                </tr>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Country of Birth:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->birth_country_fullName ?? ' '}}</td>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Nationality:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->present_nationality ?? ' '}}</td>
                </tr>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Dual Nationality:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                        @if (isset($BPSSApplication->dual_citizenship))
                            @if($BPSSApplication->dual_citizenship == 1)
                                @foreach ($dualNationality as $extraNationality)
                                    {{$extraNationality->country}}, 
                                @endforeach
                            @endif
                        @endif</td>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Former Nationality:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->former_nationality_details ?? ' '}}</td>
                </tr>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">British Nationalisation Certificate No:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->naturalisation_certificate_number ?? ' '}}</td>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Date Issued:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                        @if (isset($BPSSApplication->naturalisation_certificate_date))
                            @if(date("Y-m-d", strtotime($BPSSApplication->naturalisation_certificate_date)) !='1970-01-01')
                                {{date("d/m/Y", strtotime($BPSSApplication->naturalisation_certificate_date))}}
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Dual Citizenship:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                        <img src="{{ ($BPSSApplication->dual_citizenship ?? 0) == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Yes
                        &nbsp;&nbsp;
                        <img src="{{ isset($BPSSApplication->dual_citizenship) && empty($BPSSApplication->dual_citizenship) ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> No
                    </td>
                    <td style="width: 25%; font-weight: bold; #555555; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Lawfully in the UK:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">
                        <img src="{{ ($BPSSApplication->lawfully_resident_in_uk ?? 0) == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Yes
                        &nbsp;&nbsp;
                        <img src="{{ isset($BPSSApplication->lawfully_resident_in_uk) && empty($BPSSApplication->lawfully_resident_in_uk) ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> No
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Restrictions on continued residence in the UK:<br>
                        <img src="{{ ($BPSSApplication->continued_residence_restrictions ?? 0) == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Yes
                        &nbsp;&nbsp;
                        <img src="{{ isset($BPSSApplication->continued_residence_restrictions) && empty($BPSSApplication->continued_residence_restrictions) ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> No
                    </td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->continued_residence_restrictions_details ?? ' '}}</td>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Subject to immigration Control:<br>
                        <img src="{{ ($BPSSApplication->subject_to_immigration_control ?? 0) == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Yes
                        &nbsp;&nbsp;
                        <img src="{{ isset($BPSSApplication->subject_to_immigration_control) && empty($BPSSApplication->subject_to_immigration_control) ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> No
                    </td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->subject_to_immigration_control_details ?? ' '}}</td>
                </tr>
                <tr>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Restrictions on continued freedom to take employment in the UK:<br>
                        <img src="{{ ($BPSSApplication->freedom_to_take_employment ?? 0) == 1 ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> Yes
                        &nbsp;&nbsp;
                        <img src="{{ isset($BPSSApplication->freedom_to_take_employment) && empty($BPSSApplication->freedom_to_take_employment) ? $tickImg : $emptyBoxImg }}" style="width: 15px;" alt="Checkbox"> No
                    </td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->freedom_to_take_employment_details ?? ' '}}</td>
                    <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Home Office / Port Reference Number:</td>
                    <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$BPSSApplication->ho_port_reference_number ?? ' '}}</td>
                </tr>
            </tbody>
        </table>
    @endif
    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; padding-bottom: 5px;">Added By</h3>

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Site Admin:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $siteAdmin->firstName ?? ' '}} {{ $siteAdmin->lastName ?? ' '}}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Client Name:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->organisationName}}</td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Admin Email:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $siteAdmin->email ?? ' ' }}</td>
                <td style="width: 25%; font-weight: bold; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Admin Tel:</td>
                <td style="width: 25%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $siteAdmin->phoneNumber ?? ' ' }}</td>
            </tr>
        </tbody>
    </table>
    @if ($userDetails->bpssApplication > 0)
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Part 2a. Verification of Identity</h3>
    @else
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Verification of Identity</h3>
    @endif

    @if (isset($BPSSVR_identity_documents) && count($BPSSVR_identity_documents)>0)
        <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
            <tbody>
                <tr>
                    <td style="width: 5%; text-align: center; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;"> </td>
                    <td style="width: 55%; text-align: center; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;">Document</td>
                    <td style="width: 40%; text-align: center; font-size: 14px; word-break: break-word; white-space: normal; color: black; padding-top: 7px; padding-bottom: 7px; border: 1px solid #555555; background-color: #ccc;">Date of Issue</td>
                </tr>
                @php $docCount = 0; @endphp
                @foreach ($BPSSVR_identity_documents as $document)
                    @php $docCount++; @endphp
                    <tr>
                        <td style="width: 5%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px; text-align: center;">{{ $docCount }}</td>
                        <td style="width: 55%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px; ">{{ $document->document_name }}</td>
                        <td style="width: 40%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; font-size: 12px;">
                            @if(isset($document->document_date_of_issue_name) && date('Y-m-d',strtotime($document->document_date_of_issue_name)) != '1970-01-01'){{\Carbon\Carbon::parse($document->document_date_of_issue_name)->format('d/m/Y')}}@endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No Documents listed.</p>
    @endif

    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>

@php
    $tickImg = asset('/images/checkbox_checked.png'); // Path to tick image
    $emptyBoxImg = asset('/images/checkbox.png'); // Path to empty box image
@endphp
<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td style="width: 100%; padding-left: 10px; padding-right: 10px; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 10px; padding-bottom: 10px; border: 1px solid #555555; background-color: #ccc; font-size: 14px;">Criminal Record Declaration</td>
            </tr>
            <tr>
                <td style="width: 100%; padding-left: 10px; padding-right: 10px; word-break: break-word; white-space: normal; color: black; padding-top: 10px; padding-bottom: 10px; border: 1px solid #555555;">
                    <strong>Guidance Note: </strong>
                    The Company has Government contracts, some or all of which require the company to hold material or information, which is the property of the Government. The company has a duty to protect these assets while in its possession and this obligation extends to its employees and agents. Since you are or may become such a person please complete the following sections.<br><br>
                    Please answer the following questions honestly. In addition to your self-declaration below, a check against the National Collection of Criminal Records will be undertaken and documentary evidence sought to confirm your answers in the form of Police Act Disclosure which will be paid for by Bluescreen IT. By signing the declaration in part 5 you are giving us permission to do so.<br><br>
                    The information you give will be treated in strict confidence.<br><br>
                    Have you ever been convicted or found guilty by a court of any offence in any country (excluding parking but including all motoring offences even where a spot fine has been administrated by the police) or have you ever been put on probation (probation orders are now called community rehabilitation orders) or absolutely/conditionally discharged or bound over after being charged with any offence or is there any action pending against you? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).<br><br>

                    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                        <tbody>
                            <tr>
                                <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="@if(isset($BPSSApplication->convicted_by_court)){{ $BPSSApplication->convicted_by_court == 1 ? $tickImg : $emptyBoxImg }}@endif" style="width: 15px;" alt="Checkbox"> 
                                    Yes
                                </td>
                                <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="@if(isset($BPSSApplication->convicted_by_court)){{ $BPSSApplication->convicted_by_court == 0 ? $tickImg : $emptyBoxImg }}@endif" style="width: 15px;" alt="Checkbox"> 
                                    No
                                </td>
                                <td style="width: 60%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="{{$tickImg}}" style="width: 15px;" alt="Checkbox"> (as applicable - if yes, please give details on the following page)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br><br>
                        Have you ever been convicted by a Court Martial or sentenced to detention or dismissal whilst serving in the Armed Forces of the UK or any Commonwealth or foreign country? You need not declare convictions which are "spent" under the Rehabilitation of Offenders Act (1974).
                    <br><br>
                    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                        <tbody>
                            <tr>
                                <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="@if(isset($BPSSApplication->convicted_by_court_martial)){{ $BPSSApplication->convicted_by_court_martial == 1 ? $tickImg : $emptyBoxImg }}@endif" style="width: 15px;" alt="Checkbox"> 
                                    Yes
                                </td>
                                <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="@if(isset($BPSSApplication->convicted_by_court_martial)){{ $BPSSApplication->convicted_by_court_martial == 0 ? $tickImg : $emptyBoxImg }}@endif" style="width: 15px;" alt="Checkbox"> 
                                    No
                                </td>
                                <td style="width: 60%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="{{$tickImg}}" style="width: 15px;" alt="Checkbox"> (as applicable - if yes, please give details on the following page)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br><br>
                    Do you know of any other matters in your background which might cause your reliability or suitability to have access to government assets to be called into question?
                    <br><br>
                    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                        <tbody>
                            <tr>
                                <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="@if(isset($BPSSApplication->background_reliability)){{ $BPSSApplication->background_reliability == 1 ? $tickImg : $emptyBoxImg }}@endif" style="width: 15px;" alt="Checkbox"> 
                                    Yes
                                </td>
                                <td style="width: 20%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <img src="@if(isset($BPSSApplication->background_reliability)){{ $BPSSApplication->background_reliability == 0 ? $tickImg : $emptyBoxImg }}@endif" style="width: 15px;" alt="Checkbox"> 
                                    No
                                </td>
                                <td style="width: 60%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                <img src="{{$tickImg}}" style="width: 15px;" alt="Checkbox"> (as applicable - if yes, please give details on the following page)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br><br>
                    If you answered YES to any of the questions on this form, please give details bellow:
                    <br><br>
                    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                        <tbody>
                            <tr>
                                <td style="width: 100%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    <strong>Details:</strong> <span style="color: #C55359;">please list bellow if applicable</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 100%; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                    {{ $BPSSApplication->convictions_details ?? ' ' }}
                                    <br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br><br>
                </td>
            </tr>
        </tbody>
    </table>
    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>
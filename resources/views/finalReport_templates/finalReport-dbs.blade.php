<page backtop="0mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <bookmark title="UK Criminal Record (Basic, England & Wales)" level="0" ></bookmark>

    @if ($userDetails->bpssApplication > 0)
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Part 4. UK Criminal Record (Basic, England & Wales)</h3>
    @else
        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">UK Criminal Record (Basic, England & Wales)</h3>
    @endif

    <p style="color: #808080; font-size: 11px;"><strong>* This is not a certificate issued by DBS</strong></p>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
        <tbody>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check status</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->dbsResponse_int023_DisclosureIssueDate ? 'Complete' : ' ' }}
                </td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->dbsResponse_int023_DisclosureStatus  ?? ' '}}</td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Check Completed</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{ $userDetails->dbsResponse_int023_DisclosureIssueDate ? \Carbon\Carbon::parse($userDetails->dbsResponse_int023_DisclosureIssueDate)->format('d/m/Y') : '' }}
                </td>
                <td style="width: 33.33%; border-top: 1px solid #555555; border-bottom: 1px solid #555555;"></td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Application Reference</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->dbsResponse_int022_DBSApplicationFormReference  ?? ' '}}</td>
                <td style="width: 33.33%; border-top: 1px solid #555555; border-bottom: 1px solid #555555;"></td>
            </tr>
            <tr>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #C55359; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">Cert No</td>
                <td style="width: 33.33%; word-break: break-word; white-space: normal; color: #595959; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #555555; border-bottom: 1px solid #555555;">{{$userDetails->dbsResponse_int023_DisclosureNumber  ?? ' '}}</td>
                <td style="width: 33.33%; border-top: 1px solid #555555; border-bottom: 1px solid #555555;"></td>
            </tr>
        </tbody>
    </table>

    <br><br>
    <h3 style="font-size: 12px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">Consent Declaration</h3>
    <div style="font-size: 11px;">
        @if($userDetails->terms_accepted)
            Terms accepted on <strong>{{date("D jS M Y @ H:i:s", strtotime($userDetails->terms_accepted_date))}}</strong><br><br>
            <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                <tbody>
                    <tr>
                        <td style="width: 100%; padding-left: 10px; padding-right: 10px; word-break: break-word; white-space: normal; color: #2C3C64; padding-top: 10px; padding-bottom: 10px; border: 1px solid #555555; background-color: #ccc;">DBS Declaration</td>
                    </tr>
                    <tr>
                        <td style="width: 100%; padding-left: 10px; padding-right: 10px; word-break: break-word; white-space: normal; color: black; padding-top: 10px; padding-bottom: 10px; border: 1px solid #555555;">
                            <strong>Privacy Policy - basics check declaration: </strong><br><br>
                            I have read the Basic DBS Check Processing Privacy Policy <a href="https://www.gov.uk/government/publications/dbs-privacy-policies">https://www.gov.uk/government/publications/dbs-privacy-policies</a> and I understand how DBS will process my personal data.
                            <br><br>
                            <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 70%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                            Applicant must consent by typing I CONFIRM in box A 
                                        </td>
                                        <td style="width: 30%; color: #2C3C64; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                            A: I CONFIRM
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br><br>
                            By confirming your acceptance of the below declaration, you are giving us consent to receive an e-result regarding your basic DBS application. If consent is not given you have the option to complete a basic check via <a href="www.gov.uk/DBS">www.gov.uk/DBS</a>. 
                            <br><br><br>
                            <strong>Consent to obtain basic check electronic result </strong><br><br>
                            I consent to the DBS providing an electronic result directly to the responsible organisation that has submitted my application. I understand that an electronic result contains a message that indicates either the certificate does not contain criminal record information or to await certificate which will indicate that my certificate contains criminal record information. In some cases the responsible organisation may provide this information directly to my employer prior to me receiving my certificate. I understand if I do not consent to an electronic result being issued to the responsible organisation submitting my application that I must not proceed with this application and I should apply directly to DBS <a href="https://www.gov.uk/request-copy-criminal-record">Request a basic DBS check - GOV.UK (www.gov.uk)</a> I understand that to withdraw my consent whilst my application is in progress I must contact the DBS helpline 03000 200 190. My application will then be withdrawn.
                            <br><br>
                            <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 70%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                            Applicant must consent by typing I AGREE in box B
                                        </td>
                                        <td style="width: 30%; color: #2C3C64; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                            B: I AGREE
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br><br>
                            As the applicant you must explicitly confirm that you have provided complete and true information in support of this application.
                            <br><br><br>
                            <strong>Declaration By Applicant</strong><br><br>
                            I have provided complete and true information in support of the application, and I understand that knowingly making a false statement for this purpose is a criminal offence.
                            <br><br>
                            <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 11px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 70%; color: #C55359; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                            Applicant must consent by typing I CONFIRM in box C 
                                        </td>
                                        <td style="width: 30%; color: #2C3C64; border: 1px solid #555555; padding-left: 5px; padding-right: 5px; padding-top: 7px; padding-bottom: 7px;">
                                            C: I CONFIRM
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br><br>
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <span class="color: red;">Terms not accepted yet!</span>
        @endif
    </div>


    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{$pageNumber}}
        </div>
    </page_footer>
</page>
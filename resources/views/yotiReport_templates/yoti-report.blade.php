{{-- resources/views/yotiReport_templates/yoti-report.blade.php --}}

@php
    // -----------------------------
    // SAFE HELPERS
    // -----------------------------
    $safeStr = function ($v, $fallback = 'N/A') {
        if ($v === null) return $fallback;
        $s = trim((string)$v);
        return $s === '' ? $fallback : $s;
    };

    $fmtDate = function ($v, $format = 'd/m/Y H:i', $fallback = 'N/A') {
        if (empty($v)) return $fallback;
        try { return \Carbon\Carbon::parse($v)->format($format); } catch (\Throwable $e) { return $fallback; }
    };

    $fmtDateOnly = function ($v, $format = 'd M Y', $fallback = ' ') {
        if (empty($v)) return $fallback;
        try { return \Carbon\Carbon::parse($v)->format($format); } catch (\Throwable $e) { return $fallback; }
    };

    $fmtDob = function ($v) {
        if (empty($v)) return 'N/A';
        try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return 'N/A'; }
    };

    $safeJson = function ($v) {
        try { return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); } catch (\Throwable $e) { return ''; }
    };

    $isList = function ($arr): bool {
        if (!is_array($arr)) return false;
        $keys = array_keys($arr);
        return $keys === range(0, count($arr) - 1);
    };

    $flattenFields = function ($fields) use ($safeStr) {
        if (!is_array($fields)) return [];
        if (isset($fields['document_fields']) && is_array($fields['document_fields'])) {
            $flat = [];
            foreach ($fields['document_fields'] as $f) {
                if (!is_array($f)) continue;
                $k = $f['field_type'] ?? $f['type'] ?? $f['name'] ?? null;
                $v = $f['value'] ?? $f['parsed'] ?? $f['text'] ?? $f['content'] ?? null;
                if ($k && $v !== null) $flat[(string)$k] = $v;
            }
            if (!empty($flat)) return $flat;
        }
        return $fields;
    };

    // -----------------------------
    // HEADER VALUES
    // -----------------------------
    $levelOfAssurance = $identitySummary['levelOfAssurance'] ?? 'N/A';
    if (is_string($levelOfAssurance) && $levelOfAssurance !== 'N/A') {
        $levelOfAssurance = ucwords(strtolower(str_replace('_', ' ', $levelOfAssurance)));
    }

    $frameworkType = $identitySummary['frameworkType'] ?? 'N/A';

    $dbsObjective = $identitySummary['dbsObjective'] ?? 'N/A';
    $dbsMet = $identitySummary['dbsRequirementsMet'] ?? null;
    $rtwMet = $identitySummary['rtwRequirementsMet'] ?? null;

    $dbsText = $dbsMet === true ? 'Yes' : ($dbsMet === false ? 'No' : 'N/A');
    $rtwText = $rtwMet === true ? 'Yes' : ($rtwMet === false ? 'No' : 'N/A');

    $candidateName = $candidateSummary['fullName'] ?? 'N/A';
    $candidateDob  = $fmtDob($candidateSummary['dob'] ?? null);
    $candidateAddress = $candidateSummary['address'] ?? 'N/A';

    $createdAtFormatted   = $fmtDate($createdAt ?? null, 'd/m/Y H:i');
    $completedAtFormatted = $fmtDate($completedAt ?? null, 'd/m/Y H:i');

    $nameCmp = $identityComparison['name'] ?? ['db'=>'N/A','yoti'=>'N/A','match'=>null,'message'=>'No data'];
    $dobCmp  = $identityComparison['dob'] ?? ['db'=>'N/A','yoti'=>'N/A','match'=>null,'message'=>'No data'];
    $addrCmp = $identityComparison['address'] ?? ['db'=>'N/A','yoti'=>'N/A','match'=>null,'message'=>'No data'];

    $badgeColor = function ($match) {
        if ($match === true) return '#0f7b39';
        if ($match === false) return '#a12222';
        return '#536277';
    };

    $badgeBg = function ($match) {
        if ($match === true) return '#e7f7ee';
        if ($match === false) return '#fdecec';
        return '#eef2f7';
    };

    // Green if pass/done/approve, red otherwise, neutral for N/A/empty
    $resultTone = function ($value) {
        if ($value === null) return ['#536277', '#eef2f7'];
        $txt = strtolower(trim((string)$value));
        if ($txt === '' || $txt === 'n/a' || $txt === 'na' || $txt === 'null' || $txt === '-') {
            return ['#536277', '#eef2f7'];
        }
        if (str_contains($txt, 'pass') || str_contains($txt, 'done') || str_contains($txt, 'approve')) {
            return ['#0f7b39', '#e7f7ee'];
        }
        return ['#a12222', '#fdecec'];
    };

    $getCheckRecommendation = function ($check) {
        try {
            if (!method_exists($check, 'getReport')) return null;
            $report = $check->getReport();
            if (!$report || !method_exists($report, 'getRecommendation')) return null;
            $rec = $report->getRecommendation();
            if (!$rec || !method_exists($rec, 'getValue')) return null;
            return $rec->getValue();
        } catch (\Throwable $e) {
            return null;
        }
    };

    $getCheckBreakdown = function ($check) {
        try {
            if (!method_exists($check, 'getReport')) return [];
            $report = $check->getReport();
            if (!$report || !method_exists($report, 'getBreakdown')) return [];
            $bd = $report->getBreakdown();
            return is_array($bd) ? $bd : [];
        } catch (\Throwable $e) {
            return [];
        }
    };

    $getCheckState = function ($check) {
        try { return method_exists($check, 'getState') ? $check->getState() : null; } catch (\Throwable $e) { return null; }
    };

    $getCheckType = function ($check) {
        try { return method_exists($check, 'getType') ? $check->getType() : null; } catch (\Throwable $e) { return null; }
    };

    // Page number token for Html2Pdf
    $pageToken = '[[page_cu]] / [[page_nb]]';

    // Cover page data
    $coverName = $safeStr(($userDetails->forename ?? null), '') . ' ' . $safeStr(($userDetails->presentSurname ?? null), '');
    $coverName = trim($coverName) !== '' ? trim($coverName) : $safeStr($candidateName, 'Candidate');

    $coverAccess = $safeStr($userDetails->applicationCode ?? null, $safeStr($userID ?? null, 'N/A'));
    $coverClient = $safeStr($userDetails->organisationName ?? null, 'N/A');

    $issueDate = $fmtDateOnly($userDetails->completedDate ?? ($completedAt ?? null), 'd M Y', ' ');
@endphp

{{-- =========================================================
    COVER PAGE
========================================================= --}}
<page backtop="20mm" backbottom="20mm" backleft="0mm" backright="0mm">
    <page_header>
        <div style="text-align: right; font-size: 10px; color: #2C3C64;">
            {{ $coverName }}
        </div>
    </page_header>

    <div style="margin-top: 5px; margin-left: 0px; text-align: left;">
        @if (!empty($userDetails->logo))
            <img src="{{ storage_path('app/logos/' . $userDetails->logo) }}" style="width: 500px;" alt="Company Logo">
        @else
            <img src="{{ env('APP_URL') }}images/classified-report.png" style="width: 8cm;" alt="ClassifIeD Logo">
        @endif
    </div>

    <h1 style="color: #C55359; text-align: left; margin-top: 30px; font-size: 35px;">
        Yoti Report - ID Verification
    </h1>

    <p style="font-size: 12px; line-height: 18px;">
        <strong>Approved Access number:</strong> {{ $coverAccess }} <br>
        <strong>Candidate name:</strong> {{ $coverName }}<br>
        <strong>Client name:</strong> {{ $coverClient }}
    </p>

    <div style="border: 2px solid #2C3C64; padding: 2px; width: 184px; margin-top: 15px;">
        <div style="border: 2px solid #2C3C64; padding: 10px; width: 180px;">
            <div style="font-size: 12px; color: #2C3C64; line-height: 18px;">
                <strong>Issue date:</strong> {{ $issueDate }}<br>
            </div>
        </div>
    </div>

    <page_footer>
        <div style="font-size: 8px;">
            <p>
                ClassifIeD is trading name of BluescreenIT LTD. All information in this document has been provided to BluescreenIT LTD by a third party and we cannot therefore be held liable for any inaccuracies or omissions contained within it.
            </p><br>
            <p style="line-height: 18px;">
                <span style="font-size: 10px; color: #2C3C64;">BluescreenIT LTD</span><br>
                Plymouth Science Park, 1 Davy Rd, Plymouth, Devon PL6 8BX<br>
                Tel: +44 (0)1752 724 000 &nbsp; Email:<a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a> &nbsp; Web: <a href="ttps://classified.getclassified.co.uk/">https://classified.getclassified.co.uk/</a><br>
                BluescreenIT LTD is registered in England and Wales with RO number 89045885006<br>
                Copyright © 2025 BluescreenIT LTD. All rights reserved
            </p><br>
            <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
                Page {{ $pageToken }}
            </div>
        </div>
    </page_footer>
</page>

{{-- =========================================================
    PAGE: SUMMARY
    (Adjusted widths / wrapping so tables never overflow)
========================================================= --}}
<page backtop="0mm" backbottom="20mm" backleft="8mm" backright="8mm">
    <bookmark title="Yoti Report" level="0"></bookmark>

    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
        Yoti ID Check Report
    </h3>

    @php
        // table styles to prevent overflow
        $tdLabel = 'width: 35%; white-space: normal; word-wrap: break-word; word-break: break-word; color:#C55359; padding:6px 0; border-top:1px solid #555; border-bottom:1px solid #555; box-sizing:border-box;';
        $tdValue = 'width: 65%; white-space: normal; word-wrap: break-word; word-break: break-word; color:#595959; padding:6px 0; border-top:1px solid #555; border-bottom:1px solid #555; box-sizing:border-box;';
    @endphp

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr>
                <td style="{{ $tdLabel }}">Session Status</td>
                <td style="{{ $tdValue }}">{{ $safeStr($state ?? null) }}</td>
            </tr>
            <tr>
                <td style="{{ $tdLabel }}">Session ID</td>
                <td style="{{ $tdValue }}">{{ $safeStr($sessionId ?? null) }}</td>
            </tr>
            <tr>
                <td style="{{ $tdLabel }}">User</td>
                <td style="{{ $tdValue }}">ClassifIeD:{{ $safeStr($userID ?? null) }}</td>
            </tr>
            <tr>
                <td style="{{ $tdLabel }}">Created</td>
                <td style="{{ $tdValue }}">{{ $createdAtFormatted }}</td>
            </tr>
            <tr>
                <td style="{{ $tdLabel }}">Completed</td>
                <td style="{{ $tdValue }}">{{ $completedAtFormatted }}</td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
        Candidate Summary
    </h3>

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr>
                <td style="{{ $tdLabel }}">Name</td>
                <td style="width:65%; word-break: break-word; white-space: normal; color:#2C3C64; padding:6px 0; border-top:1px solid #555; border-bottom:1px solid #555;">
                    {{ $safeStr($candidateName) }}
                </td>
            </tr>
            <tr>
                <td style="{{ $tdLabel }}">DOB</td>
                <td style="width:65%; word-break: break-word; white-space: normal; color:#2C3C64; padding:6px 0; border-top:1px solid #555; border-bottom:1px solid #555;">
                    {{ $candidateDob }}
                </td>
            </tr>
            <tr>
                <td style="{{ $tdLabel }}">Address</td>
                <td style="width:65%; word-break: break-word; white-space: normal; color:#2C3C64; padding:6px 0; border-top:1px solid #555; border-bottom:1px solid #555;">
                    {{ $safeStr($candidateAddress) }}
                </td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
        Scheme Summary
    </h3>

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
        <tbody>
            <tr><td style="{{ $tdLabel }}">Trust Framework</td><td style="{{ $tdValue }}">{{ $safeStr($frameworkType) }}</td></tr>
            <tr><td style="{{ $tdLabel }}">Level of Assurance</td><td style="{{ $tdValue }}">{{ $safeStr($levelOfAssurance) }}</td></tr>
            <tr><td style="{{ $tdLabel }}">DBS Objective</td><td style="{{ $tdValue }}">{{ $safeStr($dbsObjective) }}</td></tr>
            <tr><td style="{{ $tdLabel }}">DBS Requirements Met</td><td style="{{ $tdValue }}">{{ $dbsText }}</td></tr>
            <tr><td style="{{ $tdLabel }}">RTW Requirements Met</td><td style="{{ $tdValue }}">{{ $rtwText }}</td></tr>
        </tbody>
    </table>

    <br><br>

    <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
        Identity Comparison (ClassifIeD vs Yoti)
    </h3>

    @php
        $rows = [
            ['Name', $nameCmp],
            ['DOB', $dobCmp],
            ['Address', $addrCmp],
        ];

        // Prevent overflow: tighter columns + wrap + fixed layout
        $cmpHead  = 'font-weight:bold; color:#C55359; padding:6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; white-space:normal; word-break:break-word;';
        $cmpCell  = 'color:#595959; padding:6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; white-space:normal;';
        $cmpField = 'color:#2C3C64; padding:6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; white-space:normal; word-break:break-word;';

        // Strong wrap – prevents overflow for long addresses / codes
        $wrapHard = 'white-space:normal; word-break:break-all; overflow-wrap:anywhere; hyphens:auto;';
    @endphp

    <table width="100%" style="border-collapse: collapse; table-layout: fixed; width: 100%; font-size: 10.5px;">
        <tbody>
            <tr>
                <td style="width: 16%; {{ $cmpHead }}">Field</td>
                <td style="width: 34%; {{ $cmpHead }}">ClassifIeD</td>
                <td style="width: 34%; {{ $cmpHead }}">Yoti</td>
                <td style="width: 16%; {{ $cmpHead }}">Result</td>
            </tr>

            @foreach ($rows as $r)
                @php
                    $label = $r[0];
                    $cmp = $r[1];
                    $m = $cmp['match'] ?? null;
                    $bg = $badgeBg($m);
                    $fg = $badgeColor($m);
                @endphp
                <tr>
                    <td style="width: 16%; {{ $cmpField }}">{{ $label }}</td>
                    <td style="width: 34%; {{ $cmpCell }}">
                        <div style="{{ $wrapHard }}">{{ $safeStr($cmp['db'] ?? null) }}</div>
                    </td>
                    <td style="width: 34%; {{ $cmpCell }}">
                        <div style="{{ $wrapHard }}">{{ $safeStr($cmp['yoti'] ?? null) }}</div>
                    </td>
                    <td style="{{ $cmpCell }}">
                        <span style="display:inline-block; padding:4px 6px; border-radius:999px; font-size:10px; font-weight:bold; background: {{ $bg }}; color: {{ $fg }};">
                            {{ $safeStr($cmp['message'] ?? null, 'No data') }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{ $pageToken }}
        </div>
    </page_footer>
</page>

{{-- =========================================================
    PAGE: LIVENESS
    (Images constrained to a fixed "frame")
========================================================= --}}
<page backtop="0mm" backbottom="20mm" backleft="8mm" backright="8mm">
    <bookmark title="Liveness" level="0"></bookmark>

    <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
        Liveness Capture
    </h3>

    @if (!empty($faceImageBase64))
        <p style="color:#595959; font-size: 11px; margin: 0 0 6px 0;"><strong>Face Capture</strong></p>
        <div style="width: fit-content; border:1px solid #ddd; padding:8px; border-radius:6px; margin-bottom:10px; box-sizing:border-box;">
            <div style="width: 300px; height:220px; overflow:hidden; text-align:center;">
                <img src="{{ $faceImageBase64 }}"
                    style="display:block; margin:0; max-width:300px; max-height:212px; width:auto; height:auto;" />
            </div>
        </div>
        <br>
    @endif

    <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
        Liveness Checks & Scores
    </h3>

    @if (!empty($sessionLivenessChecks))
        @foreach ($sessionLivenessChecks as $check)
            @php
                $type  = $safeStr($getCheckType($check), 'Liveness Check');
                $state = $safeStr($getCheckState($check), 'N/A');
                $rec   = $getCheckRecommendation($check);
                $rec   = $rec === null ? 'N/A' : $safeStr($rec, 'N/A');

                $toneState = $resultTone($state);
                $toneRec = $resultTone($rec);

                $breakdown = $getCheckBreakdown($check);
            @endphp

            <div style="border:1px solid #e6ebf2; border-radius:10px; padding:10px; margin-bottom:10px;">
                <p style="margin:0 0 6px 0; color:#2C3C64; font-size:12px; word-break:break-word;"><strong>Check Type:</strong> {{ $type }}</p>

                <p style="margin:0 0 6px 0; font-size:12px;">
                    <strong style="color:#C55359;">Check State:</strong>
                    <span style="display:inline-block; padding:3px 8px; border-radius:999px; background: {{ $toneState[1] }}; color: {{ $toneState[0] }}; font-weight:bold; font-size:10px;">
                        {{ $state }}
                    </span>
                </p>

                <p style="margin:0 0 6px 0; font-size:12px;">
                    <strong style="color:#C55359;">Recommendation:</strong>
                    <span style="display:inline-block; padding:3px 8px; border-radius:999px; background: {{ $toneRec[1] }}; color: {{ $toneRec[0] }}; font-weight:bold; font-size:10px;">
                        {{ $rec }}
                    </span>
                </p>

                @if (!empty($breakdown))
                    <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 10.5px; margin-top:8px;">
                        <tbody>
                            <tr>
                                <td style="width: 60%; font-weight:bold; color:#C55359; padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; word-break:break-word;">Sub-check</td>
                                <td style="width: 40%; font-weight:bold; color:#C55359; padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; word-break:break-word;">Result</td>
                            </tr>
                            @foreach ($breakdown as $sub)
                                @php
                                    $subName = 'N/A';
                                    $subRes = 'N/A';
                                    try {
                                        if (method_exists($sub, 'getSubCheck')) $subName = $safeStr($sub->getSubCheck(), 'N/A');
                                        if (method_exists($sub, 'getResult')) $subRes = $safeStr($sub->getResult(), 'N/A');
                                    } catch (\Throwable $e) {}
                                    $toneSub = $resultTone($subRes);
                                @endphp
                                <tr>
                                    <td style="color:#2C3C64; padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; word-break:break-word;">{{ $subName }}</td>
                                    <td style="padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555;">
                                        <span style="display:inline-block; padding:3px 8px; border-radius:999px; background: {{ $toneSub[1] }}; color: {{ $toneSub[0] }}; font-weight:bold; font-size:10px;">
                                            {{ $subRes }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    @else
        <p style="color:#595959; font-size: 12px;">No liveness checks found.</p>
    @endif

    <page_footer>
        <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
            Page {{ $pageToken }}
        </div>
    </page_footer>
</page>

{{-- =========================================================
    DOCUMENT PAGES (one per document)
    - images in frames
    - tables fixed layout + wrapping
========================================================= --}}
@if (!empty($documentsData))
    @foreach ($documentsData as $docIndex => $document)
        @php
            $docType    = $safeStr($document['type'] ?? null, 'ID Document');
            $docCountry = $safeStr($document['issuingCountry'] ?? null, 'N/A');

            $docImages = (isset($document['images']) && is_array($document['images'])) ? $document['images'] : [];
            $docFieldsRaw = $document['fieldsData'] ?? [];
            $docFields = $flattenFields($docFieldsRaw);

            $docChecks = (isset($document['relevantChecks']) && is_array($document['relevantChecks'])) ? $document['relevantChecks'] : [];
        @endphp

        <page backtop="0mm" backbottom="20mm" backleft="8mm" backright="8mm">
            <bookmark title="{{ $docType }} ({{ $docCountry }})" level="0"></bookmark>

            <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
                Document: {{ $docType }} ({{ $docCountry }})
            </h3>

            <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
                Document Images
            </h3>

            @if (!empty($docImages))
                @php
                    // Only keep images with a src
                    $imgs = array_values(array_filter($docImages, function ($i) {
                        return !empty($i['src']);
                    }));

                    // 2 per row
                    $chunks = array_chunk($imgs, 2);

                    // card sizes (tweak if you want)
                    $cardW = 260;  // total "box" width
                    $imgW  = 240;  // inner image max width
                    $imgH  = 180;  // inner image max height
                @endphp

                <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width:100%;">
                    <tbody>
                    @foreach ($chunks as $row)
                        <tr>
                            @foreach ($row as $img)
                                @php
                                    $label = $safeStr($img['label'] ?? null, 'Image');
                                    $src   = $img['src'] ?? null;
                                @endphp

                                <td style="width:50%; vertical-align: top; padding: 6px 6px 10px 0; box-sizing:border-box;">
                                    <div style="font-size:11px; color:#595959; margin:0 0 6px 0;">
                                        <strong>{{ $label }}</strong>
                                    </div>

                                    <div style="width: {{ $cardW }}px; border:1px solid #ddd; padding:8px; border-radius:6px; box-sizing:border-box;">
                                        <div style="width: {{ $imgW }}px; height: {{ $imgH }}px; overflow:hidden; text-align:center;">
                                            <img src="{{ $src }}"
                                                style="display:block; margin:0 auto; max-width:{{ $imgW }}px; max-height:{{ $imgH }}px; width:auto; height:auto;" />
                                        </div>
                                    </div>
                                </td>
                            @endforeach

                            {{-- If odd number of images, add an empty cell to keep layout stable --}}
                            @if (count($row) === 1)
                                <td style="width:50%; vertical-align: top; padding: 6px 0 10px 6px; box-sizing:border-box;">&nbsp;</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:#595959; font-size: 12px;">No document images available.</p>
            @endif

            <br>

            <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
                Document Checks
            </h3>

            @if (!empty($docChecks))
                @foreach ($docChecks as $check)
                    @php
                        $type  = $safeStr($getCheckType($check), 'Check');
                        $state = $safeStr($getCheckState($check), 'N/A');
                        $rec   = $getCheckRecommendation($check);
                        $rec   = $rec === null ? 'N/A' : $safeStr($rec, 'N/A');

                        $toneState = $resultTone($state);
                        $toneRec   = $resultTone($rec);

                        $breakdown = $getCheckBreakdown($check);
                    @endphp

                    <div style="border:1px solid #e6ebf2; border-radius:10px; padding:10px; margin-bottom:10px;">
                        <p style="margin:0 0 6px 0; color:#2C3C64; font-size:12px; word-break:break-word;"><strong>Check Type:</strong> {{ $type }}</p>

                        <p style="margin:0 0 6px 0; font-size:12px;">
                            <strong style="color:#C55359;">Check State:</strong>
                            <span style="display:inline-block; padding:3px 8px; border-radius:999px; background: {{ $toneState[1] }}; color: {{ $toneState[0] }}; font-weight:bold; font-size:10px;">
                                {{ $state }}
                            </span>
                        </p>

                        <p style="margin:0 0 6px 0; font-size:12px;">
                            <strong style="color:#C55359;">Recommendation:</strong>
                            <span style="display:inline-block; padding:3px 8px; border-radius:999px; background: {{ $toneRec[1] }}; color: {{ $toneRec[0] }}; font-weight:bold; font-size:10px;">
                                {{ $rec }}
                            </span>
                        </p>

                        @if (!empty($breakdown))
                            <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 10.5px; margin-top:8px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 60%; font-weight:bold; color:#C55359; padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; word-break:break-word;">Sub-check</td>
                                        <td style="width: 40%; font-weight:bold; color:#C55359; padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; word-break:break-word;">Result</td>
                                    </tr>
                                    @foreach ($breakdown as $sub)
                                        @php
                                            $subName = 'N/A';
                                            $subRes  = 'N/A';
                                            try {
                                                if (method_exists($sub, 'getSubCheck')) $subName = $safeStr($sub->getSubCheck(), 'N/A');
                                                if (method_exists($sub, 'getResult'))  $subRes  = $safeStr($sub->getResult(), 'N/A');
                                            } catch (\Throwable $e) {}
                                            $toneSub = $resultTone($subRes);
                                        @endphp
                                        <tr>
                                            <td style="color:#2C3C64; padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; word-break:break-word;">{{ $subName }}</td>
                                            <td style="padding: 6px 4px; border-top:1px solid #555; border-bottom:1px solid #555;">
                                                <span style="display:inline-block; padding:3px 8px; border-radius:999px; background: {{ $toneSub[1] }}; color: {{ $toneSub[0] }}; font-weight:bold; font-size:10px;">
                                                    {{ $subRes }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach
            @else
                <p style="color:#595959; font-size: 12px;">No document checks found for this document.</p>
            @endif

            <br>

            <h3 style="font-size: 14px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
                Extracted Text & Fields
            </h3>

            @if (!empty($docFields) && is_array($docFields))
                @php
                    $fieldHead = 'font-weight:bold; color:#C55359; padding:6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; white-space:normal; word-break:break-word;';
                    $fieldKey  = 'color:#2C3C64; padding:6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; white-space:normal; word-break:break-word;';
                    $fieldValTd = 'color:#595959; padding:6px 4px; border-top:1px solid #555; border-bottom:1px solid #555; white-space:normal;';

                    // Hard wrap for nasty strings
                    $fieldWrap = 'white-space:normal; word-break:break-all; overflow-wrap:anywhere; hyphens:auto;';
                @endphp

                <table width="100%" style="border-collapse: collapse; table-layout: fixed; width: 100%; font-size: 10.5px;">
                    <tbody>
                        <tr>
                            <td style="width: 35%; {{ $fieldHead }}">Field</td>
                            <td style="width: 65%; {{ $fieldHead }}">Value</td>
                        </tr>

                        @foreach ($docFields as $k => $v)
                            @php
                                $key = $safeStr($k, 'Field');
                                $val = 'N/A';

                                if (is_array($v) || is_object($v)) {
                                    $json = $safeJson($v);
                                    // add breaks after commas to help wrapping
                                    $val = $safeStr(str_replace(',', ",\n", $json), 'N/A');
                                } else {
                                    $val = $safeStr($v, 'N/A');
                                }
                            @endphp
                            <tr>
                                <td style="width: 35%; {{ $fieldKey }}">{{ ucfirst(str_replace('_',' ', $key)) }}</td>
                                <td style="width: 65%; {{ $fieldValTd }}">
                                    <div style="{{ $fieldWrap }}">
                                        {!! nl2br(e($val)) !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:#595959; font-size: 12px;">No extracted fields found.</p>
            @endif

            <page_footer>
                <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
                    Page {{ $pageToken }}
                </div>
            </page_footer>
        </page>
    @endforeach
@endif

{{-- =========================================================
    FINAL PAGE: WATCHLIST (if exists)
========================================================= --}}
@php
    $watchlistChecksFromSession = [];
    try {
        if (isset($sessionResult) && $sessionResult && method_exists($sessionResult, 'getWatchlistScreeningChecks')) {
            $tmp = $sessionResult->getWatchlistScreeningChecks();
            $watchlistChecksFromSession = is_array($tmp) ? $tmp : [];
        }
    } catch (\Throwable $e) {
        $watchlistChecksFromSession = [];
    }
@endphp

@if (!empty($watchlistChecksFromSession))
    <page backtop="0mm" backbottom="20mm" backleft="8mm" backright="8mm">
        <bookmark title="Watchlist Screening" level="0"></bookmark>

        <h3 style="font-size: 16px; color: #2C3C64; font-weight: bold; border-bottom: 2px solid #2C3C64; padding-bottom: 5px;">
            Watchlist Screening
        </h3>

        @foreach ($watchlistChecksFromSession as $check)
            @php
                $totalHits = 'N/A';
                $countries = 'N/A';

                try {
                    if (method_exists($check, 'getReport')) {
                        $report = $check->getReport();
                        if ($report && method_exists($report, 'getWatchlistSummary')) {
                            $sum = $report->getWatchlistSummary();
                            if ($sum) {
                                if (method_exists($sum, 'getTotalHits')) $totalHits = $safeStr($sum->getTotalHits(), 'N/A');
                                if (method_exists($sum, 'getAssociatedCountryCodes')) {
                                    $cc = $sum->getAssociatedCountryCodes();
                                    if (is_array($cc) && !empty($cc)) $countries = implode(', ', $cc);
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {}
            @endphp

            <div style="border:1px solid #e6ebf2; border-radius:10px; padding:10px; margin-bottom:10px;">
                <table width="100%" style="border-collapse: collapse; table-layout: fixed; max-width: 100%; font-size: 12px;">
                    <tbody>
                        <tr>
                            <td style="{{ $tdLabel }}">Total Hits</td>
                            <td style="{{ $tdValue }}">{{ $totalHits }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $tdLabel }}">Associated Countries</td>
                            <td style="{{ $tdValue }}">{{ $countries }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <page_footer>
            <div style="font-size: 11px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color:rgba(44, 60, 100, 0.4);">
                Page {{ $pageToken }}
            </div>
        </page_footer>
    </page>
@endif
@extends('layout.admin')

@section('title', "Yoti Results")

@section('sidebar')
@endsection

@section('content')
<div class="yoti-page">
    <div class="yoti-shell">
        <div class="yoti-topbar">
            <h1>ID Check Session Results</h1>
            <div class="yoti-topbar-actions">
                <span class="yoti-status yoti-status--{{ strtolower($state) }}">{{ $state }}</span>
                <a href="/yoti/sendBack/{{$userID}}" class="yoti-sendback">Send Back</a>
                <a href="{{ route('yoti.report.pdf', ['userID' => $userID]) }}" class="yoti-pdf" target="_blank" rel="noopener">
                    Download PDF
                </a>
            </div>
        </div>

        @if ($state == "COMPLETED")
            @php
                // Always use parsed summary from controller (single source of truth)
                $frameworkType       = $identitySummary['frameworkType'] ?? 'N/A';
                $levelOfAssurance    = $identitySummary['levelOfAssurance'] ?? 'N/A';
                $dbsObjective        = $identitySummary['dbsObjective'] ?? 'N/A';
                $dbsRequirementsMet  = $identitySummary['dbsRequirementsMet'] ?? null;
                $rtwRequirementsMet  = $identitySummary['rtwRequirementsMet'] ?? null;

                if (is_string($levelOfAssurance) && $levelOfAssurance !== 'N/A') {
                    $levelOfAssurance = ucwords(strtolower(str_replace('_', ' ', $levelOfAssurance)));
                }

                // -----------------------------
                // Candidate Summary (prefer identity assertion values from controller)
                // -----------------------------
                $candidateName     = $candidateSummary['fullName'] ?? null;
                $candidateDobRaw   = $candidateSummary['dob'] ?? null;
                $candidateAddress  = $candidateSummary['address'] ?? null;

                // Format DOB nicely
                $candidateDob = 'N/A';
                if (!empty($candidateDobRaw)) {
                    try {
                        $candidateDob = \Carbon\Carbon::parse($candidateDobRaw)->format('d M Y');
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                // Fallback: document fields ONLY for missing pieces
                if (empty($candidateName) || empty($candidateAddress) || $candidateDob === 'N/A') {
                    foreach ($documentsData as $d) {
                        if (empty($d['fieldsData']) || !is_array($d['fieldsData'])) continue;
                        $f = $d['fieldsData'];

                        // Name fallback
                        if (empty($candidateName)) {
                            $firstName = $f['given_names'] ?? $f['first_name'] ?? $f['forenames'] ?? null;
                            $lastName  = $f['family_name'] ?? $f['last_name'] ?? $f['surname'] ?? null;
                            $fullName  = trim(($firstName ? $firstName . ' ' : '') . ($lastName ?? ''));
                            if ($fullName !== '') $candidateName = $fullName;
                        }

                        // DOB fallback
                        if ($candidateDob === 'N/A') {
                            $rawDob = $f['date_of_birth'] ?? $f['dob'] ?? null;
                            if (!empty($rawDob)) {
                                try {
                                    $candidateDob = \Carbon\Carbon::parse($rawDob)->format('d M Y');
                                } catch (\Throwable $e) {
                                    // ignore
                                }
                            }
                        }

                        // Address fallback
                        if (empty($candidateAddress)) {
                            $addr = $f['structured_postal_address'] ?? $f['address'] ?? null;

                            if (is_object($addr) && method_exists($addr, 'getFormattedAddress')) {
                                $candidateAddress = $addr->getFormattedAddress() ?: null;
                            } elseif (is_array($addr)) {
                                $candidateAddress = $addr['formatted_address'] ?? null;
                                if (empty($candidateAddress)) {
                                    $candidateAddress = implode(', ', array_filter($addr, fn($v) => is_string($v) && trim($v) !== ''));
                                }
                            } elseif (is_string($addr) && trim($addr) !== '') {
                                // Some payloads embed JSON in a string
                                $pos = strpos($addr, '{');
                                if ($pos !== false) {
                                    $decoded = json_decode(substr($addr, $pos), true);
                                    if (is_array($decoded)) {
                                        $candidateAddress = $decoded['formatted_address'] ?? null;
                                        if (empty($candidateAddress)) {
                                            $candidateAddress = implode(', ', array_filter($decoded, fn($v) => is_string($v) && trim($v) !== ''));
                                        }
                                    } else {
                                        $candidateAddress = trim($addr);
                                    }
                                } else {
                                    $candidateAddress = trim($addr);
                                }
                            }
                        }

                        if (!empty($candidateName) && !empty($candidateAddress) && $candidateDob !== 'N/A') break;
                    }
                }

                // Normalise final display values
                $candidateName    = $candidateName ?: 'N/A';
                $candidateAddress = $candidateAddress ?: 'N/A';

                // -----------------------------
                // Status colouring helper:
                // Green if contains pass / done / approve (incl approved), Red otherwise, Neutral for N/A/empty
                // -----------------------------
                $resultClass = function ($value) {
                    if ($value === null) return 'result-neutral';
                    $txt = strtolower(trim((string)$value));

                    if ($txt === '' || $txt === 'n/a' || $txt === 'na' || $txt === 'null' || $txt === '-') {
                        return 'result-neutral';
                    }

                    if (str_contains($txt, 'pass') || str_contains($txt, 'done') || str_contains($txt, 'approve')) {
                        return 'result-good';
                    }

                    return 'result-bad';
                };

                // -----------------------------
                // Dates
                // -----------------------------
                try {
                    $createdAtFormatted = !empty($createdAt) ? \Carbon\Carbon::parse($createdAt)->format('d M Y H:i') : 'N/A';
                } catch (\Throwable $e) {
                    $createdAtFormatted = 'N/A';
                }

                try {
                    $completedAtFormatted = !empty($completedAt) ? \Carbon\Carbon::parse($completedAt)->format('d M Y H:i') : 'N/A';
                } catch (\Throwable $e) {
                    $completedAtFormatted = 'N/A';
                }

                // -----------------------------
                // Liveness
                // -----------------------------
                $livenessList = $sessionLivenessChecks
                    ?? (method_exists($sessionResult, 'getLivenessChecks') ? $sessionResult->getLivenessChecks() : []);

                // -----------------------------
                // Comparison badges
                // -----------------------------
                $matchBadgeClass = function ($match) {
                    if ($match === true) return 'badge-match';
                    if ($match === false) return 'badge-mismatch';
                    return 'badge-neutral';
                };

                $nameCmp = $identityComparison['name'] ?? ['match' => null, 'message' => 'No data', 'db' => 'N/A', 'yoti' => 'N/A'];
                $dobCmp  = $identityComparison['dob'] ?? ['match' => null, 'message' => 'No data', 'db' => 'N/A', 'yoti' => 'N/A'];
                $addrCmp = $identityComparison['address'] ?? ['match' => null, 'message' => 'No data', 'db' => 'N/A', 'yoti' => 'N/A'];

                // FINAL fallback for display: use comparison yoti address if candidate address is missing
                if (empty($candidateAddress) || $candidateAddress === 'N/A') {
                    $candidateAddress = (!empty($addrCmp['yoti']) && $addrCmp['yoti'] !== 'N/A') ? $addrCmp['yoti'] : ($candidateAddress ?: 'N/A');
                }
            @endphp

            <section class="yoti-summary yoti-summary--v2">
                <div class="yoti-summary-header">
                    <div class="yoti-summary-title">
                        <h2>Summary</h2>
                        <p class="yoti-summary-sub">Session overview and scheme compliance</p>
                    </div>

                    <div class="yoti-summary-chips">
                        <span class="chip">{{ $frameworkType }}</span>
                        <span class="chip chip--muted">{{ $levelOfAssurance }}</span>

                        @php
                            $dbsLabel = $dbsRequirementsMet === true ? 'DBS: Met' : ($dbsRequirementsMet === false ? 'DBS: Not met' : 'DBS: N/A');
                            $dbsChip  = $dbsRequirementsMet === true ? 'chip--good' : ($dbsRequirementsMet === false ? 'chip--bad' : 'chip--muted');

                            $rtwLabel = $rtwRequirementsMet === true ? 'RTW: Met' : ($rtwRequirementsMet === false ? 'RTW: Not met' : 'RTW: N/A');
                            $rtwChip  = $rtwRequirementsMet === true ? 'chip--good' : ($rtwRequirementsMet === false ? 'chip--bad' : 'chip--muted');
                        @endphp

                        <span class="chip {{ $dbsChip }}">{{ $dbsLabel }}</span>
                        <span class="chip {{ $rtwChip }}">{{ $rtwLabel }}</span>
                    </div>
                </div>

                <div class="yoti-summary-grid yoti-summary-grid--v2">
                    <div class="yoti-summary-card">
                        <label>Candidate</label>
                        <div class="summary-main">
                            <div class="summary-line">
                                <span class="k">Name</span>
                                <span class="v">{{ $candidateName ?: 'N/A' }}</span>
                                <span class="cmp-badge {{ $matchBadgeClass($nameCmp['match']) }}">{{ $nameCmp['message'] }}</span>
                            </div>
                            <div class="summary-line">
                                <span class="k">DOB</span>
                                <span class="v">{{ $candidateDob ?: 'N/A' }}</span>
                                <span class="cmp-badge {{ $matchBadgeClass($dobCmp['match']) }}">{{ $dobCmp['message'] }}</span>
                            </div>
                            <div class="summary-line summary-line--wrap">
                                <span class="k">Address</span>
                                <span class="v">{{ $candidateAddress ?: 'N/A' }}</span>
                                <span class="cmp-badge {{ $matchBadgeClass($addrCmp['match']) }}">{{ $addrCmp['message'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="yoti-summary-card">
                        <label>Session</label>
                        <div class="summary-main">
                            <div class="summary-line">
                                <span class="k">Session ID</span>
                                <span class="v mono">{{ $sessionId ?? 'N/A' }}</span>
                            </div>
                            <div class="summary-line">
                                <span class="k">User</span>
                                <span class="v">ClassifIeD:{{ $userID }}</span>
                            </div>
                            <div class="summary-line">
                                <span class="k">Created</span>
                                <span class="v">{{ $createdAtFormatted }}</span>
                            </div>
                            <div class="summary-line">
                                <span class="k">Completed</span>
                                <span class="v">{{ $completedAtFormatted }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="yoti-summary-card">
                        <label>Schemes</label>
                        <div class="summary-main">
                            <div class="summary-line">
                                <span class="k">DBS Objective</span>
                                <span class="v">{{ $dbsObjective }}</span>
                            </div>
                            <div class="summary-line">
                                <span class="k">DBS Requirements</span>
                                <span class="v {{ $dbsRequirementsMet === true ? 'result-good' : ($dbsRequirementsMet === false ? 'result-bad' : 'result-neutral') }}">
                                    {{ $dbsRequirementsMet === true ? 'Yes' : ($dbsRequirementsMet === false ? 'No' : 'N/A') }}
                                </span>
                            </div>
                            <div class="summary-line">
                                <span class="k">RTW Requirements</span>
                                <span class="v {{ $rtwRequirementsMet === true ? 'result-good' : ($rtwRequirementsMet === false ? 'result-bad' : 'result-neutral') }}">
                                    {{ $rtwRequirementsMet === true ? 'Yes' : ($rtwRequirementsMet === false ? 'No' : 'N/A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="yoti-section">
                <button class="yoti-dropdown-toggle" type="button" data-target="panel-liveness" aria-expanded="false">
                    <span>Liveness Capture</span>
                    <span class="chev">⌄</span>
                </button>

                <div id="panel-liveness" class="yoti-dropdown-panel">
                    <div class="yoti-split">
                        <div class="yoti-left">
                            <h3>Checks & scores</h3>

                            @if (!empty($livenessList))
                                @foreach ($livenessList as $livenessCheck)
                                    <div class="yoti-check-card">
                                        @php
                                            $livenessState = $livenessCheck->getState();
                                            $livenessReport = $livenessCheck->getReport();
                                            $livenessRec = ($livenessReport && $livenessReport->getRecommendation())
                                                ? $livenessReport->getRecommendation()->getValue()
                                                : 'N/A';
                                            $breakdown = $livenessReport ? $livenessReport->getBreakdown() : [];
                                        @endphp

                                        <p>
                                            <strong>Check State:</strong>
                                            <span class="{{ $resultClass($livenessState) }}">{{ $livenessState }}</span>
                                        </p>
                                        <p>
                                            <strong>Recommendation:</strong>
                                            <span class="{{ $resultClass($livenessRec) }}">{{ $livenessRec }}</span>
                                        </p>

                                        @if (!empty($breakdown))
                                            <ul class="yoti-subchecks">
                                                @foreach ($breakdown as $subCheck)
                                                    @php $subResult = $subCheck->getResult(); @endphp
                                                    <li>
                                                        <strong>{{ $subCheck->getSubCheck() }}:</strong>
                                                        <span class="{{ $resultClass($subResult) }}">{{ $subResult }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="yoti-muted">No liveness checks found.</p>
                            @endif
                        </div>

                        <div class="yoti-right">
                            @php
                                $livenessImages = [];
                                if (!empty($faceImageBase64)) {
                                    $livenessImages[] = ['label' => 'Face Capture', 'src' => $faceImageBase64];
                                }
                            @endphp

                            @if (!empty($livenessImages))
                                <div class="yoti-viewer" data-viewer>
                                    <div class="yoti-viewer-count"><span data-current>1</span>/<span data-total>{{ count($livenessImages) }}</span></div>

                                    <div class="yoti-viewer-stage">
                                        @foreach ($livenessImages as $i => $img)
                                            <img src="{{ $img['src'] }}" alt="{{ $img['label'] }}" class="yoti-viewer-image {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                                        @endforeach
                                    </div>

                                    <div class="yoti-viewer-controls">
                                        <button type="button" data-prev>←</button>
                                        <button type="button" data-next>→</button>
                                    </div>
                                </div>
                            @else
                                <div class="yoti-empty">No liveness image available.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            @if (!empty($documentsData))
                @foreach ($documentsData as $docIndex => $document)
                    <section class="yoti-section">
                        <button class="yoti-dropdown-toggle" type="button" data-target="panel-doc-{{ $docIndex }}" aria-expanded="false">
                            <span>{{ $document['type'] ?? 'ID Document' }} ({{ $document['issuingCountry'] ?? 'N/A' }})</span>
                            <span class="chev">⌄</span>
                        </button>

                        <div id="panel-doc-{{ $docIndex }}" class="yoti-dropdown-panel">
                            <div class="yoti-split">
                                <div class="yoti-left">
                                    <h3>Checks & extracted data</h3>

                                    @php
                                        $relevantDocChecks = $document['relevantChecks'] ?? [];
                                    @endphp

                                    @if (!empty($relevantDocChecks))
                                        @foreach ($relevantDocChecks as $check)
                                            @php
                                                $report = $check->getReport();
                                                $checkState = $check->getState();
                                                $recommendation = ($report && $report->getRecommendation())
                                                    ? $report->getRecommendation()->getValue()
                                                    : null;
                                                $breakdown = $report ? $report->getBreakdown() : [];
                                            @endphp

                                            <div class="yoti-check-card">
                                                <p><strong>Check Type:</strong> {{ $check->getType() }}</p>
                                                <p>
                                                    <strong>Check Status:</strong>
                                                    <span class="{{ $resultClass($checkState) }}">{{ $checkState }}</span>
                                                </p>
                                                @if ($recommendation)
                                                    <p>
                                                        <strong>Recommendation:</strong>
                                                        <span class="{{ $resultClass($recommendation) }}">{{ $recommendation }}</span>
                                                    </p>
                                                @endif

                                                @if (!empty($breakdown))
                                                    <ul class="yoti-subchecks">
                                                        @foreach ($breakdown as $subCheck)
                                                            @php $subResult = $subCheck->getResult(); @endphp
                                                            <li>
                                                                <strong>{{ $subCheck->getSubCheck() }}:</strong>
                                                                <span class="{{ $resultClass($subResult) }}">{{ $subResult }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="yoti-muted">No document checks found for this document.</p>
                                    @endif

                                    <div class="yoti-check-card">
                                        <p><strong>Extracted User Information</strong></p>
                                        @if (!empty($document['fieldsData']))
                                            <ul class="yoti-fields">
                                                @foreach ($document['fieldsData'] as $key => $value)
                                                    <li>
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        @if (is_array($value))
                                                            {{ json_encode($value) }}
                                                        @elseif (is_object($value))
                                                            {{ method_exists($value, '__toString') ? (string)$value : json_encode($value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="yoti-muted">No user details extracted from document fields.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="yoti-right">
                                    @if (!empty($document['images']))
                                        <div class="yoti-viewer" data-viewer>
                                            <div class="yoti-viewer-count"><span data-current>1</span>/<span data-total>{{ count($document['images']) }}</span></div>

                                            <div class="yoti-viewer-stage">
                                                @foreach ($document['images'] as $i => $img)
                                                    <img src="{{ $img['src'] }}" alt="{{ $img['label'] ?? 'Document image' }}" class="yoti-viewer-image {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                                                @endforeach
                                            </div>

                                            <div class="yoti-viewer-controls">
                                                <button type="button" data-prev>←</button>
                                                <button type="button" data-next>→</button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="yoti-empty">No document images available.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>
                @endforeach
            @else
                <div class="yoti-empty-block">No ID documents found.</div>
            @endif

            @php $watchlistChecksFromSession = method_exists($sessionResult, 'getWatchlistScreeningChecks') ? $sessionResult->getWatchlistScreeningChecks() : []; @endphp
            @if (!empty($watchlistChecksFromSession))
                <section class="yoti-section">
                    <button class="yoti-dropdown-toggle" type="button" data-target="panel-watchlist" aria-expanded="false">
                        <span>Watchlist Screening</span>
                        <span class="chev">⌄</span>
                    </button>
                    <div id="panel-watchlist" class="yoti-dropdown-panel">
                        <div class="yoti-left full">
                            @foreach ($watchlistChecksFromSession as $check)
                                @php $summary = $check->getReport()->getWatchlistSummary(); @endphp
                                <div class="yoti-check-card">
                                    <p><strong>Total Hits:</strong> {{ $summary->getTotalHits() }}</p>
                                    <p><strong>Associated Countries:</strong> {{ implode(', ', $summary->getAssociatedCountryCodes()) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

        @elseif ($state == "ONGOING")
            <div class="yoti-message yoti-message--info">Candidate's information is being processed, please come back shortly.</div>
        @elseif ($state == "EXPIRED")
            <div class="yoti-message yoti-message--warn">Candidate's information has expired. This typically happens 28 days after processing.</div>
        @else
            <div class="yoti-message yoti-message--error">Candidate's information could not be found. Please try again later.</div>
        @endif
    </div>
</div>
@endsection

@section('pageCSS')
<style>
/* ---------- Hard reset for this page ---------- */
.yoti-page, .yoti-page * {
    box-sizing: border-box !important;
}

/* Kill bootstrap card/container feel */
.yoti-page .container,
.yoti-page .card,
.yoti-page .card-header,
.yoti-page .card-body {
    all: unset !important;
}

.yoti-page {
    width: 100% !important;
    padding: 20px !important;
    background: #f3f5f8 !important;
    font-family: Inter, "Segoe UI", Arial, sans-serif !important;
    color: #1f2a37 !important;
}

.yoti-shell {
    max-width: 1500px !important;
    margin: 0 auto !important;
    background: #ffffff !important;
    border: 1px solid #e4e8ef !important;
    border-radius: 14px !important;
    padding: 18px !important;
}

/* ---------- Top bar ---------- */
.yoti-topbar {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 12px !important;
    border-bottom: 1px solid #e8edf3 !important;
    padding-bottom: 14px !important;
    margin-bottom: 18px !important;
}

.yoti-topbar h1 {
    margin: 0 !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    color: #1f2a37 !important;
}

.yoti-topbar-actions {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.yoti-status {
    padding: 6px 10px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
}
.yoti-status--completed { background: #e7f7ee !important; color: #0f7b39 !important; }
.yoti-status--ongoing { background: #eaf2ff !important; color: #1e4da1 !important; }
.yoti-status--expired,
.yoti-status--error { background: #fdecec !important; color: #a12222 !important; }

.yoti-sendback {
    display: inline-block !important;
    text-decoration: none !important;
    border: 1px solid #d64045 !important;
    color: #d64045 !important;
    background: #fff !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}
.yoti-sendback:hover { background: #fff3f3 !important; }

/* ---------- Summary ---------- */
.yoti-summary {
    margin-bottom: 16px !important;
}
.yoti-summary-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 10px !important;
}
.yoti-summary-item {
    border: 1px solid #e6ebf2 !important;
    border-radius: 10px !important;
    background: #fafcff !important;
    padding: 10px 12px !important;
    min-height: 72px !important;
}
.yoti-summary-item--wide {
    grid-column: span 2 !important;
}
.yoti-summary-item label {
    display: block !important;
    margin: 0 0 4px !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: .04em !important;
    color: #6e7a8a !important;
    font-weight: 700 !important;
}
.yoti-summary-item p {
    margin: 0 !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #18212f;
    word-break: break-word !important;
}

/* Existing classes kept for compatibility */
.yoti-summary-item p.ok { color: #0f7b39 !important; }
.yoti-summary-item p.bad { color: #a12222 !important; }

/* New generic result coloring */
.result-good { color: #0f7b39 !important; font-weight: 700 !important; }
.result-bad { color: #a12222 !important; font-weight: 700 !important; }
.result-neutral { color: #6b7785 !important; font-weight: 600 !important; }

/* ---------- Summary v2 ---------- */
.yoti-summary--v2 {
    border: 1px solid #e6ebf2 !important;
    border-radius: 14px !important;
    background: #ffffff !important;
    padding: 14px !important;
    margin-bottom: 16px !important;
}

.yoti-summary-header {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    gap: 12px !important;
    border-bottom: 1px solid #edf1f7 !important;
    padding-bottom: 12px !important;
    margin-bottom: 12px !important;
}

.yoti-summary-title h2 {
    margin: 0 !important;
    font-size: 16px !important;
    font-weight: 800 !important;
    color: #18212f !important;
}

.yoti-summary-sub {
    margin: 4px 0 0 !important;
    color: #6b7785 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}

.yoti-summary-chips {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    justify-content: flex-end !important;
}

.chip {
    display: inline-flex !important;
    align-items: center !important;
    padding: 6px 10px !important;
    border-radius: 999px !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    border: 1px solid #dbe3ef !important;
    background: #f7f9fc !important;
    color: #314155 !important;
}

.chip--good {
    background: #e7f7ee !important;
    color: #0f7b39 !important;
    border-color: #bfe8ce !important;
}

.chip--bad {
    background: #fdecec !important;
    color: #a12222 !important;
    border-color: #f5c2c2 !important;
}

.chip--muted {
    background: #eef2f7 !important;
    color: #536277 !important;
    border-color: #d8e0ea !important;
}

.yoti-summary-grid--v2 {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
}

.yoti-summary-card {
    border: 1px solid #e6ebf2 !important;
    border-radius: 12px !important;
    background: #fafcff !important;
    padding: 12px !important;
    min-height: 140px !important;
}

.yoti-summary-card label {
    display: block !important;
    margin: 0 0 10px !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: .06em !important;
    color: #6e7a8a !important;
    font-weight: 900 !important;
}

.summary-main {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}

.summary-line {
    display: grid !important;
    grid-template-columns: 110px 1fr auto !important;
    align-items: center !important;
    gap: 10px !important;
}

.summary-line--wrap {
    align-items: start !important;
}

.summary-line .k {
    font-size: 12px !important;
    color: #6b7785 !important;
    font-weight: 800 !important;
}

.summary-line .v {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #18212f !important;
    word-break: break-word !important;
}

.mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace !important;
    font-size: 12px !important;
}

/* responsive */
@media (max-width: 1200px) {
    .yoti-summary-grid--v2 { grid-template-columns: 1fr !important; }
    .summary-line { grid-template-columns: 110px 1fr !important; }
    .summary-line .cmp-badge { justify-self: start !important; }
    .yoti-summary-header { flex-direction: column !important; }
    .yoti-summary-chips { justify-content: flex-start !important; }
}

/* ---------- Dropdown sections ---------- */
.yoti-section {
    border: 1px solid #e6ebf2 !important;
    border-radius: 12px !important;
    margin-bottom: 12px !important;
    overflow: hidden !important;
    background: #fff !important;
}
.yoti-dropdown-toggle {
    width: 100% !important;
    border: 0 !important;
    border-bottom: 1px solid #edf1f7 !important;
    background: #f9fbff !important;
    padding: 13px 14px !important;
    text-align: left !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    cursor: pointer !important;
}
.yoti-dropdown-toggle .chev {
    transition: transform .2s ease !important;
    font-size: 16px !important;
}
.yoti-dropdown-toggle.open .chev {
    transform: rotate(180deg) !important;
}
.yoti-dropdown-panel {
    display: none !important;
    padding: 12px !important;
}
.yoti-dropdown-panel.open {
    display: block !important;
}

/* ---------- Split ---------- */
.yoti-split {
    display: grid !important;
    grid-template-columns: 42% 58% !important;
    gap: 12px !important;
}
.yoti-left, .yoti-right {
    border: 1px solid #e6ebf2 !important;
    border-radius: 10px !important;
    background: #fff !important;
    padding: 10px !important;
}
.yoti-left {
    max-height: 620px !important;
    overflow: auto !important;
}
.yoti-left.full {
    max-height: unset !important;
}
.yoti-left h3 {
    margin: 0 0 10px !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #1d2a3a !important;
}

/* ---------- Check cards ---------- */
.yoti-check-card {
    border: 1px solid #e9edf3 !important;
    border-radius: 8px !important;
    padding: 10px !important;
    margin-bottom: 10px !important;
    background: #fff !important;
}
.yoti-check-card p {
    margin: 0 0 6px !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
}
.yoti-subchecks, .yoti-fields {
    margin: 6px 0 0 !important;
    padding-left: 16px !important;
}
.yoti-subchecks li, .yoti-fields li {
    margin-bottom: 4px !important;
    font-size: 13px !important;
}

/* ---------- Viewer ---------- */
.yoti-viewer {
    display: flex !important;
    flex-direction: column !important;
    min-height: 450px !important;
}
.yoti-viewer-count {
    align-self: center !important;
    background: #4f5d75 !important;
    color: #fff !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 5px 12px !important;
    margin-bottom: 10px !important;
}
.yoti-viewer-stage {
    flex: 1 !important;
    background: #e6ebf2 !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    min-height: 350px !important;
}
.yoti-viewer-image {
    display: none !important;
    max-width: 100% !important;
    max-height: 72vh !important;
    object-fit: contain !important;
}
.yoti-viewer-image.active {
    display: block !important;
}
.yoti-viewer-controls {
    margin-top: 10px !important;
    display: flex !important;
    justify-content: center !important;
    gap: 8px !important;
}
.yoti-viewer-controls button {
    border: 1px solid #ced6e2 !important;
    background: #fff !important;
    border-radius: 8px !important;
    min-width: 42px !important;
    height: 36px !important;
    cursor: pointer !important;
    font-size: 16px !important;
}
.yoti-viewer-controls button:hover {
    background: #f2f6fc !important;
}

.yoti-empty, .yoti-empty-block, .yoti-muted {
    color: #6b7785 !important;
    font-size: 13px !important;
}
.yoti-empty {
    border: 1px dashed #d3dae6 !important;
    border-radius: 10px !important;
    min-height: 350px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #fbfcfe !important;
}
.yoti-empty-block {
    border: 1px dashed #d3dae6 !important;
    border-radius: 10px !important;
    padding: 16px !important;
    background: #fbfcfe !important;
}

.yoti-message {
    border-radius: 10px !important;
    padding: 12px 14px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}
.yoti-message--info { background: #eaf2ff !important; color: #1e4da1 !important; }
.yoti-message--warn { background: #fff4df !important; color: #9a6400 !important; }
.yoti-message--error { background: #fdecec !important; color: #a12222 !important; }

.yoti-json {
    background: #f7f9fc !important;
    border: 1px solid #e6ebf2 !important;
    border-radius: 8px !important;
    padding: 10px !important;
    font-size: 12px !important;
    overflow: auto !important;
    margin: 8px 0 0 !important;
}

.cmp-badge {
    display: inline-block !important;
    margin-top: 8px !important;
    padding: 4px 8px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    border: 1px solid transparent !important;
}

.badge-match {
    background: #e7f7ee !important;
    color: #0f7b39 !important;
    border-color: #bfe8ce !important;
}

.badge-mismatch {
    background: #fdecec !important;
    color: #a12222 !important;
    border-color: #f5c2c2 !important;
}

.badge-neutral {
    background: #eef2f7 !important;
    color: #536277 !important;
    border-color: #d8e0ea !important;
}

.yoti-pdf{
    display:inline-block !important;
    text-decoration:none !important;
    border:1px solid #2C3C64 !important;
    color:#2C3C64 !important;
    background:#fff !important;
    border-radius:8px !important;
    padding:8px 12px !important;
    font-size:13px !important;
    font-weight:700 !important;
}
.yoti-pdf:hover{ background:#eef2ff !important; }

/* ---------- Responsive ---------- */
@media (max-width: 1200px) {
    .yoti-summary-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}
@media (max-width: 900px) {
    .yoti-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .yoti-summary-item--wide {
        grid-column: span 2 !important;
    }
    .yoti-split {
        grid-template-columns: 1fr !important;
    }
    .yoti-left {
        max-height: none !important;
    }
    .yoti-viewer {
        min-height: 320px !important;
    }
}
@media (max-width: 560px) {
    .yoti-page {
        padding: 12px !important;
    }
    .yoti-shell {
        padding: 12px !important;
    }
    .yoti-summary-grid {
        grid-template-columns: 1fr !important;
    }
    .yoti-summary-item--wide {
        grid-column: span 1 !important;
    }
    .yoti-topbar {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
</style>
@endsection

@section('pageJavascript')
<script>
(function () {
    // Dropdown behaviour
    const toggles = document.querySelectorAll('.yoti-dropdown-toggle');
    toggles.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-target');
            const panel = document.getElementById(id);
            const isOpen = panel.classList.contains('open');

            panel.classList.toggle('open', !isOpen);
            this.classList.toggle('open', !isOpen);
            this.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        });
    });

    // Per-viewer carousel
    document.querySelectorAll('[data-viewer]').forEach(viewer => {
        const imgs = Array.from(viewer.querySelectorAll('.yoti-viewer-image'));
        const currentEl = viewer.querySelector('[data-current]');
        const prev = viewer.querySelector('[data-prev]');
        const next = viewer.querySelector('[data-next]');

        if (!imgs.length) return;

        let index = 0;

        function render() {
            imgs.forEach((img, i) => img.classList.toggle('active', i === index));
            if (currentEl) currentEl.textContent = String(index + 1);
            const disableNav = imgs.length <= 1;
            if (prev) prev.disabled = disableNav;
            if (next) next.disabled = disableNav;
        }

        if (prev) {
            prev.addEventListener('click', () => {
                index = (index - 1 + imgs.length) % imgs.length;
                render();
            });
        }

        if (next) {
            next.addEventListener('click', () => {
                index = (index + 1) % imgs.length;
                render();
            });
        }

        render();
    });
})();
</script>
@endsection

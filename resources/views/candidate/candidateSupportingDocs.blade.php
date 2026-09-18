@extends('layout.candidate')

@section('form-content')
<style>
/* --------------------------------------------------------------------------
 | Supporting Documents - guided candidate experience
 * -------------------------------------------------------------------------- */
.form-section{padding:20px 20px 5px;margin-bottom:20px}
.section-title{color:#2C3C64;font-weight:700;margin:0 0 15px;display:flex;align-items:center;gap:10px}
.section-title:after{content:"";flex:1;height:1px;background:#e5e9f2}
.field-row{margin-left:-10px;margin-right:-10px}
.field-row .form-group{padding-left:10px;padding-right:10px;margin-bottom:15px}
.form-control{height:40px}
.form-control:focus{
    border-color:var(--brand) !important;
    -webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(44,60,100,.18) !important;
    box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(44,60,100,.18) !important;
}
.control-label{font-weight:600;color:#2C3C64}
.help-block{margin-top:6px;color:#6b7480}
.has-error .control-label,.has-error .help-block{color:#b94a48}
.has-error .form-control{border-color:#b94a48;box-shadow:none}
.hidden{display:none !important}

/* Intro */
.supporting-intro{
    background:#fff;
    border:1px solid #e3e8ef;
    border-radius:12px;
    padding:22px;
    margin-bottom:18px;
}
.supporting-intro h2{margin:0 0 8px;color:#2C3C64;font-size:24px;font-weight:700}
.supporting-intro p{margin:0;color:#566273;font-size:15px;line-height:1.65;max-width:850px}

/* Main guidance */
.guidance-card{
    background:#fff;
    border:1px solid #e3e8ef;
    border-radius:12px;
    overflow:hidden;
    margin-bottom:18px;
}
.guidance-card-header{
    padding:18px 20px;
    border-bottom:1px solid #edf0f4;
    display:flex;
    align-items:flex-start;
    gap:12px;
}
.guidance-card-header .guide-icon{
    width:38px;height:38px;min-width:38px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    background:#eef2f7;color:#2C3C64;font-size:18px;
}
.guidance-card-header h3{margin:0 0 4px;color:#2C3C64;font-size:18px;font-weight:700}
.guidance-card-header p{margin:0;color:#687386;line-height:1.5}
.guidance-card-body{padding:20px}

/* Candidate checklist */
.requirement-checklist{display:grid;grid-template-columns:1fr;gap:12px}
.requirement-item{
    border:1px solid #e2e7ee;
    border-radius:10px;
    padding:15px;
    display:flex;
    align-items:flex-start;
    gap:12px;
    background:#fbfcfd;
}
.requirement-icon{
    width:34px;height:34px;min-width:34px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    background:#eef2f7;color:#6b7480;font-size:15px;
}
.requirement-item.is-complete{border-color:#c9e8d4;background:#f5fbf7}
.requirement-item.is-complete .requirement-icon{background:#dff3e6;color:#257343}
.requirement-item.is-active{border-color:#ecd99c;background:#fffdf5}
.requirement-item.is-active .requirement-icon{background:#fff0bd;color:#7a5a00}
.requirement-copy{flex:1;min-width:0}
.requirement-copy strong{display:block;color:#2C3C64;margin-bottom:3px;font-size:15px}
.requirement-copy span{display:block;color:#687386;line-height:1.5}
.requirement-status{font-weight:700;margin-left:auto;white-space:nowrap;font-size:13px;padding-top:4px}
.requirement-item.is-complete .requirement-status{color:#257343}
.requirement-item.is-active .requirement-status{color:#7a5a00}

/* Recommended route */
.simple-path{
    border:1px solid #dfe5ee;
    border-radius:10px;
    padding:18px;
    background:#fafbfc;
}
.simple-path-title{display:flex;align-items:center;gap:8px;font-weight:700;color:#2C3C64;font-size:16px;margin-bottom:6px}
.recommended-badge{display:inline-block;background:#e8f7ee;color:#257343;border-radius:20px;padding:3px 8px;font-size:12px;font-weight:700}
.simple-path > p{margin:0 0 15px;color:#687386;line-height:1.55}
.path-steps{display:grid;grid-template-columns:1fr auto 1fr;gap:12px;align-items:stretch}
.path-step{background:#fff;border:1px solid #e2e7ee;border-radius:9px;padding:14px}
.path-step-number{font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#7a8596;font-weight:700;margin-bottom:5px}
.path-step strong{display:block;color:#2C3C64;margin-bottom:6px}
.path-step p{margin:0;color:#687386;font-size:13px;line-height:1.5}
.path-arrow{align-self:center;color:#a1a9b5;font-size:18px}

.common-docs{display:flex;flex-wrap:wrap;gap:7px;margin-top:9px}
.common-doc-pill{
    display:inline-block;
    border:1px solid #dfe5ee;
    background:#fff;
    border-radius:18px;
    padding:5px 9px;
    color:#445064;
    font-size:12px;
}

/* Live guidance */
.next-step-card{
    border:2px solid #e2e7ee;
    border-radius:11px;
    padding:18px;
    margin-top:16px;
    background:#fff;
}
.next-step-card.state-success{border-color:#bfe3cc;background:#f5fbf7}
.next-step-card.state-warning{border-color:#ead48d;background:#fffdf5}
.next-step-card.state-info{border-color:#ced9e9;background:#f8fafd}
.next-step-heading{display:flex;align-items:flex-start;gap:11px}
.next-step-icon{font-size:21px;line-height:1;color:#2C3C64;padding-top:2px}
.next-step-card.state-success .next-step-icon{color:#257343}
.next-step-card.state-warning .next-step-icon{color:#8a6700}
.next-step-heading h4{margin:0 0 4px;color:#2C3C64;font-size:17px;font-weight:700}
.next-step-heading p{margin:0;color:#5f6b7d;line-height:1.55}
.quick-suggestions{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}
.quick-doc{
    border:1px solid #cfd7e3;
    border-radius:6px;
    background:#fff;
    padding:7px 10px;
    color:#2C3C64;
    font-size:13px;
    cursor:pointer;
}
.quick-doc:hover,.quick-doc:focus{border-color:var(--brand);outline:none}

.progress-label{display:flex;justify-content:space-between;gap:10px;margin-top:14px;color:#687386;font-size:13px}
.requirement-progress{height:7px;background:#e9edf2;border-radius:20px;overflow:hidden;margin-top:6px}
.requirement-progress-bar{height:100%;background:var(--brand);width:0%;transition:width .2s ease}

/* Name change */
.name-evidence-card{margin-top:12px}

/* Formal rules */
.rules-details{margin-top:15px;border-top:1px solid #edf0f4;padding-top:14px}
.rules-details summary{cursor:pointer;color:#2C3C64;font-weight:600;outline:none}
.rules-details summary:hover{text-decoration:underline}
.rules-content{padding:13px 2px 0;color:#687386;line-height:1.6}
.rules-content ul{margin:8px 0 8px 20px;padding:0}
.rules-content a{font-weight:600}

/* Uploader */
.upload-section-card{background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:20px;margin-bottom:18px}
.upload-section-card h3{margin:0 0 6px;color:#2C3C64;font-size:18px;font-weight:700}
.upload-section-card > p{margin:0 0 17px;color:#687386;line-height:1.55}
.doc-row{
    border:1px solid #dfe5ee;
    border-radius:10px;
    padding:16px 14px 5px;
    margin-bottom:12px;
    background:#fafbfc;
    position:relative;
}
.doc-row-number{
    position:absolute;top:-9px;left:13px;
    padding:1px 8px;border-radius:12px;background:#eef2f7;color:#2C3C64;
    font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;
}
.doc-accept{font-size:12px;color:#6b7480;margin-top:6px}
.doc-rule-note{
    display:none;
    margin-top:7px;
    padding:8px 10px;
    border-radius:6px;
    background:#f3f6fa;
    border-left:3px solid #9aa8ba;
    color:#526074;
    font-size:12px;
    line-height:1.5;
}
.doc-rule-note.is-visible{display:block}
.upload-originals-note{
    display:flex;
    align-items:flex-start;
    gap:10px;
    padding:13px 14px;
    border:1px solid #dce4ef;
    background:#f8fafd;
    border-radius:8px;
    margin:0 0 16px;
    color:#526074;
    line-height:1.55;
}
.upload-originals-note i{color:#2C3C64;margin-top:3px}
.upload-status{font-size:13px;margin-top:6px}
.progress{height:8px;margin-top:6px}
.progress-bar{background:var(--brand)}
.badge-mini{display:inline-block;padding:3px 7px;border-radius:10px;background:#eef2f7;color:#2C3C64;font-size:12px}
.doc-actions{display:flex;gap:4px;justify-content:flex-end;flex-wrap:wrap}
.doc-actions .btn{white-space:nowrap}
.add-document-wrap{text-align:right;margin-top:3px}

/* Existing documents */
.existing-documents{background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:20px;margin-bottom:18px}
.existing-documents h3{margin:0 0 6px;color:#2C3C64;font-size:18px;font-weight:700}
.existing-documents > p{margin:0 0 15px;color:#687386}
.table-docs th{color:#2C3C64;font-weight:600;border-top:0 !important}
.table-docs td,.table-docs th{vertical-align:middle !important}
.empty-docs{padding:14px;border:1px dashed #dce2ea;border-radius:8px;background:#fafbfc;color:#687386}

/* Bottom actions */
.page-actions{display:flex;justify-content:flex-end;align-items:center;gap:9px;padding:5px 20px 20px}
.page-actions .btn{min-width:130px}

/* Non DBS */
.general-doc-note{background:#f8fafd;border:1px solid #dce4ef;border-radius:10px;padding:16px;color:#526074;line-height:1.6}

@media (max-width:767px){
    .form-section{padding:14px 6px 3px}
    .supporting-intro,.guidance-card-body,.upload-section-card,.existing-documents{padding:16px}
    .guidance-card-header{padding:16px}
    .path-steps{grid-template-columns:1fr}
    .path-arrow{transform:rotate(90deg);justify-self:center}
    .requirement-item{flex-wrap:wrap}
    .requirement-status{width:100%;margin-left:46px;padding-top:0}
    .doc-actions{justify-content:flex-start}
    .page-actions{padding:5px 6px 18px;flex-direction:column-reverse;align-items:stretch}
    .page-actions .btn{width:100%}
}
</style>

<form class="form-horizontal" novalidate onsubmit="return false;">
    @csrf
    <input type="hidden" id="applicationID" value="{{ $applicantID->id ?? '' }}">
    <input type="hidden" id="existingCount" value="{{ isset($existingDocs) ? $existingDocs->count() : 0 }}">
    <input type="hidden" id="needsBasicDBS" value="{{ ((int)($RequiredChecks->basicDBS ?? 0) > 0) ? 1 : 0 }}">
    <input type="hidden" id="hasPreviousName" value="{{ (int)($hasPreviousName ?? 0) }}">

    <div class="p-5 bg-light rounded">

        <div class="supporting-intro">
            <h2>Supporting documents</h2>
            <p>
                This page will guide you through the documents you need. Choose the documents you have and the checklist below will tell you what is still needed.
            </p>
        </div>

        {{-- DBS guidance --}}
        <div id="requiredDocsPanel" class="guidance-card">
            <div class="guidance-card-header">
                <div class="guide-icon"><i class="fa fa-id-card"></i></div>
                <div>
                    <h3>What documents do I need?</h3>
                    <p>For most Basic DBS applications, the easiest option is one primary identity document plus one other eligible document. We will update this guidance as you choose or upload documents.</p>
                </div>
            </div>

            <div class="guidance-card-body">
                <div class="requirement-checklist">
                    <div class="requirement-item is-active" id="identityRequirementItem">
                        <div class="requirement-icon" id="identityRequirementIcon"><i class="fa fa-id-badge"></i></div>
                        <div class="requirement-copy">
                            <strong>Identity documents</strong>
                            <span id="identityRequirementText">Choose your first identity document below.</span>
                        </div>
                        <div class="requirement-status" id="identityRequirementStatus">Required</div>
                    </div>

                    @if(!empty($hasPreviousName))
                        <div class="requirement-item is-active name-evidence-card" id="nameRequirementItem">
                            <div class="requirement-icon" id="nameRequirementIcon"><i class="fa fa-link"></i></div>
                            <div class="requirement-copy">
                                <strong>Proof of your previous name</strong>
                                <span id="nameRequirementText">Please also provide a marriage/civil partnership certificate or deed poll.</span>
                            </div>
                            <div class="requirement-status" id="nameRequirementStatus">Required</div>
                        </div>
                    @endif
                </div>

                <div class="simple-path" style="margin-top:16px;">
                    <div class="simple-path-title">
                        Recommended option
                        <span class="recommended-badge">Usually easiest</span>
                    </div>
                    <p>If you have one of the primary identity documents below, the normal requirement is one primary document plus one more eligible document. Occasionally a third document may be needed if the first two do not confirm both your name and date of birth.</p>

                    <div class="path-steps">
                        <div class="path-step">
                            <div class="path-step-number">Step 1</div>
                            <strong>Choose one primary identity document</strong>
                            <div class="common-docs">
                                <span class="common-doc-pill">Passport</span>
                                <span class="common-doc-pill">UK photocard driving licence</span>
                                <span class="common-doc-pill">eVisa</span>
                                <span class="common-doc-pill">BRP</span>
                                <span class="common-doc-pill">Eligible birth certificate</span>
                            </div>
                        </div>

                        <div class="path-arrow"><i class="fa fa-arrow-right"></i></div>

                        <div class="path-step">
                            <div class="path-step-number">Step 2</div>
                            <strong>Then choose one more eligible document</strong>
                            <div class="common-docs">
                                <span class="common-doc-pill">Bank statement</span>
                                <span class="common-doc-pill">Utility bill</span>
                                <span class="common-doc-pill">Council Tax statement</span>
                                <span class="common-doc-pill">P45 / P60</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="next-step-card state-info" id="nextStepCard" aria-live="polite">
                    <div class="next-step-heading">
                        <div class="next-step-icon" id="nextStepIcon"><i class="fa fa-arrow-circle-right"></i></div>
                        <div>
                            <h4 id="nextStepTitle">Start with a primary identity document</h4>
                            <p id="nextStepText">Choose one of the common documents below, or select another accepted document in the upload section.</p>
                        </div>
                    </div>

                    <div class="quick-suggestions" id="quickSuggestions"></div>

                    <div class="progress-label">
                        <span id="progressText">0 of 2 identity documents selected</span>
                        <span id="routeFriendlyText">Recommended option</span>
                    </div>
                    <div class="requirement-progress" aria-hidden="true">
                        <div class="requirement-progress-bar" id="requirementProgressBar"></div>
                    </div>
                </div>

                <details class="rules-details">
                    <summary>Don't have the recommended documents? View alternative documents and the formal DBS rules</summary>
                    <div class="rules-content">
                        <p><strong>Alternative option:</strong> if you do not have a primary Group 1 document, you can provide one eligible Group 2a document plus two additional documents from Group 2a or Group 2b.</p>
                        <ul>
                            <li>The same document type cannot be counted twice.</li>
                            <li>If you have used a previous name, separate name-change evidence may also be required.</li>
                            <li>The document dropdown below groups the accepted document types for you.</li>
                        </ul>
                        <a href="https://www.gov.uk/government/publications/basic-check-guidance-and-policies/basic-check-id-checking-guidelines-from-22-april-2025#document-lists" target="_blank" rel="noopener noreferrer">
                            View the official DBS document lists <i class="fa fa-external-link"></i>
                        </a>
                    </div>
                </details>
            </div>
        </div>

        {{-- Shown when DBS is not required --}}
        <div id="generalDocumentsPanel" class="guidance-card hidden">
            <div class="guidance-card-header">
                <div class="guide-icon"><i class="fa fa-file-text-o"></i></div>
                <div>
                    <h3>Documents for your application</h3>
                    <p>Your application does not require the DBS identity-document combination shown above.</p>
                </div>
            </div>
            <div class="guidance-card-body">
                <div class="general-doc-note">
                    Upload the supporting documents requested for your screening application. You can add more than one file and return to this page later if needed.
                </div>
            </div>
        </div>

        {{-- Existing documents --}}
        <div class="existing-documents">
            <h3>Your uploaded documents</h3>
            <p>Documents already saved to your application appear here.</p>

            <div id="existingDocsArea">
                @if(isset($existingDocs) && $existingDocs->count())
                    <div class="table-responsive">
                        <table class="table table-docs" id="existingDocsTable">
                            <thead>
                                <tr>
                                    <th style="width:35%">File</th>
                                    <th style="width:30%">Document type</th>
                                    <th style="width:10%">Format</th>
                                    <th style="width:25%" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($existingDocs as $d)
                                    <tr data-id="{{ $d->id }}" data-category="{{ $d->document_category }}">
                                        <td><i class="fa fa-check-circle text-success"></i> {{ $d->document_name }}</td>
                                        <td>{{ $d->document_category }}</td>
                                        <td>{{ strtoupper($d->document_type) }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-default btn-sm" href="{{ route('candidate.supporting.download', ['id' => $d->id]) }}">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-link text-danger btn-sm btn-del-doc" data-id="{{ $d->id }}">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-docs" id="noExistingDocsMsg">
                        <i class="fa fa-info-circle"></i> You have not uploaded any documents yet.
                    </div>
                @endif
            </div>
        </div>

        {{-- Options template. Labels are candidate-friendly; values remain unchanged for validation. --}}
        <template id="docTypeOptionsTpl">
            <option value="">Choose the document you are uploading…</option>

            <optgroup label="Primary identity documents — recommended">
                <option value="Passport">Passport</option>
                <option value="Driving licence photocard (UK/IoM/CI)">UK/IoM/Channel Islands photocard driving licence</option>
                <option value="e-Visa">eVisa (UKVI View and Prove)</option>
                <option value="Biometric residence permit (BRP)">Biometric residence permit (BRP)</option>
                <option value="Application Registration Card (ARC)">Application Registration Card (ARC)</option>
                <option value="Birth certificate (within 12 months of birth)">Birth certificate issued within 12 months of birth</option>
                <option value="Adoption certificate">Adoption certificate</option>
            </optgroup>

            <optgroup label="Other government-issued documents">
                <option value="Birth certificate (more than 12 months after birth)">Birth certificate issued more than 12 months after birth</option>
                <option value="Marriage/civil partnership certificate">Marriage / civil partnership certificate</option>
                <option value="Driving licence photocard (non-UK)">Non-UK photocard driving licence</option>
                <option value="Driving licence paper (UK pre-2000)">UK paper driving licence (pre-2000)</option>
                <option value="HM Forces ID / Veteran card">HM Forces ID / Veteran card</option>
                <option value="Firearms licence">Firearms licence</option>
                <option value="Immigration document/visa/work permit (non-UK)">Immigration document / visa / work permit (non-UK)</option>
            </optgroup>

            <optgroup label="Additional supporting documents">
                <option value="Mortgage statement">Mortgage statement</option>
                <option value="Bank/building society statement">Bank / building society statement</option>
                <option value="Bank account opening letter">Bank account opening letter</option>
                <option value="Credit card statement">Credit card statement</option>
                <option value="Financial statement (pension/endowment)">Financial statement (pension / endowment)</option>
                <option value="P45">P45</option>
                <option value="P60">P60</option>
                <option value="Council Tax statement">Council Tax statement</option>
                <option value="Utility bill (not mobile)">Utility bill (not mobile phone)</option>
                <option value="Benefit statement">Benefit statement</option>
                <option value="Government/local council entitlement letter">Government / local council entitlement letter</option>
                <option value="HMRC self-assessment/tax demand letter">HMRC self-assessment / tax demand letter</option>
                <option value="EHIC/GHIC">EHIC / GHIC</option>
                <option value="EEA National ID card">EEA National ID card</option>
                <option value="Irish Passport Card">Irish Passport Card</option>
                <option value="PASS card">PASS card</option>
                <option value="Letter from school/college (16–19)">Letter from school / college (age 16–19)</option>
                <option value="Letter of sponsorship (non-UK)">Letter of sponsorship (non-UK)</option>
            </optgroup>

            <optgroup label="Previous name / other">
                <option value="Deed poll / change of name">Deed poll / change of name</option>
                <option value="Other">Other supporting document</option>
            </optgroup>
        </template>

        {{-- Upload area --}}
        <div class="upload-section-card" id="uploadSection">
            <h3>Add documents</h3>
            <p>For each document, tell us what it is and then choose the file from your device. Accepted files: <strong>PDF, JPG, PNG or WEBP</strong>, up to <strong>10MB each</strong>.</p>

            <div id="docs_container">
                <div class="doc-row" data-index="0">
                    <span class="doc-row-number">Document 1</span>
                    <div class="row field-row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">What document are you uploading? <span class="text-danger">*</span></label>
                                <select class="form-control doc-type" required></select>
                                <span class="help-block">Choose the option that best describes the document.</span>
                                <div class="doc-rule-note" aria-live="polite"></div>
                            </div>
                        </div>

                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">Choose file <span class="text-danger">*</span></label>
                                <input type="file" class="form-control doc-file" accept="application/pdf,image/jpeg,image/png,image/webp" required>
                                <span class="help-block doc-accept">PDF or image, maximum 10MB.</span>
                                <div class="progress hidden"><div class="progress-bar" role="progressbar" style="width:0%"></div></div>
                                <div class="upload-status hidden"></div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label sr-only">Document actions</label>
                                <div class="doc-actions">
                                    <button type="button" class="btn btn-link text-danger btn-sm remove-row" title="Remove this row">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                                <div class="text-right">
                                    <span class="badge-mini hidden uploaded-badge"><i class="fa fa-check"></i> Uploaded</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="add-document-wrap">
                <button type="button" class="btn btn-default" id="add_doc_btn">
                    <i class="fa fa-plus"></i> Add another document
                </button>
            </div>
        </div>

        <div id="overall_message" class="alert hidden" role="alert" aria-live="polite"></div>

        <div class="page-actions">
            <a class="btn btn-default" href="{{ route('candidate.welcome') }}">Back</a>
            <button type="button" class="btn forceBgClassified" id="upload_all_btn">
                Save and continue <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>
</form>

<script>
(function(){
    var container       = document.getElementById('docs_container');
    var addBtn          = document.getElementById('add_doc_btn');
    var uploadAllBtn    = document.getElementById('upload_all_btn');
    var overallMsg      = document.getElementById('overall_message');
    var existingCountEl = document.getElementById('existingCount');
    var existingCount   = parseInt((existingCountEl && existingCountEl.value) || '0', 10);
    var idx             = container.querySelectorAll('.doc-row').length || 0;

    var uploadUrl   = "{{ route('candidate.supporting.upload') }}";
    var completeUrl = "{{ route('candidate.supporting.complete') }}";
    var deleteUrl   = "{{ route('candidate.supporting.delete') }}";

    var tokenInput = document.querySelector('input[name="_token"]');
    var token = tokenInput
        ? tokenInput.value
        : ((document.querySelector('meta[name="csrf-token"]') || {}).content || '');

    var applicationID = (document.getElementById('applicationID') ? document.getElementById('applicationID').value : '').trim();
    var needsBasicDBS = (document.getElementById('needsBasicDBS').value || '0') === '1';
    var hasPreviousName = (document.getElementById('hasPreviousName').value || '0') === '1';

    var requiredDocsPanel = document.getElementById('requiredDocsPanel');
    var generalDocumentsPanel = document.getElementById('generalDocumentsPanel');

    if (!needsBasicDBS) {
        requiredDocsPanel.classList.add('hidden');
        generalDocumentsPanel.classList.remove('hidden');
    }

    var optTpl = document.getElementById('docTypeOptionsTpl');
    var docOptionsHtml = optTpl ? optTpl.innerHTML : '';

    function populateSelects(scope){
        (scope || container).querySelectorAll('.doc-type').forEach(function(sel){
            if (!sel.options.length && docOptionsHtml) sel.innerHTML = docOptionsHtml;
        });
    }
    populateSelects(container);

    /* These values intentionally match the existing server-side DBS rules. */
    var GROUP_1 = new Set([
        'Passport',
        'Driving licence photocard (UK/IoM/CI)',
        'e-Visa',
        'Biometric residence permit (BRP)',
        'Application Registration Card (ARC)',
        'Birth certificate (within 12 months of birth)',
        'Adoption certificate'
    ]);

    var GROUP_2A = new Set([
        'Birth certificate (more than 12 months after birth)',
        'Marriage/civil partnership certificate',
        'Driving licence photocard (non-UK)',
        'Driving licence paper (UK pre-2000)',
        'HM Forces ID / Veteran card',
        'Firearms licence',
        'Immigration document/visa/work permit (non-UK)'
    ]);

    var GROUP_2B = new Set([
        'Mortgage statement',
        'Bank/building society statement',
        'Bank account opening letter',
        'Credit card statement',
        'Financial statement (pension/endowment)',
        'P45',
        'P60',
        'Council Tax statement',
        'Utility bill (not mobile)',
        'Benefit statement',
        'Government/local council entitlement letter',
        'HMRC self-assessment/tax demand letter',
        'EHIC/GHIC',
        'EEA National ID card',
        'Irish Passport Card',
        'PASS card',
        'Letter from school/college (16–19)',
        'Letter of sponsorship (non-UK)'
    ]);

    var NAME_CHANGE_OK = new Set([
        'Marriage/civil partnership certificate',
        'Deed poll / change of name'
    ]);

    var PRIMARY_SUGGESTIONS = [
        ['Passport', 'Passport'],
        ['Driving licence photocard (UK/IoM/CI)', 'UK driving licence'],
        ['e-Visa', 'eVisa'],
        ['Biometric residence permit (BRP)', 'BRP'],
        ['Birth certificate (within 12 months of birth)', 'Birth certificate']
    ];

    var SECONDARY_SUGGESTIONS = [
        ['Bank/building society statement', 'Bank statement'],
        ['Utility bill (not mobile)', 'Utility bill'],
        ['Council Tax statement', 'Council Tax'],
        ['P45', 'P45'],
        ['P60', 'P60']
    ];

    var ALTERNATIVE_START_SUGGESTIONS = [
        ['Birth certificate (more than 12 months after birth)', 'Birth certificate'],
        ['Marriage/civil partnership certificate', 'Marriage certificate'],
        ['Driving licence photocard (non-UK)', 'Non-UK driving licence'],
        ['HM Forces ID / Veteran card', 'HM Forces / Veteran card']
    ];

    var NAME_SUGGESTIONS = [
        ['Marriage/civil partnership certificate', 'Marriage / civil partnership certificate'],
        ['Deed poll / change of name', 'Deed poll']
    ];

    /*
     * Short candidate-facing reminders for document validity.
     * These do not replace the server-side route validation; they simply help
     * candidates choose an appropriate document before uploading it.
     */
    var DOCUMENT_NOTES = {
        'Passport': 'Use a current valid passport. A UK passport may be accepted up to 6 months after expiry.',
        'Driving licence photocard (UK/IoM/CI)': 'Use a current and valid photocard driving licence.',
        'e-Visa': 'Use your UKVI eVisa details. The identity checker may need an immigration-status share code.',
        'Biometric residence permit (BRP)': 'Use your UK BRP. Some BRPs showing indefinite status may be accepted after expiry; your ID checker will confirm this.',
        'Application Registration Card (ARC)': 'The ARC must be checked using the Home Office Employer Checking Service.',
        'Birth certificate (within 12 months of birth)': 'This must be an original birth certificate issued within 12 months of birth.',
        'Driving licence photocard (non-UK)': 'Use a current and valid photocard driving licence issued outside the UK, Isle of Man and Channel Islands.',
        'Driving licence paper (UK pre-2000)': 'This must have been issued before March 2000, still be valid, and show up-to-date name and address details.',
        'Mortgage statement': 'The statement should normally have been issued within the last 12 months.',
        'Bank/building society statement': 'The statement should normally have been issued within the last 3 months. A bank-printed statement should be stamped and signed by the bank.',
        'Bank account opening letter': 'The letter should normally have been issued within the last 3 months.',
        'Credit card statement': 'The statement should normally have been issued within the last 3 months.',
        'Financial statement (pension/endowment)': 'The statement should normally have been issued within the last 12 months.',
        'P45': 'The P45 should normally have been issued within the last 12 months. It must be an original document, not an online printout or PDF.',
        'P60': 'The P60 should normally have been issued within the last 12 months. It must be an original document, not an online printout or PDF.',
        'Council Tax statement': 'The statement should normally have been issued within the last 12 months.',
        'Utility bill (not mobile)': 'The bill should normally have been issued within the last 3 months. Mobile phone bills are not accepted and an online-account printout is not acceptable for the formal ID check.',
        'Benefit statement': 'The statement should normally have been issued within the last 12 months.',
        'Government/local council entitlement letter': 'The document should normally have been issued within the last 12 months.',
        'HMRC self-assessment/tax demand letter': 'The letter should normally have been issued within the last 12 months.',
        'EHIC/GHIC': 'The card must still be valid.',
        'EEA National ID card': 'The card must still be valid.',
        'Irish Passport Card': 'The card must still be valid and cannot be counted together with an Irish passport.',
        'PASS card': 'The PASS card must still be valid. Digital PASS cards must be from an approved provider and verified using the QR code.',
        'Letter from school/college (16–19)': 'This is normally only used in exceptional circumstances for eligible 16–19 year olds and should have been issued within the last month.',
        'Letter of sponsorship (non-UK)': 'This is only valid in the circumstances allowed by the DBS guidance for applicants residing outside the UK.'
    };

    function updateDocumentNote(row){
        if (!row) return;
        var select = row.querySelector('.doc-type');
        var note = row.querySelector('.doc-rule-note');
        if (!select || !note) return;

        var copy = DOCUMENT_NOTES[select.value] || '';
        note.textContent = copy;
        note.classList.toggle('is-visible', !!copy);
    }

    function updateAllDocumentNotes(){
        container.querySelectorAll('.doc-row').forEach(updateDocumentNote);
    }

    function getAllDocTypesSelected(){
        var types = [];

        document.querySelectorAll('#existingDocsTable tbody tr').forEach(function(tr){
            var cat = (tr.getAttribute('data-category') || '').trim();
            if (cat) types.push(cat);
        });

        container.querySelectorAll('.doc-row').forEach(function(row){
            var sel  = row.querySelector('.doc-type');
            var file = row.querySelector('.doc-file');
            var type = (sel && sel.value || '').trim();
            if (!type) return;

            var countsForRequirement = (file && file.disabled) || (file && file.files && file.files.length);
            if (countsForRequirement) types.push(type);
        });

        return types;
    }

    function uniqAndDups(arr){
        var seen = new Set();
        var uniq = [];
        var dups = new Set();

        arr.forEach(function(item){
            if (seen.has(item)) dups.add(item);
            else {
                seen.add(item);
                uniq.push(item);
            }
        });

        return { uniq: uniq, dups: Array.from(dups) };
    }

    function computeDBSRequirements(){
        if (!needsBasicDBS) {
            return {
                ok:true, idOk:true, nameOk:true,
                counts:{g1:0,g2a:0,g2b:0,total:0},
                route1Ok:false, route2Ok:false,
                dups:[], picked:[]
            };
        }

        var picked = getAllDocTypesSelected();
        var ud = uniqAndDups(picked);
        var types = ud.uniq;
        var g1 = 0, g2a = 0, g2b = 0, nameOKCount = 0;

        types.forEach(function(type){
            if (GROUP_1.has(type)) g1++;
            if (GROUP_2A.has(type)) g2a++;
            if (GROUP_2B.has(type)) g2b++;
            if (NAME_CHANGE_OK.has(type)) nameOKCount++;
        });

        var total = g1 + g2a + g2b;
        var route1Ok = (g1 >= 1) && (total >= 2);
        var route2Ok = (g1 === 0) && (g2a >= 1) && ((g2a + g2b) >= 3);
        var idOk = route1Ok || route2Ok;
        var nameOk = (!hasPreviousName) || (nameOKCount >= 1);

        return {
            ok:idOk && nameOk && ud.dups.length === 0,
            idOk:idOk,
            nameOk:nameOk,
            route1Ok:route1Ok,
            route2Ok:route2Ok,
            counts:{g1:g1,g2a:g2a,g2b:g2b,total:total},
            dups:ud.dups,
            picked:types
        };
    }

    function setRequirementState(itemId, iconId, statusId, textId, complete, text){
        var item = document.getElementById(itemId);
        var icon = document.getElementById(iconId);
        var status = document.getElementById(statusId);
        var copy = document.getElementById(textId);
        if (!item) return;

        item.classList.remove('is-complete','is-active');
        item.classList.add(complete ? 'is-complete' : 'is-active');
        if (icon) icon.innerHTML = complete ? '<i class="fa fa-check"></i>' : (itemId === 'nameRequirementItem' ? '<i class="fa fa-link"></i>' : '<i class="fa fa-id-badge"></i>');
        if (status) status.textContent = complete ? 'Complete' : 'Required';
        if (copy) copy.textContent = text;
    }

    function setNextStep(state, title, text, suggestions, progressCurrent, progressTarget, routeText){
        var card = document.getElementById('nextStepCard');
        var titleEl = document.getElementById('nextStepTitle');
        var textEl = document.getElementById('nextStepText');
        var iconEl = document.getElementById('nextStepIcon');
        var suggestionsEl = document.getElementById('quickSuggestions');
        var progressText = document.getElementById('progressText');
        var routeFriendlyText = document.getElementById('routeFriendlyText');
        var progressBar = document.getElementById('requirementProgressBar');

        card.classList.remove('state-success','state-warning','state-info');
        card.classList.add('state-' + state);
        titleEl.textContent = title;
        textEl.textContent = text;

        if (state === 'success') iconEl.innerHTML = '<i class="fa fa-check-circle"></i>';
        else if (state === 'warning') iconEl.innerHTML = '<i class="fa fa-exclamation-circle"></i>';
        else iconEl.innerHTML = '<i class="fa fa-arrow-circle-right"></i>';

        suggestionsEl.innerHTML = '';
        (suggestions || []).forEach(function(suggestion){
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'quick-doc';
            btn.setAttribute('data-value', suggestion[0]);
            btn.textContent = suggestion[1];
            suggestionsEl.appendChild(btn);
        });

        var safeTarget = Math.max(1, progressTarget || 1);
        var safeCurrent = Math.min(progressCurrent || 0, safeTarget);
        var percent = Math.round((safeCurrent / safeTarget) * 100);
        progressBar.style.width = percent + '%';
        progressText.textContent = safeCurrent + ' of ' + safeTarget + ' identity document' + (safeTarget === 1 ? '' : 's') + ' selected';
        routeFriendlyText.textContent = routeText || '';
    }

    function renderRequirements(){
        if (!needsBasicDBS) return;

        var r = computeDBSRequirements();

        if (r.idOk) {
            setRequirementState(
                'identityRequirementItem','identityRequirementIcon','identityRequirementStatus','identityRequirementText',
                true,
                'You have selected enough eligible identity documents.'
            );
        } else {
            setRequirementState(
                'identityRequirementItem','identityRequirementIcon','identityRequirementStatus','identityRequirementText',
                false,
                'We will tell you exactly what to add next.'
            );
        }

        if (hasPreviousName) {
            setRequirementState(
                'nameRequirementItem','nameRequirementIcon','nameRequirementStatus','nameRequirementText',
                r.nameOk,
                r.nameOk
                    ? 'You have provided evidence linking your current and previous name.'
                    : 'Please also provide a marriage/civil partnership certificate or deed poll.'
            );
        }

        if (r.dups.length) {
            setNextStep(
                'warning',
                'One document type has been selected more than once',
                'For the DBS identity check, the same document type cannot be counted twice. Please choose a different type for one of the documents.',
                [],
                0, 1,
                'Needs attention'
            );
            return;
        }

        if (r.idOk && r.nameOk) {
            setNextStep(
                'success',
                'Everything required has been provided',
                'Your identity document requirements are complete. You can add another supporting document if you want, or save and continue.',
                [],
                r.route1Ok ? 2 : 3,
                r.route1Ok ? 2 : 3,
                'Requirements complete'
            );
            return;
        }

        if (r.idOk && !r.nameOk) {
            setNextStep(
                'warning',
                'Your identity documents are complete — one more item is needed',
                'Because you told us you have used a previous name, please upload evidence linking your names.',
                NAME_SUGGESTIONS,
                r.route1Ok ? 2 : 3,
                r.route1Ok ? 2 : 3,
                'Identity complete'
            );
            return;
        }

        /* Candidate has a primary document: the simple two-document option applies. */
        if (r.counts.g1 >= 1) {
            var remainingRecommended = Math.max(0, 2 - r.counts.total);
            setNextStep(
                'info',
                remainingRecommended === 1 ? 'Great — now add one more document' : 'Your recommended identity combination is nearly complete',
                'Your primary identity document can be used. Now choose one more eligible document. Common choices are shown below.',
                SECONDARY_SUGGESTIONS,
                Math.min(r.counts.total, 2),
                2,
                'Recommended option'
            );
            return;
        }

        /* Candidate has started the alternative option. */
        if (r.counts.g2a >= 1) {
            var altTotal = r.counts.g2a + r.counts.g2b;
            var altRemaining = Math.max(0, 3 - altTotal);
            setNextStep(
                'info',
                'You are using the alternative document option',
                'The document you selected can start the alternative option. Please add ' + altRemaining + ' more eligible document' + (altRemaining === 1 ? '' : 's') + ' from the government or additional supporting document lists.',
                SECONDARY_SUGGESTIONS,
                Math.min(altTotal, 3),
                3,
                'Alternative option'
            );
            return;
        }

        /* Additional supporting document selected before a qualifying starting document. */
        if (r.counts.g2b > 0) {
            setNextStep(
                'warning',
                'This document can help, but you still need an identity document',
                'The document you selected can be used as additional evidence, but it cannot start the check on its own. The easiest next step is to add a primary identity document.',
                PRIMARY_SUGGESTIONS.concat(ALTERNATIVE_START_SUGGESTIONS.slice(0,2)),
                r.counts.g2b,
                3,
                'Choose an identity document next'
            );
            return;
        }

        /* Nothing selected yet. */
        setNextStep(
            'info',
            'Start with a primary identity document',
            'The easiest option for most people is a passport, UK photocard driving licence, eVisa, BRP or eligible birth certificate.',
            PRIMARY_SUGGESTIONS,
            0,
            2,
            'Recommended option'
        );
    }

    function firstAvailableUploadRow(){
        var rows = Array.from(container.querySelectorAll('.doc-row'));
        var row = rows.find(function(r){
            var select = r.querySelector('.doc-type');
            var file = r.querySelector('.doc-file');
            return select && file && !select.disabled && !file.disabled && !select.value;
        });

        if (!row) {
            container.insertAdjacentHTML('beforeend', rowTemplate(idx));
            row = container.querySelector('.doc-row[data-index="' + idx + '"]');
            idx++;
        }
        return row;
    }

    document.addEventListener('click', function(e){
        var quick = e.target.closest('.quick-doc');
        if (!quick) return;

        var value = quick.getAttribute('data-value');
        var row = firstAvailableUploadRow();
        var select = row.querySelector('.doc-type');
        var file = row.querySelector('.doc-file');
        select.value = value;
        updateDocumentNote(row);
        row.scrollIntoView({behavior:'smooth', block:'center'});
        window.setTimeout(function(){ file.focus(); }, 350);
        renderRequirements();
    });

    function rowTemplate(i){
        return ''+
        '<div class="doc-row" data-index="'+i+'">'+
            '<span class="doc-row-number">Document '+(i+1)+'</span>'+
            '<div class="row field-row">'+
                '<div class="col-sm-5">'+
                    '<div class="form-group">'+
                        '<label class="control-label">What document are you uploading? <span class="text-danger">*</span></label>'+
                        '<select class="form-control doc-type" required>'+docOptionsHtml+'</select>'+
                        '<span class="help-block">Choose the option that best describes the document.</span>'+
                        '<div class="doc-rule-note" aria-live="polite"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-5">'+
                    '<div class="form-group">'+
                        '<label class="control-label">Choose file <span class="text-danger">*</span></label>'+
                        '<input type="file" class="form-control doc-file" accept="application/pdf,image/jpeg,image/png,image/webp" required>'+
                        '<span class="help-block doc-accept">PDF or image, maximum 10MB.</span>'+
                        '<div class="progress hidden"><div class="progress-bar" role="progressbar" style="width:0%"></div></div>'+
                        '<div class="upload-status hidden"></div>'+
                    '</div>'+
                '</div>'+
                '<div class="col-sm-2">'+
                    '<div class="form-group">'+
                        '<label class="control-label sr-only">Document actions</label>'+
                        '<div class="doc-actions">'+
                            '<button type="button" class="btn btn-link text-danger btn-sm remove-row" title="Remove this row"><i class="fa fa-trash"></i> Remove</button>'+
                        '</div>'+
                        '<div class="text-right"><span class="badge-mini hidden uploaded-badge"><i class="fa fa-check"></i> Uploaded</span></div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>';
    }

    function renumberRows(){
        container.querySelectorAll('.doc-row').forEach(function(row, index){
            var label = row.querySelector('.doc-row-number');
            if (label) label.textContent = 'Document ' + (index + 1);
        });
    }

    addBtn.addEventListener('click', function(){
        container.insertAdjacentHTML('beforeend', rowTemplate(idx++));
        renumberRows();
    });

    container.addEventListener('click', function(e){
        var remove = e.target.closest('.remove-row');
        if (!remove) return;

        var row = remove.closest('.doc-row');
        if (!row) return;

        if (container.querySelectorAll('.doc-row').length > 1) {
            row.parentNode.removeChild(row);
            renumberRows();
        } else {
            var select = row.querySelector('.doc-type');
            var file = row.querySelector('.doc-file');
            if (!select.disabled) select.value = '';
            if (!file.disabled) file.value = '';
        }
        renderRequirements();
    });

    container.addEventListener('change', function(e){
        var row = e.target.closest('.doc-row');
        if (row) updateDocumentNote(row);
        renderRequirements();
    });

    function setGroupError(el, on, msg){
        if (!el) return;
        var fg = el.closest('.form-group');
        if (!fg) return;
        fg.classList.toggle('has-error', !!on);

        var hb = fg.querySelector('.help-block');
        if (hb) {
            if (!hb.hasAttribute('data-original-help')) hb.setAttribute('data-original-help', hb.textContent);
            hb.textContent = on ? (msg || 'Please fix this field.') : hb.getAttribute('data-original-help');
        }
    }

    function validateRow(row){
        var file = row.querySelector('.doc-file');
        var type = row.querySelector('.doc-type');
        var ok = true;

        setGroupError(file, false);
        setGroupError(type, false);

        if (!file.files || !file.files[0]) {
            setGroupError(file, true, 'Please choose a file.');
            return false;
        }

        var f = file.files[0];
        var mimeOk = /^(application\/pdf|image\/(jpeg|png|webp))$/i.test(f.type || '');
        var sizeOk = f.size <= 10 * 1024 * 1024;

        if (!mimeOk) {
            setGroupError(file, true, 'File must be a PDF, JPG, PNG or WEBP image.');
            ok = false;
        } else if (!sizeOk) {
            setGroupError(file, true, 'File must be 10MB or less.');
            ok = false;
        }

        if (!type.value) {
            setGroupError(type, true, 'Please tell us what type of document this is.');
            ok = false;
        }

        return ok;
    }

    function uploadRow(row){
        return new Promise(function(resolve){
            var fileInput = row.querySelector('.doc-file');
            var typeSel   = row.querySelector('.doc-type');
            var progWrap  = row.querySelector('.progress');
            var progBar   = row.querySelector('.progress-bar');
            var statusEl  = row.querySelector('.upload-status');
            var badgeEl   = row.querySelector('.uploaded-badge');

            statusEl.classList.add('hidden');
            statusEl.textContent = '';
            progWrap.classList.remove('hidden');
            progBar.style.width = '0%';

            var fd = new FormData();
            fd.append('_token', token);
            fd.append('new_document_category', typeSel.value);
            fd.append('new_document', fileInput.files[0]);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl, true);

            xhr.upload.addEventListener('progress', function(event){
                if (event.lengthComputable) {
                    progBar.style.width = Math.round((event.loaded / event.total) * 100) + '%';
                }
            });

            xhr.onreadystatechange = function(){
                if (xhr.readyState !== 4) return;

                progBar.style.width = '100%';

                try {
                    var res = JSON.parse(xhr.responseText || '{}');
                    if (xhr.status === 200 && res.status === 1) {
                        statusEl.classList.remove('hidden');
                        statusEl.innerHTML = '<span class="text-success"><i class="fa fa-check"></i> Uploaded successfully</span>';
                        badgeEl.classList.remove('hidden');
                        existingCount++;
                        existingCountEl.value = existingCount;

                        fileInput.disabled = true;
                        typeSel.disabled = true;
                        row.querySelectorAll('.remove-row').forEach(function(btn){
                            btn.disabled = true;
                            btn.classList.add('disabled');
                        });

                        renderRequirements();
                        resolve({ok:true});
                    } else {
                        statusEl.classList.remove('hidden');
                        statusEl.innerHTML = '<span class="text-danger"><i class="fa fa-times"></i> Upload failed. Please try again.</span>';
                        resolve({ok:false});
                    }
                } catch (err) {
                    statusEl.classList.remove('hidden');
                    statusEl.innerHTML = '<span class="text-danger"><i class="fa fa-times"></i> The server could not process this upload.</span>';
                    resolve({ok:false});
                }
            };

            xhr.send(fd);
        });
    }

    document.addEventListener('click', async function(e){
        var btn = e.target.closest('.btn-del-doc');
        if (!btn) return;

        var id = btn.getAttribute('data-id');
        if (!id || !confirm('Delete this document?')) return;

        try {
            var resp = await fetch(deleteUrl, {
                method:'POST',
                headers:{'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},
                body:new URLSearchParams({_token:token,id:id})
            });
            var data = await resp.json();

            if (resp.ok && data.ok) {
                var row = document.querySelector('#existingDocsTable tr[data-id="'+id+'"]');
                if (row) row.parentNode.removeChild(row);

                existingCount = Math.max(0, existingCount - 1);
                existingCountEl.value = existingCount;

                var tbody = document.querySelector('#existingDocsTable tbody');
                if (tbody && !tbody.querySelector('tr')) {
                    var tableWrap = document.getElementById('existingDocsTable').closest('.table-responsive');
                    if (tableWrap) tableWrap.remove();

                    var empty = document.createElement('div');
                    empty.id = 'noExistingDocsMsg';
                    empty.className = 'empty-docs';
                    empty.innerHTML = '<i class="fa fa-info-circle"></i> You have not uploaded any documents yet.';
                    document.getElementById('existingDocsArea').appendChild(empty);
                }

                renderRequirements();
            } else {
                alert('Could not delete the document. Please try again.');
            }
        } catch (err) {
            alert('Could not delete the document. Please try again.');
        }
    });

    async function markCompleteAndGo(){
        try {
            var resp = await fetch(completeUrl, {
                method:'POST',
                headers:{'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},
                body:new URLSearchParams({_token:token})
            });
            var data = await resp.json();

            if (!resp.ok || !data.ok) {
                overallMsg.className = 'alert alert-danger';
                overallMsg.textContent = (data && data.message)
                    ? data.message
                    : 'The document requirements are not complete yet.';
                overallMsg.classList.remove('hidden');
                return false;
            }

            window.location.href = "{{ route('candidate.submit.review') }}";
            return true;
        } catch (err) {
            overallMsg.className = 'alert alert-danger';
            overallMsg.textContent = 'We could not confirm your documents. Please try again.';
            overallMsg.classList.remove('hidden');
            return false;
        }
    }

    uploadAllBtn.addEventListener('click', async function(){
        overallMsg.classList.add('hidden');

        if (!applicationID || isNaN(parseInt(applicationID, 10))) {
            overallMsg.className = 'alert alert-danger';
            overallMsg.textContent = 'We could not find your application. Please refresh the page and try again.';
            overallMsg.classList.remove('hidden');
            return;
        }

        var rows = Array.from(container.querySelectorAll('.doc-row'));
        var activeRows = rows.filter(function(row){
            var input = row.querySelector('.doc-file');
            return input && !input.disabled && input.files && input.files.length > 0;
        });

        if (activeRows.length === 0 && existingCount === 0) {
            overallMsg.className = 'alert alert-danger';
            overallMsg.textContent = 'Please add at least one document before continuing.';
            overallMsg.classList.remove('hidden');
            document.getElementById('uploadSection').scrollIntoView({behavior:'smooth',block:'start'});
            return;
        }

        if (activeRows.length === 0 && existingCount > 0) {
            if (needsBasicDBS) {
                var existingReq = computeDBSRequirements();
                if (!existingReq.ok) {
                    overallMsg.className = 'alert alert-danger';
                    overallMsg.textContent = existingReq.idOk
                        ? 'Your identity documents are complete, but another required supporting document is still missing.'
                        : 'Your DBS identity document requirements are not complete yet. Follow the guidance above to see what to add next.';
                    overallMsg.classList.remove('hidden');
                    requiredDocsPanel.scrollIntoView({behavior:'smooth',block:'start'});
                    return;
                }
            }
            await markCompleteAndGo();
            return;
        }

        var allOK = true;
        activeRows.forEach(function(row){
            if (!validateRow(row)) allOK = false;
        });
        if (!allOK) return;

        uploadAllBtn.disabled = true;
        addBtn.disabled = true;
        uploadAllBtn.innerHTML = 'Uploading… <i class="fa fa-spinner fa-spin"></i>';

        var successCount = 0;
        var failCount = 0;

        for (var i = 0; i < activeRows.length; i++) {
            var result = await uploadRow(activeRows[i]);
            if (result.ok) successCount++;
            else failCount++;
        }

        uploadAllBtn.disabled = false;
        addBtn.disabled = false;
        uploadAllBtn.innerHTML = 'Save and continue <i class="fa fa-arrow-right"></i>';

        if (needsBasicDBS) {
            renderRequirements();
            var req = computeDBSRequirements();
            if (!req.ok) {
                overallMsg.className = 'alert alert-warning';
                overallMsg.textContent = req.idOk
                    ? 'Your identity documents are complete, but the previous-name evidence is still needed.'
                    : 'Your files were uploaded, but you still need another document. Follow the guidance above to see what to add next.';
                overallMsg.classList.remove('hidden');
                requiredDocsPanel.scrollIntoView({behavior:'smooth',block:'start'});
                return;
            }
        }

        if (successCount && !failCount) {
            await markCompleteAndGo();
        } else if (successCount) {
            overallMsg.className = 'alert alert-warning';
            overallMsg.textContent = successCount + ' document' + (successCount === 1 ? '' : 's') + ' uploaded, but ' + failCount + ' failed. Please retry the failed upload' + (failCount === 1 ? '' : 's') + '.';
            overallMsg.classList.remove('hidden');
        } else {
            overallMsg.className = 'alert alert-danger';
            overallMsg.textContent = 'No documents were uploaded. Please try again.';
            overallMsg.classList.remove('hidden');
        }
    });

    updateAllDocumentNotes();
    renderRequirements();
})();
</script>
@endsection
@extends('layout.candidate')

@section('form-content')
<style>
.form-section{padding:20px 20px 5px;margin-bottom:20px}
.section-title{color:#2C3C64;font-weight:700;margin:0 0 15px;display:flex;align-items:center;gap:10px}
.section-title:after{content:"";flex:1;height:1px;background:#e5e9f2}
.help-block{margin-top:6px;color:#6b7480}
.idv-requirements{margin:10px 0 0;padding-left:18px}
.idv-requirements li{margin-bottom:8px}
.idv-requirements i{width:18px;text-align:center;margin-right:6px;color:#2C3C64}
</style>

<div class="p-5 bg-light rounded">
    <div class="form-section">
        <h3 class="section-title">Digital Identity Verification</h3>

        @if((int)($RequiredChecks->YotiVerifcation ?? 0) === 2)
            <div class="alert alert-success" role="status">
                <i class="fa fa-check-circle" aria-hidden="true"></i>
                <strong>Your digital identity verification is complete.</strong>
            </div>

            <a href="{{ $nextRoute }}" class="btn forceBgClassified">
                Continue <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
        @else
            <p class="help-block" style="font-size:15px; margin-bottom:10px;">
                To complete your identity verification, have an acceptable identity document available and use a device with a camera.
            </p>

            <ul class="idv-requirements help-block" style="font-size:15px;">
                <li><i class="fa fa-id-card" aria-hidden="true"></i><strong>ID document:</strong> Passport and/or driving licence.</li>
                <li><i class="fa fa-camera" aria-hidden="true"></i><strong>Camera required:</strong> Use a phone, tablet or computer with a working camera.</li>
                <li><i class="fa fa-mobile" aria-hidden="true"></i><strong>Using a computer?</strong> The verification service may allow you to continue on a smartphone.</li>
            </ul>

            <div class="alert alert-info" style="margin-top:15px;">
                If you cannot use a camera-enabled device or cannot complete the online verification, contact
                <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a> for assistance.
            </div>

            <a href="{{ url('/applicant/yoti') }}" class="btn forceBgClassified">
                Start secure identity verification <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="{{ route('candidate.welcome') }}" class="btn btn-default">Back</a>

            <p class="help-block" style="margin-top:15px;">
                When Yoti reports the verification as complete, this section will automatically be marked complete when you return to the portal.
            </p>
        @endif
    </div>
</div>
@endsection

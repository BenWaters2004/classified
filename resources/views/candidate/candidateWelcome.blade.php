@extends('layout.candidate')

@section('form-content')
<div class="p-5 bg-light rounded">
    <h2>Welcome, {{ Auth::user()->firstName }} {{ Auth::user()->lastName }}!</h2>

    <p>
        Welcome to the Get ClassifIeD candidate portal. Your employer,
        <strong>{{ $userDetails->organisationName }}</strong>, has asked you to complete the screening sections shown in the menu.
        Please work through each required section and save it before moving on.
    </p>

    <div class="alert alert-info" role="status">
        <strong>Your progress is saved as you complete each section.</strong>
        You can sign out and return later if you need to find information or documents.
    </div>

    <h3 style="color:#2C3C64; margin-top:25px;">Checks requested</h3>
    <ul>
        @if((int)($RequiredChecks->basicDBS ?? 0) > 0)
            <li>Basic DBS criminal record check</li>
        @endif
        @if((int)($RequiredChecks->YotiVerifcation ?? 0) > 0)
            <li>Digital identity verification</li>
        @endif
        @if((int)($RequiredChecks->employmentRef ?? 0) > 0)
            <li>BPSS employment history</li>
        @endif
        @if((int)($RequiredChecks->academicRef ?? 0) > 0)
            <li>BPSS academic history</li>
        @endif
        @if((int)($RequiredChecks->personalRef ?? 0) > 0)
            <li>BPSS personal references</li>
        @endif
        <li>Supporting documents</li>
    </ul>

    <hr>

    <p>
        BIT Group, a trading name of BluescreenIT Ltd, is processing the screening on behalf of your employer.
        Our Security Vetting Team may contact you if more information is needed. Calls from our Plymouth office use the
        <strong>01752</strong> area code.
    </p>

    <p>
        If you need help, visit <a href="{{ route('candidate.help') }}">Help &amp; Support</a>,
        call <a href="tel:+441752724000">+44 (0)1752 724 000</a>, or email
        <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a>.
    </p>

    <a href="{{ route('candidate.personal.edit') }}" class="btn forceBgClassified">
        {{ (int)($RequiredChecks->aboutYou ?? 1) === 2 ? 'Continue application' : "Let's get started" }}
        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
    </a>
</div>
@endsection

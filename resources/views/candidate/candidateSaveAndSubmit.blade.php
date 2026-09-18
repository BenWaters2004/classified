@extends('layout.candidate')

@section('form-content')
@php
    $missingCount = collect($sections)->filter(fn($s) => ($status[$s['key']] ?? 0) !== 2)->count();
@endphp

<style>
.section{padding:20px;margin-bottom:20px;background:#fff;border-radius:4px;border:1px solid #e5e9f2}
.section-title{color:#2C3C64;font-weight:700;margin:0 0 15px;display:flex;align-items:center;gap:10px}
.status-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:12px}
.status-ok{background:#e9f7ef;color:#1e7e34}
.status-bad{background:#fdecea;color:#b94a48}
.small-note{font-size:13px;color:#6b7480}
.review-list li{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:8px 0;border-bottom:1px solid #eef1f5}
.review-list li:last-child{border-bottom:0}
</style>

<div class="p-5 bg-light rounded">
    <div class="section">
        <h3 class="section-title">Review and submit</h3>
        <p class="small-note">
            Check that every required section is complete. Use the Edit links if you need to make a change before submitting.
        </p>

        @if($missingCount === 0)
            <span class="status-pill status-ok"><i class="fa fa-check-circle"></i> All required sections complete</span>
        @else
            <span class="status-pill status-bad"><i class="fa fa-exclamation-circle"></i> {{ $missingCount }} section{{ $missingCount === 1 ? '' : 's' }} incomplete</span>
        @endif
    </div>

    <div class="section">
        <h4 class="section-title">Checklist</h4>
        <ul class="list-unstyled review-list">
            @foreach($sections as $s)
                @php $value = (int)($status[$s['key']] ?? 0); @endphp
                <li>
                    @if($value === 2)
                        <span class="status-pill status-ok"><i class="fa fa-check-circle"></i> Complete</span>
                    @else
                        <span class="status-pill status-bad"><i class="fa fa-exclamation-circle"></i> Incomplete</span>
                    @endif
                    <strong>{{ $s['label'] }}</strong>
                    <a href="{{ $s['route'] }}" class="btn btn-link btn-xs">Edit</a>
                </li>
            @endforeach
        </ul>
    </div>

    <form method="POST" action="{{ route('candidate.submit') }}">
        @csrf
        <div class="section">
            <h4 class="section-title">Final declaration</h4>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="final_declaration" name="final_declaration" value="1" required>
                    I confirm that the information I have provided is true, complete and accurate to the best of my knowledge.
                </label>
            </div>
            @error('final_declaration')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="text-right">
            <a class="btn btn-default" href="{{ route('candidate.welcome') }}">Back</a>
            <button type="submit" id="submit_btn" class="btn forceBgClassified" disabled>
                Submit application <i class="fa fa-paper-plane" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</div>

<script>
(function(){
    var btn = document.getElementById('submit_btn');
    var declaration = document.getElementById('final_declaration');
    var allComplete = {{ $allComplete ? 'true' : 'false' }};

    function update(){
        btn.disabled = !(allComplete && declaration && declaration.checked);
    }

    if (declaration) declaration.addEventListener('change', update);
    update();
})();
</script>
@endsection

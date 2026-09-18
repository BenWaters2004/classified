@extends('layout.admin')

@section('title', 'Compose Message')

@section('content')
<div class="container-fluid">

    @if(session('warning')) <div class="alert alert-warning">{{ session('warning') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    @if(session('errors_list'))
        <div class="alert alert-danger">
            <strong>Some messages failed:</strong>
            <ul style="margin-top:10px;">
                @foreach(session('errors_list') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="content">
        <div class="cm-card">
            <div class="cm-card__header">
                <div>
                    <h3 class="cm-title">Compose Message</h3>
                    <p class="cm-subtitle">{{ $recipients->count() }} recipients selected</p>
                </div>
                <div>
                    <a href="{{ route('candidateMessaging.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>

            {{-- Recipients preview --}}
            <div class="cm-section">
                <div class="cm-section__header">
                    <h4 class="cm-section__title">Recipients</h4>

                    @if($recipients->count() > 5)
                        <button type="button" class="btn btn-default btn-sm" id="toggleRecipientsBtn">
                            View all
                        </button>
                    @endif
                </div>

                <div class="table-responsive cm-table-wrap">
                    <table class="table table-bordered cm-table" id="recipientsTable">
                        <thead>
                            <tr>
                                <th>Name</th><th>Email</th><th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipients as $idx => $r)
                                <tr class="recipient-row {{ $idx >= 5 ? 'recipient-hidden' : '' }}">
                                    <td>{{ trim(($r->forename ?? '').' '.($r->surname ?? '')) }}</td>
                                    <td>{{ $r->email ?? '-' }}</td>
                                    <td>{{ $r->phone ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($recipients->count() > 5)
                    <small class="text-muted">Showing first 5 recipients. Click “View all” to expand.</small>
                @endif
            </div>

            {{-- Composer --}}
            <div class="cm-section">
                <h4 class="cm-section__title">Message</h4>

                <form method="POST" action="{{ route('candidateMessaging.send') }}" id="sendForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST">

                    @foreach($selectedUsers as $uid)
                        <input type="hidden" name="selected_users[]" value="{{ $uid }}">
                    @endforeach

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Channel</label>
                                <select name="channel" id="channel" class="form-control" required>
                                    <option value="email" selected>Email</option>
                                    <option value="sms">SMS</option>
                                </select>
                                <small class="text-muted" id="smsHint" style="display:none;">Tip: keep SMS short and avoid heavy formatting.</small>
                            </div>
                            <div class="form-group" id="shortenUrlsGroup" style="display:none; margin-top:10px;">
                                {{-- IMPORTANT: ensures a value is always sent --}}
                                <input type="hidden" name="shorten_urls" value="0">

                                <label style="display:flex; align-items:center; gap:10px; font-weight:600;">
                                    <input type="checkbox" name="shorten_urls" id="shorten_urls" value="1" checked>
                                    Shorten URLs in SMS
                                </label>

                                <small class="text-muted">
                                    Disable this if your SMS doesn’t include any links.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-8" id="subjectGroup">
                            <div class="form-group">
                                <label>Email Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="Subject line">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label id="messageLabel">Email Message</label>
                        <textarea name="message" id="message" class="form-control" rows="10"></textarea>
                    </div>

                    <div class="cm-footer">
                        <button type="submit" class="btn cm-btn-primary">Send</button>
                        <a href="{{ route('candidateMessaging.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@section('pageCSS')
<style>
.cm-card{
    background:#fff;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(16,24,40,0.06);
    padding:18px;
}
.cm-card__header{
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
    border-bottom:1px solid #eef2f7;
    padding-bottom:14px; margin-bottom:14px;
}
.cm-title{ margin:0; color:#2C3C64; font-weight:700; }
.cm-subtitle{ margin:6px 0 0; color:#6b7280; }
.cm-section{ margin-top:16px; }
.cm-section__header{ display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:8px; }
.cm-section__title{ margin:0; color:#2C3C64; font-weight:700; }
.cm-table-wrap{ border-radius:12px; overflow:hidden; border:1px solid #eef2f7; }
.cm-table{ margin-bottom:0; }
.cm-table thead th{ background:#fbfcff; }
.cm-btn-primary{
    background:#C55359; color:#fff; border:none;
    padding:10px 14px; border-radius:10px; font-weight:700;
}
.cm-btn-primary:hover{ opacity:0.92; color:#fff; }
.cm-footer{
    display:flex; justify-content:flex-end; gap:10px;
    margin-top:14px; padding-top:14px; border-top:1px solid #eef2f7;
}
.recipient-hidden{ display:none; }
</style>
@endsection


@section('pageJavascript')
<script src="https://cdn.tiny.cloud/1/d7f1y603g6xg6ub9a9agvb462o89bprz4bykfduod7gbtg4f/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // --- Recipients toggle ---
    const toggleBtn = document.getElementById('toggleRecipientsBtn');
    if (toggleBtn) {
        let expanded = false;
        toggleBtn.addEventListener('click', function () {
            expanded = !expanded;
            document.querySelectorAll('#recipientsTable .recipient-row').forEach((row, idx) => {
                if (idx >= 5) row.classList.toggle('recipient-hidden', !expanded);
            });
            toggleBtn.textContent = expanded ? 'Show less' : 'View all';
        });
    }

    const form = document.getElementById('sendForm');
    const sendBtn = form ? form.querySelector('button[type="submit"]') : null;

    const channel = document.getElementById('channel');
    const subjectGroup = document.getElementById('subjectGroup');
    const messageLabel = document.getElementById('messageLabel');
    const smsHint = document.getElementById('smsHint');
    const textarea = document.getElementById('message');
    const shortenUrlsGroup = document.getElementById('shortenUrlsGroup');

    function ensureTextareaVisible() {
        if (textarea) {
            textarea.style.display = '';
            textarea.removeAttribute('aria-hidden');
        }
    }

    function initTiny() {
        if (!window.tinymce || !textarea) return;
        if (tinymce.get('message')) return;

        tinymce.init({
            selector: '#message',
            height: 320,
            menubar: false,
            plugins: ['lists', 'link', 'table', 'autolink', 'charmap', 'searchreplace', 'visualblocks', 'wordcount'],
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | removeformat',
            setup: function (editor) {
                editor.on('change keyup', function () {
                    editor.save();
                });
            }
        });
    }

    function destroyTiny() {
        if (!window.tinymce) return;
        const editor = tinymce.get('message');
        if (editor) {
            editor.save();
            editor.remove();
        }
        ensureTextareaVisible();
    }

    function updateChannelUI() {
        if (!channel) return;

        if (channel.value === 'sms') {
            destroyTiny();
            if (subjectGroup) subjectGroup.style.display = 'none';
            if (messageLabel) messageLabel.textContent = 'SMS Message';
            if (smsHint) smsHint.style.display = 'block';

            if (shortenUrlsGroup) shortenUrlsGroup.style.display = 'block'; // ✅ show
        } else {
            if (subjectGroup) subjectGroup.style.display = 'block';
            if (messageLabel) messageLabel.textContent = 'Email Message';
            if (smsHint) smsHint.style.display = 'none';

            if (shortenUrlsGroup) shortenUrlsGroup.style.display = 'none'; // ✅ hide

            initTiny();
        }
    }

    function smsHasUrl(text) {
        return /(https?:\/\/|www\.)\S+/i.test(text || '');
    }

    if (channel.value === 'sms') {
        const cb = document.getElementById('shorten_urls');
        if (cb && !smsHasUrl(textarea.value)) cb.checked = false;
    }

    function validateForm() {
        // sync TinyMCE -> textarea
        if (window.tinymce) {
            const editor = tinymce.get('message');
            if (editor) editor.save();
        }

        const msg = (textarea?.value || '').trim();
        if (!msg) {
            alert('Please enter a message.');
            const editor = window.tinymce ? tinymce.get('message') : null;
            if (editor) editor.focus();
            else textarea?.focus();
            return false;
        }

        if (channel && channel.value === 'email') {
            const subjectInput = document.querySelector('input[name="subject"]');
            if (subjectInput && !subjectInput.value.trim()) {
                alert('Please enter an email subject.');
                subjectInput.focus();
                return false;
            }
        }

        return true;
    }

    // Initial state
    updateChannelUI();
    if (channel) channel.addEventListener('change', updateChannelUI);

    // ---- HARDEN SUBMISSION ----
    // Some admin templates hijack submit; we force POST submission ourselves.
    if (sendBtn && form) {
        sendBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation(); // prevent other global handlers
            e.stopImmediatePropagation?.();

            if (!validateForm()) return;

            // Use requestSubmit if available (respects form method/action/CSRF)
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }, true); // capture phase to beat other handlers
    }

    // Also prevent any GET navigation caused by submit handlers
    if (form) {
        form.addEventListener('submit', function (e) {
            // If something else triggers submit, still validate
            if (!validateForm()) {
                e.preventDefault();
                return;
            }
        });
    }
});
</script>
@endsection
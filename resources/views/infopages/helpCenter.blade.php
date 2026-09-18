@extends('layout.admin')

@section('title', "Admin - Help Centre")

@section('sidebar')
@endsection

@section('content')

<!-- Iframe Modal (Interactive Guide) -->
<div class="modal fade" id="iframeModal" tabindex="-1" role="dialog" aria-labelledby="iframeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 90%; width: 1000px;">
    <div class="modal-content" style="border-radius: 10px;">

      <div class="modal-header" style="background-color: #2C3C64; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;opacity:1;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="iframeModalLabel" style="font-weight: bold;">How to add a candidate</h4>
      </div>

      <div class="modal-body" style="height: 600px; padding: 0; overflow: hidden;">
        <script async src="https://js.storylane.io/js/v2/storylane.js"></script>
        <div class="sl-embed" style="position:relative;padding-bottom:calc(49.90% + 25px);width:100%;height:0;transform:scale(1)">
            <iframe loading="lazy" class="sl-demo" src="https://getclassified.storylane.io/demo/wlshvfbjpzjh?embed=inline" name="sl-embed" allow="fullscreen" allowfullscreen style="position:absolute;top:0;left:0;width:100%!important;height:100%!important;border:1px solid rgba(63,95,172,0.35);box-shadow: 0px 0px 18px rgba(26, 19, 72, 0.15);border-radius:10px;box-sizing:border-box;"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
    <section class="back-card">

        <!-- Title Row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <i class="fa fa-question-circle"></i> Help Centre
                    <small class="pull-right">Date: {{ date("d F Y") }}</small>
                </h2>
            </div>
        </div>

        <!-- Info Row -->
        <div class="row">
            <div class="col-xs-12">
                <p class="help-info">
                    For any issues regarding candidate applications, please contact the screening team.
                    For issues with ClassifIeD, please contact the technical team.
                </p>
            </div>
        </div>

        <!-- Service Health / Status Page -->
        <div class="row">
            <div class="col-xs-12">
                <div class="status-box">
                    <h3><i class="fas fa-signal"></i> Service Health</h3>
                    <p class="mb-0">
                        If you’re experiencing issues, please check our service health page for live status updates:
                        <a href="https://classified.statuspage.io/" target="_blank" rel="noopener">classified.statuspage.io</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="row contact-section">
            <!-- Screening Team -->
            <div class="col-md-6">
                <div class="contact-box">
                    <h3><i class="fas fa-user-shield"></i> Screening Team</h3>
                    <p><strong>Nicola Knight & Katie Kaute</strong></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:screening@thinkbitgroup.co.uk">screening@thinkbitgroup.co.uk</a></p>
                    <p><i class="fas fa-phone"></i> +44 (0) 1752 724000</p>
                    <p><i class="fas fa-map-marker-alt"></i> 1 Davy Rd, Plymouth, Devon, PL6 8BX</p>
                </div>
            </div>

            <!-- Technical Support -->
            <div class="col-md-6">
                <div class="contact-box">
                    <h3><i class="fas fa-user-cog"></i> Technical Support</h3>
                    <p><strong>Ben Waters</strong></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:ben.waters@thinkbitgroup.co.uk">ben.waters@thinkbitgroup.co.uk</a></p>
                    <p><i class="fas fa-phone"></i> +44 (0) 1752 270139</p>
                    <p><i class="fas fa-map-marker-alt"></i> 1 Davy Rd, Plymouth, Devon, PL6 8BX</p>
                </div>
            </div>
        </div>

        <!-- Spacer + Divider -->
        <div class="section-divider"></div>

        <!-- Guides Section -->
        <div class="guides-section">
            <div class="box-header with-border">
                <h3 class="box-title text-center">ClassifIeD Guides</h3>
                <p class="section-subtitle text-center">Step-by-step PDFs and an interactive walkthrough.</p>
            </div>

            <div class="row justify-content-center">
                @php
                    $guides = [
                        [
                            'title' => 'ClassifIeD Site Admin Manual',
                            'description' => 'A complete reference for site administrators, covering essential tasks such as user management and candidate processing. This manual provides step-by-step instructions to ensure smooth operation and compliance within ClassifIeD.',
                            'file' => 'classified_site_admin_manual.pdf',
                            'inter' => '0'
                        ],
                        [
                            'title' => 'How to Add a New Candidate',
                            'description' => 'A quick-start guide for efficiently adding new candidates to the system. While this process is also covered in the full Site Admin Manual, this guide provides a concise, step-by-step walkthrough to streamline candidate onboarding.',
                            'file' => 'how_to_add_new_candidate.pdf',
                            'inter' => '1'
                        ]
                    ];
                @endphp

                @foreach ($guides as $guide)
                    <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-stretch">
                        <div class="card guide-card shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $guide['title'] }}</h5>
                                <p class="card-text">{{ $guide['description'] }}</p>

                                <a href="{{ asset('guides/' . $guide['file']) }}" class="btn btn-primary mt-auto" target="_blank">
                                    <i class="fa-solid fa-file-pdf"></i> View Guide
                                </a>

                                @if ($guide['inter'] == 1)
                                    <a onclick="openIframe()" class="btn btn-secondary">
                                        <i class="fa-solid fa-video"></i> Interactive Guide
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </section>
</div>
@endsection

@section('pageCSS')
<style>
/* Intro Info */
.help-info {
    font-size: 16px;
    color: #2C3C64;
    margin-bottom: 20px;
}

/* Service Health box */
.status-box {
    background: #f9f9f9;
    padding: 18px 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.08);
    margin-bottom: 22px;
    border-left: 4px solid #2C3C64;
}

.status-box h3 {
    color: #2C3C64;
    font-size: 18px;
    margin: 0 0 8px 0;
}

.status-box i {
    margin-right: 8px;
}

.status-box a {
    font-weight: 700;
    color: #2C3C64;
    text-decoration: underline;
}

.status-box a:hover {
    color: #C55359;
}

/* Contact Section */
.contact-section {
    margin-top: 10px;
}

.contact-box {
    background: #f9f9f9;
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.08);
    height: 100%;
    border: 1px solid rgba(44, 60, 100, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.contact-box:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 18px rgba(0, 0, 0, 0.10);
}

.contact-box h3 {
    color: #C55359;
    font-size: 20px;
    margin-bottom: 10px;
}

.contact-box p {
    margin: 6px 0;
    font-size: 15.5px;
    color: #2C3C64;
}

.contact-box i {
    color: #C55359;
    margin-right: 8px;
}

.contact-box a {
    color: #2C3C64;
}

.contact-box a:hover {
    color: #C55359;
}

/* Divider between sections */
.section-divider {
    height: 1px;
    background: rgba(44, 60, 100, 0.12);
    margin: 28px 0;
}

/* Guides section header */
.guides-section {
    margin-top: 6px;
}

.box-header.with-border {
    margin-bottom: 18px;
}

.box-title {
    color: #2C3C64;
    font-weight: 800;
    letter-spacing: 0.2px;
}

.section-subtitle {
    margin: 8px 0 0 0;
    color: #6c757d;
    font-size: 14px;
}

/* Guide cards */
.guide-card {
    border: 1px solid rgba(44, 60, 100, 0.10);
    border-radius: 14px;
    margin-bottom: 20px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    min-height: 260px;
    display: flex;
    flex-direction: column;
}

.guide-card:hover {
    transform: translateY(-4px);
    box-shadow: 4px 10px 18px rgba(0, 0, 0, 0.10);
}

.guide-card .card-body {
    display: flex;
    flex-direction: column;
    text-align: center;
    padding: 22px;
    flex-grow: 1;
}

.guide-card .card-title {
    font-weight: 800;
    font-size: 18px;
    color: #2C3C64;
    margin-bottom: 12px;
}

.guide-card .card-text {
    font-size: 14px;
    color: #6c757d;
    flex-grow: 1;
}

.guide-card .btn-primary {
    border-radius: 10px;
    font-weight: 700;
    background-color: #C55359 !important;
    border: none;
    margin-top: auto;
    padding: 10px 12px;
}

.guide-card .btn-primary:hover {
    background-color: #9c4145 !important;
}

.guide-card .btn-secondary {
    border-radius: 10px;
    font-weight: 700;
    background-color: #2C3C64 !important;
    border: none;
    margin-top: 8px;
    color: white;
    padding: 10px 12px;
}

.guide-card .btn-secondary:hover {
    background-color: rgb(56, 74, 119) !important;
    color: white;
}

/* Page shell */
.back-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    margin-block: 2rem;
}

.page-header {
    color: #2C3C64;
}

/* Responsive */
@media (max-width: 768px) {
    .contact-box {
        width: 100%;
        text-align: center;
        margin-bottom: 18px;
    }

    .section-divider {
        margin: 22px 0;
    }
}
</style>
@endsection

@section('pageJavascript')
<script>
    function openIframe() {
        $('#iframeModal').modal('show');
    }
</script>
@endsection
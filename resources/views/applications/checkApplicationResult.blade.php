@extends('layout.admin')

@section('title', 'Send Application Request')

@section('content')


<div class="container-fluid">
  <section class="content">
    <div class="Newbox">
      <div class="box-header">
        <h3 class="box-title">Check DBS application</h3>
      </div><!-- /.box-header -->
      <div class="box-body">

        <div class="row">
          <div class="col-md-3">
            <strong style="color: #C55359;">Disclosure Status</strong>
          </div>
          <div class="col-md-6">
            {{$dbsResponse['dbsResponse_int023_DisclosureStatus']}}
          </div>
        </div>

        <div class="row">
          <div class="col-md-3">
            <strong style="color: #C55359;">Disclosure Type</strong>
          </div>
          <div class="col-md-6">
            {{$dbsResponse['dbsResponse_int023_DisclosureType']}}
          </div>
        </div>

        <div class="row">
          <div class="col-md-3">
            <strong style="color: #C55359;">Disclosure Number</strong>
          </div>
          <div class="col-md-6">
            {{$dbsResponse['dbsResponse_int023_DisclosureNumber']}}
          </div>
        </div>
        @if (isset($dbsResponse['dbsResponse_int023_ErrorCode']) && !empty($dbsResponse['dbsResponse_int023_ErrorCode'][0]))
        <div class="row">
          <div class="col-md-3">
            <strong style="color: #C55359;">Error Code</strong>
          </div>
          <div class="col-md-6">
            {{$dbsResponse['dbsResponse_int023_ErrorCode'][0]}}
          </div>
        </div>

        <div class="row">
          <div class="col-md-3">
            <strong style="color: #C55359;">Error Reason</strong>
          </div>
          <div class="col-md-6">
            {{$dbsResponse['dbsResponse_int023_ErrorReason'][0]}}
          </div>
        </div>

        @endif

      </div>
    </div>
  </section>
</div>

<style>
  .Newbox {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    margin-block: 2rem;
  }
  .box-title {
    color: #2C3C64;
  }
</style>
@endsection

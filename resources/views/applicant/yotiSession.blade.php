@extends('layout.default')

@section('title', "Digital Identity Verification")

@section('sidebar')
@endsection

@section('content')
<div class="row">
    <iframe 
        src="{{ $iframeUrl }}" 
        style="height:605px; width:100%; border:none;" 
        allow="camera">
    </iframe>
  </div>

@endsection


@section('pageCSS')
@endsection

@section('pageJavascript')
@endsection
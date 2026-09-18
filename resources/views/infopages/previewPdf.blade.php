@extends('layout.admin')

@section('title', "Admin - Help Center")

@section('sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">Image Preview: {{$documentName}}
          </h2>
        </div>
        <!-- /.col -->
      </div>

      <!-- info row -->
      <div class="row">
        <div class="col-sm-2">
          <p class="" style="margin-top: 10px;">
            <a href="{{ env('APP_URL') }}downloadfile/{{$resourceType}}/{{$resourceId}}"><button type="button" class="btn btn-primary">Download</button></a>
          </p>
          <p class="" style="margin-top: 10px;">
            <img src="{{$documentPath}}" alt="{{$documentName}}" />
          </p>
        </div>
      </div>
      <!-- /.row -->



    </section>
</div>
@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')

@endsection


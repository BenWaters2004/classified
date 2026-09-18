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
        <div class="col-sm-3">
          <p class="" style="margin-top: 10px;">
            <a href="{{ env('APP_URL') }}downloadfile/{{$resourceType}}/{{$resourceId}}"><button type="button" class="btn btn-danger"><i class="fa fa-download"></i> Download</button></a>
          </p>
        </div>
        <div class="col-sm-3">
          <p class="" style="margin-top: 10px;">
            <a href="{{ env('APP_URL') }}downloadfile/{{$resourceType}}/{{$resourceId}}/2"><button type="button" class="btn btn-success"><i class="fa fa-search"></i> View Image</button></a>
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


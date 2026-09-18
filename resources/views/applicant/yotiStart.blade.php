@extends('layout.default')

@section('title', "Digital Identity Verification")

@section('sidebar')
@endsection

@section('content')
<br />
<div class="row">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Yoti Identity Verification</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <p><strong>This step will require a camera and your ID.</strong> <br/><br />
            If you are using a device that does not have a camera, you will be given the option to switch to a smartphone.<br />If you do not have access to a device with a camera you will need to verify your ID's in person with your employer, please contact screening@thinkbitgroup.co.uk so we can help arrange this for you.<br /><br /><strong>You will need either your driving license or passport on hand.</strong></p>

            <a href="{{ env('APP_URL') }}applicant/yoti" class="forceBgClassified btn">Continue</a>
            
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

@endsection


@section('pageCSS')
@endsection

@section('pageJavascript')
@endsection
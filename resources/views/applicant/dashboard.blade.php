@extends('layout.default')

@section('title', "Admin - Dashboard")

@section('sidebar')
@endsection

@section('content')
    <p>&nbsp;</p>

@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')
<script type="text/javascript">
$('#originalDate, #newDate').datepicker({
    autoclose: true,
    format: "mm/dd/yyyy",
});
</script>
@endsection

@extends('layout.default')

@section('title', "Applicant Registration")

@section('content')
<style type="text/css">
  .img-responsive{
    display: none !important;
  }
</style>
      <div style="text-align: center;">
        @if (env('APP_ENV') == 'production')
          <img src="{{ env('APP_URL') }}images/logo_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.png"/>
        @else
          <img src="{{ env('APP_URL') }}images/logo_demo.png"/>
        @endif
      </div>

      <p class="text-center">&nbsp;</p>

      <form class="form-signin" action="{{ env('APP_URL') }}register/createAccount" method="post">
        {{ csrf_field() }}
        <h3 class="form-signin-heading">Account Setup</h3>
        <p>Please enter a password for your account</p>
        <label for="password" class="sr-only">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password"  autocomplete="false" required>
        <label for="password" class="sr-only">Repeat Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat Password"  autocomplete="false" required>
        <input type="hidden" name="accessUrlCode" value="{{$accessUrlCode}}">
        <br />
        @if ($errors->any() && $errors->has('message'))
          <div class="row"><div class="col-xs-12 text-red"> 
            {{$errors->first('message')}}
          </div></div>
        @endif

        <div class="row"><div class="col-xs-12">You password must: <br /> - be at least 14 character long<br /> - contain  at least 1 upper case letter<br /> - contain  at least 1 lower case letter<br /> - contain  at least 1 number<br /> - contain at least 1 special character from the following list: @$!%*#?<br /><br />
        <button class="btn btn-lg forceBgClassified btn-block" type="submit">Register Account</button>
      </form>

    

@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')

@endsection

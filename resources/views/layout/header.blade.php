<!-- Logo -->
<div class="container" style="margin-top: 20px; margin-bottom:20px;">
  <div class="row">
  <div class="col-xs-6 wrapper">
    <a href="{{ env('APP_URL') }}login" class="logo" style="width: 100%; padding: 0;">
          @if (env('APP_ENV') == 'production')
            <img class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_{{ str_replace(' ', '', strtolower(env('APP_NAME'))) }}.webp"/>
          @else
            <img class="img-responsive logo" src="{{ env('APP_URL') }}images/logo_demo.png"/>
          @endif
        </a>
  </div>

  </div>
  @if (Auth::check())
  <div class="row">
    <div class="col-xs-6 wrapper">
      <div class="text-left" style=" margin-top: 10px;margin-left: 15px;"><a href="{{ env('APP_URL') }}login"><button type="button" class="forceBgClassified btn btn-info" style="margin-bottom: 10px;"><i class="fa fa-home"></i> Home</button></a></div>
    </div>
    <div class="col-xs-6 wrapper">
      <div class="text-right" style=" margin-top: 10px;margin-right: 12px;">Logged in as {{ Auth::user()->firstName }} {{ Auth::user()->lastName }} | <a href="{{ env('APP_URL') }}applicant/logout">Logout</a></div>
    </div>
  </div>
  @endif
</div>

@extends('layout.default')

@section('title', "BPSS Application Step 1")

@section('sidebar')
@endsection

@section('content')
<div style="clear: both;"></div>
<div class="row">
    <div class="col-md-6">
      <div class="box">
      <div class="box-header">
        <h3 class="box-title">Update Profile Details</h3>
      </div><!-- /.box-header -->
      <div class="box-body">
        <?php $emailUnique = true;?>
        @if ($errors->any() && $errors->has('userEmail'))
          @foreach ($errors->all() as $error)
            <?php if ($error == 'email_exists') { $emailUnique = false;} ?>
          @endforeach
        @endif

        @if ($errors->any() && $errors->has('userEmail'))
          @foreach ($errors->all() as $error)
            <?php if ($error == 'password_do_not_match') { $emailUnique = false;} ?>
          @endforeach
        @endif
        <form method="post" action="{{ env('APP_URL') }}applicant/updateUserDetails">
            {{ csrf_field() }}

            <div class="form-group @if ($errors->any() && $errors->has('userTitle')) has-error @endif">
              <label for="userTitle">Title </label>
              <input type="text" class="form-control" name="userTitle" id="userTitle" maxlength="30" value="@if($userDetails->DBSApplicationID > 0){{ $userDetails->applicationTitle }}@else{{ $userDetails->title }}@endif">
              <p class="help-block"  id="error_userTitle" @if ($errors->any() && $errors->has('userTitle')) @else style="display:none;" @endif>Please enter a title!</p>
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('firstName')) has-error @endif">
              <label for="firstName">First Name *</label>
              <input type="text" class="form-control" name="firstName" id="firstName" maxlength="30" value="@if($userDetails->DBSApplicationID > 0){{ $userDetails->applicationForename }}@else{{ $userDetails->firstName }}@endif" required>
              <p class="help-block"  id="error_firstName" @if ($errors->any() && $errors->has('firstName')) @else style="display:none;" @endif>Please enter a first name!</p>
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('lastName')) has-error @endif">
              <label for="lastName">Last Name *</label>
              <input type="text" class="form-control" name="lastName" id="lastName" maxlength="30" value="@if($userDetails->DBSApplicationID > 0){{ $userDetails->applicationPresentSurname }}@else{{ $userDetails->lastName }}@endif" required>
              <p class="help-block"  id="error_lastName" @if ($errors->any() && $errors->has('lastName')) @else style="display:none;" @endif>Please enter a last name!</p>
            </div>

            <div class="form-group">
              <label for="lastName">Organisation</label>
              <input class="form-control" name="organisationID" type="text"  value="{{$userDetails->organisationName}}" placeholder="{{$userDetails->organisationName}}" disabled="disabled">
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('userEmail')) has-error @endif">
              <label for="userEmail">Email</label>
              <input type="email" class="form-control" name="userEmail" id="userEmail" value="{{ $userDetails->email }}">
              @if (!$emailUnique) 
                <p class="help-block" id="error_userEmail_notUnique">The email is already registered in the database.</p>
              @else
                <p class="help-block" id="error_userEmail" @if ($errors->any() && $errors->has('userEmail')) @else style="display:none;" @endif>Please enter a valid email address.</p>
              @endif
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('password')) has-error @endif">
              <label for="password">Update Password (leave blank if you don't want to update this)</label>
              <input type="password" class="form-control" name="password" id="password" value=""  autocomplete="false">
              <p class="help-block"  id="error_password" @if ($errors->any() && $errors->has('password')) @else style="display:none;" @endif>Passwords do not match!</p>
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('password_confirmation')) has-error @endif">
              <label for="_confirmation">Repeat Password (leave blank if you don't want to update this)</label>
              <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="" autocomplete="false">
              <p class="help-block"  id="error_password_confirmation" @if ($errors->any() && $errors->has('password_confirmation')) @else style="display:none;" @endif>Passwords do not match!</p>
              <p class="help-block"  id="password_validation" style="display:none; color:#f00;"></p>
            </div>

            <div class="form-group">&nbsp;</div>

            <div class="form-group">
              <input type="hidden" name="userID" id="userID" value="{{ $userDetails->id }}">
              <button type="submit" class="forceBgClassified btn" id="saveProfile">Save</button>
            </div>

        </form>
      </div><!-- /.box-body -->
    </div><!-- /.box -->
    </div>
  </div>

@endsection


@section('pageCSS')
 
@endsection

@section('pageJavascript')
@include('applicant.bpssValidations')
<script type="text/javascript">
  $("#password, #password_confirmation").focusout(function() {
    var password = $( "#password" ).val();
    var password_confirmation = $( "#password_confirmation" ).val();
    validate_password(password, password_confirmation);
  });

  $(document).on("click", "#saveProfile", function() {
    var password = $( "#password" ).val();
    var password_confirmation = $( "#password_confirmation" ).val();
    validate_password(password, password_confirmation);
  });

  function validate_password(password, password_confirmation){

    if((password_confirmation.length > 0 && password_confirmation.length <14) || (password.length > 0 && password.length < 14)) {
      $('#saveProfile').attr('type','button');

      $("#password_validation").html('The password must be at least 14 characters long!');  
      $("#password_validation").show();     


    } else if(password_confirmation != password && password.length > 0) {
      $('#saveProfile').attr('type','button');

      $("#password_validation").html('The password and confirmation do not match!');  
      $("#password_validation").show();
    } else if (!validatePasswordMinimum(password) && password.length > 0) {
      $('#saveProfile').attr('type','button');

      $("#password_validation").html('The password does not satisfy the minimum requirement: minimum 14 characters long and must contain at least 1 lowercase, 1 uppercase, 1 number and 1 special character!');  
      $("#password_validation").show();
    } else {
      $("#password_validation").hide();
      $('#saveProfile').attr('type','submit');
    }
  }

  function validatePasswordMinimum(password){
  	var pattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{14,}$/;
    return pattern.test(password);
  }
</script>
@endsection


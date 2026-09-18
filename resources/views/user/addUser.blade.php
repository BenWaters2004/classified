@extends('layout.admin')

@section('title', 'Add New User')

@section('content')

<?php //echo'<pre>';print_r($countries);echo'</pre>'; ?>

<div class="container-fluid">
  <section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box">
      <div class="box-header">
        <h3 class="box-title">Add New User</h3>
      </div><!-- /.box-header -->
      <div class="box-body">
        <?php $emailUnique = true;?>
        @if ($errors->any() && $errors->has('userEmail'))
          @foreach ($errors->all() as $error)
            <?php if ($error == 'email_exists') { $emailUnique = false;} ?>
          @endforeach
        @endif
        <form id="addCustomerDetails" method="POST" action="{{ env('APP_URL') }}users/saveUser">
        {{ csrf_field() }}

            <div class="form-group @if ($errors->any() && $errors->has('userTitle')) has-error @endif">
              <label for="userTitle">Title *</label>
              <input type="text" class="form-control" name="userTitle" id="userTitle" maxlength="30" value="{{ old('userTitle') }}" required>
              <p class="help-block"  id="error_userTitle" @if ($errors->any() && $errors->has('userTitle')) @else style="display:none;" @endif>Please enter a title!</p>
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('firstName')) has-error @endif">
              <label for="firstName">First Name *</label>
              <input type="text" class="form-control" name="firstName" id="firstName" maxlength="30" value="{{ old('firstName') }}" required>
              <p class="help-block"  id="error_firstName" @if ($errors->any() && $errors->has('firstName')) @else style="display:none;" @endif>Please enter a first name!</p>
            </div>

            <div class="form-group @if ($errors->any() && $errors->has('lastName')) has-error @endif">
              <label for="lastName">Last Name *</label>
              <input type="text" class="form-control" name="lastName" id="lastName" maxlength="30" value="{{ old('lastName') }}" required>
              <p class="help-block"  id="error_lastName" @if ($errors->any() && $errors->has('lastName')) @else style="display:none;" @endif>Please enter a last name!</p>
            </div>

            <div class="form-group">
              <label for="lastName">Base Organisation *</label>
              @if (count($availableOrganisations) > 0)
                <select  name="organisationID" class="form-control">
                  @foreach ($availableOrganisations as $organisation)
                    <option value="{{ $organisation->id }}" @if ($organisation->id == 1) selected="selected" @endif>{{ $organisation->organisationName }}</option>
                  @endforeach
                </select>
              @else
              <input class="form-control" name="organisationID" type="text"  value="1" placeholder="N/A" disabled="disabled">
              @endif
            </div>

            

          <div class="form-group @if ($errors->any() && $errors->has('password')) has-error @endif">
            <label for="password">Password *</label>
            <input type="search" class="form-control" name="password" id="password" maxlength="50" value="" required>
            <p class="help-block"  id="error_password" @if ($errors->any() && $errors->has('password')) @else style="display:none;" @endif>Please enter a valid password!</p>
          </div>

            <div class="form-group @if ($errors->any() && $errors->has('userEmail')) has-error @endif">
              <label for="userEmail">Email</label>
              <input type="email" class="form-control" name="userEmail" id="userEmail" value="{{ old('userEmail') }}"  required>
              @if (!$emailUnique) 
                <p class="help-block" id="error_userEmail_notUnique">The email is already registered in the database.</p>
              @else
                <p class="help-block" id="error_userEmail" @if ($errors->any() && $errors->has('userEmail')) @else style="display:none;" @endif>Please enter a valid email address!</p>
              @endif
              
            </div>


          <div class="form-group">
              <button type="submit" class="forceBgClassifIeD btn">Save</button>
            </div>

        </form>
      </div><!-- /.box-body -->
    </div><!-- /.box -->
    </div>
  </div>
  </section>
</div>
@endsection

@section('pageJavascript')
<script type="text/javascript">
  

 
</script>
@endsection

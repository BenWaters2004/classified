@extends('layout.admin')

@section('title', 'Send Application Request')

@section('content')


<div class="container-fluid">
  <section class="content">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">New Applicant</h3>
      </div><!-- /.box-header -->
      <div class="box-body">

        <div class="row">
         <div class="col-md-6">
          @php $emailUnique = true;@endphp
          @if ($errors->any() && $errors->has('emailAddress'))
            @foreach ($errors->all() as $error)
              <?php if ($error == 'email_exists') { $emailUnique = false;} ?>
            @endforeach
          @endif
            <form action="{{ env('APP_URL') }}applications/sendApplicationRequest" method="post">
            {{ csrf_field() }}
              <div class="box-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group @if ($errors->any() && $errors->has('emailAddress')) has-error @endif">
                      <label for="emailAddress">Email address</label>
                      <input type="text" class="form-control" id="emailAddress" name="emailAddress" placeholder="Enter email" value="{{ old('emailAddress') }}">
                      @if (!$emailUnique) 
                        <p class="help-block" id="error_emailAddress_notUnique">The email is already registered in the database.</p>
                      @else
                        <p class="help-block" id="error_emailAddress" @if ($errors->any() && $errors->has('emailAddress')) @else style="display:none;" @endif>Please enter a valid email address!</p>
                      @endif
                    </div>
                  </div>
                </div>
<!-- 
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group @if ($errors->any() && $errors->has('countryCode')) has-error @endif">
                      <label for="countryCode">Country Code</label>
                      <input type="text" class="form-control" id="countryCode" name="countryCode" placeholder="+44" value="{{ old('countryCode') }}">
                      <p class="help-block"  id="error_countryCode" @if ($errors->any() && $errors->has('countryCode')) @else style="display:none;" @endif>Please enter a valid country code!</p>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="form-group @if ($errors->any() && $errors->has('mobileNumber')) has-error @endif">
                      <label for="mobileNumber">Mobile number</label>
                      <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" placeholder="7123456789" value="{{ old('mobileNumber') }}">
                      <p class="help-block"  id="error_mobileNumber" @if ($errors->any() && $errors->has('mobileNumber')) @else style="display:none;" @endif>Please enter a valid mobile number!</p>
                    </div>
                  </div>
                </div>
 -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group @if ($errors->any() && $errors->has('forename')) has-error @endif">
                      <label for="forename">Forename</label>
                      <input type="text" class="form-control" id="forename" name="forename" value="{{ old('forename') }}" maxlength="50">
                      <p class="help-block"  id="error_forename" @if ($errors->any() && $errors->has('forename')) @else style="display:none;" @endif>Please enter a forename!</p>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group @if ($errors->any() && $errors->has('surname')) has-error @endif">
                      <label for="surname">Surname</label>
                      <input type="text" class="form-control" id="surname" name="surname" value="{{ old('surname') }}" maxlength="50">
                      <p class="help-block"  id="error_surname" @if ($errors->any() && $errors->has('surname')) @else style="display:none;" @endif>Please enter a surname!</p>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="organisation">Organisation</label>
                      @inject('checkAccess', 'App\Http\Controllers\Controller')
                      @if ($checkAccess->checkAccess('superuser') || $checkAccess->checkAccess('siteuser'))
                      <!-- added siteuser so that they can select site as well -->
                      <select  name="organisationID" class="form-control">
                        @foreach ($availableOrganisations as $organisation)
                          <option value="{{ $organisation->id }}" @if ($organisation->id == \Auth::user()->organisationID) selected="selected" @endif>{{ $organisation->organisationName }}</option>
                        @endforeach
                      </select>
                      @else
                      <input class="form-control" type="text"  value="{{$adminProfile->organisationName}}" disabled="disabled">
                      <input name="organisationID" type="hidden"  value="{{$adminProfile->organisationID}}">
                      @endif
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="applicationType">Application Type</label>
                      <select  name="applicationType" class="form-control">
                        <option value="1">DBS Application Only</option>
                        <option value="2" selected="selected">DBS and BPSS Applications</option>
                        <option value="3">Work Experience Application Only</option>
                      </select>
                    </div>
                  </div>

                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn forceBgClassified">Send Application Request</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('pageJavascript')
<script type="text/javascript">
 
</script>
@endsection

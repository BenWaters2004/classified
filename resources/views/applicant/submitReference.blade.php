@extends('layout.default')

@section('title', "Applicant Reference")

@section('content')

      <h1 class="text-left">Reference Request</h1>
      <h3 class="text-left">Please fill out the following reference form for <strong>{{$userDetails->firstName}} {{$userDetails->lastName}}</strong></h3>

      <form class="form" action="{{ env('APP_URL') }}reference/submitReferenceDetails" method="post">
        {{ csrf_field() }}
        <div class="form-group">
          <label for="candidate_dob">Date of birth of candidate (dd/mm/yyyy)</label>
          <div class="row">
            <div class="col-md-8">
              <input type="input" name="candidate_dob" id="candidate_dob" class="form-control" placeholder="dd/mm/yyyy" required="required">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="candidate_relatedetails">Are you related to the subject? If so, please state your relationship.</label>
          <div class="row">
            <div class="col-md-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="candidate_relate" id="candidate_relate_yes" value="yes">
                <label class="form-check-label" for="candidate_relate_yes">Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;

                <input class="form-check-input" type="radio" name="candidate_relate" id="candidate_relate_no" value="no" checked>
                <label class="form-check-label" for="candidate_relate_no">No</label>
              </div>
            </div>
            <div class="col-md-6">
              <input type="input" name="candidate_relatedetails" id="candidate_relatedetails" class="form-control" placeholder="If yes, please state your relationship">
            </div>
          </div>          
        </div>

        <div class="form-group">
          <label for="candidate_period_known">Over what period have you know the subject?</label>
          <div class="row">
            <div class="col-md-4">
              <input type="input" name="candidate_period_known_from" id="candidate_period_known_from" class="form-control" placeholder="dd/mm/yyyy" required="required">
            </div>
            <div class="col-md-4">
              <input type="input" name="candidate_period_known_to" id="candidate_period_known_to" class="form-control" placeholder="dd/mm/yyyy" required="required">
            </div>
          </div>          
        </div>

        <div class="form-group">
          <label for="candidate_natureofaq">Please state the nature and depth of your acquaintance</label>
          <div class="row">
            <div class="col-md-8">
              <input type="input" name="candidate_natureofaq" id="candidate_natureofaq" class="form-control" required="required">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="candidate_subjecthonest">Do you believe the subject to be strictly honest, conscientious and discreet?</label>
          <div class="row">
            <div class="col-md-8">
              <input type="input" name="candidate_subjecthonest" id="candidate_subjecthonest" class="form-control" required="required">
            </div>
          </div>
        </div>

        <div class="form-group">          
          <div class="row">
            <div class="col-md-8">
              <label for="candidate_factorsconcerning">Do you know of any factor concerning the subject which might cause his / her fitness for employment on sensitive work to be questioned? Is so, please give details.<br />
                <span style="color: #f00;">(Among the factors which are relevant are significant financial difficulties, abuse of alcohol or drugs, an extravagant mode of living or signs of mental or physical illness which may impair judgement or reliability.)</span>
              </label>
              <textarea class="form-control" name="candidate_factorsconcerning" id="candidate_factorsconcerning" rows="3" required="required"></textarea>
            </div>
          </div>
        </div>

        <p><span style="color: #f00;">The above answers are correct to the best of my knowledge and belief</span></p>

        <div class="row">
          <div class="col-md-8">
            <hr style="border: 1px solid #636b6f;" />
          </div>
        </div>

        <div class="form-group">
          <label for="reference_fullname">Full Name</label>
          <div class="row">
            <div class="col-md-8">
              <input type="input" name="reference_fullname" id="reference_fullname" class="form-control" required="required">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="reference_contactaddress">Contact Address</label>
          <div class="row">
            <div class="col-md-8">
              <input type="input" name="reference_contactaddress" id="reference_contactaddress" class="form-control" required="required">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="reference_contacttelephone">Telephone Number</label>
          <div class="row">
            <div class="col-md-8">
              <input type="input" name="reference_contacttelephone" id="reference_contacttelephone" class="form-control" required="required">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="reference_email">Email</label>
          <div class="row">
            <div class="col-md-8">
              <input type="email" name="reference_email" id="reference_email" class="form-control" required="required">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8">
            <hr style="border: 1px solid #636b6f;" />
          </div>
        </div>
        <div class="row">
          <div class="col-md-8">
            <p><strong>Important: Data Protection Act (2018) and GDPR.</strong> This form contains "personal data" as defined by the Data Protection Act 2018 and the General Data Protection Regulation (GDPR). It has been supplied to the appropriate HR or Security authority exclusively for the purpose of the Baseline Personnel Security Standard (BPSS). Information relating to the applicant will not be shared with any unauthorised personnel.</p>
          </div>
        </div>
        
        <input type="hidden" name="referenceCode" value="{{$referenceCode}}">

        <br />
        @if ($errors->any() && $errors->has('message'))
          <div class="row"><div class="col-xs-12 text-red"> 
            {{$errors->first('message')}}
          </div></div>
        @endif

        <div class="form-group">
          <div class="row">
            <div class="col-md-4">
              <button class="forceBgClassified btn btn-lg btn-block" type="submit">Submit Reference</button>
            </div>
        </div>
      </div>
      </form>

    

@endsection


@section('pageCSS')
<style type="text/css">
.datepicker {
  z-index:10000 !important;
}
</style>
@endsection

@section('pageJavascript')
<script type="text/javascript">

  $('#candidate_dob, #candidate_period_known_from, #candidate_period_known_to').datepicker({
      autoclose: true,
      format: "dd/mm/yyyy",

  });
</script>
@endsection

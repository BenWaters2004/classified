@extends('layout.admin')

@section('title', 'Check DBS application')

@section('content')


<div class="container-fluid">
  <section class="content">
    <div class="Newbox">
      <div class="box-header">
        <h3 class="box-title">Check DBS application</h3>
      </div><!-- /.box-header -->
      <div class="box-body">

        <div class="row">
         <div class="col-md-6">

            <form action="{{ env('APP_URL') }}applications/getApplicationResultForm" method="post">
            {{ csrf_field() }}
              <div class="box-body">

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group @if ($errors->any() && $errors->has('refnumber')) has-error @endif">
                      <label for="refnumber">E-reference number</label>
                      <input type="text" class="form-control" id="refnumber" name="refnumber" value="" maxlength="12" required>
                      <p class="help-block"  id="error_refnumber" @if ($errors->any() && $errors->has('refnumber')) @else style="display:none;" @endif>Please enter a valid E-reference number!</p>
                    </div>
                  </div>                  
                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn forceBgClassified">Check DBS</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
  .Newbox {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    margin-block: 2rem;
  }
  .box-title {
    color: #2C3C64;
  }
</style>
@endsection

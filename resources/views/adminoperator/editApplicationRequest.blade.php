@extends('layout.admin')

@section('title', 'Send Application Request')

@section('content')


<div class="container-fluid">
  <section class="content">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Edit Applicant</h3>
      </div><!-- /.box-header -->
      <div class="box-body">

        <div class="row">
         <div class="col-md-8">
            <form action="{{ env('APP_URL') }}adminoperator/updateUserFiles" method="post"  enctype="multipart/form-data">
            {{ csrf_field() }}
              <div class="box-body">

                <div class="row">
                  <div class="col-md-12">
                    @if(isset($userFiles) && count($userFiles)>0)
                      <table style="width: 100%;">
                        <thead>
                          <tr>
                            <th style="width:50%">Document Name</th>
                            <th style="width:20%">Document Type</th>
                            <th style="width:30%">Actions</th>
                          </tr>
                        </thead>
                        <tbody>                       
                            @foreach ($userFiles as $userfile)
                              <tr id="block_file_{{$userfile->id}}">
                                <td>{{$userfile->document_name}}</td>
                                <td>{{$userfile->file_type}}</td>
                              <td>
                                <a href="{{ env('APP_URL') }}downloadfile/new_applicant/{{$userfile->id}}/1" target="_blank"><button type="button"  class="btn btn-success pull-left" style="margin-bottom:5px;"><i class="icon fa fa-download"></i> Download</button></a>&nbsp;&nbsp;
                                <button type="button" id="remove_document_{{$userfile->id}}" document-id="{{$userfile->id}}" class="btn btn-danger pull-right" style="margin-bottom:5px;"><i class="icon fa fa-times"></i> Remove</button>
                              </td>
                            </tr>
                            @endforeach                       
                        </tbody>
                      </table>
                      <hr style="width:100%;">
                    @else
                    There are no files uploaded for this user<br /><hr /><br />
                    @endif
                  </div>

                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group @if ($errors->any() && $errors->has('newfile')) has-error @endif">
                      <label for="newfile">Add File</label>
                      <input type="file" name="newfile" id="newfile" paceholder="Select PDF Document" accept="application/pdf">
                      <input type="hidden" name="userid" value="{{$userDetails->id}}">
                      <p class="help-block"  id="error_newfile" @if ($errors->any() && $errors->has('newfile')) @else style="display:none;" @endif>Please upload a PDF file!</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="newfileType">File Type</label>
                      <select  name="newfileType" class="form-control">
                        <option value="" selected="selected">Select File Type</option>
                        <option value="misc">Misc</option>
                        <option value="secmx">Security Matrix</option>
                        <option value="mkden">MK Denial</option>
                      </select>
                      <p class="help-block text-red"  id="error_newfileType" @if ($errors->any() && $errors->has('newfileType')) @else style="display:none;" @endif>Please select a file type!</p>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="submit">&nbsp;</label><br />
                      <button type="submit" class="btn forceBgClassified" id="uploadFile">Upload</button>
                    </div>
                  </div>
                </div>


              </div>
              <!-- /.box-body -->

              
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
  $(document).on("click", '[id^="remove_document_"]', function() {
    var documentID = $(this).attr('document-id');
    $.ajax({
      url: "<?php echo env('APP_URL'); ?>" + "adminoperator/removeFile", 
          method: "POST",
          data: {"_token":"{{ csrf_token() }}", "documentID":documentID},
          success: function(result){
            var deleteResult = JSON.parse(result);
            if(deleteResult.status == 1){
              $("#block_file_"+documentID).remove();
            } else {
              alert('Error: Please try again!');
            }
          },
          error: function(result){
            alert('Error: Please try again!');
          },
    });
  });
</script>
@endsection

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <br />
        <h3 align="center">Upload Excel File</h3>
        <br />
        <div class="panel panel-default">
            <div class="panel-heading"><b>Select Excel File</b></div>
            <div class="panel-body">
                <form id="uploadImage" action="{{route('upload_file')}}" method="post">
                    @csrf
                    <div class="form-group">
                        {{-- <label>File Upload</label> --}}
                        <input type="file" name="uploadFile" id="uploadFile" accept="*" />
                    </div>
                    <div class="form-group">
                        <input type="submit" id="uploadSubmit" value="Upload" class="btn btn-info" />
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100"></div>
                    </div>
                    <div id="targetLayer" style="display:none;"></div>
                </form>
                <div id="loader-icon" style="display:none;">
                    <p class="info">Loading...</p>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>
    <script>
        $(document).ready(function() {
            var theFile = $('#uploadFile');
            $('#uploadImage').submit(function(event) {
                if (theFile[0].files[0].size > 10 * 1000000) {
                    alert("Please upload files of size less than 10MB.");
                }

                if (theFile.val()) {
                    event.preventDefault();
                    $('#loader-icon').show();
                    $('#targetLayer').hide();

                    $(this).ajaxSubmit({
                        target: '#targetLayer',
                        beforeSubmit: function() {
                            $('.progress-bar').width('50%');
                        },
                        uploadProgress: function(event, position, total, percentageComplete) {
                            $('.progress-bar').animate({
                                width: percentageComplete + '%'
                            }, {
                                duration: 1000
                            });
                        },
                        success: function(response) {
                            if(response.success)
                            {
                                $('#loader-icon').hide();
                                // $('#targetLayer').show();
                                var json_name = response.data.json_file_name;
                                window.location = "{{route('sheet')}}?f="+json_name;
                            }
                            else
                            {
                                $('#targetLayer').html(response.msg);
                                $('#targetLayer').show();
                            }
                        },
                        error: function() {
                            // alert('ok');
                            $('#loader-icon').hide();
                        },
                        resetForm: true
                    });
                }
                return false;
            });
        });
    </script>
</body>

</html>

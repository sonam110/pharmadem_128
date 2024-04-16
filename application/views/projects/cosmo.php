<!DOCTYPE html>
<html>
<head>
    <title>AJAX Progress Example</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <style>

#progress-bar {
  width: 100%;
  height: 10px;
  background-color: #ccc;
}

#progress-bar-inner {
  width: 0%;
  height: 100%;
  background-color: #0c0;
}

#progress-percentage {
  margin-top: 10px;
  font-size: 16px;
}

    </style>


    <script>

$(document).ready(function() {
    $('#progressbar').progressbar({
        value: 0,
        max: 100
    });
    $('#progress-text').text('0%');

    $.ajax({
        url: '<?php echo site_url('projects/deploy_test'); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            $('#progressbar').progressbar('value', 100);
            $('#progress-text').text('100%');
            $('#output').html(data.output);
        },
        error: function(xhr, status, error) {
            $('#output').html('An error occurred: ' + error);
        },
        xhrFields: {
            onprogress: function(e) {
                if (e.lengthComputable) {
                    var percent = Math.round((e.loaded / e.total) * 100);
                    $('#progressbar').progressbar('value', percent);
                    $('#progress-text').text(percent + '%');
                }
            }
        }
    });
});

    </script>
</head>
<body>
   
<div id="progress-bar">
  <div id="progress-bar-inner"></div>
</div>
<div id="progress-percentage">0%</div>
<div id="output"></div>

</body>
</html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

    <!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Project Analysis</h1>
      </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo lang('home') ?></a></li>
              <li class="breadcrumb-item"><a href="<?php echo url('/projects') ?>"><?php echo lang('projects') ?></a></li>
              <li class="breadcrumb-item active">Analysis</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<!-- Main content -->

<!-- Main content -->
<section class="content">

 


<div class="row">
          <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
              <div class="card-header d-flex p-0">
       
          
              </div><!-- /.card-header -->
              <div class="card-body">

              <div class="container">
  <div class="row">
    <div class="col-md-5 column">
    <div class="form-group">
            <label for="formClient-Role">Project</label>
            <select name="project_id" id="project_id" class="form-control select2" required onchange="setDefaultSelection()">
              <option value="">Select Project</option>
              <?php foreach ($this->projects_model->getjobscompleted() as $row): ?>
                <?php
                        $checkjobinserts = $this->projects_model->checkjobinserts($row['pid']);

        ?>
                <?php $sel = !empty(get('project_name')) && get('project_name')==$row['pid'] ? 'selected' : '' ?>
                <?php if($checkjobinserts) {  ?>
                  
                <option value="<?php echo $row['pid'] ?>" <?php echo $sel ?>><?php echo $row['project_name'] ?></option>
                <?php } ?>
              <?php endforeach ?>
            </select>
          </div>
    </div>
    <div class="col-md-3 column">
    <label for="xRange">  Volume <= :</label>
<input type="number" id="xRange" name="xRange" value="100">
<label for="yRange">Yeild >= :</label>
<input type="number" id="yRange" name="yRange" value="0">

    </div>
    <div class="col-md-2 column">
    <div class="form-group">
            <label for="formClient-Role">Temparature</label>
            <select name="tempa" id="tempa" class="form-control select2" required>
              <option value="" selected>Select Temparature</option>
              <option value="50">50</option>
              <option value="25">25</option>
              <option value="10">10</option>
            </select>
          </div>
    </div>
   <div class="col-md-2 column">
  <div class="form-group">
    <label for="formClient-Role">&nbsp;</label>
      <button id="plotgen" class="form-control" style="background-color:#9bc0e7; text-color:#212529">Generate Plot</button>
  </div>
   </div>
  </div>
</div>
<?php
$backgroundColor10 = '#' . substr(md5(rand()), 0, 6);
?>            
         
<div id="tt"></div>   
    <canvas id="myChart" width="400" height="200"></canvas>

    <button id="scatterExportButton" style="">Export Scatter Data</button>

</div>

            
          </div>
        </div>
                  </div>
                 
                      
                  
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- ./card -->
          </div>


          <!-- /.col -->
        </div>

        
        <!-- /.row -->
        <!-- END CUSTOM TABS -->
    
</section>


<script>
  

function setDefaultSelection() {
   // alert("SDF");
    
  // Get the selected value from the first select box
  var selectedValue = document.getElementById("project_id").value;

  // Get the second select box
  var secondSelect = document.getElementById("tempa");

  // Set the default selection for the second select box based on the selected value
  switch (selectedValue) {
    case "option1":
      secondSelect.value = "default";
      break;
    case "option2":
      secondSelect.value = "optionA";
      break;
    case "option3":
      secondSelect.value = "optionB";
      break;
    default:
      secondSelect.value = "default";
  }
}


$(document).ready(function() {
  var scatterChart; // Declare scatterChart variable

  if (scatterChart) {
    scatterChart.destroy();
  }

  

  $('#applyRange').on('click', function() {
    //var xRange = parseInt($('#yRange').val());
    //var yRange = parseInt($('#xRange').val());

    var xRange = parseInt($('#yRange').val());
    var yRange = parseInt($('#xRange').val());


      // Check if x is greater than y, if not, show an alert and return early
  if (xRange <= yRange) {
    alert("The X range must be greater than the Y range.");
    return;
  }

    // Update the chart's options with the user-defined ranges
    scatterChart.options.scales.xAxes[0].ticks.min = 0;
    //scatterChart.options.scales.xAxes[0].ticks.max = xRange;
  scatterChart.options.scales.xAxes[0].ticks.max = yRange;
    scatterChart.options.scales.yAxes[0].ticks.min = 0;
    //scatterChart.options.scales.yAxes[0].ticks.max = yRange;
  scatterChart.options.scales.yAxes[0].ticks.max = xRange;

    // Update the chart
    scatterChart.update();
  });

  //$('#tempa').on('change', function() {
    $('#plotgen').on('click', function() {
    if (document.getElementById('project_id').value == "") {
      alert("Please select a project");
      $('#tempa').val(0);
      return false;
    } else {
      $.ajax({
        url: '<?php echo url('analysis/getscatterfinal') ?>',
        type: 'POST',
        data: {
          projectid: $('#project_id').val(),
          tempa: $('#tempa').val(),
          xRange: $('#xRange').val(),
          yRange: $('#yRange').val()

        },
        cache: false, // Disable caching for the AJAX request

        success: function(dataPoints) {

          if (scatterChart) {
        scatterChart.destroy();
      }

      // Clear the canvas element
      var canvas = document.getElementById('myChart');
      var ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      
          var parsedData = JSON.parse(dataPoints);
          var labels = [];
          var data = [];

          parsedData.forEach(function(point) {
            labels.push(point.label);
            data.push({
              x: parseFloat(point.y),
              y: parseFloat(point.x)
            });
          });

          var xAxisMin = 0;
          var xAxisMax = 100;
          var yAxisMin = 0;
          var yAxisMax = 100;

   

          scatterChart = new Chart(ctx, {
            type: 'scatter',
            data: {
              labels: labels,
              datasets: [{
                label: 'Yeild vs Plot',
                data: data,
                backgroundColor: 'rgba(255, 99, 71, 0.8)',
                pointRadius: 5
              }]
            },
            options: {
              responsive: true,
              scales: {
                yAxes: [{
                  display: true,
                  stacked: true,
                  ticks: {
                    min: yAxisMin,
                    max: yAxisMax
                  },
                  scaleLabel: {
                    display: true,
                    labelString: 'Volume'
                  }
                }],
                xAxes: [{
                  display: true,
                  stacked: true,
                  ticks: {
                    min: xAxisMin,
                    max: xAxisMax
                  },
                  scaleLabel: {
                    display: true,
                    labelString: 'Yeild'
                  }
                }]
              },
              tooltips: {
                callbacks: {
                  label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index];
                    var x = data.datasets[0].data[tooltipItem.index].x;
                    var y = data.datasets[0].data[tooltipItem.index].y;
                    return label + ': (' + x + ', ' + y + ')';
                  }
                }
              }
            }
          });

          // Export button click event
          $('#scatterExportButton').on('click', function() {
            exportScatterData(scatterChart);
          });
        }
      });
    }
  });

  function exportScatterData(chart) {
  var scatterData = chart.data.datasets[0].data;
  var labels = chart.data.labels;
  var tooltipData = chart.tooltip._data; // Extract tooltip data

  var csvRows = [];

  // Prepare the CSV header row with tooltip data
  var headerRow = ['Solvent Fraction', 'Yield', 'Volume',];
  csvRows.push(headerRow.join(','));

  // Get the current range values
  var xRange = chart.options.scales.xAxes[0].ticks;
  var yRange = chart.options.scales.yAxes[0].ticks;

  // Prepare the CSV rows within the range
  for (var i = 0; i < scatterData.length; i++) {
    var xValue = scatterData[i].x;
    var yValue = scatterData[i].y;
    var tooltip = tooltipData[i]; // Get tooltip data for the current point

    // Check if the data point is within the range
    if (xValue >= xRange.min && xValue <= xRange.max && yValue >= yRange.min && yValue <= yRange.max) {
      var row = [labels[i].replace(/,/g, ''), xValue, yValue, JSON.stringify(tooltip)]; // Include tooltip data as a JSON string
      csvRows.push(row.join(','));
    }
  }

  // Convert the CSV rows to a CSV string
  var csvData = csvRows.join('\n');

  // Set appropriate headers for the file download
  var fileName = 'scatter_data.csv';
  var contentType = 'text/csv';
  var blob = new Blob([csvData], { type: contentType });

  if (navigator.msSaveBlob) {
    // For IE browser
    navigator.msSaveBlob(blob, fileName);
  } else {
    // For other browsers
    var link = document.createElement('a');
    if (link.download !== undefined) {
      // Set the download attribute for modern browsers
      var url = URL.createObjectURL(blob);
      link.setAttribute('href', url);
      link.setAttribute('download', fileName);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
}

});








</script>
<?php include viewPath('includes/footer'); ?>


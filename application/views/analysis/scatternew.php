<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>
<style>
    /* Custom CSS for active tab */
    .nav-tabs .nav-item .nav-link.active {
        font-weight: bold; /* Make the text bold */
    }
</style>

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
            <select name="project" id="project" class="form-control select2" required onchange="setDefaultSelection()">
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


            
      		</div>

             




                             	<!-- view.php -->
<ul class="nav nav-tabs" id="temperatureTabs">
    <li class="nav-item">
        <a class="nav-link active" data-temperature="50" href="#">10C</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-temperature="25" href="#">25C</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-temperature="10" href="#">50C</a>
    </li>
</ul>

<div id="loader" style="display: none;">
  <div class="spinner-border text-primary" role="status">
    <span class="sr-only">Loading...</span>
  </div>
</div><br>
<label for="xRange">X-Axis Range:</label>
<input type="number" id="xRange" value="0">
<label for="yRange">Y-Axis Range:</label>
<input type="number" id="yRange" value="100">
<button id="updatePlot">Update Plot</button>
<br>
<canvas id="scatter-plot"></canvas>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

              <script>
$(document).ready(function () {
    let chart = null; // Initialize the chart variable

    // Select the "10C" tab by default when the page loads
    //$('#temperatureTabs a[data-temperature="50"]').tab('show');

    // Declare scatterData once in this scope
    let scatterData = [];

    const loader = $('#loader'); // The loader element, replace with the actual loader element selector

    $('#temperatureTabs a').on('click', function (e) {
        e.preventDefault();
        const selectedTemperature = $(this).data('temperature');
       // $('#temperatureTabs a[data-temperature='selectedTemperature']').tab('show');
        loader.show(); // Show the loader when making the AJAX request

        // Make an AJAX request to fetch scatter plot data based on selectedTemperature and update the plot
        $.ajax({
            url: '<?= base_url('analysis/get_scatter_data') ?>', // Replace 'controller_name' with the actual name of your controller
            method: 'post',
            data: {
                project: $('#project').val(),
                temperature: selectedTemperature
            },
            beforeSend: function() {
                // This function is called before the AJAX request is sent
                // You can show your loader here
                loader.show();
            },
            success: function (data) {
                scatterData = JSON.parse(data); // Update the existing scatterData, don't redeclare it
                const tempa = selectedTemperature; // Store the selected temperature
                //alert(tempa);

                const ctx = document.getElementById('scatter-plot').getContext('2d');
                var xAxisMin = 0;
                var xAxisMax = 100;
                var yAxisMin = 0;
                var yAxisMax = 100;

                new Chart(ctx, {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: 'Yield vs Plot',
                            data: scatterData.map(point => ({
                                x: parseFloat(point[`${tempa}cyl`]), // Construct the key dynamically
                                y: parseFloat(point[`${tempa}cvl`]), 
                                tooltip: point['w1_solvent_system'].toString() // Ensure it's treated as a string
                            })),
                            backgroundColor: 'rgba(12, 19, 79, 0.6)',
                pointRadius: 5,
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
                                    labelString: 'Yield'
                                }
                            }]
                        },
                        tooltips: {
    callbacks: {
        label: function(tooltipItem, data) {
            var label = data.labels[tooltipItem.index];
            var x = data.datasets[0].data[tooltipItem.index].x;
            var y = data.datasets[0].data[tooltipItem.index].y;

            // Extract w1_solvent_system from your data
            var w1SolventSystem = data.datasets[0].data[tooltipItem.index].tooltip;

            return `Solvent System: ${w1SolventSystem} -> (${x}, ${y})`;
        }
    }
}

                    }
                });
            },
            complete: function() {
                // This function is called after the AJAX request is completed, regardless of success or failure
                // You can hide your loader here
                loader.hide();
            }
        });
    });


    $('#updatePlot').on('click', function () {
        console.log('Button clicked'); // Add this line to check if the event is triggered
        const xRange = parseInt($('#xRange').val());
        const yRange = parseInt($('#yRange').val());

        if (chart) {
            console.log('Updating chart scales.');
            chart.options.scales.xAxes[0].ticks.min = xRange;
            chart.options.scales.xAxes[0].ticks.max = 100; // Set max value as needed
            chart.options.scales.yAxes[0].ticks.min = yRange;
            chart.options.scales.yAxes[0].ticks.max = 100; // Set max value as needed

            chart.update();
        }
    });

});

</script>

<?php include viewPath('includes/footer'); ?>


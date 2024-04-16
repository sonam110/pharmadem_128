<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chart Example</title>
    <!-- Add Bootstrap stylesheet -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style id="compiled-css" type="text/css">

.wrapper {/* ww w .j  av a  2  s.c o  m*/
   width: 800px;
   height: 400px;
   overflow-x: scroll;
}
.chartWrapper {
   width: 6000px;
}


      </style> 

</head>

<body>
    <div class="container-fluid my-5">


 <div class="row">
    <div class="col-3">
      <div class="row justify-content-center">
            <div class="col-md-12" >
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Enter Range Values</h5>
                    </div>
                    <div class="card-body">
                        <form id="myForm" method="post" action="<?= base_url('projects/generate') ?>">

<div class="form-group">
                                <label for="set1_start">Range for 10C mg_ml </label>
                                <select id="selapi_10_operator" name="selapi_10_operator" class="form-control">
    <option value="greater_than">Greater Than</option>
    <option value="less_than">Less Than</option>
   
  </select>

                            </div>
<div class="form-group">
                                <label for="set1_start">Enter Value</label>
                                <input type="number" class="form-control" name="set1_start" step="any" required>
                            </div>
<hr>
<div class="form-group">
                                <label for="set1_start">Range for 50C mg_ml </label>
                                <select id="selapi_50_operator" name="selapi_50_operator" class="form-control">
    <option value="greater_than">Greater Than</option>
    <option value="less_than">Less Than</option>
   
  </select>
                            </div>
 <div class="form-group">
                                <label for="set1_end">Enter Value</label>
                                <input type="number" class="form-control" name="set1_end" step="any" required >
                            </div>
                            

<hr>

<div class="form-group">
                                <label for="set1_start">Range for IMP1_mgml_10C </label>
                                <select id="selimp1_10_operator" name="selimp1_10_operator" class="form-control">
    <option value="greater_than">Greater Than</option>
    <option value="less_than">Less Than</option>
   
  </select>
                            </div>
                           
                            <div class="form-group">
                                <label for="set2_start">Enter Value</label>
                                <input type="number" class="form-control" name="set2_start" step="any" required >
                            </div>
<hr>
<div class="form-group">
                                <label for="set1_start">Range for IMP1_mgml_50C </label>
                                <select id="selimp1_50_operator" name="selimp1_50_operator" class="form-control">
    <option value="greater_than">Greater Than</option>
    <option value="less_than">Less Than</option>
   
  </select>
                            </div>
                            <div class="form-group">
                                <label for="set2_end">Enter Value</label>
                                <input type="number" class="form-control" name="set2_end" step="any" required >
                            </div>
<hr>

<div class="form-group">
                                <label for="set1_start">Range for IMP2_mgml_10C </label>
                                <select id="selimp2_10_operator" name="selimp2_10_operator" class="form-control">
    <option value="greater_than">Greater Than</option>
    <option value="less_than">Less Than</option>
   
  </select>
                            </div>

                            <div class="form-group">
                                <label for="set3_start">Enter Value</label>
                                <input type="number" class="form-control" name="set3_start" step="any" required >
                            </div>

<hr>

<div class="form-group">
                                <label for="set1_start">Range for IMP2_mgml_50C </label>
                                <select id="selimp2_50_operator" name="selimp2_50_operator" class="form-control">
    <option value="greater_than">Greater Than</option>
    <option value="less_than" >Less Than</option>
   
  </select>
                            </div>
                            <div class="form-group">
                                <label for="set3_end">Enter Value</label>
                                <input type="number" class="form-control" name="set3_end" step="any" required >
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="set3_end">Records To Process</label>
                                <input type="number" class="form-control" name="recordsp" step="any" required >
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Chart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-9">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.1"></script>
	<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
	<script src="https://www.chartjs.org/chartjs-plugin-zoom/0.5.0/samples/chartjs-plugin-zoom.js"></script>
    


      <div class="chart-container" style="position: relative; height:400px; width:100%">
    <canvas id="myChart"></canvas>
</div>

    
<?php
if(isset($sname)) { ?>

    <script>

        var lbl = [];
var dt = [];
for (var i = 1; i <= 100; i++) {
    lbl.push("this_is_my_lable_name_" + i);
}
for (var i = 1; i <= 100; i++) {
    dt.push(Math.floor((Math.random() * 100) + 1));
}
        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($sname); ?>,
                datasets: [{
                    label: '10c_mg_ml',
                    data: <?php echo json_encode($l10c_mg_ml); ?>,
                    backgroundColor: '#060047',
                    borderColor: '#060047',
                    borderWidth: 1
                }, {
                    label: '50C_mg_ml',
                    data: <?php echo json_encode($l50C_mg_ml); ?>,
                    backgroundColor: '#3A1078',
                    borderColor: '#3A1078',
                    borderWidth: 1
                }, {
                    label: 'IMP1_mgml_10C',
                    data: <?php echo json_encode($IMP1_mgml_10C); ?>,
                    backgroundColor: '#0E8388',
                    borderColor: '#0E8388',
                    borderWidth: 1
                }, {
                    label: 'IMP1_50C_mg_ml',
                    data: <?php echo json_encode($IMP1_50C_mg_ml); ?>,
                    backgroundColor: '#2D033B',
                    borderColor: '#2D033B',
                    borderWidth: 1
                },{
                    label: 'IMP2_mgml_10C',
                    data: <?php echo json_encode($IMP2_mgml_10C); ?>,
                    backgroundColor: '#735F32',
                    borderColor: '#735F32',
                    borderWidth: 1
                }, {
                    label: 'IMP2_mgml_50C',
                    data: <?php echo json_encode($IMP2_mgml_50C); ?>,
                    backgroundColor: '#A13333',
                    borderColor: '#A13333',
                    borderWidth: 1
                }]
            },
            options: {
					// Elements options apply to all of the options unless overridden in a dataset
					// In this case, we are setting the border of each bar to be 2px wide and green
					elements: {
						rectangle: {
							borderWidth: 2,
							borderColor: 'rgb(0, 255, 0)',
							borderSkipped: 'bottom'
						}
					},
					responsive: true,
					legend: {
						position: 'bottom',
					},
					title: {
						display: true,
						text: 'Solvent Chart'
					},
					pan: {
						enabled: true,
						mode: 'xy',
						speed: 10,
						threshold: 10
					},
					zoom: {
						enabled: true,
						mode: 'y',
						limits: {
							max: 10,
							min: 0.5
						}
					},
					scales: {
						xAxes: [{
							ticks: {
								min: 'March',
								max: 'May'
							}
						}]
					}
				}
        });
    </script>




<?php }?>

    
    <?php
    //echo $selectq;
    ?>
  

</div>
<!-- Add Bootstrap JavaScript -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
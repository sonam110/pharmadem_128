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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.1"></script>
	<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
	<script src="https://www.chartjs.org/chartjs-plugin-zoom/0.5.0/samples/chartjs-plugin-zoom.js"></script>


<div class="chart-container" style="position: relative; height:30vh; width:60vw">
    <canvas id="scatter-chart"></canvas>
</div>
<?php
$da="";
$la="";
?>
 <?php foreach ($data as $row):
   $da.='{x: '.$row["API_Cooling_yield"].','. ' y: '. $row["API_Solvent_vol_50C"].'},';
   $la.="'".$row['Solvent_System']."' ,";
 endforeach; ?>
 
<?php
   $yourString = rtrim($da, ",");
   $youLa = rtrim($la, ",");
//echo $youLa;
?>
<script>
    var scatterChart = new Chart(document.getElementById("scatter-chart"), {
        type: 'scatter',

        data: {
            datasets: [{
                label: 'Scatter Plot',
                data: [<?php echo $yourString; ?>],
                backgroundColor: '',
                borderColor: '#13005A',
                borderWidth: 1
            }]
            
        },
        
        options: {
            scales: {
                xAxes: [{
                    type: 'linear',
                    position: 'bottom',
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },

            tooltips: {
                enabled: true,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '(' + tooltipItem.xLabel + ', ' + tooltipItem.yLabel + ')';
                        return label;
                    }
                }
            }


        }
    });
</script>


</body>
</html>
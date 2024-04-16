<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8"/>
        <title></title>
        <!-- Load Google chart api -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    [{type: 'string', label: 'Solvent Name'}, {type: 'number', label: 'API_Solvent_vol_50C'}, 'log10_25C', 'log10_50C'],
					<?php
						foreach ($chart_data as $data) {
							echo "[' $data->Solvent_System ',' $data->API_Solvent_vol_50C ',' $data->log10_25C ',' $data->log10_50C '],";
						}
					?>
                ]);

                var options = {
                    chart: {
                        title: 'Solvent Name',
                        subtitle: 'API_Solvent_vol_50C, log10_25C, and log10_50C:'
                    }
                };

          var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
 	 //var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_material'));

                chart.draw(data, options);
            }
        </script>
    </head>
    <body>        
        <div id="columnchart_material" style="width: 100%; height: 600px;"></div>
    </body>
</html>
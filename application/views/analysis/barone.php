<?php
// Start the session
//session_start();

// Assuming the necessary data is available in the session and no additional session handling is required

// Get the common labels
$commonLabels = array_keys($alignedData10);
$commonLabels = array_intersect($commonLabels, array_keys($alignedData25), array_keys($alignedData50));
$commonLabels = array_values($commonLabels);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bar Chart View</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart"></canvas>

    <script>
        var ctx = document.getElementById('myChart').getContext('2d');

        var data = {
            labels: <?php echo json_encode($commonLabels); ?>,
            datasets: <?php echo json_encode($_SESSION['additionalDatasets']); ?>
        };

        var options = {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Solvents'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Mg/Ml Values'
                    }
                }
            },
            plugins: {
                zoom: {
                    zoom: {
                        wheel: {
                            enabled: true
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'xy'
                    },
                    pan: {
                        enabled: true,
                        mode: 'xy'
                    },
                    limits: {
                        x: { min: 'original', max: 'original' },
                        y: { min: 'original', max: 'original' }
                    }
                },
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Impurity Rejection Plot'
                }
            }
        };

        // Create the chart
        var chart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    </script>
</body>
</html>
<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
 
 <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
 <!-- Include Chart.js and Chart.js Zoom plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
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
         
              <div class="card-body">
            
              <div class="container-fluid my-5">


<h2 style="color:red;font-size:12px;font-weight:bold;">Please make sure you clear the data before and after the generate chart</h2>

<div class="row">
   <div class="col-3">
     <div class="row justify-content-center">
           <div class="col-md-12" >
               <div class="card">
                   <div class="card-header">
                       <h5 class="mb-0">Enter Range Values</h5>
                   </div>
                   <div class="card-body">
                       <form id="myForm" method="post" action="<?= base_url('analysis/generate') ?>">

                       <div class="form-group">
                               <label for="set1_start">Select Project </label>
                               <select name="project_id" id="project_id" class="form-control select2" required>
      <option value="">Select Project</option>
      <?php foreach ($this->projects_model->getjobscompleted() as $row): ?>
        <?php
                        $checkjobinserts = $this->projects_model->checkjobinserts($row['pid']);

        ?>
        <?php $sel = !empty($_POST['project_id']) && $_POST['project_id'] == $row['pid'] ? 'selected' : '' ?>
        <?php if($checkjobinserts) {  ?>
        <option value="<?php echo $row['pid'] ?>" <?php echo $sel ?>><?php echo $row['project_name'] ?></option>
        <?php } ?>
      <?php endforeach ?>
    </select>
  
 </select>
                           </div>

<div class="form-group">
                               <label for="set1_start">Range for 50C mg_ml </label>
                               <select id="selapi_50_operator" name="selapi_50_operator" class="form-control">
  <option value="greater_than" <?php echo isset($_POST['selapi_50_operator']) && $_POST['selapi_50_operator'] == 'greater_than' ? 'selected' : ''; ?>>Greater Than</option>
  <option value="less_than" <?php echo isset($_POST['selapi_50_operator']) && $_POST['selapi_50_operator'] == 'less_than' ? 'selected' : ''; ?>>Less Than</option>
</select>
                           </div>
<div class="form-group">
                               <label for="set1_end">Enter Value</label>
                               <input type="number" class="form-control" name="set1_start" step="any" required value="<?php echo isset($_POST['set1_start']) ? $_POST['set1_start'] : ''; ?>">
                           </div>
                           

<hr>
<div class="form-group">
                               <label for="set1_start">Range for 25C mg_ml </label>
                               <select id="selapi_25_operator" name="selapi_25_operator" class="form-control">
                               <option value="greater_than" <?php echo isset($_POST['selapi_25_operator']) && $_POST['selapi_25_operator'] == 'greater_than' ? 'selected' : ''; ?>>Greater Than</option>
  <option value="less_than" <?php echo isset($_POST['selapi_25_operator']) && $_POST['selapi_25_operator'] == 'less_than' ? 'selected' : ''; ?>>Less Than</option>

  
 </select>

                           </div>
<div class="form-group">
                               <label for="set1_start">Enter Value</label>
                               <input type="number" class="form-control" name="set2_start" step="any" required value="<?php echo isset($_POST['set2_start']) ? $_POST['set2_start'] : ''; ?>">
                           </div>
<hr>
<div class="form-group">
                               <label for="set1_start">Range for 10C mg_ml </label>
                               <select id="selapi_10_operator" name="selapi_10_operator" class="form-control">
   <option value="greater_than">Greater Than</option>
   <option value="less_than">Less Than</option>
  
 </select>

                           </div>
<div class="form-group">
                               <label for="set1_start">Enter Value</label>
                               <input type="number" class="form-control" name="set3_start" step="any" required value="<?php echo isset($_POST['set3_start']) ? $_POST['set3_start'] : ''; ?>">
                           </div>
<hr>


          
                           <hr>
                           <div class="form-group">
                               <label for="set3_end">Records To Process</label>
                               <input type="number" class="form-control" name="recordsp" step="any" required value="<?php echo isset($_POST['recordsp']) ? $_POST['recordsp'] : ''; ?>">
                           </div>
                           <button type="submit" class="btn btn-primary">Generate Chart</button>
                           <input type="button" class="btn btn-primary" onclick="location.href='<?php echo url('analysis/clearss') ?>';" value="Clear" />

                       </form>

                       <script>
  function clearForm() {
    document.getElementById('myForm').reset();
  }
</script>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <div class="col-9">


  
  


<?php if(isset($alignedValues10)) { 

//print_r($alignedValues10);


  $jbd = $this->projects_model->getprojectdetails($datadet['project_id']);
  // Retrieve existing project details from the session or create an empty array
  $projectDetails = isset($_SESSION['projectDetails']) ? $_SESSION['projectDetails'] : [];

  // Create a new array with the details
$newDetail = [
  'project_name' => $jbd[0]->project_name,
  'selapi_50_operator' => $datadet['selapi_50_operator'],
  'set1Start' => $datadet['set1Start'],
  'selapi_20_operator' => $datadet['selapi_20_operator'],
  'set2Start' => $datadet['set2Start'],
  'selapi_10_operator' => $datadet['selapi_10_operator'],
  'set3Start' => $datadet['set3Start']
];

// Append the new detail to the existing project details
$projectDetails[] = $newDetail;
// Store the updated project details in the session
$_SESSION['projectDetails'] = $projectDetails;

//print_r($_SESSION['projectDetails']);



?>
<span>
  Solvents Count : <?php echo ($scount-1);?>
<?php foreach ($projectDetails as $key => $detail): ?>
    <?php if ($key === 0): ?>
        <h3><strong><?php echo $detail['project_name']; ?> (vs)</strong></h3>
    <?php else: ?>
        <h3><?php echo $detail['project_name']; ?></h3>
    <?php endif; ?>
    [50C mg_ml: <?php echo $detail['selapi_50_operator']; ?>, <?php echo $detail['set1Start']; ?>]
    [25C mg_ml: <?php echo $detail['selapi_20_operator']; ?>, <?php echo $detail['set2Start']; ?>]
    [10C mg_ml: <?php echo $detail['selapi_10_operator']; ?>, <?php echo $detail['set3Start']; ?>]
    
<?php endforeach; ?>
    </span>

<?php } ?>
  

<div style="width: 100%; overflow-x: auto; overflow-y: auto">
  <div style="width: 2000px; height: 500px">
    
    <canvas id="myChart" height="400" width="0"></canvas>
 
  </div>
</div>


<?php

if(isset($alignedValues10)) {
// Example data arrays

// Check if additional datasets are stored in session
if (isset($_SESSION['additionalDatasets'])) {
  // Retrieve additional datasets from session
  $additionalDatasets = $_SESSION['additionalDatasets'];
} else {
  // Create an empty array for additional datasets
  $additionalDatasets = [];
}


$data10 =$alignedValues10;
$data25 =$alignedValues25;
$data50 =$alignedValues50;


// Randomize background colors
$backgroundColor10 = '#' . substr(md5(rand()), 0, 6);
$backgroundColor25 = '#' . substr(md5(rand()), 0, 6);
$backgroundColor50 = '#' . substr(md5(rand()), 0, 6);

$newDataset10 = [
  'label' => $_SESSION['projectname'] . ' 50c mg/ml',
  'data' => $data50,
  'backgroundColor' => $backgroundColor10,
  'borderColor' => '#34495E',
  'borderWidth' => 1
];

$newDataset25 = [
  'label' => $_SESSION['projectname'] . ' 25c mg/ml',
  'data' => $data25,
  'backgroundColor' => $backgroundColor25,
  'borderColor' => '#285C6F',
  'borderWidth' => 1
];

$newDataset50 = [
  'label' => $_SESSION['projectname'] . ' 10c mg/ml',
  'data' => $data10,
  'backgroundColor' => $backgroundColor50,
  'borderColor' => '#006D90',
  'borderWidth' => 1
];



// Add the new datasets to the additional datasets array
$additionalDatasets[] = $newDataset10;
$additionalDatasets[] = $newDataset25;
$additionalDatasets[] = $newDataset50;

// Store the additional datasets in the session
$_SESSION['additionalDatasets'] = $additionalDatasets;

//print_r($additionalDatasets);
?>


<script>
  // Convert PHP variables to JavaScript variables using JSON encoding
  const additionalDatasets = <?php echo json_encode($additionalDatasets); ?>;
  let myChart; // Variable to store the Chart instance

  // Function to calculate the total count of data points
  function calculateTotalCount(data) {
    let totalCount = 0;
    data.forEach((item) => {
      Object.values(item.data).forEach((value) => {
        totalCount += parseFloat(value);
      });
    });
    return totalCount;
  }

  document.addEventListener("DOMContentLoaded", function() {
    // Data for the three datasets
    const data = additionalDatasets;

    // Labels for the X-axis (keys from the data object)
    const labels = Object.keys(data[0].data);

    // Calculate the maximum and minimum values from all datasets
    const maxValues = data.map((item) => Math.max(...Object.values(item.data)));
    const minValues = data.map((item) => Math.min(...Object.values(item.data)));

    // Find the overall maximum and minimum values
    const overallMax = Math.max(...maxValues);
    const overallMin = Math.min(...minValues);

    // Output the counts
    console.log("Number of data points:", calculateTotalCount(data));
    console.log("Number of labels:", labels.length);

    // Values for each dataset
    const datasets = data.map((item) => ({
      label: item.label,
      data: Object.values(item.data),
      backgroundColor: item.backgroundColor,
      borderColor: item.borderColor,
      borderWidth: item.borderWidth,
    }));

    // Chart configuration
    const config = {
      type: "bar",
      data: {
        labels: labels,
        datasets: datasets,
      },
      options: {
        responsive: true,
        scales: {
          x: {
            beginAtZero: true,
          },
          y: {
            min: overallMin, // Set the minimum value
            max: overallMax, // Set the maximum value
            beginAtZero: true,
          },
        },
        plugins: {
          zoom: {
            zoom: {
              wheel: {
                enabled: true,
              },
              pinch: {
                enabled: true,
              },
              mode: "xy",
            },
            pan: {
              enabled: true,
              mode: "xy",
            },
            limits: {
              x: { min: "original", max: "original" },
              y: { min: "original", max: "original" },
            },
          },
          legend: {
            position: "top",
          },
          title: {
            display: true,
            text: "Impurity Rejection Plot",
          },
        },
      },
    };

    // Create the chart
    const ctx = document.getElementById("myChart").getContext("2d");
    myChart = new Chart(ctx, config); // Assign the instance to the 'myChart' variable
  });
</script>

<!-- HTML element to display the total count -->
<div id="totalCount"></div>




<button id="exportButton">Export Data</button>

<script>
  // Function to convert data to CSV format
  function convertDataToCSV(data, summaryData) {
    var csvContent = "data:text/csv;charset=utf-8,";

    // Add the summary data in the header section
    csvContent += "Summary\n";
    csvContent += summaryData + "\n\n";

    // Add the custom headers in the first row
    var headers = ["Solvent"];
    headers.push("50C", "25C", "10C");
    csvContent += headers.join(",") + "\n";

    // Iterate over the rows of data
    for (var i = 0; i < data[0].length; i++) {
      var row = [];

      // Collect the solvent name
      var solventName = data[0][i];

      // If the solvent name contains a comma, enclose it in quotes
      if (solventName.includes(",")) {
        solventName = '"' + solventName + '"';
      }

      row.push(solventName);

      // Collect the data for each column in the row
      for (var j = 1; j < data.length; j++) {
        row.push(data[j][i]);
      }

      csvContent += row.join(",") + "\n";
    }

    return encodeURI(csvContent);
  }

  // Function to handle export button click event
  function handleExportButtonClick() {
    // Get the chart data
    var chartData = myChart.data; // Assuming your chart instance variable is named 'myChart'

    // Extract labels and data values
    var labels = chartData.labels;
    var datasets = chartData.datasets;

    // Create an array to store the CSV rows
    var csvRows = [labels];

    // Iterate over datasets to collect data values
    datasets.forEach(function (dataset) {
      csvRows.push(dataset.data);
    });

    // Add the summary data
    var summaryData = "Solvents Count: <?php echo ($scount); ?>\n";
    <?php foreach ($projectDetails as $key => $detail): ?>
      summaryData += "<?php if ($key === 0): ?><strong><?php echo $detail['project_name']; ?> (vs)</strong><?php else: ?><?php echo $detail['project_name']; ?><?php endif; ?>\n";
      summaryData += "50C mg_ml: <?php echo $detail['selapi_50_operator']; ?>, <?php echo $detail['set1Start']; ?>\n";
      summaryData += "25C mg_ml: <?php echo $detail['selapi_20_operator']; ?>, <?php echo $detail['set2Start']; ?>\n";
      summaryData += "10C mg_ml: <?php echo $detail['selapi_10_operator']; ?>, <?php echo $detail['set3Start']; ?>\n\n";
    <?php endforeach; ?>

    // Remove HTML tags from summaryData, excluding square brackets
    summaryData = summaryData.replace(/<[^>]+>/g, "").replace(/[\[\]]/g, "");

    // Convert data to CSV format
    var csvData = convertDataToCSV(csvRows, summaryData);

    // Create a temporary anchor element
    var link = document.createElement("a");
    link.setAttribute("href", csvData);
    link.setAttribute("download", "chart_data.csv");
    link.click();
  }

  // Attach click event listener to the export button
  var exportButton = document.getElementById("exportButton");
  exportButton.addEventListener("click", handleExportButtonClick);
</script>






<?php
}
?>


<hr>



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

    

      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</div>
    
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php include viewPath('includes/footer'); ?>


<script>

    $("#examplec10").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy",{
                extend: 'excel',
                title: '<?php echo $project_details->project_name ?>',
                messageTop: 'Created Data - PROJECT NAME : "<?php echo $project_details->project_name ?>", PROJECT CODE <?php echo $jstatus[0]->project_id ?>, Job ID: <?php echo $jstatus[0]->id ?>'
            }, "csv", "pdf", "print"]
    }).buttons().container().appendTo('#examplec10_wrapper .col-md-6:eq(0)');


    $("#examplec25").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy",{
                extend: 'excel',
                title: '<?php echo $project_details->project_name ?>',
                messageTop: 'Created Data - PROJECT NAME : "<?php echo $project_details->project_name ?>", PROJECT CODE <?php echo $jstatus[0]->project_id ?>, Job ID: <?php echo $jstatus[0]->id ?>'
            }, "csv", "pdf", "print"]
    }).buttons().container().appendTo('#examplec25_wrapper .col-md-6:eq(0)');


    $("#examplec50").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy",{
                extend: 'excel',
                title: '<?php echo $project_details->project_name ?>',
                messageTop: 'Created Data - PROJECT NAME : "<?php echo $project_details->project_name ?>", PROJECT CODE <?php echo $jstatus[0]->project_id ?>, Job ID: <?php echo $jstatus[0]->id ?>'
            }, "csv", "pdf", "print"]
    }).buttons().container().appendTo('#examplec50_wrapper .col-md-6:eq(0)');

    </script>
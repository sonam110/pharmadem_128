<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<style>
#overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    color: #fff;
    text-align: center;
    padding-top: 20%;
}

.overlay-content {
    font-size: 24px;
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
<div id="overlay">
    <div class="overlay-content">
        Data Optimization is going on...
    </div>
</div>

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
    <label for="set1_start">Select Projects </label>
    <select name="project_id[]" id="project_id" class="form-control select2" required>
        <?php foreach ($this->projects_model->getjobscompleted() as $row): ?>
            <?php
            $checkjobinserts = $this->projects_model->checkjobinserts($row['pid']);
            ?>
            <?php $sel = !empty($_POST['project_id']) && in_array($row['pid'], (array)$_POST['project_id']) ? 'selected' : '' ?>
            <?php if($checkjobinserts) {  ?>
                <option value="<?php echo $row['pid'] ?>" <?php echo $sel ?>><?php echo $row['project_name'] ?></option>
            <?php } ?>
        <?php endforeach ?>
    </select>
</div>

       
<button type="button" id="fetchDataButton" style="display:none">Fetch Data</button>
                   
                       </form>
                  

   

                   </div>
                   <button id="submitDataButton" class="btn btn-sm btn-warning" style="font-size:16px;">Optimize Data</button>
               </div>
           </div>
       </div>
       
   </div>


    
   

    

<div style="max-height: 800px; /* Set the maximum height for vertical scrollbar */
  overflow-y: auto; /* Enable vertical scrollbar when content overflows */
  max-width: 100%; /* Set the maximum width for horizontal scrollbar */
  overflow-x: auto; /">
<!-- <button id="filterButton">Filter</button> -->

<div id="fetchedData"></div>
    <div>
 
      

    </div>
  
   <div>
</div>

            </div><br><br>
            <div style="text-align:center">
            <h2 id="hname" style="text-align:center"></h2>
            <div id="projectNames" style="text-align:center;margin-bottom:10px;"></div>
            </div>

       
            <canvas id="barChart" width="1000" height="500"></canvas>


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

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Get the query string parameter 'selected_pid'
    const urlParams = new URLSearchParams(window.location.search);
    const selectedPid = urlParams.get('id');

    // If 'selected_pid' exists and is not null
    if (selectedPid !== null) {
        // Find the select box element by its ID
        const selectBox = document.getElementById('project_id');

        // Loop through the options and select the one that matches 'selected_pid'
        for (let i = 0; i < selectBox.options.length; i++) {
            const option = selectBox.options[i];
            if (option.value === selectedPid) {
                option.selected = true;
                break; // Exit the loop once the option is found
            }
        }

         // Trigger a click event on the fetchDataButton button
         const fetchDataButton = document.getElementById('fetchDataButton');
        if (fetchDataButton) {
            fetchDataButton.click();
        }
        selectBox.setAttribute('disabled', 'disabled');
        fetchDataButton.setAttribute('disabled', 'disabled');
    }
});
</script>


<script>
    var table;
    var barChart;
    var headerRow = [];
    var chartCreated = false;
    var filteredData; // Declare filteredData globally


        // Event handler for fetching data
        $('#fetchDataButton').click(function () {
            var selectedProjects = Array.from(document.querySelectorAll("#project_id option:checked")).map(function (option) {
                return option.value;
            });

            if (selectedProjects.length === 0) {
                alert("Please select at least one project.");
                return;
            }

            if (selectedProjects.length > 5) {
                alert("You can select a maximum of 5 projects.");
                return;
            }

            $('#fetchDataButton').prop('disabled', true);

            $.ajax({
    type: "POST",
    url: "<?php echo site_url('analysis/getfecthdata'); ?>",
    data: { selectedProjects: selectedProjects },
    dataType: "json",
    success: function (response) {
        
        $('#fetchedData').html('<table id="dataTable" class="table table-bordered table-hover table-striped dataTable no-footer dtr-inline" style="font-size:8px;width: 100%;"></table>');

        var projectsData = response.data;
        
        var tableData = [];

        // Create the header row dynamically
        headerRow = ['Solvent Name'];

        // Add project names and units (10cmgml, 25cmgml, 50cmgml, results_10_id, results_50_id) to the header
        for (var i = 0; i < projectsData.length; i++) {
            var projectName = projectsData[i].projectName;
            headerRow.push(projectName + ' (10cmgml)');
            headerRow.push(projectName + ' (25cmgml)');
            headerRow.push(projectName + ' (50cmgml)');
            headerRow.push(projectName + ' (results_10_id)');
            headerRow.push(projectName + ' (results_50_id)');
        }

        //tableData.push(headerRow);

        // Determine the maximum number of result sets available
        var maxResults = 0;
        for (var i = 0; i < projectsData.length; i++) {
            maxResults = Math.max(maxResults, projectsData[i].results_10.length);
        }

      // Populate the data rows
for (var i = 0; i < maxResults; i++) {
    var dataRow = [];

    // Add "Solvent Name" in the first column
    var solventName = projectsData[0].solvent_w1_solvent_system[i] || '';
    dataRow.push(solventName);

    // Add result values for each project and unit (10cmgml, 25cmgml, 50cmgml, results_10_id, results_50_id)
    for (var j = 0; j < projectsData.length; j++) {
        var projectData = projectsData[j];
        var results_10 = projectData.results_10[i] || '';
        var results_25 = projectData.results_25[i] || '';
        var results_50 = projectData.results_50[i] || '';
        var results_10_id = projectData.results_10_id[i] || '';
        var results_50_id = projectData.results_50_id[i] || '';

        dataRow.push(parseFloat(results_10)); // Parse as a float
        dataRow.push(parseFloat(results_25)); // Parse as a float
        dataRow.push(parseFloat(results_50)); // Parse as a float
        dataRow.push(parseInt(results_10_id)); // Parse as an integer
        dataRow.push(parseInt(results_50_id)); // Parse as an integer

        if (parseFloat(results_10) > parseFloat(results_50)) { // Compare as floats
        tableData.push(dataRow);
        }
    }
   
}

table = $('#dataTable').DataTable({
        data: tableData,
        paging: false,
        dom: '<"top"iBf>rt<"bottom"lp><"clear">',
        buttons: [],
        columns: [
            { title: "Solvent Name" },
            { title: " (10cmgml)" },
            { title: " (25cmgml)" },
            { title: " (50cmgml)" },
            { title: " (results_10_id)" },
            { title: " (results_50_id)" }
        ]
    });

        // Create DataTable with filter inputs
        //createDataTable(projectsData, tableData);

        // Enable the "Filter" button after fetching data
        $('#filterButton').prop('disabled', false);
        $('#fetchDataButton').prop('disabled', false);

// Handle the "Submit" button click
$('#submitDataButton').on('click', function () {
    var selectedData = getAllData();

    // Convert the data to JSON
    var jsonData = JSON.stringify(selectedData);

    function showOverlay() {
    $("#overlay").show();
}

function hideOverlay() {
    $("#overlay").hide();
}

// Send the data via a POST request
$.ajax({
    type: "POST",
    url: "<?php echo site_url('analysis/get1050'); ?>",
    data: jsonData, // Send the JSON string
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    beforeSend: function () {
        showOverlay(); // Show the overlay before making the AJAX call
    },
    success: function (response) {
        // Handle the success response from the server if needed
        if (response.message === "Done") {
            // Display a success message
            // 2000 milliseconds = 2 seconds
        } else {
            // Handle other responses if needed
        }
    },
    error: function (jqXHR, textStatus, errorThrown) {
       // console.log("AJAX Error:", textStatus, errorThrown);
        //console.log("Response:", jqXHR.responseText);
        //alert("Error sending data to the server. See the console for details.");
    },
    complete: function () {
        hideOverlay(); // Hide the overlay when the AJAX call is complete
        //alert("Updates are done.");
            // Reload the page after a short delay (e.g., 2 seconds)
        //setTimeout(function() { location.reload();}, 1000); 
    }
});

});

        // Optionally, you can work with 'tableData' or 'createOrUpdateChart(tableData)' here.
    },
    error: function () {
        alert("Error fetching data.");
        $('#fetchDataButton').prop('disabled', false); // Enable the "Fetch Data" button on error
    },
});



    // Function to get all data from DataTable
function getAllData() {
    var allData = [];

    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
        var rowData = this.data();

        var solventName = rowData[0];
        var results = [];

        for (var i = 4; i < rowData.length; i += 5) {
            var results_10_id = rowData[i];
            var results_50_id = rowData[i + 1];
            results.push({ results_10_id: results_10_id, results_50_id: results_50_id });
        }
        //console.log("Selected row data: ", results); // Log the selected data

        allData.push(results);
    });

    return allData;
}

        });

 // Event handler for applying filters
$('#filterButton').click(function () {
    applyFiltersAndRedraw();
});

// Event handler for creating/updating the chart
$('#createChartButton').click(function () {
    var visibleData = [];

    table.rows().every(function () {
        if (this.nodes().to$().is(':visible')) {
            visibleData.push(this.data());
        }
    });

    createOrUpdateChart(visibleData);
});

function createDataTable(projectsData) {
    // Clear previous DataTable instance, if any
    if (table) {
        table.destroy();
    }

    // Initialize tableData array
    var tableData = [];

    // Populate tableData with data rows
    for (var i = 0; i < projectsData.length; i++) {
        var project = projectsData[i];
        var projectName = project.projectName;
        var solventSystem = project.solvent_w1_solvent_system;
        var results_10 = project.results_10;
        var results_25 = project.results_25;
        var results_50 = project.results_50;
        var results_10_id = project.results_10_id;
        var results_50_id = project.results_50_id;

        // Add data rows for each solvent system
        for (var j = 0; j < solventSystem.length; j++) {
            var dataRow = [
                solventSystem[j],
                parseFloat(results_10[j] || 0),
                parseFloat(results_25[j] || 0),
                parseFloat(results_50[j] || 0),
                parseInt(results_10_id[j] || 0),
                parseInt(results_50_id[j] || 0)
            ];
            tableData.push(dataRow);
        }
    }

    // Create header row HTML
    var headerRowHtml = '<tr><th>Solvent Name</th>';
    for (var k = 0; k < projectsData.length; k++) {
        var projectName = projectsData[k].projectName;
        headerRowHtml += '<th>' + projectName + ' (10cmgml)</th>' +
            '<th>' + projectName + ' (25cmgml)</th>' +
            '<th>' + projectName + ' (50cmgml)</th>' +
            '<th>' + projectName + ' (results_10_id)</th>' +
            '<th>' + projectName + ' (results_50_id)</th>';
    }
    headerRowHtml += '</tr>';

    // Set header row HTML
    $('#dataTable').html(headerRowHtml);

    // Initialize DataTable
    table = $('#dataTable').DataTable({
        data: tableData,
        paging: false,
        dom: '<"top"iBf>rt<"bottom"lp><"clear">',
        buttons: [],
        columns: [
            { title: "Solvent Name" },
            { title: " (10cmgml)" },
            { title: " (25cmgml)" },
            { title: " (50cmgml)" },
            { title: " (results_10_id)" },
            { title: " (results_50_id)" }
        ]
    });

    // Remove the last empty row
    table.row(table.rows().length - 2).remove().draw();
}


        function applyFiltersAndRedraw() {
    // Get filter values and update the DataTable
    var filterValues = {};
    var filterApplied = false; // Track if any filters are applied

    $('.project-filter').each(function () {
        var project = $(this).data('project');
        var units = ['50cmgml', '25cmgml', '10cmgml'];
        var projectFilters = {};

        for (var i = 0; i < units.length; i++) {
            var unit = units[i];
            var max = parseFloat($('#' + project + '_' + unit + '_max').val());
            var min = parseFloat($('#' + project + '_' + unit + '_min').val());

            if (!isNaN(max) || !isNaN(min)) {
                projectFilters[unit] = { max: max, min: min };
                filterApplied = true;
            }
        }

        if (!$.isEmptyObject(projectFilters)) {
            filterValues[project] = projectFilters;
        }
    });

    // Apply filters based on filterValues
    table.rows().every(function () {
        var data = this.data();
        var solventName = data[0];
        var showRow = true;

        for (var project in filterValues) {
            var units = filterValues[project];

            for (var unit in units) {
                var max = units[unit].max;
                var min = units[unit].min;
                var dataIndex = headerRow.indexOf(project + ' (' + unit + ')');
                var value = parseFloat(data[dataIndex]);

                // Check if the value is NaN
                if (isNaN(value)) {
                    showRow = false;
                    break;
                }

                // Check if the value is outside the filter range
                if ((!isNaN(max) && value > max) || (!isNaN(min) && value < min)) {
                    showRow = false;
                    break;
                }
            }
        }

        // Show or hide the row based on filter values
        this.nodes().to$().toggle(showRow);
    });

    // Redraw the DataTable
    table.draw();

    // Get the filtered data after applying filters
    tableData = table.rows({ filter: 'applied' }).data().toArray();
//console.log(tableData);
    // Update the chart with the filtered data
    //createOrUpdateChart(tableData);

    // Update the chart based on the filtered data
    if (!filterApplied) {
        // If no filters are applied, show all data on the chart
        resetChart();
    }
}



// Event handler for exporting CSV
$('#exportCSVButton').click(function () {
    // Use DataTables' buttons().trigger() method to export filtered data
    table.buttons(1).trigger(); // Use the index 1 for the "CSV" button
});


function createOrUpdateChart(dd) {

   // Extract data from the table
   var maxAllowedYValue = 300; // Set the maximum allowed value on the y-axis to 300

   
        var table = document.getElementById('dataTable');
        var headerRow = table.rows[0];
        var dataRows = Array.from(table.rows).slice(2);

        var labels = dd.map(row => row[0]); // Use dd data for labels
        var datasets = [];

  // Extract project names from the header row
var projectNames = Array.from(headerRow.cells).slice(1).map(cell => cell.textContent);

// Create a Set to store unique simplified project names
var uniqueSimplifiedProjectNames = new Set();

// Iterate through the project names and extract the unique simplified names
projectNames.forEach(function(name) {
    // Extract only the part of the name before the first "(" character
    var simplifiedName = name.split(' (')[0];
    // Trim any extra spaces
    simplifiedName = simplifiedName.trim();
    // Store the simplified name in the Set
    uniqueSimplifiedProjectNames.add(simplifiedName);
});

// Convert the Set back to an array
var uniqueSimplifiedProjectNamesArray = Array.from(uniqueSimplifiedProjectNames);

// Store the unique simplified project names in a JavaScript variable
var uniqueSimplifiedProjectNamesString = uniqueSimplifiedProjectNamesArray.join(' vs ');

// Display the unique simplified project names using innerHTML
var projectNamesElement = document.getElementById('projectNames');
projectNamesElement.innerHTML = "<b>" + uniqueSimplifiedProjectNamesString+"</b>";

document.getElementById('hname').innerHTML= "Impurity Rejection Plot";
datasets = [];


  // Create a dataset for each project column, filtering data points exceeding maxAllowedYValue
  for (var i = 1; i < headerRow.cells.length; i++) {
        var columnName = headerRow.cells[i].textContent;
        var data = dd.map(row => {
            var value = parseFloat(row[i]);
            // Filter data points exceeding maxAllowedYValue
            return value > maxAllowedYValue ? maxAllowedYValue : value;
        });
    
    datasets.push({
        label: columnName,
        data: data,
        backgroundColor: getRandomColor(),
        borderColor: getRandomColor(),
        borderWidth: 1
    });
}

        // Create the bar chart
var ctx = document.getElementById('barChart').getContext('2d');
if (barChart) {
    barChart.destroy(); // Destroy the existing chart if it exists
}

barChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: datasets
    },
    options: {
        scales: {
            x: {
                stacked: true,
                stacked: true,
            title: {
                display: true,
                text: 'X-Axis Label' // Add your X-axis label here
            }
            },
            y: {
                beginAtZero: true,
                max: maxAllowedYValue, // Set the maximum allowed value on the y-axis
                title: {
                    display: true,
                    text: 'Solubility mg/ml' // Add the y-axis label
                }
            }
        },
        plugins: {
            legend: {
                display: true, // Display the legend
                position: 'bottom' // Position the legend at the bottom
            }
        }
    }
});

chartCreated = true;


// Function to get project names from table headers
function getProjectNames(table) {
    var headers = table.getElementsByTagName('th');
    var projectNames = [];
    for (var i = 1; i < headers.length; i++) {
        projectNames.push(headers[i].textContent);
    }
    return projectNames;
}

// Function to generate random colors
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}



}




    </script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php include viewPath('includes/footer'); ?>


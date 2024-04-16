<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

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
    <label for="set1_start">Select Projects </label>
    <select name="project_id[]" id="project_id" class="form-control select2" required multiple size="10">
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

       
<button type="button" id="fetchDataButton">Fetch Data</button>
                   
                       </form>
                  

   

                   </div>
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
alert("DG");
        var projectsData = response.data;

        // Create the header row dynamically
        var headerRow = ['Solvent Name'];

        // Add project names and units (10cmgml, 25cmgml, 50cmgml) to the header
        for (var i = 0; i < projectsData.length; i++) {
            var projectName = projectsData[i].projectName;
            headerRow.push(projectName + ' (10cmgml)');
            headerRow.push(projectName + ' (25cmgml)');
            headerRow.push(projectName + ' (50cmgml)');
        }

        // Determine the maximum number of result sets available
        var maxResults = 0;
        for (var i = 0; i < projectsData.length; i++) {
            maxResults = Math.max(maxResults, projectsData[i].results_10.length);
        }

        // Create DataTable with filter inputs and apply filtering
        var dataTable = $('#dataTable').DataTable({
            data: [],
            columns: [],
        });

        for (var i = 0; i < maxResults; i++) {
            var dataRow = [];
            var solventName = projectsData[0].solvent_w1_solvent_system[i] || '';
            dataRow.push(solventName);

            var rowShouldBeAdded = false; // Flag to check if the row should be added

            for (var j = 0; j < projectsData.length; j++) {
                var projectData = projectsData[j];
                var results_10 = parseFloat(projectData.results_10[i]) || 0;
                var results_50 = parseFloat(projectData.results_50[i]) || 0;

                dataRow.push(results_10);
                dataRow.push(results_25);
                dataRow.push(results_50);

                if (results_10 > results_50) {
                    // If results_10 is greater than results_50 for any project, add the row
                    rowShouldBeAdded = true;
                }
            }

            if (rowShouldBeAdded) {
                dataTable.row.add(dataRow);
            }
        }

        dataTable.draw();

        // Enable the "Filter" button after fetching data
        $('#filterButton').prop('disabled', false);
        $('#fetchDataButton').prop('disabled', false);
    },
    error: function () {
        alert("Error fetching data.");
        $('#fetchDataButton').prop('disabled', false); // Enable the "Fetch Data" button on error
    },
});

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

       // Function to create the DataTable with filter inputs and custom buttons
function createDataTable(projectsData, tableData) {
    // Clear previous DataTable instance, if any
    if (table) {
        table.destroy();
    }

    // Create the header row with filter inputs for all projects and units
    var headerRowHtml = '<tr role="row"><th>Solvent Name</th>';
    for (var i = 0; i < projectsData.length; i++) {
        var projectName = projectsData[i].projectName;
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_50cmgml_max" class="project-filter" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_50cmgml_max" class="project-filter" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_25cmgml_max" class="project-filter" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_25cmgml_min" class="project-filter" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_10cmgml_max" class="project-filter" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_10cmgml_min" class="project-filter" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
    }
    headerRowHtml += '</tr>';

    $('#dataTable').html(headerRowHtml);

    // Add event listener for filter input changes
    $('.project-filter').on('change', function () {
        // No need to apply filters here; they will be applied when clicking the "Filter" button
    });

    // Initialize DataTable with export options and a filteredData variable
    var filteredData; // Initialize a variable to store filtered data

    table = $('#dataTable').DataTable({
    data: tableData,
   // scrollY: '3000px',
   // scrollCollapse: false,
    paging: false,
    dom: '<"top"iBf>rt<"bottom"lp><"clear">',
    buttons: [
        {
            text: 'Filter',
            action: function () {
                applyFiltersAndRedraw();
            }
        },
        {
            text: 'Copy',
            extend: 'copyHtml5',
            exportOptions: {
                columns: ':visible', // Export only visible columns
                modifier: {
                    search: 'applied' // Export only filtered data
                }
            }
        },
        {
            text: 'CSV',
            extend: 'csvHtml5',
            exportOptions: {
                rows: ':visible',
                modifier: {
                    search: 'applied'
                }
            }
        },
        {
            text: 'Create Chart',
            action: function () {
                var visibleData = [];

                table.rows().every(function () {
                    if (this.nodes().to$().is(':visible')) {
                        visibleData.push(this.data());
                    }
                });
                
                createOrUpdateChart(visibleData);
            }
        }
    ],
    columns: headerRow.map(function (title) {
        return { title: title };
    })
});


    table.row(table.rows().length - 1).remove().draw();
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


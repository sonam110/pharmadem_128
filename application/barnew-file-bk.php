<?php
defined("BASEPATH") or exit("No direct script access allowed"); ?>

<?php include viewPath("includes/header"); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<style type="text/css">
        #dataTable td{
            font-size: 10px !important;
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
              <li class="breadcrumb-item"><a href="#"><?php echo lang(
                  "home"
              ); ?></a></li>
              <li class="breadcrumb-item"><a href="<?php echo url(
                  "/projects"
              ); ?>"><?php echo lang("projects"); ?></a></li>
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
                       <form id="myForm" method="post" action="<?= base_url(
                           "analysis/generate"
                       ) ?>">
                        <div class="form-group">
                        <label for="set1_start"> Job Type </label>
                         <select class="form-control" name="solvents" id="solvents" required >
                            <option value="" selected disabled>Select Job Type</option>
                            <option value="Pure_68">Pure_68</option>
                            <option value="Binary_1085">Binary_2278</option>
                            <option value="Tertiary-16400">Tertiary-50116</option>
                        </select>
                    </select>
                    </div>
                       <div class="form-group">
                            <label for="set1_start">Select Projects </label>
                            <span id ="projectDiv"></span>
                           
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

       
            <canvas id="barChart" width="1000" height="500" ></canvas>


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
    $(document).ready(function() {
        $('#solvents').change(function() {
            var solvent = $(this).val();
            $('#fetchedData').html('');
            if (solvent !== '') {
                $.ajax({
                    url: '<?= site_url(
                        "analysis/get_projects_by_solvent"
                    ) ?>', // Update the URL to your controller method
                    type: 'POST',
                    data: {solvent: solvent},
                    dataType: 'html',
                    success: function(response) {
                        $('#projectDiv').html(response); // Clear existing options
                        
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                 // Clear options if no solvent is selected
            }
        });
    });


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
                url: "<?php echo site_url("analysis/getfecthdata"); ?>",
                data: { selectedProjects: selectedProjects },
                dataType: "json",
                success: function (response) {
                    $('#fetchedData').html('<table id="dataTable" class="table table-bordered table-hover table-striped dataTable no-footer dtr-inline" style="font-size:8px;width: 100%;"></table>');

                    var projectsData = response.data;
                    var tableData = [];

                    var pureDataArray = {
                    'pure_data1': '(0.0, 0.1, 0.9)',
                    'pure_data2': '(0.0, 0.25, 0.75)',
                    'pure_data3': '(0.0, 0.5, 0.5)',
                    'pure_data4': '(0.0, 0.75, 0.25)',
                    'pure_data5': '(0.0, 0.9, 0.1)'
                    };

                    var terDataArray = {

                        'pure_data1': '(0.0, 0.1, 0.75, 0.15)',
                        'pure_data2': '(0.0, 0.25, 0.50, 0.25)',
                        'pure_data3': '(0.0, 0.5, 0.25, 0.25)'

                    }


                    // Create the header row dynamically
                    headerRow = ['Solvent Name'];

                    // Add project names and units (10cmgml, 25cmgml, 50cmgml) to the header
                    for (var i = 0; i < projectsData.length; i++) {
                        var projectName = projectsData[i].projectName;
                        headerRow.push(projectName + ' (10cmgml)');
                        headerRow.push(projectName + ' (25cmgml)');
                        headerRow.push(projectName + ' (50cmgml)');
                        headerRow.push(projectName + ' 10 CVL');
                        headerRow.push(projectName + ' 25 CVL');
                        headerRow.push(projectName + ' 50 CVL');

                        headerRow.push(projectName + ' 10 Yeild');
                        headerRow.push(projectName + ' 25 Yeild');
                        headerRow.push(projectName + ' 50 Yeild');

                        //headerRow.push(projectName + ' System Name');

                    }

                    tableData.push(headerRow);
                    //tableData.push(dataRow);

                    // Determine the maximum number of result sets available
                    var maxResults = 0;
                    for (var i = 0; i < projectsData.length; i++) {
                        maxResults = Math.max(maxResults, projectsData[i].results_10.length);
                    }

                    // Populate the data rows
                    for (var i = 0; i < maxResults; i++) {
                        var dataRow = [];

                        // Add "Solvent Name" in the first column
                        var solventName = projectsData[0].results_10_ssystem_name[i] || '';
                        console.log(projectsData);
                        //dataRow.push(solventName);
                     
                        if (solventName) {
                            if ((projectsData[0].results_10_rtype[i] != "Pure_68") && (projectsData[0].results_10_rtype[i] != "Tertiary-16400")) {
                                if (pureDataArray[projectsData[0].results_10_wt_fraction[i]]) {
                                    dataRow.push(solventName + "-" + pureDataArray[projectsData[0].results_10_wt_fraction[i]].replace("-", pureDataArray[projectsData[0].results_10_wt_fraction[i]]));
                                } else {
                                    dataRow.push(solventName);
                                }
                            } else if (projectsData[0].results_10_rtype[i] === "Tertiary-16400") {
                                if (terDataArray[projectsData[0].results_10_wt_fraction[i]]) {
                                    dataRow.push(solventName + "-" + terDataArray[projectsData[0].results_10_wt_fraction[i]].replace("-", terDataArray[projectsData[0].results_10_wt_fraction[i]]));
                                } else {
                                    dataRow.push(solventName);
                                }
                            } else {
                                dataRow.push(solventName);
                            }
                        } else {
                            console.error("Solvent name is undefined or null for  " + (i + 1));
                            dataRow.push('');
                        }
                        

                        // Add result values for each project and unit (10cmgml, 25cmgml, 50cmgml)
                        for (var j = 0; j < projectsData.length; j++) {
                            var projectData = projectsData[j];
                            var results_10 = projectData.results_10[i] || '';
                            var results_25 = projectData.results_25[i] || '';
                            var results_50 = projectData.results_50[i] || '';

                            var results_10cvl = projectData.results_10_10cvl[i] || '';
                            var results_25cvl = projectData.results_25_10cvl[i] || '';
                            var results_50cvl = projectData.results_50_50cvl[i] || '';

                            var results_10cyl = projectData.results_10_10cyl[i] || '';
                            var results_25cyl = projectData.results_25_25cyl[i] || '';
                            var results_50cyl = projectData.results_50_50cyl[i] || '';
                        

                            if (!isNaN(parseFloat(results_10))) {
                               dataRow.push(parseFloat(results_10).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_25))) {
                               dataRow.push(parseFloat(results_25).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_50))) {
                               dataRow.push(parseFloat(results_50).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_10cvl))) {
                               dataRow.push(parseFloat(results_10cvl).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_25cvl))) {
                               dataRow.push(parseFloat(results_25cvl).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_50cvl))) {
                               dataRow.push(parseFloat(results_50cvl).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_10cyl))) {
                               dataRow.push(parseFloat(results_10cyl).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_25cyl))) {
                               dataRow.push(parseFloat(results_25cyl).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                            if (!isNaN(parseFloat(results_50cyl))) {
                               dataRow.push(parseFloat(results_50cyl).toFixed(3)); 
                            } else {
                                dataRow.push(null);
                            }
                                                        
                           /* dataRow.push(parseFloat(results_25).toFixed(3)); 
                            dataRow.push(parseFloat(results_50).toFixed(3)); 
                            dataRow.push(parseFloat(results_10cvl).toFixed(3)); 
                            dataRow.push(parseFloat(results_25cvl).toFixed(3)); 
                            dataRow.push(parseFloat(results_50cvl).toFixed(3)); 
                            dataRow.push(parseFloat(results_10cyl).toFixed(3)); 
                            dataRow.push(parseFloat(results_25cyl).toFixed(3)); 
                            dataRow.push(parseFloat(results_50cyl).toFixed(3)); */
                        }

                        //var systemName = projectsData[0].results_10_ssystem_name[i] || '';
                       // dataRow.push(systemName);

                        tableData.push(dataRow);
                    }

                    // Create DataTable with filter inputs
                    createDataTable(projectsData, tableData);

                    // Enable the "Filter" button after fetching data
                    $('#filterButton').prop('disabled', false);
                    $('#fetchDataButton').prop('disabled', false);
                    tableData = table.rows({ search: 'applied' }).data().toArray();

                    //createOrUpdateChart(tableData);

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
            '<input type="number" id="' + projectName + '_10cmgml_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_10cmgml_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_25cmgml_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_25cmgml_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_50cmgml_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_50cmgml_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
         headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_10cvl_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_10cvl_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_25cvl_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_25cvl_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_50cvl_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_50cvl_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
         headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_10cyl_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_10cyl_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_25cyl_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_25cyl_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
            '</th>';
        headerRowHtml += '<th>' +
            '<input type="number" id="' + projectName + '_50cyl_max" class="project-filter form-control" data-project="' + projectName + '" placeholder="Max">' +
            '<input type="number" id="' + projectName + '_50cyl_min" class="project-filter form-control" data-project="' + projectName + '" placeholder="Min">' +
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
        'colvis', 
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
                    action: function () {
                        var selectedProjects = Array.from(document.querySelectorAll("#project_id option:checked")).map(function (option) {
                            return option.value;
                        });
                        $.ajax({
                            type: "POST",
                            url: "<?php echo site_url("analysis/generateCsv"); ?>",
                            data: { selectedProjects: selectedProjects },
                            dataType: "json",
                            success: function (response) {
                                // Convert the CSV data to a Blob
                                var blob = new Blob([response.csvData], { type: 'text/csv' });
                                
                                // Create a temporary link element
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(blob);
                                
                                // Set the filename for the download
                                link.download = 'project_results.csv';
                                
                                // Trigger the download
                                link.click();
                            },
                            error: function () {
                                alert("Error fetching data.");
                            }
                        })
                        // window.location = "<?php echo url(
                         "/projects/generatePdf/"
                     ); ?>"+job_id;
                    },
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
        //var units = ['10cmgml', '25cmgml', '50cmgml'];
    var units = ['10cmgml', '25cmgml', '50cmgml','10cvl', '25cvl', '50cvl','10cyl', '25cyl', '50cyl'];
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
        //resetChart();
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

 const plugin = {
  id: 'customCanvasBackgroundColor',
  beforeDraw: (chart, args, options) => {
    const {ctx} = chart;
    ctx.save();
    ctx.globalCompositeOperation = 'destination-over';
    ctx.fillStyle = options.color || '#99ffff';
    ctx.fillRect(0, 0, chart.width, chart.height);
    ctx.restore();
  }
}; 

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
            customCanvasBackgroundColor: {
                color: 'white',
              },
            legend: {
                display: true, // Display the legend
                position: 'bottom' // Position the legend at the bottom
            }
        }
        
    },
    plugins: [plugin],
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
<?php include viewPath("includes/footer"); ?>


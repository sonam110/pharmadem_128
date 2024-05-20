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
.red{
    color: red;
}
.green{
    color: green;
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
              <li class="breadcrumb-item active">Solubility Correction</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<!-- Main content -->
<div id="overlay">
    <div class="overlay-content">
        Data Solubility correction is going on...
    </div>
</div>
<?php
$project_details = $this->projects_model->getById($jstatus[0]->project_id);

 $check_existing_data = $this->db->get_where('solubility_corrected_predicted_data', array('job_id' => $jstatus[0]->id))->row_array();
?>
<!-- Main content -->
<section class="content">

<div class="row">
          <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
         
              <div class="card-body">
            
              <div class="container-fluid my-5">

                <div class="row">
                   <div class="col-12">
                     <div class="row justify-content-center">
                           <div class="col-md-12" >
                            <div id="cosmo"></div>
                               <div class="card">
                                   <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3"><?php echo lang('projects') ?></h3>
                                <h4 class="card-title p-3">PROJECT CODE <?php echo $jstatus[0]->project_id ?>, Job ID: <?php echo $jstatus[0]->id ?>,
                                PROJECT NAME <?php echo $project_details->project_name ?></h4>
                            
                                
                              </div>
                                
                                   <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                      
                                           <button id = "btnSubmit" class="btn btn-primary" type="button"  style="font-size:16px;">Run Solubility Correction </button>
                                            <div id="loading-image" style="display:none">Please Wait...<img src="<?php echo base_url();?>icons8-dots-loading.gif" /></div>

                                          
                                       </div>
                                       
                                       <div class="col-md-8">
                                    <?php echo form_open_multipart('projects/savesolubilitydata', [ 'class' => 'form-validate', 'autocomplete' => 'off' 
                                    , 'enctype'=>"multipart/form-data"]); ?>
                                    <div class="info-box shadow-lg">

                                    <div class="info-box-content">
                                    <span class="info-box-text"><h6>Upload Known Solubility</h6></span>

                                        <div class="row mb-2">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="inputGroupFile" aria-describedby="inputGroupFileAddon" name="file" required>
                                                    <label class="custom-file-label" for="inputGroupFile">Choose file</label>
                                                </div>
                                                 &nbsp;&nbsp;
                                                <a href="<?php echo site_url('projects/downloadSampleExcel'); ?>">Download Sample</a>
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                        <div class="row mb-2">
                                            <div class="col-lg-12 text-left">
                                            <input type="hidden" name="jobid" id="jobid" value="<?php echo $jstatus[0]->id;?>" />
                                            <input type="hidden" name="type" id="type" value="3" />
                                            <button type="submit" class="btn btn-primary" value="">Upload</button>
                                        </div>
                                        </div>

                                       


                                    </div>

                                    </div>
                                     <?php echo form_close(); ?>
                                </div>
                           
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
<table id="dataTable" class="table table-bordered table-hover table-striped dataTable no-footer dtr-inline" style="font-size:8px;width: 100%;">
    <thead>
        <tr role="row"><th>Id</th><th>Solvent Name</th>
            <th><input type="number" id="known_solubility_10_max" class="project-filter "  placeholder="Max"><input type="number" id="known_solubility_10_min" class="project-filter "  placeholder="Min"></th>
            <th><input type="number" id="predicted_solubility_10_max" class="project-filter "  placeholder="Max"><input type="number" id="predicted_solubility_10_min" class="project-filter "  placeholder="Min"></th>
             <th><input type="number" id="corrected_solubility_10_max" class="project-filter "  placeholder="Max"><input type="number" id="corrected_solubility_10_min" class="project-filter "  placeholder="Min"></th>

            <th><input type="number" id="known_solubility_25_max" class="project-filter "  placeholder="Max"><input type="number" id="known_solubility_25_min" class="project-filter "  placeholder="Min"></th>
            <th><input type="number" id="predicted_solubility_25_max" class="project-filter "  placeholder="Max"><input type="number" id="predicted_solubility_25_min" class="project-filter "  placeholder="Min"></th>
             <th><input type="number" id="corrected_solubility_25_max" class="project-filter "  placeholder="Max"><input type="number" id="corrected_solubility_25_min" class="project-filter "  placeholder="Min"></th>

            <th><input type="number" id="known_solubility_50_max" class="project-filter "  placeholder="Max"><input type="number" id="known_solubility_50_min" class="project-filter "  placeholder="Min"></th>
            <th><input type="number" id="predicted_solubility_50_max" class="project-filter "  placeholder="Max"><input type="number" id="predicted_solubility_50_min" class="project-filter "  placeholder="Min"></th>
             <th><input type="number" id="corrected_solubility_50_max" class="project-filter "  placeholder="Max"><input type="number" id="corrected_solubility_50_min" class="project-filter "  placeholder="Min"></th>
        </tr>
        <tr>
            <th>Job ID</th>
            <th>System Name</th>
            <th>Known Sol 10c</th>
            <th>Predicted Sol 10C</th>
            <th>Corrected Sol 10C</th>
            <th>Known Sol 25c</th>
            <th>Predicted Sol 25c</th>
            <th>Corrected Sol 25c</th>
            <th>Known Sol 50c</th>
            <th>Predicted Sol 50c</th>
            <th>Corrected Sol 50c</th>

        </tr>
    </thead>
    <tbody>
        <!-- Data will be dynamically added here -->
    </tbody>
</table>

        <div>
     
        </div>
      
       <div>
    </div>

    </div><br><br>
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

<script type="text/javascript">
var rowsData = [];


$(document).ready(function() {
    getData();
});


function getData() {

    var job_id = '<?php echo $jstatus[0]->id ?>';
    var filterValues = {};
    var filterApplied = false; 
    $('.project-filter').each(function () {
        var units = ['known_solubility_10', 'predicted_solubility_10', 'corrected_solubility_10','known_solubility_25', 'predicted_solubility_25', 'corrected_solubility_25','known_solubility_50', 'predicted_solubility_50', 'corrected_solubility_50'];
        var projectFilters = {};

        for (var i = 0; i < units.length; i++) {
            var unit = units[i];
            var max = parseFloat($('#' + unit + '_max').val());
            var min = parseFloat($('#' + unit + '_min').val());

            if (!isNaN(max) || !isNaN(min)) {
                projectFilters[unit] = { max: max, min: min };
                filterApplied = true;
            }
        }

        if (!$.isEmptyObject(projectFilters)) {
            filterValues = projectFilters;
        }
    });
    $.ajax({
        url: "<?php echo site_url('projects/getpredictedSolnew'); ?>",
        type: "POST",
        data: { job_id: job_id,filterValues:filterValues},
        dataType: "json",
        success: function(response) {
            var pdata = response;
            rowsData.length = 0;
            rowsData.push(...pdata); // Use spread syntax to push array elements into rowsData
            
            // Call function to populate DataTable after data is loaded
            populateDataTable();
        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    alert(item);
                });
            } else {
                // Handle other types of errors or no errors in the response
                alert("An error occurred. Please try again later.");
            }
        },
    });
}



function populateDataTable() {
    var data = rowsData;
    console.log(data);
   var dataTable =  $('#dataTable').DataTable({
        data: data,
        destroy: true,
        buttons: [
        'colvis', 
        {
            text: 'Filter',
            action: function () {
                getData();
            }
        },
        "copy",
        {
             extend:
                'excel' ,// Add Excel download button
                filename: function () {
                // Dynamic CSV file name based on your logic
                  return '<?php echo $project_details->project_name ?>';
              }
          },
        ],
        
        columnDefs: [
            { targets: 2, type: 'natural' } // Use natural sorting for the 'ssystem_name' column
        ],
        order: [
            [2, 'asc'], // Sort by 'ssystem_name' in ascending order
            [6, 'asc']  // Then by 'temp' in ascending order
        ],
        columns: [
            { data: 'job_id' },
            { data: 'ssystem_name' },
            { data: 'known_solubility_10' },
            { data: 'predicted_solubility_10' },
            { data: 'corrected_solubility_10' },
            { data: 'known_solubility_25' },
            { data: 'predicted_solubility_25' },
            { data: 'corrected_solubility_25' },
            { data: 'known_solubility_50' },
            { data: 'predicted_solubility_50' },
            { data: 'corrected_solubility_50' }
        
        ]
        
    });
    // Append Excel download button to the DataTable container
    dataTable.buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
   // console.log(dataTable.columns().header().to$().map((i, el) => $(el).text()).get());


}


/*
function applyFiltersAndRedraw() {
    // Get filter values and update the DataTable
    var filterValues = {};
    var filterApplied = false; // Track if any filters are applied
    $('.project-filter').each(function () {
        var units = ['known_solubility_10', 'predicted_solubility_10', 'corrected_solubility_10','known_solubility_25', 'predicted_solubility_25', 'corrected_solubility_25','known_solubility_50', 'predicted_solubility_50', 'corrected_solubility_50'];
        var projectFilters = {};

        for (var i = 0; i < units.length; i++) {
            var unit = units[i];
            var max = parseFloat($('#' + unit + '_max').val());
            var min = parseFloat($('#' + unit + '_min').val());

            if (!isNaN(max) || !isNaN(min)) {
                projectFilters[unit] = { max: max, min: min };
                filterApplied = true;
            }
        }

        if (!$.isEmptyObject(projectFilters)) {
            filterValues = projectFilters;
        }
    });

    var headerRow = [];
        var dataTable = $('#dataTable').DataTable({
        // Other options...
        "paging": true, // Enable pagination
        "pageLength": 10, // Number of rows per page
        "lengthMenu": [5, 10, 25, 50], // Page length options
    });

    if (dataTable) {
        var headerCells = dataTable.columns().header().toArray();
        for (var i = 0; i < headerCells.length; i++) {
            headerRow.push($(headerCells[i]).text().trim());
        }
    }

    // Apply filters based on filterValues
    dataTable.rows().every(function () {
        var data = this.data();
      
        //var solventName = data['ssystem_name'];
        var showRow = true;

            var units = filterValues;
         
        for (var unit in units) {
            var max = units[unit].max;
            var min = units[unit].min;
            var value = parseFloat(data[unit]);
            // Check if the value is NaN
           // Check if the value is blank or NaN
            if (isNaN(value) || value === '') {
                showRow = false;
                break;
            }

            // Check if the value is outside the filter range
            if ((!isNaN(max) && value > max) || (!isNaN(min) && value < min)) {
                showRow = false;
                break;
            }
        }

        // Show or hide the row based on filter values
        this.nodes().to$().toggle(showRow);
    });

    // Redraw the DataTable
    dataTable.draw();

    // Get the filtered data after applying filters
    tableData = dataTable.rows({ filter: 'applied' }).data().toArray();
//console.log(tableData);
    // Update the chart with the filtered data
    //createOrUpdateChart(tableData);

    // Update the chart based on the filtered data
    if (!filterApplied) {
        // If no filters are applied, show all data on the chart
        //resetChart();
    }
}*/

</script>

<script>
$(document).ready(function() {
    $("#btnSubmit").click(function(){
      var job_id = '<?php echo $jstatus[0]->id ?>';
      var project_id = '<?php echo $jstatus[0]->project_id ?>';
      $('#loading-image').show();
        $.ajax({
        url: '<?php echo url('projects/run_solubility_correction') ?>/'+job_id,
        type: 'post',
        data: {
            job_id: job_id
        },
        async: true,
        success: function(response) {
           console.log(response);
          if(response=="Done") {
            $('#loading-image').hide();
             $('#cosmo').html("<h4 class='green'>Activity Executed Successfully</h4>");
             window.location.href = '<?php echo url('projects/solubilityCorrection/') ?>' + project_id;

            
           
          } 
          else {
           $('#loading-image').hide();
           $('#cosmo').html("<h4 class='red'>"+response+"</h4>");
           // window.location.href = '<?php echo url('projects/solubilityCorrection/') ?>' + project_id;

            
           
          }
      
        }
        });
    }); 
});
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php include viewPath('includes/footer'); ?>


<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<?php
//print_r($jstatus[0]->id);
$project_details = $this->projects_model->getById($jstatus[0]->project_id);


?>

<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Job Status</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo lang('home') ?></a></li>
              <li class="breadcrumb-item"><a href="<?php echo url('/projects') ?>"><?php echo lang('projects') ?></a></li>
              <li class="breadcrumb-item active"><?php echo $jstatus[0]->id ?></li>
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
                <h3 class="card-title p-3">View Job Details --> <?php echo $jstatus[0]->id ?></h3>
          
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
				  <div class="row">
      		<div class="col-sm-4" style="padding-left: 50px;">
      			<br>
                <h2>
      			PROJECT CODE <?php echo $jstatus[0]->project_id ?><br><hr>
                PROJECT NAME <?php echo $project_details->project_name ?>
</h2>
      			<br>
      		</div>
      		<div class="col-sm-8" style="padding-left: 50px;">
      			<table class="table table-bordered table-striped">
      				<tbody>
      					<tr>
      						<td width="160"><strong>Structure</strong></td>
      						<td><?php echo $jstatus[0]->structure_code ?></td>
      					</tr>
      					<tr>
      						<td><strong>INP Name</strong></td>
      						<td><?php echo $jstatus[0]->inp_filename ?></td>
      					</tr>
      					<tr>
      						<td><strong>Smile</strong>:</td>
      						<td><?php echo $jstatus[0]->smiles ?></td>
      					</tr>
                <tr>
      						<td><strong>MOL Weight</strong>:</td>
      						<td><?php echo $jstatus[0]->mol_weight ?></td>
      					</tr>
      					<tr>
      						<td><strong>Processed On</strong>:</td>
      						<td><?php if ($jstatus[0]->process_start!=''){ echo $jstatus[0]->process_start; } ?></td>
      					</tr>
      					<tr>
      						<td><strong>Status</strong>:</td>
      						<td><?php echo str_replace("Cosmo","DEM",$jstatus[0]->cosmo_status); ?></td>
      					</tr>
      					<tr>
      						<td><strong>Process End Time </strong></td>
      						<td><?php if ($jstatus[0]->process_end!=''){ echo $jstatus[0]->process_end; 
                            
                            $datetime_1 = $jstatus[0]->process_start; 
$datetime_2 = $jstatus[0]->process_end; 
 

$start_datetime = new DateTime($datetime_1); 
$diff = $start_datetime->diff(new DateTime($datetime_2)); 
echo "<hr>DEM File Generated in ..<br>"; 
//echo $diff->days.' Days total<br>'; 
//echo $diff->y.' Years<br>'; 
//echo $diff->m.' Months<br>'; 
echo $diff->d.' Days<br>'; 
echo $diff->h.' Hours<br>'; 
echo $diff->i.' Minutes<br>'; 
echo $diff->s.' Seconds<br>';
 ?>
 <hr>
 <?php if(!$this->projects_model->checkactivityexists($jstatus[0]->project_id)) { ?>
 <a href="<?php echo url('projects') ?>/activity/<?php echo $jstatus[0]->id;?>" class="btn btn-warning btn-sm">Run Solubility For This DEM File</a>
 <a href="<?php echo url('projects') ?>/custom/<?php echo $jstatus[0]->id;?>" class="btn btn-success btn-sm">Run Custom Solubility For This DEM File</a>

 
  <?php } else { echo "<h3 style=color:green>Already Acitivity is Finished, Please Check Results</h3>";}?>
   <?php if(!$this->projects_model->checkactivityphexists($jstatus[0]->project_id)) { ?>
    <a href="javascript:;" onclick="run_ph_solubility(<?php echo $jstatus[0]->id ?>);" class="btn btn-info btn-sm">pH and Biorelevant Solubility</a>
    <div id="cosmo"></div>
     <div id="loading-image" style="display:none">Please Wait...<img src="<?php echo base_url();?>icons8-dots-loading.gif" /></div>  <?php } else { echo "<h4 style=color:green>Already PH Acitivity is Finished, Please Check Results</h4>";}?>


                          <?php } ?><hr>
                            <?php if($jstatus[0]->cosmo_status=="Processing" || $jstatus[0]->cosmo_status=="Pending") { ?>
                              <div id="cosmo"><a href="#" onclick="updateUserStatus('<?php echo $jstatus[0]->id ?>')" class="btn btn-success btn-sm">Check Job Status</a></div>
                              <div id="loading-image" style="display:none">Please Wait...<img src="<?php echo base_url();?>icons8-dots-loading.gif" /></div>
                              <?php
                              } 
?>
                        </td>
      					</tr>
      				</tbody>
      			</table>
      		</div>
      	</div>
                  </div>
                 
				  
                  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

                  <?php if($jstatus[0]->cosmo_status=="Cosmo File Generated") { ?>
<div class="button-container">
        <button id="click">
            View Generated DEM File Data
        </button> 
        <a href="<?php echo url('projects/dpdf/'.$jstatus[0]->id) ?>" target=blank><img src="<?php echo base_url();?>download-pdf.webp" style="max-width:100px" /></a>
    </div>
<div id="element" class ="bottom" style="display:none;font-size:10px;margin-top:11px;">

<?php 
print("<pre>".print_r($jstatus[0]->cosmo_data,true)."</pre>");

//echo $jstatus[0]->cosmo_data; ?>
    </div>
    
<?php
}
?>
<script>
         $("#click").click(function () {
           
            //$("#element").offset().top + 10 // +10 (pixels) reduces the margin.
  
            $("#element").toggle();
        });

       
    </script>
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

<?php include viewPath('includes/footer'); ?>

<script>
	$('#dataTable1').DataTable({
    "order": []
  });
</script>

<script>

function run_ph_solubility(job_id) {
   $('#loading-image').show();
    $.ajax({
        url: "<?php echo site_url('projects/phSolubility'); ?>",
        type: "POST",
        data: { job_id: job_id},
        dataType: "json",
        success: function(response) {
            if(response=="done") {
                $('#loading-image').hide();
                document.getElementById('cosmo').innerHTML="<h3>Activity Executed Successfully </h3>"; 
                console.log(response);
                 window.location.href = '<?php echo url('projects') ?>';
            }
        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    alert(item);
                });
            } else {
                $('#loading-image').hide();
                document.getElementById('cosmo').innerHTML="<h3>Activity Executed Successfully </h3>"; 
                 window.location.href = '<?php echo url('projects') ?>';
               
            }
        },
    });
}

 
window.updateUserStatus = (id) => {
 
 $('#loading-image').show();
  $.get( '<?php echo url('projects/checkjob') ?>/'+id, {
    status: status
  }, (data, status) => {
    if (data=='done') {
      // code
      $('#loading-image').hide();
      document.getElementById('cosmo').innerHTML="<h3>DEM File Generated Successfully </h3>";  

    }else{
        $('#loading-image').hide();
      alert('DEM file not generated....');
    }
  })
}

</script>

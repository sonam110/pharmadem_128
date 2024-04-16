<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<?php
//print_r($jstatus[0]->id);
$project_details = $this->projects_model->getById($jstatus[0]->project_id);

//$last_details = $this->projects_model->get_jobrecords1($jstatus[0]->project_id);

if($this->projects_model->checktempprocess($jstatus[0]->id, '10')) {

  echo "Yes";
}


?>
<style>
.progress {
    height: 20px;
    margin-bottom: 20px;
    overflow: hidden;
    background-color: #f5f5f5;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
}
.progress-bar {
    float: left;
    width: 0%;
    height: 100%;
    font-size: 12px;
    line-height: 20px;
    color: #fff;
    text-align: center;
    background-color: #337ab7;
    -webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
    -webkit-transition: width .6s ease;
    -o-transition: width .6s ease;
    transition: width .6s ease;
}


</style>

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
<?php echo form_open_multipart('projects/runact/'.$jstatus[0]->id.'', [ 'class' => 'form-validate', 'autocomplete' => 'off' ]); ?>
<div class="row">
          <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
              <div class="card-header d-flex p-0">
                <h3 class="card-title p-3">Sub Acitivity Details >> 
      			PROJECT CODE <?php echo $jstatus[0]->project_id ?>, 
                PROJECT NAME <?php echo $project_details->project_name ?>,
                Job Status -> Job Code : <?php echo $jstatus[0]->id ?>
</h3>
          
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
				  <div class="row">
      		
      		<div class="col-sm-12" style="padding-left: 5px;">
      		<?php if($this->projects_model->checkactivityexists($jstatus[0]->project_id)) { ?>
              <div class="shadow-lg rounded" style="background-color:#FFF;  pointer-events: none;
    opacity: 0.4;"> <h3 style="color:green">Already Acitivity is Finished, Please Check Results</h3>
              <?php } else { ?>
                <div class="shadow-lg rounded" style="background-color:#FFF"> 
                <?php } ?>
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    
                        <div class="widget-content widget-content-area br-6 p-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlSelect1">List of Solvents</label>
                                        <select class="form-control" name="solvents" id="solvents">
                                            <option value="">Select Solvents</option>
                                            <option value="Pure_68">Pure_68</option>
                                            <option value="Binary_1085">Binary_2278</option>
                                            <!-- /.<option value="Tertiary-16400">Tertiary-50116</option> -->
                                        </select>
                                    </div>
                                </div>
                               
                            </div>
                            <input id = "btnSubmit" class="btn btn-primary" type="button" value="Run Solubility Now"/>
                                <div id="loading-image" style="display:none">Please Wait...<img src="<?php echo base_url();?>icons8-dots-loading.gif" /></div>
                            </div><br>
                            <input type=hidden id="jvalue" value="<?php echo $jstatus[0]->id ?>" />
                            <input type=hidden id="stype" value="" />
                            <div id="cosmo"></div>
                            <div id="cosmoq"></div>
                        </div>
                        <div class="row">
                         
                        <div class="row mt-3">
                        
                        <div class="progress" id="progress-bar" style="display:;">
    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
        <span class="sr-only">0% Complete</span>
    </div>
</div>

<div id="records"></div>

<table id="record-table">
    <thead>
 
    </thead>
    <tbody>
        <!-- Placeholder rows for existing records -->
        <tr>
            <td></td>
            <td> </td>
            <td> </td>
        </tr>
    
        <!-- New record will be added here -->
    </tbody>
</table>



                        </div>
                    </div>
                </div>
            
      		</div>
      	</div>
                  </div>
                 
                  <?php echo form_close(); ?>			  
                  
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>

window.updateUserStatus = (id) => {

  //$('#fetch-records').click(function() {
       
  //  });

 
 $('#loading-image').show();

 

  $.get( '<?php echo url('projects/checkjob') ?>/'+id, {
    status: status
  }, (data, status) => {
    if (data=='done') {
      // code
      $('#loading-image').hide();
      document.getElementById('cosmo').innerHTML="<h3>Cosmo File Generated Successfully </h3>";  

    }else{
        $('#loading-image').hide();
      alert('Cosmo File is not yet Generated....');
    }
  })
}

</script>



<script>
$(document).ready(function() {
  
  $('#solvents').on('change', function() {
 //alert(this.value);


  document.getElementById('stype').value = this.value;
});

    $("#btnSubmit").click(function(){
      //checkRecordsStatus();
    
if(document.getElementById('solvents').value=="") {
  alert("Please select list of solvents");
  return false;
  document.getElementById('solvents').focus();
}


      var j= document.getElementById('jvalue').value;
      var js= document.getElementById('stype').value;
    
      $('#loading-image').show();
      
      //setInterval(getjb(j), 10);
      //getd(j);


      $.ajax({
    url: '<?php echo url('projects/runact_new') ?>/'+j,
    type: 'post',
    data: {
        status: js
        
    },
    async: true,
    success: function(response) {
      if(response=="Pending") {
        $('#loading-image').hide();
        document.getElementById('cosmo').innerHTML="<h3 style=color:red>Already one of job is running, you have wait until its finished to run next activity... </h3>"; 

        // Call the controller function here
        $.ajax({
                url: '<?php echo url('projects/addqueue') ?>',
                type: 'post',
                data: {
                  stype: js,
                  jobid: j
                },
                success: function(response) {
                  if(response=="Queue Added") { 
                    document.getElementById('cosmoq').innerHTML="<h3 style=color:green>Job Added to Queue... </h3>"; 
                    window.location.href = '<?php echo url('projects') ?>';

                  }
                    // Handle the response from the controller function
                },
                error: function(xhr, status, error) {
                    // Handle any errors that occur during the AJAX request
                }
            });

      } 
      else {
        $('#loading-image').hide();
        document.getElementById('cosmo').innerHTML="<h3>Activity Executed Successfully </h3>"; 
        console.log(response);
      }
  
    }
});
     

    }); 
});




</script>

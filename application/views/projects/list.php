<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<style>
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 9999;
}

#processing-message {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 24px;
}

.progress-bar {
  width: 100px;
  height: 10px;
  background-color: #ccc;
}

.progress-bar-fill {
  height: 100%;
  background-color: #0f0;
  transition: width 0.3s ease-in-out;
}


/* CSS */
.button-87 {

  text-align: center;
  text-transform: uppercase;
  transition: 0.5s;
  background-size: 200% auto;
  color: white;
  border-radius: 10px;
  display: block;
  border: 0px;
  font-weight: 700;
  box-shadow: 0px 0px 14px -7px #f09819;
  background-image: linear-gradient(45deg, #FF512F 0%, #F09819  51%, #FF512F  100%);
  cursor: pointer;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

.button-87:hover {
  background-position: right center;
  /* change the direction of the change here */
  color: #fff;
  text-decoration: none;
}

.button-87:active {
  transform: scale(0.95);
}

</style>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><?php echo lang('projects') ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?php echo url('/') ?>"><?php echo lang('home') ?></a></li>
          <li class="breadcrumb-item active"><?php echo lang('projects') ?></li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex p-0">
                <h3 class="card-title p-3"><?php echo lang('projects') ?></h3>
                <div class="ml-auto p-2">
                    <?php if (hasPermissions('project_add')): ?>
            <a href="<?php echo url('projects/killqueueall') ?>" class="btn btn-primary btn-sm"><span class="pr-1"></span> Kill all Queue Jobs </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo url('projects/killall') ?>" class="btn btn-primary btn-sm"><span class="pr-1"></span> Kill all Active Jobs </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="<?php echo url('projects/del') ?>" class="btn btn-primary btn-sm"><span class="pr-1"></span> Kill DEM Jobs</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <a href="<?php echo url('projects/add') ?>" class="btn btn-primary btn-sm"><span class="pr-1"><i class="fa fa-plus"></i></span> <?php echo lang('project_add') ?></a>
                    <?php endif ?>
                </div>
              </div>
              <div id="overlay">
    
  <div id="processing-message">Processing...</div>

</div>

              <!-- /.card-header -->
              <div class="card-body">
                 <div class="row">
                    Filter: &nbsp; &nbsp;<div class="com-md-6">
                     <select class="form-control" onchange="getProject(this.value);">
                        <option value="0" <?php  echo (@$_GET['type'] =='') ? 'selected' : '' ?>>All</option>
                        <option value="1" <?php echo  (@$_GET['type'] =='progress') ? 'selected' : '' ?>>Job In Progress</option>
                        <option value="2" <?php echo  (@$_GET['type'] =='dmfile') ? 'selected' : '' ?>>DM File In process</option>
                        <option value="3" <?php echo (@$_GET['type'] =='queue') ? 'selected' : '' ?>>Job In Queue</option>
                      
                     </select>
                    </div>
                 </div>
                 <br>
                <table id="example1" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                    <th><?php echo lang('id') ?></th>
                    <th>Project Name</th>
                    <th>Customer</th>
                    <th>Project Code</th>
                    <th>Contact</th>
                    <th>Submission</th>
                    <th>Results & Data</th>
		 
                     <!-- /.<th><?php echo lang('action') ?></th> -->
                  </tr>
                  </thead>
                  <tbody>
                 
                  <?php foreach ($projects as $row): ?>
                    <tr>
                      <td width="60"><?php echo $row->id ?></td>
                      <td width="50" class="text-center">
                        <?php echo $row->project_name ?>

                      </td>
                      <td>
                        <?php echo ucfirst($this->customers_model->getById($row->customer_id)->name) ?>
                      </td>
                      <td><?php echo $row->project_code ?></td>
                      <td><?php echo ucfirst($this->customers_model->getById($row->customer_id)->phone) ?></td>
                      <td>
                        
                      <?php if(!$this->projects_model->checkjobexists($row->id)) { ?>
                      <a href="<?php echo url('projects') ?>/submit/<?php echo $row->id;?>" class="btn btn-success btn-sm">Submit</a>
                      <?php } else { ?>
                        <?php
                        $cj = $this->projects_model->getJobdetails($row->id);
                        
                        
                        if($cj[0]->cosmo_status=="Cosmo File Generated") {
                        ?> <a href="<?php echo url('projects') ?>/jstatus/<?php echo $row->id;?>" class="btn btn-primary btn-sm"> <i class="fas fa-flask"></i> DEM Info</a>

                        <?php }  else {

                          ?>
                          <a href="<?php echo url('projects') ?>/jstatus/<?php echo $row->id;?>" class="btn btn-warning btn-sm">Check Status</a>
                        
                        <?php } 
                        } ?>
                      </td>
                      <td>
                        <?php
                        $checkjob = $this->projects_model->checkjobcompleted($row->id);
                        $checkjobinserts = $this->projects_model->checkjobinserts($row->id);

                        
                        /*$jobExistsq = $this->projects_model->checkJobExistsinQueue($row->id);
                        $data['jobExistsq'] = $jobExistsq;*/

                        $jobExistsq = $this->projects_model->checkJobinQueue($row->id);
                        $data['jobExistsq'] = $jobExistsq;

                        $queryJobsMaster = $this->db->query("SELECT COUNT(*) AS num_records FROM jobs_master WHERE  project_id = $row->id and cosmo_status = 'Processing'");
                        $pendingDMFileJobCount = $queryJobsMaster->row()->num_records;
                            //print_r($jobExistsq);
                            //echo $jobExistsq;
                        //echo $checkjob;
                        //print_r($checkjob);
                        ?>
                        <?php if ($pendingDMFileJobCount > 0): ?>
                           <a href="" class="btn btn-sm btn-info" title="DM File In process..." data-toggle="tooltip"><i class="fa fa-spinner"></i></a>
                          <?php endif; ?>
                        <?php if ($jobExistsq): ?>
                          <a href="" class="btn btn-sm btn-warning" title="In Queue..." data-toggle="tooltip"><i class="fas fa-clock"></i></a>
                           
                          <?php else: ?>
                              
                          <?php endif; ?>
                                  
                      <?php if($this->projects_model->checkactivityexists($row->id)) { ?>
                      <a href="<?php echo url('projects') ?>/results/<?php echo $row->id;?>" class="btn btn-success btn-sm" title="Check Results"><i class="fas fa-layer-group"></i></a>
                        <?php if($checkjob==0 ) {  ?>
                          <div id="loading-image" style="display:"><img src="<?php echo base_url();?>icons8-dots-loading.gif" /></div>
                          <?php } else { ?>
                            <!-- /. <a href="<?php echo url('projects/createdata/'.$row->id) ?>" class="btn btn-sm btn-primary" title="Create Data" data-toggle="tooltip"><i class="fas fa-clipboard"></i></a>  -->
                           <!-- /. <a href="<?php echo url('projects/createdataforcharts/'.$row->id) ?>" class="btn btn-sm btn-primary" title="Create Data" data-toggle="tooltip"><i class="fas fa-plus"></i></a> -->
                       
                           <?php if($checkjobinserts) {  ?>


                            <?php $opcheck = $this->projects_model->getprojectdetails($row->id); ?>
                              <?php if($opcheck[0]->optimised=='Yes') { ?>
                              <a href="<?php echo url('projects/showcreateddata/'.$row->id) ?>" class="btn btn-sm btn-info" title="Show Data" data-toggle="tooltip"><i class="fas fa-list-alt"></i></a>
                      <?php } else { ?>
                        <button class="button-90 btn btn-sm btn-primary" data-id="<?php echo $row->id;?>" title="Create Data" data-toggle="tooltip"><i class="fas fa-cogs"></i></button> 

                        <?php } ?>
                          <?php } else { ?>

                            <button class="button-80 btn btn-sm btn-primary" title="Create Data" data-toggle="tooltip"><i class="fas fa-plus"></i></button> 

                            <?php } ?>
                           <div id="ptext"></div>
                           <?php } } ?>
                    </td>
                       
                      <!-- /.
                      <td>
                        <?php if (hasPermissions('users_edit')): ?>
                          <a href="<?php echo url('users/edit/'.$row->id) ?>" class="btn btn-sm btn-primary" title="<?php echo lang('edit_user') ?>" data-toggle="tooltip"><i class="fas fa-edit"></i></a>
                        <?php endif ?>
                        <?php if (hasPermissions('users_view')): ?>
                          <a href="<?php echo url('users/view/'.$row->id) ?>" class="btn btn-sm btn-info" title="<?php echo lang('view_user') ?>" data-toggle="tooltip"><i class="fa fa-eye"></i></a>
                        <?php endif ?>
                        <?php if (hasPermissions('users_delete')): ?>
                          <?php if ($row->id!=1 && logged('id')!=$row->id): ?>
                            <a href="<?php echo url('users/delete/'.$row->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Do you really want to delete this user ?')" title="<?php echo lang('delete_user') ?>" data-toggle="tooltip"><i class="fa fa-trash"></i></a>
                          <?php else: ?>
                            <a href="#" class="btn btn-sm btn-danger" title="<?php echo lang('delete_user_cannot') ?>" data-toggle="tooltip" disabled><i class="fa fa-trash"></i></a>
                          <?php endif ?>
                        <?php endif ?>
                      </td> -->
                    </tr>
                  <?php endforeach ?>
                  

                  </tbody>
                </table>
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->



<?php include viewPath('includes/footer'); ?>


<script>

function getProject(type){
    if(type=='1'){
       window.location.href = '<?php echo url('projects?type=progress') ?>';
    } else if(type=='2'){
       window.location.href = '<?php echo url('projects?type=dmfile') ?>';
    } else if(type=='3'){
       window.location.href = '<?php echo url('projects?type=queue') ?>';
    } else{
      window.location.href = '<?php echo url('projects') ?>';
    }
}
$('#overlay').hide();

$('.button-90').click(function () {
        var id = $(this).data('id');

        window.location.href = "analysis/barnew1?id="+id;

        // Now, 'id' contains the value you added to the button
        // You can use this value for further processing or sending it via AJAX
        console.log("Clicked button with ID:", id);
    });


 // Attach click event listener to each button
$('.button-80').on('click', function() {

  
  var clickedButton = $(this);
var progressBarHtml = '<div class="progress-bar"><div class="progress-bar-fill"></div></div>';

var row = $(this).closest('tr');
var data1 = row.find('td:nth-child(1)').text();
//alert(data1);

clickedButton.parent().html(progressBarHtml);
$('#overlay').show();
$.ajax({
  url: '<?php echo url('projects/insertresults10') ?>/'+data1,
  type: 'post',
  data: {
    data1: data1
  },
  beforeSend: function() {
    $('#processing-message').text('Temp 10 Processing...(dont press back button or refresh the page ...)');
  },
  async: true,
  complete: function(response) {
    clickedButton.parent().html('10 done');
    console.log(response);
    //$('#overlay').hide();
    
    // Make the next AJAX request for insertresults25
    $.ajax({
      url: '<?php echo url('projects/insertresults25') ?>/'+data1,
      type: 'post',
      data: {
        data1: data1
      },
      beforeSend: function() {
    $('#processing-message').text('Temp 25 Processing...(dont press back button or refresh the page ...)');
  },
      async: true,
      complete: function(response) {
        clickedButton.parent().html('25 done');
        console.log(response);
        //$('#overlay').hide();
        
        // Make the final AJAX request for insertresults50
        $.ajax({
          url: '<?php echo url('projects/insertresults50') ?>/'+data1,
          type: 'post',
          data: {
            data1: data1
          },
          beforeSend: function() {
    $('#processing-message').text('Temp 50 Processing...(dont press back button or refresh the page ...)');
  },
          async: true,
          complete: function(response) {
            clickedButton.parent().html('50 done');
            console.log(response);
            $('#overlay').hide();
            location.reload();

          }
        });
      }
    });
  }
});

     

    }); 
  

</script>



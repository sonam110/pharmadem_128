<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>
<style>

.progress-bar {
  width: 100%;
  height: 20px;
  background-color: #f1f1f1;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background-color: #4caf50;
  transition: width 0.3s ease-in-out;
}

.progress-bar-text {
  font-size: 14px;
  color: #fff;
  text-align: center;
  line-height: 20px;
}


</style>
<?php
$project_details = $this->projects_model->getById($jstatus[0]->project_id);

$solventcountall = $this->db->select('solvents_count,solvent_type')->where('job_id', $jstatus[0]->id)->get('  job_results_ph_count')->row();

if ($solventcountall) {

  $solventsCount = (($solventcountall->solvents_count)*3);

  //echo $solventsCount;
}
else {

  $solventsCount=0;
}

$pr = $this->projects_model->checkjobresultsexitsPh($jstatus[0]->id);



if($pr) {
$totalSum1 = 0;
foreach ($pr as $row1) {
  if (is_numeric($row1['solvent_activity_finished'])) {
    $totalSum1 += $row1['solvent_activity_finished'];
  }
}
}
//print_r($br);



//$tr = $this->projects_model->checkjobresultsexits("Tertiary-16400", $jstatus[0]->id);
if($pr){$pr=$totalSum1;}else {$pr=0;}
//if($tr){$tr=$tr[0]->solvent_activity_finished;}else {$tr=0;}
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jobs', 'Pure', 'Binary'],
          ['Jobs Count', <?php echo $pr;?>, <?php echo $br;?> ]
        
        ]);

        var options = {
          chart: {
           // title: 'Company Performance',
            //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          },
          bars: 'horizontal' // Required for Material Bar Charts.
        };

        

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>

<link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css'>


<style>
#tabs {
    width: 95%;
    margin-left: auto;
    margin-right: auto;
    margin-top: 10px;
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
                <h4 class="card-title p-3">PROJECT CODE <?php echo $jstatus[0]->project_id ?>, Job ID: <?php echo $jstatus[0]->id ?>,
                PROJECT NAME <?php echo $project_details->project_name ?></h4>
                
                <div class="ml-auto p-2">
                    <?php if (hasPermissions('project_add')): ?>
                      <a href="<?php echo url('projects/add') ?>" class="btn btn-primary btn-sm"><span class="pr-1"><i class="fa fa-plus"></i></span> <?php echo lang('project_add') ?></a>
                    <?php endif ?>
                </div>
              </div>
              
              <!-- /.card-header -->
              <div class="card-body">
              <div class="row">
          <div class="col-7">
              <div id="barchart_material" style="width: 600px; height: 100px;"></div>
              
          </div>
          <div class="col-5">
          <div class="info-box">
<span class="info-box-icon bg-info"><i class="far fa-flag"></i></span>
<div class="info-box-content">
<div class="ribbon-wrapper">
<div class="ribbon bg-primary">
Stats
</div>
</div>
<span class="info-box-text">Solvents Combinations Processed</span>
<span class="info-box-number"><?php echo "ph_12 ".$pr ?></span>
<div id="record_count" style="font-size:26px;font-weight:bold;margin-top:10px;">..</div>



	<div id="progress_bar" style="display: none;">
  <img src="<?php echo base_url();?>icons8-dots-loading.gif" />
	</div>


  
</div>

</div>

<div class="progress" style="display:none;">
<div id="progress-barn" class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">

</div>
<div id="prc"></div>
</div>


<div id="loading-image" style="display:none">Please Wait...<img src="<?php echo base_url();?>icons8-dots-loading.gif" /></div>


          </div>
          </div>
    <!-- View -->
<button onclick="confirmAction('<?php echo site_url('projects/deletedataph/'.$jstatus[0]->id); ?>')" class="btn btn-danger">Delete All Job Data</button>

<script>
  function confirmAction(url) {
    if (confirm('Are you sure you want to proceed?')) {
      window.location.href = url;
    }
  }
</script>
    <?php

foreach ($results_type as $row) {
    //print_r($row->solvent_type)
$mg=$row->solvent_type;
if($mg=="Binary_1085") {
  $mg="Binary 2275";
}

if($mg=="Tertiary-16400") {
  $mg="Tertiary 50116";
}
if($mg=="ph_12") {
  $mg="ph_12";
}

?> <hr>

<div class="row">
          <div class="col-4">
          <button class="btn btn-primary" type="button"><?php echo $mg;?></button>
          </div>
          <div class="col-8">
          <?php
$job_results_exists = $this->projects_model->checkjobresultsexitsPh('ph_12', $jstatus[0]->id);

//print_r($job_results_exists[0]->process_start);


//$job_results_exists = $query->result();
    $datetime_1 = @$job_results_exists[0]->process_start;
    $last_index = count($job_results_exists) - 1;
    $datetime_2 = @$job_results_exists[$last_index]->process_end;

//$datetime_1 = $job_results_exists[0]->process_start; 
//$datetime_2 = $job_results_exists[0]->process_end; 

$start_datetime = new DateTime($datetime_1); 
$diff = $start_datetime->diff(new DateTime($datetime_2)); 
echo "Job Executed in ..<br>"; 
//echo $diff->days.' Days total<br>'; 
//echo $diff->y.' Years<br>'; 
//echo $diff->m.' Months<br>'; 
echo $diff->d.' Days, '; 
echo $diff->h.' Hours, '; 
echo $diff->i.' Minutes,' ; 
echo $diff->s.' Seconds';
?>
          </div>
          </div>

    
    <table class="table table-striped table-bordered" style="font-size:10px;width:100%" id="<?php echo $row->solvent_type;?>" style="margin-top:10px;">
       <thead>
          <tr>
            
             <th>Solvent Name</th>
             <th>API</th>
              <th>Data @ (0.0, 0.1, 0.9)</th>
             <th>Data @ (0.0, 0.25, 0.75)</th>
             <th>Data @ (0.0, 0.5, 0.5)</th>
             <th>Data @ (0.0, 0.75, 0.25)</th>
             <th>Data @ (0.0, 0.9, 0.1)</th>
             <th>Temp</th>
             <th>Temp</th>
           
             
          </tr>
       </thead>
       <tbody>
        <?php
        $results = $this->projects_model->getresultsfullPh($jstatus[0]->project_id,$jstatus[0]->id,$row->solvent_type);
      
   

        ?>
          <?php if($results): ?>
          <?php foreach($results as $result): ?>
         
          <tr>
             <td><?php echo $result['solvents']; ?></td>
             <td><?php echo $result['solvent_result_name']; ?></td>
             <td><?php echo $result['pure_data1']; ?></td>
             <td><?php echo $result['pure_data2']; ?></td>
             <td><?php echo $result['pure_data3']; ?></td>
             <td><?php echo $result['input_temp_10']; ?></td>
             <td><?php echo $result['input_temp_20']; ?></td>
             <td><?php echo $result['input_temp_50']; ?></td>
          
             
          </tr>
         <?php endforeach; ?>
         <?php endif; ?>
       </tbody>
     </table>
    
    <?php
    
}
       ?>

  




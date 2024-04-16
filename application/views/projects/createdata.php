<?php
$project_details = $this->projects_model->getById($jstatus[0]->project_id);
ini_set('memory_limit', '-1');
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
          <li class="breadcrumb-item active">Create Data</li>
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
<h3 class="card-title p-3">Created Data - PROJECT NAME <b>"<?php echo $project_details->project_name ?>"</b>, PROJECT CODE <?php echo $jstatus[0]->project_id ?>, Job ID: <?php echo $jstatus[0]->id ?></h3>
<ul class="nav nav-pills ml-auto p-2">
<li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">50 &deg;C</a></li>
<li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">25 &deg;C</a></li>
<li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">10 &deg;C</a></li>

</ul>
</div>
<div class="card-body">
<div class="tab-content">

<div class="tab-pane active" id="tab_1">

<table id="examplec10" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                  <th>ID</th>
                   
                    <th>SS Name</th>
              
                    <th>50 C_mg/ml</th>
                    <th>50 C_VL</th>
                    <th>50 C_Yeild</th>
                
		 
                     <!-- /.<th><?php echo lang('action') ?></th> -->
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($cdata50 as $row50): 
                    //echo $row10->input_temp_50;
                    ?>
                    <tr>
                    <td><?php echo $row50->jbid;?></td>
                    <td><?php echo $row50->w1_solvent_system;?></td>
                
                    <td><?php $this->projects_model->get10mgml($row50->pure_data1,$row50->job_id);?> </td>
                    <td><?php $this->projects_model->get10cvl($row50->pure_data1,$row50->w1_density,$row50->job_id);?> </td>
                    <td><?php $this->projects_model->get10cYN($row50->pure_data1,$row50->job_id,$row50->job_id,$row50->solvents);?> </td>
                    </tr>
                   
                  <?php endforeach ?>
                  </tbody>
                </table>

                <canvas id="scatter-chart"></canvas>
              
    



</div>

<div class="tab-pane" id="tab_2">
<table id="examplec25" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                 <th>ID</th>
                   
                    <th>SS Name</th>
             
                    <th>25 C_mg/ml</th>
                    <th>25 C_VL</th>
                    <th>25 C_Yeild</th>
                
		 
                     <!-- /.<th><?php echo lang('action') ?></th> -->
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($cdata25 as $row25): ?>
                    <tr>
                  
                    <td ><?php echo $row25->jbid;?></td>
                    <td ><?php echo $row25->w1_solvent_system;?></td>
                    
                    <td ><?php $this->projects_model->get10mgml($row25->pure_data1,$row25->job_id);?> </td>
                   
                   
                    <td ><?php $this->projects_model->get10cvl($row25->pure_data1,$row25->w1_density,$row25->job_id);?> </td>
                    
                    
                    <td><?php $this->projects_model->get10cYN($row25->pure_data1,$row25->job_id,$row25->job_id,$row25->solvents);?> </td>
                    
                    
                    </tr>
                   
                  <?php endforeach ?>
                  </tbody>
                </table>

</div>

<div class="tab-pane" id="tab_3">
<table id="examplec50" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                  
                   <th>ID</th>
                    <th>SS Name</th>
                 
                    <th>10 C_mg/ml</th>
                    <th>10 C_VL</th>
                    <th>10 C_Yeild</th>
                
		 
                     <!-- /.<th><?php echo lang('action') ?></th> -->
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($cdata10 as $row10): ?>
                    <?php
                  if(!empty($this->projects_model->getfirstvalue10($row10->pure_data1))) {
                   ?>
                    <tr>
                    
                    <td ><?php echo $row10->jbid;?></td>
                    <td ><?php echo $row10->w1_solvent_system;?></td>
                    
              
             
                    
                    <td ><?php $this->projects_model->get10mgml($row10->pure_data1,$row10->job_id);?> </td>
                   
                   
                    <td ><?php $this->projects_model->get10cvl($row10->pure_data1,$row10->w1_density,$row10->job_id);?> </td>
                     
                    <td><?php $this->projects_model->get10cYN($row10->pure_data1,$row10->job_id,$row10->job_id,$row10->solvents);?> </td>

                    
                    </tr>
                    <?php } ?>
                   
                  <?php endforeach ?>
                  </tbody>
                </table>

</div>

</div>

</div>
</div>

</div>

</div>

       <!--  <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex p-0">
                <h3 class="card-title p-3">Create Data</h3>
              
              </div>
              
         
              <div class="card-body">
                <table id="examplec" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                    <th><?php echo lang('id') ?></th>
                   
                    <th>SS Name</th>
                    <th>AT 10 & 50 </th>
                    <th>LAT 10 & 50</th>
                    <th>10 & 50 C_mg/ml</th>
                    <th>10 & 50 C_VL</th>
                    <th>10 & 50 C_Yeild</th>
                
		 
                
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($cdata as $row): ?>
                    <tr>
                    <td ><?php echo $row->jbid;?></td>
                   
                    <td ><?php echo $row->w1_solvent_system;?> (<?php echo $row->input_temp_10;?> <?php echo $row->input_temp_50;?>)</td>
                    
                    <td ><?php $this->projects_model->getfirstvalue($row->combined_column);?> (<?php echo $row->input_temp_10;?> <?php echo $row->input_temp_50;?>)</td>
                    
                   
                    <td ><?php $this->projects_model->getlat10($row->combined_column);?> (<?php echo $row->input_temp_10;?> <?php echo $row->input_temp_50;?>)</td>
                    <?php
                        //$session = session();

                        if($row->input_temp_50==50) {
                        $mgml=$this->projects_model->get10mgmlcal($row->combined_column);
                        //$session->set('50mgml', $mgml);
                        $this->session->set_userdata('mgml', $mgml);

                        } else {$mgml="";}

                         //echo $mgml;
                    ?>
                    
                    <td ><?php $this->projects_model->get10mgml($row->combined_column);?> (<?php echo $row->input_temp_10;?> <?php echo $row->input_temp_50;?>)</td>
                   
                   
                    <td ><?php $this->projects_model->get10cvl($row->combined_column,$row->w1_density);?> (<?php echo $row->input_temp_10;?> <?php echo $row->input_temp_50;?>)</td>
                    
                    
                    <td ><?php $this->projects_model->get10cY($row->combined_column);?> (<?php echo $row->input_temp_10;?> <?php echo $row->input_temp_50;?>)</td>
                    
                    
                    
                    
                    </tr>
                   
                  <?php endforeach ?>
                  </tbody>
                </table> -->
              </div>

              <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

    <div>
        <canvas id="myChart"></canvas>
    </div>
    <div>z
        <canvas id="myChart2"></canvas>
    </div>

              <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#examplec10').DataTable();

            // Get DataTable data
            var data = table.data().toArray();

            // Bar chart
            var labels = [];
            var values = [];
            for (var i = 0; i < data.length; i++) {
                labels.push(data[i][1]);
                values.push(data[i][2]);
            }

            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Data',
                        data: values,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            // Scatter chart
            var labels2 = [];
            var values2 = [];
            for (var i = 0; i < data.length; i++) {
                labels2.push(data[i][1]);
                values2.push({x: data[i][2], y: data[i][3]});
            }

            var ctx2 = document.getElementById('myChart2').getContext('2d');
            var chart2 = new Chart(ctx2, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Data',
                        data: values2,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            type: 'linear',
                            position: 'bottom'
                        }]
                    }
                }
            });
        });
    </script>


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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    

<?php include viewPath('includes/footer'); ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
 
 <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script>

$("#examplec").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#examplec_wrapper .col-md-6:eq(0)');

    $("#examplec10").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#examplec10_wrapper .col-md-6:eq(0)');


    $("#examplec25").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#examplec25_wrapper .col-md-6:eq(0)');


    $("#examplec50").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#examplec50_wrapper .col-md-6:eq(0)');




window.updateUserStatus = (id, status) => {
  $.get( '<?php echo url('projects/change_status') ?>/'+id, {
    status: status
  }, (data, status) => {
    if (data=='done') {
      // code
    }else{
      alert('<?php echo lang('user_unable_change_status') ?>');
    }
  })
}

</script>



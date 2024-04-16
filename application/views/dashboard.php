<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>
<!-- Content Header (Page header) -->
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">    <?php echo lang('dashboard');?>
</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo lang('home');?></a></li>
              <li class="breadcrumb-item active"><?php echo lang('dashboard');?></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
              <h3><?php echo $customer_count; ?></h3>

                Customers

              </div>
              <div class="icon">
              <i class="ionicons ion-ios-contact-outline"></i>
              </div>

              <a href="<?php echo url('customers') ?>" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
              <h3><?php echo $project_count; ?></h3>

              Projects
              </div>
              <div class="icon">
              <i class="ionicons ion-settings"></i>
              </div>
              <a href="<?php echo url('projects') ?>" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                
             <?php

        $sum = 0;
        foreach ($pending_projects as $project) {
        $sum += $project->jobs_pcount;
        }

            ?>
    
          <h3><?php echo $sum; ?></h3>
          Jobs in Progress
              </div>
              <div class="icon">
              
              <i class="ionicons ion-ios-timer-outline"></i>
              </div>
              <a href="<?php echo url('projects?type=progress') ?>" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
               
                <h3><?php echo $pendingDMFileJobCount; ?></h3>
               DM File in Process
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>

              <a href="<?php echo url('projects?type=dmfile') ?>" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
               
                <h3><?php echo $cpuUsage; ?></h3>
               Server Load
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>

              <a href="#" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->


               <!-- ./col -->
               <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
               
                <h3><?php echo $pendingJobCount; ?></h3>
               Jobs in Queue
              </div>
              <div class="icon">
                <i class="ion ion-clock"></i>
              </div>

              <a href="<?php echo url('projects?type=queue') ?>" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->

           <!-- ./col -->
           <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box" style="background-color:#007bff">
              <div class="inner" style="color:#FFF!important;">
               
                <h3><?php echo $user_count; ?></h3>
               Number of Users
              </div>
              <div class="icon">
                <i class="ion ion-clock"></i>
              </div>

              <a href="<?php echo url('users') ?>" class="small-box-footer"><?php echo lang('dashboard_more_info');?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <div class="row">

             </section>
    <!-- /.content -->

<?php include viewPath('includes/footer'); ?>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo $url->assets ?>js/pages/dashboard.js"></script>

<script>
$(document).ready(function() {
    // Define a function to retrieve server load details
    function getServerLoad() {
        $.ajax({
            url: '<?php echo base_url("dashboard/getload"); ?>',
            success: function(data) {
                $('#server-load').html(data);
            }
        });
    }

    // Call the function immediately
    getServerLoad();

    // Call the function every 10 seconds
    setInterval(function() {
        getServerLoad();
    }, 10000);
});

  </script>
<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<style>

.column-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin: 5px;
}

.column {
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
  padding: 10px;
  margin: 5px;
  transition: transform 0.3s;
  text-align: center;
  width: 100px;
}

.column:hover {
  transform: translateY(-3px);
  background-color: #F9FBE7;
  box-shadow: 2px 5px 5px rgba(0, 0, 0, 0.5);
  border: 1px solid #F9FBE7;
}

.column:active {
  transform: translateY(0);
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
  border: 1px solid #ddd;
  transition: transform 0.1s;
}

.column::before {
  content: "";
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  z-index: -1;
  border-radius: 5px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
}

.column:hover::before {
  transform: translate(3px, 3px);
  box-shadow: 2px 5px 10px rgba(0, 0, 0, 0.5);
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

<!-- Main content -->
<section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-info">

              <a href="<?php echo url('analysis/scatter') ?>" class="small-box-footer btn-secondary btn-lg">Scatter Plot Volume vs Yeild <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
            <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
           

              <a href="<?php echo url('analysis/barnew') ?>" class="small-box-footer btn-secondary btn-lg">Bar Charts <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          
        </div>
        <!-- /.row -->
        <div class="row">

             </section>
    <!-- /.content -->

<?php include viewPath('includes/footer'); ?>
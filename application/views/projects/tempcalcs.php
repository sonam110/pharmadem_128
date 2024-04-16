<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>


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
                <h3 class="card-title p-3">Create Data</h3>
              
              </div>
              
              <!-- /.card-header -->
              <div class="card-body">
                <table id="examplec" class="table table-bordered table-hover table-striped">
                  <thead>
                  <tr>
                    <th><?php echo lang('id') ?></th>
                   
                    <th>SS Name</th>
                    <th>AT 10 </th>
                    <th>LAT 10</th>
                    <th>10 C_mg/ml</th>
                    <th>10  C_VL</th>
                    <th>10 C_Yeild</th>
                
		 
                     <!-- /.<th><?php echo lang('action') ?></th> -->
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($cdata as $row): ?>
                    <tr>
                    <td style="font-size:8px;"><?php echo $row->cid;?></td>
                   
                    <td style="font-size:8px;"><?php echo $row->s1;?> -> <?php echo $row->s2;?></td>
                    
                    <td style="font-size:8px;"><?php $this->projects_model->getfirstvalue($row->pure_data);?> </td>
                    
                   
                    <td style="font-size:8px;"><?php $this->projects_model->getlat10($row->pure_data);?> </td>
                
                    
                    <td style="font-size:8px;"><?php $this->projects_model->get10mgml($row->pure_data);?> </td>
                   
                   
                    <td style="font-size:8px;"><?php $this->projects_model->get10cvl($row->pure_data,"1.023");?> </td>
                    
                    
                    <td style="font-size:8px;"><?php $this->projects_model->get10cY($row->pure_data);?> </td>
                    
                    
                    
                    
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
 
 <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script>

$("#examplec").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,order: [[0, 'desc']],
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#examplec_wrapper .col-md-6:eq(0)');



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



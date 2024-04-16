<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


</div>
<!-- ./wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
    <!-- ./ Powered and Developed by <a href="http://www.vincatis.com/" target=blank>Vincatis</a> Tenchnologies-->
  
      &nbsp; &nbsp; &nbsp; &nbsp; 
      <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y') ?> <a href="<?php echo url('/') ?>">
    Pharma DEM Solutions
    </a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->






<script src="<?php echo $url->assets ?>plugins/jquery/jquery.min.js"></script>

<script src="<?php echo $url->assets ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="<?php echo $url->assets ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/jszip/jszip.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>

<!-- jquery-validation -->
<script src="<?php echo $url->assets ?>plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo $url->assets ?>plugins/jquery-validation/additional-methods.min.js"></script>

<!-- Bootstrap Switch -->
<script src="<?php echo $url->assets ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

<!-- Select2 -->
<script src="<?php echo $url->assets ?>plugins/select2/js/select2.full.min.js"></script>

<!-- AdminLTE App -->
<script src="<?php echo $url->assets ?>js/adminlte.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="<?php echo $url->assets ?>js/demo.js"></script>

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
      order: [[0, 'desc']],
    });

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    $.validator.setDefaults({
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });

  });
  
  
</script>

</body>
</html>



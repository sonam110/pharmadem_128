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

</style>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Solvents Master</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?php echo url('/') ?>"><?php echo lang('home') ?></a></li>
          <li class="breadcrumb-item active">Solvents Master</li>
        </ol>
      </div>
    </div>
    <div id="overlay">
  <div id="processing-message">Processing...</div>
</div>
  </div><!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">

  <!-- Default card -->
  <div class="card">
    <div class="card-header with-border">
      <h3 class="card-title">Solvents List</h3>

   

    </div>
    <div class="card-body">
      <table id="dataTable1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th><?php echo lang('id') ?></th>
            <th>Solvent Name</th>
            <th>SMILES Code</th>
            <th>Molecular Weight</th>
            <th>Schema Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($cdata as $row): ?>
            <tr>
              <td width="60"><?php echo $row->s_id ?></td>
              <td>
                <?php echo $row->solvent1_name ?>
              </td>
              <td class="smiles-code"></td>
                <td class="molecular-weight"></td>
                <td class="schema-image"></td>
                <td>
                    <button class="get-details-btn btn btn-primary btn-sm" data-solvent-id="<?php echo $row->s_id; ?>">Get Details</button>
                </td>
            </tr>
          <?php endforeach ?>

        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->

</section>
<!-- /.content -->

<?php include viewPath('includes/footer'); ?>

<script>
$(document).ready(function() {
    $('#overlay').hide();

    // Bind click event for the "Get Details" button
    $('.get-details-btn').on('click', function() {
        $('#overlay').show();
        var solventId = $(this).data('solvent-id');
        var row = $(this).closest('tr');
        
        // Make the AJAX request
        $.ajax({
            url: '<?php echo site_url('projects/getSolventDetails') ?>' + '/' + solventId,
            type: 'GET',
            dataType: 'json',

            success: function(response) {
                if (response.error) {
                    // Handle error response
                    alert(response.error);
                    $('#overlay').hide();
                } else {
                    $('#overlay').hide();

                    // Update the table cells with the solvent details
                    row.find('.smiles-code').text(response.smiles_code);
                    row.find('.molecular-weight').text(response.molecular_weight);

                    // Display the schema image
                    var img = document.createElement('img');
                    img.src = 'data:image/png;base64,' + response.schema_image;
                    row.find('.schema-image').empty().append(img);
                }
            },
            beforeSend: function() {
             $('#processing-message').text('Fecting Solvent Details Please Wait...');
            },
            error: function(xhr, status, error) {
                // Handle error conditions
                console.log(xhr.responseText);
            }
        });
    });
});
</script>
<script>
	$('#dataTable1').DataTable({
    "order": [],
    "pageLength":100
  });

  

</script>

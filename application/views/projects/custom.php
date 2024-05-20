<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>
<?php
$project_details = $this->projects_model->getById($jstatus[0]->project_id);

?>
<style>

.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 40vh;
}

select {
  width: 200px;
  height: 200px;
}

.button-container {
  margin-top: 20px;
  display: flex;
  justify-content: center;
}

button {
  margin: 0 10px;
  padding: 8px 16px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

button:hover {
  background-color: #45a049;
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
      		<div class="col-sm-12" style="padding-left: 50px;">
      			<br>
       
      			<br>
      		</div>
      		
      	</div>
                  </div>
                  <?php echo form_open_multipart('#', [ 'id' => 'myForm', 'class' => 'form-validate', 'autocomplete' => 'off' ]); ?>
         
                  <div class="row">
<div class="col-lg-3 col-6">

<div class="">

<select id="masterSelect" multiple class="form-control">
    <?php foreach ($cdata as $row): ?>
    <option value="<?php echo $row->s_id ?>" data-id="id1"><?php echo $row->s_id ?> . <?php echo $row->solvent1_name ?></option>
    <?php endforeach ?>
    
  </select>

</div>
</div>

<div class="col-lg-3 col-6" style="display: flex;
    align-items: center;
    flex-wrap: wrap;">

<div class="">

<button id="copyButton" type="button" style="form-control vertical-algin:center">Copy Selected Options</button>

</div>
</div>

<div class="col-lg-3 col-6">

<div class="">

<select id="targetSelect" multiple class="form-control" name="targetSelect[]">
  <!-- Selected options will be copied here -->
</select>
    
  </select>
</div>
</div>

<div class="col-lg-3 col-6">

<div class="">

<select class="form-control" name="solvents" id="solvents">
    <option value="">Select Job Type</option>
    <option value="Pure_68">Pure_68</option>
    <option value="Binary_1085">Binary_2278</option>
    <option value="Tertiary-16400">Tertiary-50116</option>
</select>

</div>
<br>
<div class="">
<button id="retrieveIDsButton" type="submit">Process Custom</button>

    </div>
    <div id="cstats"></div>
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
    <div id="cosmo"></div>
    <div id="cosmoq"></div>

</section>





<?php include viewPath('includes/footer'); ?>
<script>
$(document).ready(function() {
  // Check if there are saved selected values in local storage
  var savedSelectedValues = sessionStorage.getItem('selectedValues');
  if (savedSelectedValues) {
    $('#targetSelect').val(savedSelectedValues.split(','));
  }

  // Function to call the controller function and handle the response
  function callControllerFunction() {
    var sids = $('#targetSelect').val();
    var type = $('#solvents').val();
    var jid = <?php echo $jstatus[0]->id ?>;
  
    $.ajax({
      url: '<?php echo url('projects/customcalulation') ?>',
      type: 'post',
      data: {
        ids: sids,
        jtype: type,
        jobid: jid
      },
      success: function(response) {
        if(response=="not done") {

        }
        if(response=="Pending") {
         
          document.getElementById('cosmo').innerHTML="<h3 style=color:red>Already one of job is running, you have wait until its finished to run next activity... </h3>"; 

          // Call the controller function here
          $.ajax({
                  url: '<?php echo url('projects/addcustomjobqueue') ?>',
                  type: 'post',
                  data: {
                    ids: sids,
                    jtype: type,
                    jobid: jid
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
        if (response === "done") { 
          $('#cosmoq').html("<h3 style='color:green'>Custom Calculations Done. </h3>");
          // Remove saved progress on completion
          sessionStorage.removeItem('selectedValues');
        } else {
          // If the process is not done, continue after a delay
          setTimeout(callControllerFunction, 5000);
        }
        // Handle the response from the controller function
      },
      error: function(xhr, status, error) {
        // Handle any errors that occur during the AJAX request
      }
    });
  }
 // Start the process
 callControllerFunction();
  // Retrieve IDs Button Click Handler
  $('#retrieveIDsButton').click(function() {
    var selectedValue = $('#solvents').val();
    if (selectedValue === "") {
      alert("Please select an option from the dropdown.");
      return false; // Prevent form submission
    }

    // Save the selected values in local storage
    sessionStorage.setItem('selectedValues', $('#targetSelect').val().join(','));

    var selectElement = document.getElementById('targetSelect');
    var options = Array.from(selectElement.options);

  if (options.length > 0) {
    options.sort(function(a, b) {
      return a.value - b.value;
    });
  

    options.forEach(function(option) {
      selectElement.appendChild(option);
    });

    $('#targetSelect option').prop('selected', true);
    $("#retrieveIDsButton").attr("disabled", true);

    setTimeout(function() {
     // window.location.href = "<?php echo url('projects') ?>"
    }, 5000);

    $('#cstats').html("<h4 style='color:green'>Calculations Started.. </h4>");

   } else {
        alert("Please copy selected options.");
        return false; // Prevent form submission
    }
  });



  // Copy Button Click Handler
  $('#copyButton').click(function() {
    var selectedOptions = $('#masterSelect option:selected').clone();
    var currentSelectionCount = $('#targetSelect option').length;

    if (currentSelectionCount + selectedOptions.length > 15) {
      // Display an error message or perform any desired action
      $('#processing-message').text('Maximum selection limit reached.');
      return;
    }

    // Filter out already selected options
    selectedOptions = selectedOptions.filter(function() {
      return !$('#targetSelect option[value="' + this.value + '"]').length;
    });

    selectedOptions.appendTo('#targetSelect');
  });

  // Retrieve selected values in targetSelect
  $('#retrieveSelectedButton').click(function() {
    $('#targetSelect option').prop('selected', true);
    // Display selected count
    var selectedCount = $('#targetSelect option:selected').length;
    $('#selectedCount').text('Selected: ' + selectedCount);
    var selectedValues = $('#targetSelect').val();
    
    console.log(selectedValues);

    // Do whatever you want with the selected values
  });
});
</script>


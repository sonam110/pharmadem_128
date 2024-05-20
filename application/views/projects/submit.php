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

  #el02 { /* Text and background colour, blue on light gray */
color:#00f;

}

    table{
        width: 100%;
        margin-bottom: 20px;
		border-collapse: collapse;
    }
    table, th, td{
        border: 1px solid #cdcdcd;
    }
    table th, table td{
        padding: 10px;
        text-align: left;
    }
    fieldset {position:relative} /* For legend positioning */
    #el08 legend {font-size:2em} /* Bigger text */
</style>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function(){
        $(".add-row").click(function(){
          
            var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='text' class='form-control' placeholder=''></td><td><input type='text' class='form-control' placeholder=''></td><td><input type='text' class='form-control' placeholder=''></td></tr>";
            $(".table tbody").append(markup);
        });
        
        // Find and remove selected table rows
        $(".delete-row").click(function(){
            $(".table tbody").find('input[name="record"]').each(function(){
            	if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
    });    
</script>

<script>
    $(document).ready(function(){
        $(".add-row1").click(function(){

          
            var markup = "<tr><td><input type='checkbox' name='record1'></td><td><input type='text' class='form-control' placeholder=''></td><td><input type='text' class='form-control' placeholder=''></td><td><input type='text' class='form-control' placeholder=''></td></tr>";
            $(".table1 tbody").append(markup);
        });
        
        // Find and remove selected table rows
        $(".delete-row1").click(function(){
            $(".table1 tbody").find('input[name="record1"]').each(function(){
            	if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
    });    
</script>


<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Project Submission</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo lang('home') ?></a></li>
              <li class="breadcrumb-item"><a href="<?php echo url('/projects') ?>"><?php echo lang('projects') ?></a></li>
             
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<!-- Main content -->

<!-- Main content -->
<section class="content">

<div id="overlay">
  <div id="processing-message">Please wait checking for any running jobs/dem file generations...</div>
</div>


<?php echo form_open_multipart('projects/msavenew', [ 'class' => 'form-validate', 'autocomplete' => 'off' ]); ?>
<div class="row">
          <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
             <!-- /.card-header -->
              <div class="card-body">


              <div class="container">

              <div class="info-box shadow-lg">

<div class="info-box-content">
<div id="loading-message" style="margin-top:10px; margin-bottom:10px;font-weight:bold;color:green;"></div>

<span class="info-box-text"><h4 class="mb-1">(<?php echo $Project->project_name;?>) Submission</h4></span>
              <div class="row mb-4">
              
</div>
  <div class="row mb-2">
    <div class="col-md-2">Structure</div>
    <div class="col-md-3"><select class="form-control" name="structure" required>
                                            <option hidden="">Select Structure</option>
                                            <option>Crystal</option>
                                            <option>SDF</option>
                                            <option>Mol</option>
                                            <option selected>Smile</option>
                                        </select></div>
                                        <div class="col-md-2 text-left">Smiles</div>
    <div class="col-md-5 text-left"><input type="text" name="Smile" id="Smile" class="form-control" placeholder=""></div>

  </div>

  <div class="row mb-2">
    <div class="col-md-2 text-left">Mol Weight</div>
    <div class="col-md-3 text-left"><input type="text" name="mweight" class="form-control" placeholder="" required></div>

    <div class="col-md-2 text-left">JOB Name</div>
    <div class="col-md-3 text-left"><input type="text" name="mname" class="form-control" placeholder="" required></div>
</div>
 
  <div class="row mb-2">
  <div class="col-md-2">Charge</div>
    <div class="col-md-3"><input type="text" name="mvalue" class="form-control" placeholder="" required></div>
    

    <div class="col-md-2 text-left"></div>
    <div class="col-md-3 text-left"></div>
  </div>
  
  </div>
  </div>

  
<div class="info-box shadow-lg">

<div class="info-box-content">
<span class="info-box-text"><h3>Solid State Properties</h3></span>

    <div class="row mb-2">
        <div class="col-md-auto text-right">Known Solubility (mg/ml)</div>
        <div class="col-md-2"><input type="text" name="kns" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Hfuss Value</div>
        <div class="col-md-2"><input type="text" name="hfvalue" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">MP</div>
        <div class="col-md-2"><input type="text" name="mp" class="form-control" placeholder=""></div>
    </div>


 

</div>

</div>


<div class="info-box shadow-lg">

<div class="info-box-content">
<span class="info-box-text"><h3> Solubility</h3></span>

    <div class="row mb-2" >
    
        <div class="col-md-auto text-right">Solvent Name</div>
        <div class="col-md-2">
           <select   class="form-control" name="s_name[]">
            <option value="" selected disabled> Select Solvent</option>
            <?php foreach ($cdata as $row): ?>
            <option value="<?php echo $row->solvent1_name ?>" data-id="id1"><?php echo $row->s_id ?> . <?php echo $row->solvent1_name ?></option>
            <?php endforeach ?>
            
          </select>
       </div>

        <div class="col-md-auto text-right">Solubility Value</div>
        <div class="col-md-2"><input type="text" name="s_value[]" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">MG/ML</div>
        <div class="col-md-auto text-right">Temp</div>
           <div class="col-md-2">
               <select   class="form-control" name="temp[]" required>
                <option value="10" > 10</option>
                <option value="25" > 25</option>
                <option value="50" > 50</option>
              </select>
           </div>
        <button type="button"  class="btn btn-info" id="addButton">Add New</button>

    </div>
    <div id="textBoxContainer"></div>


</div>

</div>


<div class="info-box shadow-lg">

<div class="info-box-content">
<span class="info-box-text"><h3>Excipients</h3></span>

    <div class="row mb-2">
        <div class="col-md-auto text-right">Excipients Name</div>
        <div class="col-md-2"><input type="text" name="e_name" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Wt,/W</div>
        <div class="col-md-2"><input type="text" name="e_wt" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Grade</div>
        <div class="col-md-2"><input type="text" name="e_grade" class="form-control" placeholder=""></div>
    </div>

    <div class="row mb-2">
        <div class="col-md-auto text-right">Excipients Name</div>
        <div class="col-md-2"><input type="text" name="e_name1" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Wt,/W</div>
        <div class="col-md-2"><input type="text" name="e_wt1" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Grade</div>
        <div class="col-md-2"><input type="text" name="e_grade1" class="form-control" placeholder=""></div>
    </div>

    <div class="row mb-2">
        <div class="col-md-auto text-right">Excipients Name</div>
        <div class="col-md-2"><input type="text" name="e_name2" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Wt,/W</div>
        <div class="col-md-2"><input type="text" name="e_wt2" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Grade</div>
        <div class="col-md-2"><input type="text" name="e_grade2" class="form-control" placeholder=""></div>
    </div>

    <div class="row mb-2">
        <div class="col-md-auto text-right">Excipients Name</div>
        <div class="col-md-2"><input type="text" name="e_name3" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Wt,/W</div>
        <div class="col-md-2"><input type="text" name="e_wt3" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Grade</div>
        <div class="col-md-2"><input type="text" name="e_grade3" class="form-control" placeholder=""></div>
    </div>

    <div class="row mb-2">
        <div class="col-md-auto text-right">Excipients Name</div>
        <div class="col-md-2"><input type="text" name="e_name4" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Wt,/W</div>
        <div class="col-md-2"><input type="text" name="e_wt4" class="form-control" placeholder=""></div>

        <div class="col-md-auto text-right">Grade</div>
        <div class="col-md-2"><input type="text" name="e_grade4" class="form-control" placeholder=""></div>
    </div>


</div>

</div>

 

<div class="row mb-2">
<div class="col-lg-12 text-left">
                            <input type="hidden" name="project_code" id="project_code" value="<?php echo $Project->id;?>" />
                                <button type="submit" class="btn btn-primary" value="">Submit Now</button>
                            </div>
                            </div>
                            </div>

      	</div>
                  </div>
                  <!-- /.tab-pane -->
                  
				  
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

        <?php echo form_close(); ?>
</section>

<?php include viewPath('includes/footer'); ?>

<script>
$('#overlay').hide();
$(document).ready(function() {

function checkRecords() {
    $.ajax({
      url: '<?php echo url('projects/check_records') ?>',
      type: 'POST',
      dataType: 'json',
      beforeSend: function() {
        // Show loading message or lock the page
        $('#overlay').show();
       
        // Add code to lock the page or show a loading spinner
      },
      success: function(response) {
        
        if (response.recordsExist) {
            //alert(response);

            var counter = 5;
          $('#loading-message').text('Processing, please wait...');
          $('#processing-message').text('Existing JOB/DEM file generation in process, try again after some time... Redirecting in ' + counter + ' seconds');

          // Display the counter
          var redirectInterval = setInterval(function() {
            counter--;
            $('#processing-message').text('Existing JOB/DEM file generation in process, try again after some time... Redirecting in ' + counter + ' seconds');

            if (counter <= 0) {
              clearInterval(redirectInterval);
              // Perform the redirect
              window.location.href = '<?php echo url('projects') ?>';
            }
          }, 1000);
                

          // Call your controller function to handle the records
          // e.g., $.ajax({ url: 'your_controller_url', type: 'POST', ... });
        } else {
            $('#overlay').hide();
            $('#loading-message').text('No running jobs or DEM file generations..You can submit DEM file generation');
            setTimeout(function() {
          $('#loading-message').hide();
        }, 5000);
          // Continue loading the page
          // e.g., location.reload();
        }
      },
      error: function(xhr, status, error) {
        // Handle error conditions
        console.log(xhr.responseText);
      }
    });
  }

  // Call the AJAX function
 // checkRecords();
});

</script>


<script>
	$('#dataTable1').DataTable({
    "order": []
  });

  

</script>

<script>
    $(document).ready(function(){
        // Add text box
        $("#addButton").click(function(){
            var textBoxHtml = '<div class="row mb-2 textBoxWrapper" > <div class="col-md-auto text-right">Solvent Name</div> <div class="col-md-2"> <select   class="form-control" name="s_name[]" required> <option value="" selected disabled> Select Solvent</option> <?php foreach ($cdata as $row): ?> <option value="<?php echo $row->s_id ?>" data-id="id1"><?php echo $row->s_id ?> . <?php echo $row->solvent1_name ?></option> <?php endforeach ?> </select> </div> <div class="col-md-auto text-right">Solubility Value</div> <div class="col-md-2"><input type="text" name="s_value[]" class="form-control" placeholder="" required></div> <div class="col-md-auto text-right">MG/ML</div><div class="col-md-auto text-right">Temp</div> <div class="col-md-2"> <select   class="form-control" name="temp[]" required> <option value="10" > 10</option> <option value="25" > 25</option> <option value="50" > 50</option> </select> </div><button type="button" class="removeButton btn btn-danger">-</button>';
            $("#textBoxContainer").append(textBoxHtml);
        });

        // Remove text box
        $("#textBoxContainer").on("click", ".removeButton", function(){
            $(this).parent(".textBoxWrapper").remove();
        });
    });
</script>


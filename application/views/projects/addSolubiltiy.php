<?php

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); 

   
?>

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



<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Known Solubility</h1>
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


<div class="row">
          <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
             <!-- /.card-header -->
              <div class="card-body">


              <div class="container">

        
<?php echo form_open_multipart('projects/savesolubilitydata', [ 'class' => 'form-validate', 'autocomplete' => 'off' ]); ?>    

<div class="info-box shadow-lg">

<div class="info-box-content">
<span class="info-box-text"><h3> Solubility</h3></span>
<div class="row mb-2" >
         <?php if($solubiltyData =='') { ?>
        <div class="col-md-auto text-right">Solvent Name</div>
        <div class="col-md-2">
           <select   class="form-control" name="s_name[]" required>
            <option value="" selected disabled> Select Solvent</option>
            <?php foreach ($cdata as $row): ?>
            <option value="<?php echo $row->solvent1_name ?>" data-id="id1"><?php echo $row->s_id ?> . <?php echo $row->solvent1_name ?></option>
            <?php endforeach ?>
            
          </select>
       </div>

        <div class="col-md-auto text-right">Solubility Value</div>
        <div class="col-md-2"><input type="text" name="s_value[]" class="form-control" placeholder="" required></div>

        <div class="col-md-auto text-right">MG/ML</div>
        <div class="col-md-auto text-right">Temp</div>
       <div class="col-md-2">
           <select   class="form-control" name="temp[]" required>
            <option value="10" > 10</option>
            <option value="25" > 25</option>
            <option value="50" > 50</option>
          </select>
       </div>
         <?php } ?>
        <button type="button"  class="btn btn-info" id="addButton">Add New</button>

    </div>

    <?php if($solubiltyData !='') { ?>
     <?php foreach ($solubiltyData as $data): ?>
     <div class="textBoxContainerold">
    <div class="row mb-2 textBoxWrapper" > <div class="col-md-auto text-right">Solvent Name</div> <div class="col-md-2"> <select   class="form-control" name="s_name[]" required>  <?php foreach ($cdata as $row): ?> <option value="<?php echo $row->solvent1_name ?>" data-id="id1"  <?php echo ($data->s_name == $row->solvent1_name) ? 'selected' : '' ?>><?php echo $row->s_id ?> . <?php echo $row->solvent1_name ?></option> <?php endforeach ?> </select> </div> <div class="col-md-auto text-right">Solubility Value</div> <div class="col-md-2"><input type="text" name="s_value[]" value="<?php echo $data->s_value ?>" class="form-control" placeholder="" required></div> <div class="col-md-auto text-right">MG/ML</div><div class="col-md-auto text-right">Temp</div> <div class="col-md-2"> <select   class="form-control" name="temp[]" required> <option value="10"  <?php echo ($data->temp == '10') ? 'selected' : '' ?>> 10</option> <option value="25"  <?php echo ($data->temp == '25') ? 'selected' : '' ?> > 25</option> <option value="50" <?php echo ($data->temp == '50') ? 'selected' : '' ?> > 50</option> </select> </div><button type="button" class="removeButton btn btn-danger">-</button></div>
    <?php endforeach ?>
      <?php } ?>

    <div id="textBoxContainer"></div>

    <div class="row mb-2">
        <div class="col-lg-12 text-left">
        <input type="hidden" name="jobid" id="jobid" value="<?php echo $jobDetail[0]->id;?>" />
        <input type="hidden" name="type" id="type" value="1" />
        <button type="submit" class="btn btn-primary" value="">Submit Now</button>
        </div>
    </div>


</div>

</div>
  <?php echo form_close(); ?>
  <div class="info-box shadow-lg">
<div class="text-center"> OR</div>
</div>

<?php echo form_open_multipart('projects/savesolubilitydata', [ 'class' => 'form-validate', 'autocomplete' => 'off' 
, 'enctype'=>"multipart/form-data"]); ?>
<div class="info-box shadow-lg">

<div class="info-box-content">
<span class="info-box-text"><h3>Uplaod File</h3></span>

    <div class="row mb-2">
    <div class="col-md-6">
        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputGroupFile" aria-describedby="inputGroupFileAddon" name="file" required>
                <label class="custom-file-label" for="inputGroupFile">Choose file</label>

            </div>
            &nbsp;&nbsp;
            <a href="<?php echo site_url('projects/downloadSampleExcel'); ?>">Download Sample</a>

            
        </div>
    </div>
</div>
    <div class="row mb-2">
        <div class="col-lg-12 text-left">
        <input type="hidden" name="jobid" id="jobid" value="<?php echo $jobDetail[0]->id;?>" />
        <input type="hidden" name="type" id="type" value="2" />
        <button type="submit" class="btn btn-primary" value="">Upload</button>  

    </div>
    </div>

   


</div>

</div>
  <?php echo form_close(); ?>
 


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

      
</section>

<?php include viewPath('includes/footer'); ?>
<script>
    $(document).ready(function(){
        // Add text box
        $("#addButton").click(function(){
            var textBoxHtml = '<div class="row mb-2 textBoxWrapper" > <div class="col-md-auto text-right">Solvent Name</div> <div class="col-md-2"> <select   class="form-control" name="s_name[]" required> <option value="" selected disabled> Select Solvent</option> <?php foreach ($cdata as $row): ?> <option value="<?php echo $row->solvent1_name ?>" data-id="id1"><?php echo $row->s_id ?> . <?php echo $row->solvent1_name ?></option> <?php endforeach ?> </select> </div> <div class="col-md-auto text-right">Solubility Value</div> <div class="col-md-2"><input type="text" name="s_value[]" class="form-control" placeholder="" required></div> <div class="col-md-auto text-right">MG/ML</div> <div class="col-md-auto text-right">Temp</div> <div class="col-md-2"> <select   class="form-control" name="temp[]" required> <option value="10" > 10</option> <option value="25" > 25</option> <option value="50" > 50</option> </select> </div><button type="button" class="removeButton btn btn-danger">-</button>';
            $("#textBoxContainer").append(textBoxHtml);
        });

        // Remove text box
        $("#textBoxContainer").on("click", ".removeButton", function(){
            $(this).parent(".textBoxWrapper").remove();
        });
        $(".textBoxContainerold").on("click", ".removeButton", function(){
            $(this).parent(".textBoxWrapper").remove();
        });

        
    });
</script>




<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<style>
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
<?php echo form_open_multipart('projects/msave', [ 'class' => 'form-validate', 'autocomplete' => 'off' ]); ?>
<div class="row">
          <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
              <div class="card-header d-flex p-0">
                 <button class="btn btn-success" onclick="history.back()">Go Back</button>

              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
				  <div class="row">


<div class="row layout-top-spacing" id="cancel-row">


                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="shadow-lg rounded" style="background-color:#F1DEC9">
                        <div class="widget-content widget-content-area br-6 p-3">
                            <h4 class="mb-1">(<?php echo $Project->project_name;?>) Submission</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label for="exampleFormControlSelect1"><h4 class="mb-1">Structure</h4></label>
                                        <select class="form-control" name="structure">
                                            <option hidden="">Select Structure</option>
                                            <option>Crystal</option>
                                            <option>SDF</option>
                                            <option>Mol</option>
                                            <option>Smile</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label for="exampleFormControlSelect1"><h4 class="mb-1">INP File Name</h4></label>
                                        <input type="text" name="mname" class="form-control" placeholder="">
                                    </div></div>
                                    <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label for="exampleFormControlSelect1"><h4 class="mb-1">Value</h4></label>
                                        <input type="text" name="mvalue" class="form-control" placeholder="">
                                    </div>
                                    </div></div>
                                    <div class="form-group mb-4">
                                        <label for="exampleFormControlSelect1"><h5 class="mb-2">Smiles (simplified molecular-input line-entry system)</h5></label>
                                        <textarea id="Smile" name="Smile" rows="4" cols="50"></textarea>
                                        
                                    </div></div>
                                <div class="col-lg-6">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlInput1">File</label>
                                        <input type="file" name="sfile" class="form-control" placeholder="">
                                        <hr>
                                        <input type="checkbox" id="ap1" name="ap1" value="API">
<label for="vehicle1"> API</label><br>
<input type="checkbox" id="impurity" name="impurity" value="IMPURITY">
<label for="vehicle2"> IMPURITY</label><br>
<input type="checkbox" id="reactant" name="reactant" value="REACTANT">
<label for="vehicle3"> REACTANT</label><br>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
</div>
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="shadow-lg rounded" style="background-color:#C8B6A6">
                        <div class="widget-content widget-content-area br-6 p-3">
                            <h4>Solid State properties</h4>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlInput1">Hfus</label>
                                        <input type="text" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlInput1">MP</label>
                                        <input type="text" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlInput1">Known Solubility</label>
                                        <input type="text" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>

                        </div>
</div> <hr>
                    </div>
                   
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="shadow-lg rounded" style="background-color:#F1DEC9">
                        <div class="widget-content widget-content-area br-6 p-3">
                            <div class="row">
                                <h4>Solubility</h4>&nbsp;&nbsp;&nbsp;&nbsp;
                                <span style="font-size:20px;font-weight:900;color:green">+</span>
                            </div>

    <input type="button" class="add-row" value="Add Row">
  
    <table class="table"> 
        <thead>
            
        </thead>
        <tbody>
            <tr><td>  </td>
                <td>    <label for="exampleFormControlInput1">Solvent Name</label>
                                        <input type="text" class="form-control" placeholder=""></td>
                <td><label for="exampleFormControlInput1">Solubility Value</label>
                                        <input type="text" class="form-control" placeholder=""></td>
                <td><label for="exampleFormControlInput1">Unit</label>
                                        <input type="text" class="form-control" placeholder=""></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="delete-row">Delete Row</button>

                            <div class="row">
                               
                        </div>
</div>
                    </div>
                    <hr>
<div class="shadow-lg rounded" style="background-color:#C8B6A6">
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    
                        <div class="widget-content widget-content-area br-6 p-3">
                            <div class="row">
                                <h4>Excipients</h4>&nbsp;&nbsp;&nbsp;&nbsp;
                                <span style="font-size:20px;font-weight:900;color:green">+</span>
                            </div>

<input type="button" class="add-row1" value="Add Row">
  
    <table class="table1">
        <thead>
            
        </thead>
        <tbody>
            <tr><td>  </td>
                <td>    <label for="exampleFormControlInput1"> Excipients Name </label>
                                        <input type="text" class="form-control" placeholder=""></td>
                <td><label for="exampleFormControlInput1"> Wt,/W </label>
                                        <input type="text" class="form-control" placeholder=""></td>
                <td><label for="exampleFormControlInput1"> Grade </label>
                                        <input type="text" class="form-control" placeholder=""></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="delete-row1">Delete Row</button>

                           
                        </div>
                       
                        </div>     </div>  
                        <hr>
                        <div class="shadow-lg rounded" style="background-color:#A4907C"> 
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    
                        <div class="widget-content widget-content-area br-6 p-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlSelect1">List of Solvents</label>
                                        <select class="form-control" id="exampleFormControlSelect1">
                                            <option hidden="">Select Solvents</option>
                                            <option>Pure_68</option>
                                            <option>Binary _1085</option>
                                            <option>Tertiary-16400</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group  mb-4">
                                        <label for="exampleFormControlInput1">Input Temparature</label>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                                                    <label class="custom-control-label" for="customCheck1">10</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="customCheck2">
                                                    <label class="custom-control-label" for="customCheck2">20</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="customCheck3">
                                                    <label class="custom-control-label" for="customCheck3">50</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary" value="">Submit Now</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                          
                        </div>
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
	$('#dataTable1').DataTable({
    "order": []
  });
</script>

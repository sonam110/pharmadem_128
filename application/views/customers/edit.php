<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php include viewPath('includes/header'); ?>

<!-- Content Header (Page header) -->

<!-- Content Header (Page header) -->
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo lang('customers') ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo lang('home') ?></a></li>
              <li class="breadcrumb-item"><a href="<?php echo url('/customers') ?>"><?php echo lang('users') ?></a></li>
              <li class="breadcrumb-item"><a href="<?php echo url('/customers/view/'.$User->id) ?>"><?php echo $Customer->id ?></a></li>
              <li class="breadcrumb-item active"><?php echo lang('customer') ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<!-- Main content -->

<!-- Main content -->
<section class="content">

<?php echo form_open_multipart('customers/update/'.$Customer->id, [ 'class' => 'form-validate', 'autocomplete' => 'off' ]); ?>

  <div class="row">
    <div class="col-sm-6">
      <!-- Default card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?php echo lang('customer_basic') ?></h3>
        </div>
        <div class="card-body">

          <div class="form-group">
            <label for="formClient-Name"><?php echo lang('user_name') ?></label>
            <input type="text" class="form-control" name="name" id="formClient-Name" required placeholder="<?php echo lang('user_enter_name') ?>" value="<?php echo $Customer->name ?>" autofocus />
          </div>


          <div class="form-group">
            <label for="formClient-Contact"><?php echo lang('user_contact') ?></label>
            <input type="text" class="form-control" name="phone" id="formClient-Contact" placeholder="<?php echo lang('user_enter_contact') ?>" value="<?php echo $Customer->phone ?>" />
          </div>

	<?php if (logged('id')!=$User->id): ?>
            
          <?php endif ?>
          <div class="form-group">
            <label for="formClient-Status"><?php echo lang('user_status') ?></label>
            <select name="status" id="formClient-Status" class="form-control" <?php echo logged('id')==$Customer->id ? 'disabled' : '' ?>>
              <?php $sel = $Customer->status==1 ? 'selected' : '' ?>
              <option value="1" <?php echo $sel ?>><?php echo lang('user_active') ?></option>
              <?php $sel = $Customer->status==0 ? 'selected' : '' ?>
              <option value="0" <?php echo $sel ?>><?php echo lang('user_inactive') ?></option>
            </select>
          </div>

        </div>
        <!-- /.card-body -->

      </div>
      <!-- /.card -->

      
      
    </div>
    <div class="col-sm-6">
      <!-- Default card -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?php echo lang('user_other_details') ?></h3>
        </div>
        <div class="card-body">

          <div class="form-group">
            <label for="formClient-Address"><?php echo lang('user_address') ?></label>
            <textarea type="text" class="form-control" name="address" id="formClient-Address" placeholder="<?php echo lang('user_enter_address') ?>" rows="3"><?php echo $Customer->address ?></textarea>

          </div>

           <div class="form-group">
            <label for="formClient-Image"><?php echo lang('user_image') ?></label>
            <input type="file" class="form-control" name="image" id="formClient-Image" placeholder="<?php echo lang('user_upload_image') ?>" accept="image/*" onchange="previewImage(this, '#imagePreview')">
          </div>
          <div class="form-group" id="imagePreview">
            <img src="<?php echo customerProfile($Customer->id) ?>" class="img-circle" alt="<?php echo lang('user_upload_image_preview') ?>" width="100" height="100">
          </div>
          

        </div>
        <!-- /.card-body -->

      </div>
      <!-- /.card -->
    
     

    </div>
  </div>

  <!-- Default card -->
  <div class="card">
    <div class="card-footer">
      <div class="row">
        <div class="col"><a href="<?php echo url('/users') ?>" onclick="return confirm('Are you sure you want to leave?')" class="btn btn-flat btn-danger"><?php echo lang('cancel') ?></a></div>
        <div class="col text-right"><button type="submit" class="btn btn-flat btn-primary"><?php echo lang('submit') ?></button></div>
      </div>
    </div>
    <!-- /.card-footer-->

  </div>
  <!-- /.card -->
<?php echo form_close(); ?>

</section>
<!-- /.content -->


<script>
  $(document).ready(function() {
    $('.form-validate').validate();

      //Initialize Select2 Elements
    $('.select2').select2()

  })

  function previewImage(input, previewDom) {

    if (input.files && input.files[0]) {

      $(previewDom).show();

      var reader = new FileReader();

      reader.onload = function(e) {
        $(previewDom).find('img').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }else{
      $(previewDom).hide();
    }

  }

</script>

<?php include viewPath('includes/footer'); ?>


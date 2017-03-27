<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
</style>
<div class="col-md-6 col-md-offset-3 clearfix margin-top">
	<?php if (isset($success) && $success>0 && $success == "1") {?>
		<div class="alert alert-success fade in alert-dismissable">DATA UPLOADED SUCCESSFULLY</div>
	<?php } ?>
	<?php  $att=array("name"=>'drawing_rights_csv_form','id'=>'drawing_rights_csv_form'); echo form_open_multipart('rtk_management/drawing_rights_csv',$att); ?>
		</select>
		<input type="file" name="file" id="file" required="required" class="form-control"><br>
		<button type="submit">Upload</button>
	<?php echo form_close(); ?>
</div>
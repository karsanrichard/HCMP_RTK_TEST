<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
</style>
<div class="col-md-6 col-md-offset-3 clearfix margin-top">
	<?php if (isset($success) && $success>0 && $success == "1") {?>
		<div class="alert alert-success fade in alert-dismissable">DATA UPLOADED SUCCESSFULLY</div>
	<?php } ?>
	<?php  $att=array("name"=>'allocation_csv_form','id'=>'allocation_csv_form'); echo form_open_multipart('rtk_management/allocation_csv',$att); ?>
		<select name="month" class="form-control">
			<option value="0">Select Month</option>
			<?php $months_count = count($months); for ($i=0; $i < $months_count; $i++) { ?>
				<option value="<?php echo $months[$i];?>"> <?php echo $months[$i]; ?></option>
			<?php }?>
		</select>
		<input type="file" name="file" id="file" required="required" class="form-control"><br>
		<button type="submit">Upload</button>
	<?php echo form_close(); ?>
</div>
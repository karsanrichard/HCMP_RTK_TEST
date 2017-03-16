<?php //echo "<pre>";print_r($allocation_list); ?>
<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
</style>
<div class="col-md-12 clearfix margin-top">
<table class="table table-bordered">
	<thead>
		<th>Allocation Month</th>
		<th>Subcounties Allocated</th>
		<th>Allocation Status</th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php foreach ($allocation_list as $key => $value) {?>
			<tr>
				<td><?php echo $value['month_name']; ?></td>
				<td><?php echo $value['allocated_districts'].'/'.$value['total_districts']; ?></td>
				<td><?php echo $value['allocation_status']; ?></td>
				<td>
					<a class="btn btn-primary" href="<?php echo base_url().'rtk_management/cmlt_allocation_list_by_month/'.$county_id.'/'.$value['month_name'].'/'.$value['month_year'] ?>">View allocation</a>
					<a class="btn btn-primary" href="<?php echo base_url().'rtk_management/download_allocation_list/scmlt/NULL/'.$district_id?>">Download list</a>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

</div>
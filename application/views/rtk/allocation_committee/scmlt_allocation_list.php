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
		<th>Sites Allocated</th>
		<th>Allocation Status</th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php foreach ($allocation_list as $key => $value) {?>
			<tr>
				<td><?php echo $value['month_name']; ?></td>
				<td><?php echo $value['allocated_facilities'].'/'.$value['total_facilities']; ?></td>
				<td><?php echo $value['allocation_status']; ?></td>
				<td>
				<?php if($value['allocation_status'] == 'Complete'): ?>
					<a class="btn btn-primary" href="#">View allocations</a>
					<a class="btn btn-primary" href="<?php echo base_url().'rtk_management/download_allocation_list/scmlt/NULL/'.$district_id?>">Download list</a>
					<a class="btn btn-primary" href="#">Continue allocation</a>
				<?php else: ?>
					<a class="btn btn-primary" href="#">Begin allocation</a>

				<?php endif; ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

</div>
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
					<a class="btn btn-primary" href="#">View allocations</a>
					<a class="btn btn-primary" href="#">Download list</a>
					<a class="btn btn-primary" href="#">Continue allocation</a>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

</div>
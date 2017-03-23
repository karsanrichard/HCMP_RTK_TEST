	<?php //echo "<pre>";print_r($allocation_list); ?>
<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
</style>
<div class="col-md-12 clearfix margin-top">
<table class="table table-bordered datatable" id="allocation_list_table">
	<thead>
		<th>Allocation Month</th>
		<!-- <th>Sites Allocated</th> -->
		<th>Allocation Status</th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php foreach ($allocation_list as $key => $value) {?>
			<tr>
				<td><?php echo $value['month_name']; ?></td>
				<!--  <td> -->
				<?php //echo $value['allocated_facilities'].'/'.$value['total_facilities']; ?>
				<!-- </td> -->
				<td><?php echo $value['allocation_status']; ?></td>
				<td>
				<?php if($value['allocation_status'] == 'Complete'): ?>
					<a class="btn btn-success col-md-4" href="<?php echo base_url().'rtk_management/edit_allocation_report_monthly/'.$district_id.'/'.$value['month_name'].'/'.$value['month_year'] ?>"><i class="glyphicon glyphicon-eye-open"></i> View/Edit</a>
					
					<!-- <a class="btn btn-primary" href="<?php //echo base_url().'rtk_management/download_allocation_list/scmlt/NULL/'.$district_id?>"> -->
					<!-- <i class="glyphicon glyphicon-download"></i> Download
					</a> -->
				<?php else: ?>
					<a class="btn btn-primary" href="#">Begin allocation</a>

				<?php endif; ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

</div>

<script>
	$('#allocation_list_table').dataTable({
     "sDom": "T lfrtip",
     "aaSorting": [],
     "bJQueryUI": false,
      "bPaginate": false,
      "oLanguage": {
        "sLengthMenu": "_MENU_ Records per page",
        "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
      },
      "oTableTools": {
      "aButtons": [      
      
      ],  
      "sSwfPath": "<?php echo base_url();?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
    }
  });
</script>
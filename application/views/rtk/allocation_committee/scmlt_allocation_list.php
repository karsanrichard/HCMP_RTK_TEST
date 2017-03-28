	<?php //echo "<pre>";print_r($allocation_list); ?>
<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
</style>
<div class="col-md-12 clearfix margin-top">
<table class="table table-bordered datatable">
	<thead>
		<th>Allocation Month</th>
		<!-- <th>Sites Allocated</th> -->
		<th>Allocation Status</th>
		<th>Approval Status</th>
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
				<?php if($value['approval_status'] == 'Approved'): ?>
					<p class="label label-success">Approved</p>
				<?php elseif($value['approval_status'] == 'Pending'): ?>					
					<p class="label label-warning">Pending</p>
				<?php elseif($value['approval_status'] == 'Rejected'): ?>
					<p class="label label-danger">Rejected</p>
				<?php else: ?>
					<p class="label label-info">Unable to retrieve status, contact system administrator</p>
				<?php endif; ?>					
				</td>
				<td>
				<?php if($value['allocation_status'] == 'Complete'): ?>
					<a class="btn btn-success" href="<?php echo base_url().'rtk_management/edit_allocation_report_monthly/'.$district_id.'/'.$value['month_name'].'/'.$value['month_year'] ?>"><i class="glyphicon glyphicon-eye-open"></i> View/Edit</a>
					<?php if($value['approval_status'] == 'Approved'): ?>
						<a class="btn btn-primary" href="<?php echo base_url().'rtk_management/download_allocation_list/scmlt/'.$county_id.'/'.$district_id.'/'.$value['month_name'].'/'.$value['month_year'] ?>"><i class="glyphicon glyphicon-download"></i> Download</a>
					<?php endif; ?>
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
$('.datatable').dataTable({
     "sDom": "T lfrtip",
     "aaSorting": [],
     "bJQueryUI": false,
      "bPaginate": true,
      "oLanguage": {
        "sLengthMenu": "_MENU_ Records per page",
        "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
      },
      "oTableTools": {
      "aButtons": [      
      "copy",
      "print",
      {
        "sExtends": "collection",
        "sButtonText": 'Save',
        "aButtons": ["csv", "xls", "pdf"]
      }
      ],  
      "sSwfPath": "<?php echo base_url();?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
    }
  });
</script>
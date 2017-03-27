<?php //echo "<pre>";print_r($allocation_list); ?>
<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
</style>
<div class="col-md-12 clearfix margin-top">
<table class="table table-bordered" id="allocation_list_table">
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
					<a class="btn btn-success" href="<?php echo base_url().'rtk_management/cmlt_allocation_list_by_month/'.$county_id.'/'.$value['month_name'].'/'.$value['month_year'] ?>"><i class="glyphicon glyphicon-eye-open"></i> View/Edit</a>
					<!-- <a class="btn btn-primary" href="<?php //echo base_url().'rtk_management/download_allocation_list/scmlt/NULL/'.$district_id?>">
					<!-- <i class="glyphicon glyphicon-download"></i> Download</a> -->
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
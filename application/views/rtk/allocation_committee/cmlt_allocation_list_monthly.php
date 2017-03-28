<?php //echo "<pre>";print_r($allocation_details); ?>
<style type="text/css">
	.margin-top{
		margin-top: 30px!important;
	}
	.red{
		color: red;
	}
	.margin-sm{
		margin:10px 0px!important;
    padding:  10px!important;
	}

  .margin-vert{
    margin:10px 0px!important;
  }

	.no-margin{
		margin:0!important;
	}
	.no-padding{
		padding:0!important;
	}
.dash{    
    /*padding: 15px;*/
    border: 1px #ECE8E8 solid;
    border-bottom: 8px solid #428bca;
    border-radius: 0px 6px 6px 10px;
    /*min-width: 20%;*/
    /*width: 30%;*/
    height: auto;
    /*margin-top: 20px;*/
    color: #428bca;
}
.dash a{
  text-decoration: none;
}
.details{
  font-size: 25px;  
  height: 10%;
  border-bottom: 1px ridge #ccc;

}
.facils{
  height: 10%;
  padding-top: 2px;
  font-size: 15px;
}
.dash:hover{
  background: #FCFAFA;
  border: 1px #EBD3D3 solid;
  color: #900000;
  border-bottom: 8px solid #003300;

}
.extra>span,.extra>span>a:hover{
  font-size: 30px;text-shadow: 0px 0px #009900;
  text-decoration: none;
}
.progress{
  height: 8px;
}
</style>

<div class="title col-md-12">
	<center>
	<h4>Allocation Month: <?php echo $allocation_date; ?></h4>
	</center>
</div>

<div class="col-md-6">
  <table class="table table-bordered table-condensed">
  <tbody>
    <tr>
      <td><strong>Screening Total</strong></td>
      <td><?php echo $screening_total; ?></td>
    </tr>
    <tr>
      <td><strong>Screening Used</strong></td>
      <td><?php echo $screening_used; ?></td>
    </tr>
    <tr>
      <td><strong>Screening Available</strong></td>
      <td><?php echo $screening_total-$screening_used; ?></td>
    </tr>
  </tbody>
</table>
</div>

<div class="col-md-6">
  <table class="table table-bordered table-condensed">
  <tbody>
    <tr>
      <td><strong>Confirmatory Total</strong></td>
      <td><?php echo $confirmatory_total; ?></td>
    </tr>
    <tr>
      <td><strong>Confirmatory Used</strong></td>
      <td><?php echo $confirmatory_used; ?></td>
    </tr>
    <tr>
      <td><strong>Confirmatory Available</strong></td>
      <td><?php echo $confirmatory_total-$confirmatory_used; ?></td>
    </tr>
  </tbody>
</table>
</div>

</div>
<div class="col-md-12 clearfix ">
<?php foreach($allocation_details as $key => $value) : ?>
	<div id="rtk" class="dash col-md-4 margin-sm">
      <div class="details"><?php echo $value['district_name'];?> Sub - County</div><br/>
      <?php if($value['status'] == "Unallocated"): ?>
      <div class="col-md-12 no-padding facils red"> 
      <strong><?php echo $value['status']; ?></strong> <i class="glyphicon glyphicon-warning-sign"></i>
      <div class="margin-vert">
        <?php echo $value['approval_message']; ?>
      </div>
      </div>
      <div class="col-md-12 no-padding no-margin">
        <!-- <a class="btn btn-primary" href="<?php echo base_url().'rtk_management/allocate_from_list/'.$value['district_id'].'/'.$selected_month.'/'.$selected_year; ?>">Begin Allocation</a> -->
        <!-- <a class="btn btn-primary" href="<?php echo base_url().'rtk_management/district_allocation_table/'.$value['district_id'].'/'.$selected_month.'/'.$selected_year; ?>">Begin Allocation</a> -->
      	<a class="btn btn-primary" href="#" disabled="true">Pending Allocation</a>
      </div> 
      <?php else: ?>
      <div class="col-md-12 no-padding facils"> 
      <strong><?php echo $value['status']; ?></strong> <i class="glyphicon glyphicon-ok"></i>
      <div class="margin-vert">
        <?php echo $value['approval_message']; ?>
      </div>
      </div> 
      <div class="col-md-12 no-padding no-margin">
      	<a class="btn btn-success" href="<?php echo base_url().'rtk_management/view_allocation_report_county/'.$value['district_id'].'/'.$selected_month.'/'.$selected_year; ?>">View/Verify Allocation</a>
      	<!-- <a class="btn btn-primary" href="#">Download Allocation</a> -->
      </div>
      <?php endif; ?>       
    </a>
  </div>
<?php endforeach; ?>
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

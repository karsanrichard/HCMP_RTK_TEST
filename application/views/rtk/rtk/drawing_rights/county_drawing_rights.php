<?php //echo "<pre>";print_r($drawing_rights);exit; ?>
<style type="text/css">
  .margin-top{
    margin-top: 30px!important;
  }
</style>
<div class="col-md-12 clearfix margin-top">
<table class="table table-bordered table-condensed table-hover table-responsive" id="datatable">
<!-- <table class="table table-bordered" id=""> -->
  <thead>
    <tr>
    <th rowspan="2">Year</th>
    <th rowspan="2">Duration</th>
    <th colspan="3">Screening</th>
    <th colspan="3">Confirmatory</th>
    <th rowspan="2">Actions</th>
    </tr>
    <tr>
    <th>Total</th>
    <th>Allocated</th>
    <th>Balance</th>

    <th>Total</th>
    <th>Allocated</th>
    <th>Balance</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($drawing_rights as $key => $value) {?>
      <tr>
        <td><?php echo $value['year']; ?></td>
        <td><?php echo $value['duration']; ?></td>
        <td><?php echo $value['screening_total']; ?></td>
        <td><?php echo $value['screening_allocated']; ?></td>
        <td><?php echo $value['screening_balance']; ?></td>
        <td><?php echo $value['confirmatory_total']; ?></td>
        <td><?php echo $value['confirmatory_allocated']; ?></td>
        <td><?php echo $value['confirmatory_balance']; ?></td>
        <td>
          <a class="btn btn-success" href="<?php echo base_url().'rtk_management/county_drawing_rights_details/'.$county_id ?>"><i class="glyphicon glyphicon-eye-open"></i> View Distribution</a>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>

</div>
<script>
$('#datatable').dataTable({
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
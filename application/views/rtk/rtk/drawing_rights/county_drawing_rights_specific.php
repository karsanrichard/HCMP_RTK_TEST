<?php //echo "<pre>";print_r($drawing_rights);exit; ?>
<style type="text/css">
  .margin-top{
    margin-top: 30px!important;
  }
</style>
<div class="col-md-12 clearfix margin-top">
<div class="col-md-12">
<center>
  <h3>Drawing Rights</h3>
</center>
<div class="col-md-6">
<table class="table table-bordered table-condensed">
  <tbody>
    <tr>
      <td><strong>Screening Total</strong></td>
      <td><?php echo $county_drawing_data['screening_total']; ?></td>
    </tr>
    <tr>
      <td><strong>Screening Used</strong></td>
      <td><?php echo $county_drawing_data['screening_used']; ?></td>
    </tr>
    <tr>
      <td><strong>Screening Available</strong></td>
      <td><?php echo $county_drawing_data['screening_total']-$county_drawing_data['screening_used']; ?></td>
    </tr>
  </tbody>
</table>
</div>

<div class="col-md-6">
<table class="table table-bordered table-condensed">
  <tbody>
    <tr>
      <td><strong>Confirmatory Total</strong></td>
      <td><?php echo $county_drawing_data['confirmatory_total']; ?></td>
    </tr>
    <tr>
      <td><strong>Confirmatory Used</strong></td>
      <td><?php echo $county_drawing_data['confirmatory_used']; ?></td>
    </tr>
    <tr>
      <td><strong>Confirmatory Available</strong></td>
      <td><?php echo $county_drawing_data['confirmatory_total']-$county_drawing_data['confirmatory_used']; ?></td>
    </tr>
  </tbody>
</table>
</div>
  
  
</div>
<?php if ($distribution_status > 0): ?>
<?php //echo "DATA PRESENT"; ?>
<table class="table table-bordered table-condensed table-hover table-responsive" id="datatable">
  <thead>
    <tr>
    <th rowspan="2">Subcounty</th>
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
        <td><?php echo $value['subcounty']; ?></td>
        <td><?php echo $value['screening_total']; ?></td>
        <td><?php echo $value['screening_allocated']; ?></td>
        <td><?php echo $value['screening_balance']; ?></td>
        <td><?php echo $value['confirmatory_total']; ?></td>
        <td><?php echo $value['confirmatory_allocated']; ?></td>
        <td><?php echo $value['confirmatory_balance']; ?></td>
        <td>
          <a class="btn btn-success" href="<?php echo base_url().'rtk_management/county_drawing_rights_details/'.$county_id ?>"><i class="glyphicon glyphicon-eye-open"></i> Edit Totals</a>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>
<?php else: ?>
  <?php  $att=array("name"=>'drawing_rights_form','id'=>'drawing_rights_form'); echo form_open('rtk_management/save_subcounty_drawing_rights',$att); ?>
  <table class="table table-bordered table-condensed table-hover table-responsive dataTable" id="datatable">
    <thead>
      <th>Subcounty</th>
      <th>Screening amount</th>
      <th>Confirmatory amount</th>
    </thead>
    <tbody>
    <?php for ($i=0; $i < $subcounty_count; $i++) { ?>
      <tr>
        <td>
          <input type="hidden" name="<?php echo "subcounty_id[$i]"; ?>" value="<?php echo $subcounties[$i]['id']; ?>">
          <input type="hidden" name="<?php echo "county_id[$i]"; ?>" value="<?php echo $subcounties[$i]['county']; ?>">
          <?php echo $subcounties[$i]['district']; ?>
        </td>
        <td>
          <input class="form-control" type="number" name="<?php echo "screening_allocated[$i]"; ?>" placeholder="Screening amount">
        </td>
        <td>
          <input class="form-control" type="number" name="<?php echo "confirmatory_allocated[$i]"; ?>" placeholder="Confirmatory amount">
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>

  <input type="submit" class="btn btn-primary btn-large" value="Save Drawing Rights">
  <?php echo form_close(); ?>
<?php endif ?>
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
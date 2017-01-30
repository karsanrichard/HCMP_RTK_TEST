<style type="text/css">
.dash{    
    padding: 15px;
    border: 1px #ECE8E8 solid;
    border-bottom: 8px solid #428bca;
    border-radius: 0px 6px 6px 10px;
    min-width: 20%;
    width: 30%;
    height: auto;
    margin-top: 20px;
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

<?php include('rca_sidabar.php');?>

<div id="container" style="min-width: 310px; height: auto; margin-left: 20%  ">        

<div style="font-size: 20px; margin-top:2%;float-left:4%;">Drawing Rights per Year<br/> Screening: <?php echo $county_details[0]['screening_drawing_rights']; ?> Balance: <?php echo $county_details[0]['screening_current_amount']; ?><br>
Confirmatory: <?php echo $county_details[0]['confimatory_drawing_rights']; ?> Balance: <?php echo $county_details[0]['confirmatory_current_amount']; ?>

<button id ="edit_dr" class="btn btn-primary" style="float:right; margin-left:3%">Edit Drawing Rights</button>
<button id ="open_report" class="btn btn-primary" style="float:right; ">View Allocated Facilities</button>
</div>
<div class="row" style="width:100%; margin-top:2%;margin-left:4%;">
<?php

// echo "<pre>";
//         print_r($district_details);
//         print_r($district_array);die;

  foreach ($district_details as $key => $value) {
    $district_id = $value['id'];
    $district_name = $value['district'];
 
    if (in_array($district_id, $district_array)) {

      $done = '<p style = "color:red;"> Do Allocation</p>';
      $edit_link = '';
      $link = '<a href='. base_url().'rtk_management/district_allocation_table/'.$district_id.'>';

    }else{
      $done = '<p style = "color:green;"> Allocated</p>';
      $link = '<a href="">'; 
      $edit_link = '<a href = '. base_url().'rtk_management/edit_county_allocation_report/'.$district_id.'>(Edit)</a>';

    }

?>
  <div id="rtk" class="dash span3" style="width:35%;float:left; border-spacing: 10px;"">
    <?php echo $link;?>
      <div class="details"><?php echo $district_name;?> Sub - County</div><br/>
      <div class="facils"> <?php echo $done.' '.$edit_link; ?></div>      
      <div class="facils">Total Facilities Reported in <?php echo $facilities_data[$district_id]['reporting_month']; ?>: <?php echo $facilities_data[$district_id]['reported_facilities']; ?>/<?php echo $facilities_data[$district_id]['total_facilities']; ?></div>      
    </a>
  </div>
  <?php
   }
?>
  
<br/>

  

</div>
    
<script>
$(document).ready(function() {
 
  // $('#pending_facilities').dataTable({
  //    "sDom": "T lfrtip",
  //    "aaSorting": [],
  //    "bJQueryUI": false,
  //     "bPaginate": true,
  //     "oLanguage": {
  //       "sLengthMenu": "_MENU_ Records per page",
  //       "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
  //     },
  //     "oTableTools": {
  //     "aButtons": [      
  //     "copy",
  //     "print",
  //     {
  //       "sExtends": "collection",
  //       "sButtonText": 'Save',
  //       "aButtons": ["csv", "xls", "pdf"]
  //     }
  //     ],  
  //     "sSwfPath": "<?php echo base_url();?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
  //   }
  // });
  // $("#pending_facilities").tablecloth({theme: "paper",         
  //   bordered: true,
  //   condensed: true,
  //   striped: true,
  //   sortable: true,
  //   clean: true,
  //   cleanElements: "th td",
  //   customClass: "data-table"
  // });

  
  $('#open_report').click(function(){
    window.location.href = "<?php echo base_url().'rtk_management/cmlt_allocation_report'?>";
  });

  $('#edit_dr').click(function(){
    window.location.href = "<?php echo base_url().'rtk_management/edit_drawing_rights'?>";
  });
});
</script>

<!--Datatables==========================  --> 
<script src="http://cdn.datatables.net/1.10.0/js/jquery.dataTables.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/datatable/jquery.dataTables.min.js" type="text/javascript"></script>  
<script src="<?php echo base_url(); ?>assets/datatable/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/datatable/TableTools.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/datatable/ZeroClipboard.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/datatable/dataTables.bootstrapPagination.js" type="text/javascript"></script>
<!-- validation ===================== -->
<script src="<?php echo base_url(); ?>assets/scripts/jquery.validate.min.js" type="text/javascript"></script>



<link href="<?php echo base_url(); ?>assets/boot-strap3/css/bootstrap-responsive.css" type="text/css" rel="stylesheet"/>
<link href="<?php echo base_url(); ?>assets/datatable/TableTools.css" type="text/css" rel="stylesheet"/>
<link href="<?php echo base_url(); ?>assets/datatable/dataTables.bootstrap.css" type="text/css" rel="stylesheet"/>

<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/tablecloth/assets/css/tablecloth.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>assets/datatable/jquery.dataTables.js"></script>
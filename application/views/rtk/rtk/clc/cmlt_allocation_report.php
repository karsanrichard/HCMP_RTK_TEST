<style>

.dataTables_filter{
  float: right;
}
#pending_facilities_length{
  float: left;  
}
table{
  font-size: 13px;
}


#pending_facilities_paginate{
  font-size: 13px;
  float: right;
  padding:4px;
}
#pending_facilities_info{
  font-size: 15px; 
  float: left;
}

#pending_facilities_filter{
  float: right;
}
.nav li{
  float: left;
  margin-left: 20px;
}
 .DTTT_container{margin-top: 1em;}
    #banner_text{width: auto;}
    .divide{height: 2em;}

input{
    border: 1px solid #C0C0C0 ; 
    border-radius: 15px;
    background: #F0F0F0 ;
    margin: 0 0 10px 0;
    width: auto;
}

</style>


<div class="main-container" style="width: 100%;float: right;">

<div class="span12" style="align:center; font-size:16px;  width:100% margin-left: 10%"> 
<b>Available amount of Kits in <?php echo $county_data[0]['name'];?>:</b> 
Screening: <?php echo $county_data[0]['screening_current_amount']?>, Confirmatory: <?php echo $county_data[0]['confirmatory_current_amount']?>. <br/><br/>
<button id ="download_report" class="btn btn-primary" style="float:right;">Download Allocation Report</button>
<!-- </div>  -->
<!-- <br/>
<br/>
<br/>
<br/> -->
<!-- <div class="span3" style="float:left">
ul class="nav nav-tabs nav-stacked" style="width:100%;"
<ul class="nav nav-tabs nav-stacked " style="width:100%;">
</ul>
</div> -->
</div>
<br/>

       
  <table id="allocation_table" class="data-table"> 
    <thead>
    <tr>        
       <tr>        
      <th align="">County</th>
      <th align="">Sub-County</th>
      <th align="">MFL</th>
      <th align="">Facility Name</th>     
      <th align="center" colspan="6">Screening</th>      
      <th align="center" colspan="6">Confirmatory</th> 
    </tr>    
    <tr>
          
      <th align="center"></th>
      <th align="center"></th>
      <th align="center"></th>
      <th align="center"></th>
 
      <th align="center">Ending Balance</th>      
      <th align="center">AMC</th>
      <th align="center">Months of Stock</th>
      <th align="center">Quantity Allocated by County</th>
      <th align="center">Feedback/Remarks</th>
      <th align="center">Decision (Supply, Monitor, Distribute</th>

      <th align="center">Ending Balance</th>      
      <th align="center">AMC</th>
      <th align="center">Months of Stock</th>
      <th align="center">Quantity Allocated by County</th>
      <th align="center">Feedback/Remarks</th>
      <th align="center">Decision (Supply, Monitor, Distribute</th>     
      
    </tr>
      
    </thead>

    <tbody>
      <?php
      if(count($result)>0){
      
       foreach ($result as $value) {
      
        if ($value['decision_s']== "REDISTRIBUTE") {
          $style_s = "style='background-color:#ff7f50'"; //red
        }
        elseif ($value['decision_s']== "MONITOR" ){

          $style_s = "style='background-color:#ffdb58'";//yellow
        }
        elseif ($value['decision_s']== "RESUPPLY"){

          $style_s = "style='background-color:#5efb6e'";//green
        }

        if ($value['decision_c']== "REDISTRIBUTE") {
          $style_c = "style='background-color:#ff7f50'";//red
        }
        elseif ($value['decision_c']== "MONITOR"){

          $style_c = "style='background-color:#ffdb58'";//yellow
         
        }
        elseif ($value['decision_c']== "RESUPPLY"){

          $style_c = "style='background-color:#5efb6e'";//green
          
        }
               
        
        ?> 
        <tr> 
          <td align=""><?php echo $value['county_name'];?></td>
          <td align=""><?php echo $value['district_name'];?></td>              
          <td align=""><?php echo $value['facility_code'];?></td>
          <td align=""><?php echo $value['facility_name'];?></td>  

          <td align="center"><?php echo $value['ending_bal_s'];?></td>     
          <td align="center"><?php echo $value['amc_s'];?></td> 
          <td align="center"><?php echo $value['mmos_s'];?></td> 
          <td align="center"><?php echo $value['allocate_s'];?></td> 
          <td align="center"><?php echo $value['remark_s'];?></td> 
          <td align="center" <?php echo $style_s;?> > <?php echo $value['decision_s'];?></td> 

          <td align="center"><?php echo $value['ending_bal_c'];?></td>     
          <td align="center"><?php echo $value['amc_c'];?></td> 
          <td align="center"><?php echo $value['mmos_c'];?></td> 
          <td align="center"><?php echo $value['allocate_c'];?></td> 
          <td align="center"><?php echo $value['remark_c']; ?> </td> 
          <td align="center"<?php echo $style_c;?>><?php echo $value['decision_c'];?></td> 

                    
        </tr>
        <?php 
        $count++;

      }

      }else{ ?>
      <tr>There are No Facilities that have been Allocated</tr>
      <?php }
      ?>      

    </tbody>
  </table>

</div>
<br/>
<br/>
<br/>
    
<script>
$(document).ready(function() {
 
  $('#allocation_table').dataTable({
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
   
  $("#allocation_table").tablecloth({theme: "paper",         
    bordered: true,
    condensed: true,
    striped: true,
    sortable: true,
    clean: true,
    cleanElements: "th td",
    customClass: "data-table"
  });
  
$('#download_report').click(function(){
  var countyid = <?php echo $county_data[0]['id'];?>
   // var url = "<?php echo base_url() . 'rtk_management/get_remaining_districts/'; ?>";
    window.location.href = "<?php echo base_url().'rtk_management/download_allocation_county/'?>"+countyid;
    // alert(url);
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



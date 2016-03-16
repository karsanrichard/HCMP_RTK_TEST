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

<!-- <div class="span3" style="float:center; font-size:16px; color:green; width:100%"> <b>Available amount of Kits in <?php echo $county_name;?>:</b><br/>
Screening: <?php echo $drawing_rights[0]['screening']?>, Confirmatory: <?php echo $drawing_rights[0]['screening']?>, Tie Breaker: <?php echo $drawing_rights[0]['screening']?>.

</div> -->
<!-- <br/>
<br/>
<br/>
<br/>
<div class="span3" style="float:left">
ul class="nav nav-tabs nav-stacked" style="width:100%;"
<ul class="nav nav-tabs nav-stacked " style="width:100%;">
</ul>
</div> -->
<br/>         
  <table id="allocation_table" class="data-table"> 
    <thead>
    <tr>        
       <tr>        
      <th align="">County</th>
      <th align="">Sub-County</th>
      <th align="">MFL</th>
      <th align="">Facility Name</th>     
      <th align="center" colspan="7">Screening</th>      
      <th align="center" colspan="7">Confirmatory</th>      
      <th align="center" colspan="7">TieBreaker</th> 
      <!-- <th align="center" colspan="7">DBS Bundles</th>  -->
    </tr>    
    <tr>
          
      <th align="center"></th>
      <th align="center"></th>
      <th align="center"></th>
      <th align="center"></th>      
      <th align="center">Ending Balance</th>      
      <th align="center">AMC</th>
      <th align="center">Months of Stock</th>
      <th align="center">Quantity Requested for Allocation</th>
      <th align="center">Quantity Allocated by National</th>
      <th align="center">Feedback/Remarks</th>
      <th align="center">Decision (Supply, Monitor, Distribute)</th>

      <th align="center">Ending Balance</th>      
      <th align="center">AMC</th>
      <th align="center">Months of Stock</th>
      <th align="center">Quantity Requested for Allocation</th>
      <th align="center">Quantity Allocated by National</th>
      <th align="center">Feedback/Remarks</th>
      <th align="center">Decision (Supply, Monitor, Distribute)</th>

      <th align="center">Ending Balance</th>      
      <th align="center">AMC</th>
      <th align="center">Months of Stock</th>
      <th align="center">Quantity Requested for Allocation</th>  
      <th align="center">Quantity Allocated by National</th>
      <th align="center">Feedback/Remarks</th>
      <th align="center">Decision (Supply, Monitor, Distribute)</th>

      <!-- <th align="center">Ending Balance</th>      
      <th align="center">AMC</th> 
      <th align="center">Months of Stock</th>
      <th align="center">Quantity to Allocate (20 pack)</th>               
      <th align="center">Quantity Allocated by National</th>
      <th align="center">Feedback/Remarks</th>
      <th align="center">Decision (Supply, Monitor, Distribute)</th> -->
    </tr>
      
    </thead>

    <tbody>
      <?php
        // echo '<pre>';print_r($allocations);

      if(count($allocations)>0){
        $count = 0;
        foreach ($allocations as $value) {
        $amc_s = str_replace(',', '',$value['amc_s']);
        $amc_c = str_replace(',', '',$value['amc_c']);
        $amc_t = str_replace(',', '',$value['amc_t']);        
        $amc_d = str_replace(',', '',$value['amc_d']);        

        if($amc_s==''){
          $amc_s = 0;
        }

        if($amc_c==''){
          $amc_c = 0;
        }

        if($amc_t==''){
          $amc_t = 0;
        }
        if($amc_d==''){
          $amc_d = 0;
        }
        if ($amc_d <50) {
          $amc_d_20 = ceil($amc_d/20);
          $amc_d_50 = '-';
        }else{
          $amc_d_20 = '-';
          $amc_d_50 = ceil($amc_d/50);
        }
        $mmos_s = ceil(($amc_s * 4)/50);
        $mmos_c = ceil(($amc_c * 4)/30);
        $mmos_t = ceil(($amc_t * 4)/20);
      
        if ($mmos_s >6) {
          $style_s = "style='background-color:#ff7f50'"; //red
          $decision_s = "REDISTRIBUTE";
        }
        elseif ($mmos_s>=4 && $mmos_s<6){

          $style_s = "style='background-color:#ffdb58'";//yellow
          $decision_s = "MONITOR";
        }
        elseif ($mmos_s<4){

          $style_s = "style='background-color:#5efb6e'";//green
          $decision_s = "RESUPPLY";
        }

        if ($mmos_c >6) {
          $style_c = "style='background-color:#ff7f50'";//red
          $decision_c = "REDISTRIBUTE";
        }
        elseif ($mmos_c>=4 && $mmos_c<6){

          $style_c = "style='background-color:#ffdb58'";//yellow
          $decision_c = "MONITOR";
        }
        elseif ($mmos_c<4){

          $style_c = "style='background-color:#5efb6e'";//green
          $decision_c = "RESUPPLY";
        }

        if ($mmos_t >6) {
          $style_t = "style='background-color:#ff7f50'";//red
          $decision_t = "REDISTRIBUTE";
        }
        elseif ($mmos_t>=4 && $mmos_t<6){

          $style_t = "style='background-color:#ffdb58'";//yellow
          $decision_t = "MONITOR";
        }
        elseif ($mmos_t<4){

          $style_t = "style='background-color:#5efb6e'";//green
          $decision_t = "RESUPPLY";
        }
                
        ?> 
        <tr>         
        
          <td align=""><?php echo $value['county'];?></td>
          <td align=""><?php echo $value['district'];?></td>              
          <td align=""><?php echo $value['facility_code'];?></td>
          <td align=""><?php echo $value['facility_name'];?></td>  

          <td align="center"><?php echo $value['ending_bal_s'];?></td>     
          <td align="center"><?php echo $value['amc_s'];?></td>
          <td align="center"><?php echo $mmos_s;?></td> 
          <td align="center"><?php echo $value['allocate_s'];?></td>
          <td align="center"><input style="width:40px" class="screening_input" id="q_allocate_s<?php echo $count ?>" name="q_allocate_s[<?php echo $count ?>]" value = '<?php $value['allocate_s']?>'/></td> 
          <td align="center"><input style="width:40px" class="screening_input" id="feedback_s<?php echo $count ?>" name="feedback_s[<?php echo $count ?>]" /></td> 
          <td align="center" <?php echo $style_s;?> > <?php echo $decision_s;?></td> 
 

          <td align="center"><?php echo $value['ending_bal_c'];?></td>    
          <td align="center"><?php echo $value['amc_c'];?></td>
          <td align="center"><?php echo $mmos_c;?></td> 
          <td align="center"><?php echo $value['allocate_c'];?></td>
          <td align="center"><input style="width:40px" class="confirm_input" id="q_allocate_c<?php echo $count ?>"name="q_allocate_c[<?php echo $count ?>]" value = '<?php $value['allocate_c']?>'/></td> 
          <td align="center"><input style="width:40px" class="confirm_input" id="feedback_c<?php echo $count ?>"name="feedback_c[<?php echo $count ?>]" /></td> 
          <td align="center"<?php echo $style_c;?>><?php echo $decision_c;?></td> 

          <td align="center"><?php echo $value['ending_bal_t'];?></td>    
          <td align="center"><?php echo $value['amc_t'];?></td>
          <td align="center"><?php echo $mmos_t;?></td> 
          <td align="center"><?php echo $value['allocate_t'];?></td>          
          <td align="center"><input style="width:40px" class="tiebreaker_input" id="q_allocate_t<?php echo $count ?>"name="q_allocate_t[<?php echo $count ?>]" value = '<?php $value['allocate_t']?>'/></td> 
          <td align="center"><input style="width:40px" class="tiebreaker_input" id="feedback_t<?php echo $count ?>"name="feedback_t[<?php echo $count ?>]" /></td> 
          <td align="center" <?php echo $style_t;?>><?php echo $decision_t;?></td> 
          
          
         <!--  <td align="center"><?php echo $value['ending_bal_d'];?></td>      
          <td align="center"><?php echo $value['amc_d'];?></td>
          <td align="center"><?php echo $mmos_d;?></td> 
          <td align="center"><?php echo $value['allocate_d'];?></td>  -->          
          
        </tr>
        <?php 
        $count++;

      }

      }else{ ?>
      <tr>There are no Facilities to Allocate</tr>
      <?php }
      ?>      

    </tbody>
  </table>
  </form>
<?php form_close(); ?>

</div>
<br/>
<br/>
<br/>
    <div id="message" type="text" style="margin-left: 0%; width:200px;color:blue;font-size:120%">Saving</div>
        <input class="btn btn-primary" type="submit"   id="confirm"  value="Save" style="margin-left: 0%; width:100px" >
    

    <div class="modal fade" id="next_modal">
  <div class="modal-dialog">
    <div class="modal-content">      
      <div class="modal-body">
        <span id="report_status"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="go_home">
            Back to Home
        </button>
        <button type="button" class="btn btn-default" <a id="next_report_btn">
            Next Report
        </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
   
  $("#allocation_table").tablecloth({theme: "paper",         
    bordered: true,
    condensed: true,
    striped: true,
    sortable: true,
    clean: true,
    cleanElements: "th td",
    customClass: "data-table"
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


 
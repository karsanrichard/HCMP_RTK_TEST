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

.margin-vert{
  margin:10px 0!important;
}

.margin-hor{
  margin:0px 5px!important;
}

</style>


<div class="main-container" style="width: 100%;float: right;">

<div class="span12" style="align:center; font-size:16px;  width:100% margin-left: 10%"> 
<?php if ($status == "Pending") {
  $status_message = '<span class="label label-warning"> Pending </span>';
  $button_business = '<input class="btn btn-primary" type="submit"   id="confirm_new"  value="Save Allocation" style="margin-left: 0%; width:300px" />';
}elseif ($status == "Rejected") {
  $button_business = '<input class="btn btn-primary" type="submit"   id="confirm_new"  value="Save Allocation" style="margin-left: 0%; width:300px" />';
  $status_message = '<span class="label label-danger"> Rejected </span>';
}elseif ($status == "Approved") {
  $status_message = '<span class="label label-success"> Approved </span>';
  $button_business = '<input class="btn btn-success" type="" disabled="true" id="confirm_approved"  value="Allocation can not be edited" style="margin-left: 0%; width:300px" />';
}else{
  $status_message = '<span class="label label-info"> Unreachable. Kindly contact system administrator. </span>';
  $button_business = '<input class="btn btn-success" type="" disabled="true" id="confirm_new"  value="Allocation can not be edited" style="margin-left: 0%; width:300px" />';
}
 ?>
<center><p>Allocation approval status: <?php echo $status_message; ?></p></center>

<b>Available amount of Kits in <?php echo $county_name;?>:</b><br/>

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

Guide:<b class="label label-success margin-hor"> Green :- Redistribute </b> <b class="label label-warning margin-hor"> Yellow :- Monitor &nbsp;</b><b class="label label-danger margin-hor"> Red :- Ressuply</b>

<div>
  <?php if ($success_status == '1'): ?>
    <div class="col-md-6 col-md-offset-3 alert alert-success">
      Allocation has been saved <strong>Successfully </strong>
    </div>

  <?php endif; ?>
</div>
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
<?php
    $screening_current_amount = str_replace(',', '',$screening_current_amount);
    $confirmatory_current_amount = str_replace(',', '',$confirmatory_current_amount);
    $tiebreaker_current_amount = str_replace(',', '',$tiebreaker_current_amount);

    $attributes = array('name' => 'myform', 'id' => 'myform');
    echo form_open('rtk_management/edit_district_allocation_report_monthly/'.$district_id.'/'.$selected_month.'/'.$selected_year, $attributes);

?>
  <input type="hidden" id="countyid" name="county_id" value="<?php echo $county_id;?>"> 
  <input type="hidden" name="district_id" id = "district_id" value="<?php echo $district_id;?>"> 
  <input type="hidden" id="screening_current_amount" value="<?php echo $screening_current_amount;?>"> 
  <input type="hidden" id="confirmatory_current_amount" value="<?php echo $confirmatory_current_amount;?>"> 
  <input type="hidden" id="tiebreaker_current_amount" value="<?php echo $tiebreaker_current_amount;?>"> 
          
  <table id="allocation_table" class="data-table table table-bordered"> 
    <thead>
    <tr>        
       <tr>        
      <th align="">County</th>
      <th align="">Sub-County</th>
      <th align="">MFL</th>
      <th align="">Facility Name</th>     
      <th align="center" colspan="7">Screening</th>      
      <th align="center" colspan="7">Confirmatory</th> 
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
      if(count($allocation_details)>0){
        $count = 0;
       foreach ($allocation_details as $value) {
        //$zone = str_replace(' ', '-',$value['zone']);
        // echo "<pre>";print_r($value);
        $facility_code = $value['facility_code'];

        // echo $facility_code;

        $ending_bal_s_latest =ceil($final_dets[$facility_code]['end_bal'][0]['closing_stock']); 
        $ending_bal_c_latest =ceil($final_dets[$facility_code]['end_bal'][1]['closing_stock']); 
        $ending_bal_t_latest =ceil($final_dets[$facility_code]['end_bal'][2]['closing_stock']);
        $ending_bal_d_latest =ceil($final_dets[$facility_code]['end_bal'][3]['closing_stock']);
        

        $days_out_of_stock_s =ceil($final_dets[$facility_code]['end_bal'][0]['days_out_of_stock']); 
        $days_out_of_stock_c =ceil($final_dets[$facility_code]['end_bal'][1]['days_out_of_stock']); 
        $days_out_of_stock_t =ceil($final_dets[$facility_code]['end_bal'][2]['days_out_of_stock']);
        $days_out_of_stock_d =ceil($final_dets[$facility_code]['end_bal'][3]['days_out_of_stock']);

        $q_requested_s =ceil($final_dets[$facility_code]['end_bal'][0]['q_requested']); 
        $q_requested_c =ceil($final_dets[$facility_code]['end_bal'][1]['q_requested']); 
        $q_requested_t =ceil($final_dets[$facility_code]['end_bal'][2]['q_requested']);
        $q_requested_d =ceil($final_dets[$facility_code]['end_bal'][3]['q_requested']);

        // $amc_s = str_replace(',', '',$my_amcs[$count][0]);
        // $amc_c = str_replace(',', '',$my_amcs[$count][1]);
        // $amc_t = str_replace(',', '',$my_amcs[$count][2]);
        // $amc_d = str_replace(',', '',$my_amcs[$count][3]);

        $amc_s = str_replace(',', '',$final_dets[$facility_code]['amcs'][0]['amc']);
        $amc_c = str_replace(',', '',$final_dets[$facility_code]['amcs'][1]['amc']);
        $amc_t = str_replace(',', '',$final_dets[$facility_code]['amcs'][2]['amc']);        
        $amc_d = str_replace(',', '',$final_dets[$facility_code]['amcs'][3]['amc']);


        $amc_s = round($final_dets[$facility_code]['amc'][0]['amc'] + 0);
        $amc_c = round($final_dets[$facility_code]['amc'][1]['amc'] + 0);
        $amc_t = round($final_dets[$facility_code]['amc'][2]['amc'] + 0);        
        $amc_d = round($final_dets[$facility_code]['amc'][3]['amc'] + 0);  
        
        $ending_bal_s = str_replace(',', '',$final_dets[$facility_code]['amc'][0]['closing_stock']);
        $ending_bal_c = str_replace(',', '',$final_dets[$facility_code]['amc'][1]['closing_stock']);
        $ending_bal_t = str_replace(',', '',$final_dets[$facility_code]['amc'][2]['closing_stock']);  
        $ending_bal_d = str_replace(',', '',$final_dets[$facility_code]['amc'][3]['closing_stock']);
        // echo "<pre>First ";print_r($amc_s);exit;

        // echo "<pre>";print_r($amc[$count][0]);
        // exit;      

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

        if($ending_bal_s==''){
          $ending_bal_s = 0;
        }
        if($ending_bal_c==''){
          $ending_bal_c = 0;
        }
        if($ending_bal_t==''){
          $ending_bal_t = 0;
        }
        if($ending_bal_d==''){
          $ending_bal_d = 0;
        }

        // $mmos_s = ceil(($amc_s * 4)/50);
        // $mmos_c = ceil(($amc_c * 4)/30);
        // $mmos_t = ceil(($amc_t * 4)/20);

        $mmos_s = intval($ending_bal_s/$amc_s);
        $mmos_c = intval($ending_bal_c/$amc_c);
        $mmos_t = intval($ending_bal_t/$amc_t);

        $recommended_s = ($amc_s * 4) - $ending_bal_s_latest;
        $recommended_c = ($amc_c * 4) - $ending_bal_c_latest;
        $recommended_t = ($amc_t * 4) - $ending_bal_t_latest;

        $recommended_s = ($recommended_s<0)? 0: $recommended_s;
        $recommended_c = ($recommended_c<0)? 0: $recommended_c;
        $recommended_t = ($recommended_t<0)? 0: $recommended_t;

        $recommended_s = round($recommended_s / 100) * 100;
        $recommended_c = round($recommended_c / 30) * 30;

        
        

        // $mmos_c = ceil(($amc_c * 4));
        // $mmos_t = ceil(($amc_t * 4));
      
        if ($mmos_s >6) {
          // $style_s = "style='background-color:#ff7f50'"; //red
          $style_s = "style='background-color:#5efb6e'"; //green
          $decision_s = "REDISTRIBUTE";
        }
        elseif ($mmos_s>=4 && $mmos_s<6){
          // $style_s = "style='background-color:#ffdb58'";//yellow
          $style_s = "style='background-color:#ffdb58'";//yellow
          $decision_s = "MONITOR";
        }
        elseif ($mmos_s<4){
          // $style_s = "style='background-color:#5efb6e'";//green
          $style_s = "style='background-color:#ff7f50'";//red
          $decision_s = "RESUPPLY";
        }
        

        if ($mmos_c >6) {
          // $style_c = "style='background-color:#ff7f50'";//red
          $style_c = "style='background-color:#5efb6e'";//green
          $decision_c = "REDISTRIBUTE";
        }
        elseif ($mmos_c>=4 && $mmos_c<6){
          $style_c = "style='background-color:#ffdb58'";//yellow
          $decision_c = "MONITOR";
        }
        elseif ($mmos_c<4){
          // $style_c = "style='background-color:#5efb6e'";//green
          $style_c = "style='background-color:#ff7f50'";//red
          $decision_c = "RESUPPLY";
        }

        if ($mmos_t >6) {
          // $style_t = "style='background-color:#ff7f50'";//red
          $style_t = "style='background-color:#5efb6e'";//green
          $decision_t = "REDISTRIBUTE";
        }
        elseif ($mmos_t>=4 && $mmos_t<6){
          $style_t = "style='background-color:#ffdb58'";//yellow
          $decision_t = "MONITOR";
        }
        elseif ($mmos_t<4){
          // $style_t = "style='background-color:#5efb6e'";//green
          $style_t = "style='background-color:#ff7f50'";//red
          $decision_t = "RESUPPLY";
        }
        
        // echo "THIS: <Pre>";print_r($ending_bal_s);
        ?> 
        <tr>  

        <input type="hidden" name="row_id[<?php echo $count ?>]" value="<?php echo $value['id'];?>">
        <input type="hidden" name="allocation_date[<?php echo $count ?>]" value="<?php echo $selected_year.'-'.$selected_month.'-'.date('d');?>">

          <td align=""><?php echo $county_name;?></td>
          <td align=""><?php echo $district_name;?></td>              
          <td align=""><?php echo $value['facility_code'];?></td>
          <td align=""><?php echo $value['facility_name'];?></td>  

          <td align="center"><?php echo $ending_bal_s_latest;?></td>     
          <td align="center"><?php echo $amc_s;?></td> 
          <td align="center"><?php echo $mmos_s;?></td>
          <td align="center"><input style="width:40px" class="screening_input" id="q_allocate_s<?php echo $count ?>" name="q_allocate_s[<?php echo $count ?>]" value = '<?php echo $value['allocate_s'];?>'/></td> 
          <td align="center">
          <textarea style="width:100px" class="screening_input" id="feedback_s<?php echo $count ?>" name="feedback_s[<?php echo $count ?>]" value = '<?php echo $value['remark_s'];?>'><?php echo $value['remark_s'];?></textarea>
          </td> 
          <td align="center" <?php echo $style_s;?> > <?php echo $decision_s;?></td> 

          <td align="center"><?php echo $ending_bal_c_latest;?></td>     
          <td align="center"><?php echo $amc_c;?></td> 
          <td align="center"><?php echo $mmos_c;?></td> 
          <td align="center"><input style="width:40px" class="confirm_input" id="q_allocate_c<?php echo $count ?>" name="q_allocate_c[<?php echo $count ?>]" value = '<?php echo $value['allocate_c'];?>'/></td> 
          <td align="center">
          <textarea style="width:100px" class="confirm_input" id="feedback_c<?php echo $count ?>" name="feedback_c[<?php echo $count ?>]" value = '<?php echo $value['remark_c'];?>'><?php echo $value['remark_c'];?></textarea>
          </td> 
          <td align="center"<?php echo $style_c;?>><?php echo $decision_c;?></td> 

                    
        </tr>
        <?php 
        $count++;

      }

      }else{ ?>
      <tr>There are No Facilities which did not Allocate</tr>
      <?php }
      ?>      

    </tbody>
  </table>

</div>
<br/>
<br/>
<br/>
    <!-- <div id="message" type="text" style="margin-left: 0%; width:200px;color:blue;font-size:120%"></div> -->
        <!-- <input class="btn btn-primary" type="submit"   id="confirm"  value="Save Data" style="margin-left: 0%; width:100px" /> -->

        <div class="col-md-12">
          <h3>County comments: </h3>
          <textarea class="form-control margin-vert" readonly="true"><?php echo $status_comment; ?></textarea>
          <?php echo $button_business; ?>
        </div>
        <!-- <input class="btn btn-primary" type="submit"   id="confirm_new"  value="Save Allocation" style="margin-left: 0%; width:300px" /> -->
<?php form_close(); ?>
    

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
      
      ],  
      "sSwfPath": "<?php echo base_url();?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
    }
  });
   
  // $("#allocation_table").tablecloth({theme: "paper",         
  //   bordered: true,
  //   condensed: true,
  //   striped: true,
  //   sortable: true,
  //   clean: true,
  //   cleanElements: "th td",
  //   customClass: "data-table"
  // });
  function validate_input(){
    
  }
 
// $('.screening_input').change(function() {
          
//          var sc = $(this).val();

//           if (isNaN(sc)){
//               alert("Please enter numbers only");
//               $(this).css("background-color", "pink");
            
//           }

//     });
//     $('.confirm_input').change(function() {
          
//          var sc = $(this).val();

//           if (isNaN(sc)){
//               alert("Please enter numbers only");
//               $(this).css("background-color", "pink");
            
//           }

//     });
    $('#confirm').click( function() {

        // alert('Saving Allocation is Still work in Progress');
        var sum_screening=0;
        var sum_confirm=0;
        var sum_tiebreaker=0;
        var screening_current_amount = $('#screening_current_amount').val();
        var confirmatory_current_amount = $('#confirmatory_current_amount').val();
        var tiebreaker_current_amount = $('#tiebreaker_current_amount').val();
       
        $('.screening_input').each(function() {
          sum_screening += Number($(this).val());
         
        });
        $('.confirm_input').each(function() {
          sum_confirm += Number($(this).val());
          
        });
        $('.tiebreaker_input').each(function() {
          sum_tiebreaker += Number($(this).val());

        });
       
        var new_screening_amount = screening_current_amount - sum_screening;
        var new_confirmatory_amount = confirmatory_current_amount - sum_confirm;
        var new_tiebreaker_amount = tiebreaker_current_amount - sum_tiebreaker;
           // alert(new_screening_amount);

        // if (sum_screening>screening_current_amount) {
        //  alert('The available amount of Screening is less the the amount you have allocated.</br>Available Amount: '+screening_current_amount+'<br/> Hint: Please check the values you entered');
        // } else if (sum_confirm>confirmatory_current_amount) {
        //   alert('The available amount of Confirmatory is less the the amount you have allocated. <br/>Available Amount: '+confirmatory_current_amount+' <br/> Hint: Please check the values you entered');
        // }
        // // else if (sum_tiebreaker>tiebreaker_current_amount) {
        // //   alert('The available amount of TieBreaker is less the the amount you have allocated. <br/>Available Amount: '+tiebreaker_current_amount+' <br/> Hint: Please check the values you entered');
        // // }
        // else{
          $('#message').html('The Allocation Report is Being Saved. Please Wait');                                         
          $('#message').css('font-size','13px');                                         
          $('#message').css('color','green'); 
        
          // save_allocation_report();
          var url = "<?php echo base_url() . 'rtk_management/edit_district_allocation_report_monthly/'.$district_id.'/'.$selected_month.'/'.$selected_year; ?>";
                    
          var data = $('#myform').serializeArray();
          data.push({name: 'new_screening_amount', value: new_screening_amount},{name: 'new_confirmatory_amount', value: new_confirmatory_amount}, {name: 'new_tiebreaker_amount', value: new_tiebreaker_amount});
          $.ajax({
            url : url, // or whatever
            type : 'POST',
            data : data,
                success : function (response) {                    
                  console.log(response);
                }
            });
               
       // }//else brace
        
    });

$('#next_report_btn').button().click(function(e)
{
    var next_id = $('#next_report_btn').val();
    var url = "<?php echo base_url() . 'rtk_management/district_allocation_table/'; ?>";
    var site_url_link = url+next_id;
    window.location.href = site_url_link;
});
$('#go_home').button().click(function(e)
{    var countyid = $('#countyid');
    var url = "<?php echo base_url() . 'rtk_management/cmlt_allocation_dashboard/'; ?>"; 
    var site_url_link = url+countyid;
    window.location.href = site_url_link;
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



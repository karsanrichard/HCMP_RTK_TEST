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
<b>Available amount of Kits in <?php echo $county_name;?>:</b><br/>
Screening: <?php echo $screening_current_amount?>, Confirmatory: <?php echo $confirmatory_current_amount?>. <br/><br/>
Guide:<b style="color:green;"> Green :- Resupply, </b> <b style="background-color:yellow;"> Yellow :- Monitor, &nbsp;</b><b style="color:red;"> Red :- Redistribute</b>
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
    echo form_open('rtk_management/submit_district_allocation_report', $attributes);

?>
<form id="myform">
  <input type="hidden" id="countyid" name="county_id" value="<?php echo $countyid;?>"> 
  <input type="hidden" name="district_id" id = "district_id" value="<?php echo $districtid;?>"> 
  <input type="hidden" id="screening_current_amount" value="<?php echo $screening_current_amount;?>"> 
  <input type="hidden" id="confirmatory_current_amount" value="<?php echo $confirmatory_current_amount;?>"> 
  <input type="hidden" id="tiebreaker_current_amount" value="<?php echo $tiebreaker_current_amount;?>"> 
          
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
        $facil = $value['code'];

        $ending_bal_s =ceil($value['ending_bal_s']); 
        $ending_bal_c =ceil($value['ending_bal_c']);

        $amc_s =ceil($value['amc_s']); 
        $amc_c =ceil($value['amc_c']); 

          

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

        $mmos_s = $value['mmos_s'];
        $mmos_c = $value['mmos_c'];

        if ($value['decision_s'] =='REDISTRIBUTE' ) {
          $style_s = "style='background-color:#ff7f50'"; //red
        }
        elseif ($value['decision_s'] == "MONITOR"){

          $style_s = "style='background-color:#ffdb58'";//yellow
        }
        elseif ($value['decision_s'] == "RESUPPLY" ){

          $style_s = "style='background-color:#5efb6e'";//green
        }else{
          $style_s = "style='background-color:#fff'";
        }

        if ($value['decision_c'] =='REDISTRIBUTE' ) {
          $style_c = "style='background-color:#ff7f50'"; //red
        }
        elseif ($value['decision_c'] == "MONITOR"){

          $style_c = "style='background-color:#ffdb58'";//yellow
        }
        elseif ($value['decision_c'] == "RESUPPLY" ){

          $style_c = "style='background-color:#5efb6e'";//green
        }else{
          $style_c = "style='background-color:#fff'";
        }

        
        ?> 
        <tr>  

        <!-- <input type="hidden" name="county_id<?php echo $count ?>" value="<?php echo $value['county_id'];?>">  -->
        <!-- <input type="hidden" name="county<?php echo $count ?>" value="<?php echo $value['county'];?>">  -->
        <!-- <input type="hidden" name="district_id<?php echo $count ?>" value="<?php echo $district_id;?>">  -->
        <!-- <input type="hidden" name="district<?php echo $count ?>" value="<?php echo $value['district']?>">  -->
        <!-- <input type="hidden" name="zone<?php echo $count ?>" value="<?php echo $zone;?>">  -->
        <!-- <input type="hidden" name="user_id<?php echo $count ?>" value="<?php echo 1;?>">  -->

        <input type="hidden" name="facility_name[<?php echo $count ?>]" value="<?php echo $value['name'];?>"> 
        <input type="hidden" name="facility_code[<?php echo $count ?>]" value="<?php echo $value['code'];?>"> 
        <input type="hidden" name="commodity_id[<?php echo $count ?>]" value="<?php echo $count;?>"> 

        <input type="hidden" name="ending_bal_s[<?php echo $count ?>]" value="<?php echo $ending_bal_s;?>"> 
        <input type="hidden" name="mmos_s[<?php echo $count ?>]" value="<?php echo $mmos_s;?>"> 
        <input type="hidden" name="amc_s[<?php echo $count ?>]" value="<?php echo $amc_s;?>"> 
        <input type="hidden" name="decision_s[<?php echo $count ?>]" value="<?php echo $decision_s;?>"> 
        
        <input type="hidden" name="ending_bal_c[<?php echo $count ?>]" value="<?php echo $ending_bal_c;?>"> 
        <input type="hidden" name="mmos_c[<?php echo $count ?>]" value="<?php echo $mmos_c;?>"> 
        <input type="hidden" name="amc_c[<?php echo $count ?>]" value="<?php echo $amc_c;?>"> 
        <input type="hidden" name="decision_c[<?php echo $count ?>]" value="<?php echo $decision_c;?>"> 

          <td align=""><?php echo $county_name;?></td>
          <td align=""><?php echo $district_name;?></td>              
          <td align=""><?php echo $value['facility_code'];?></td>
          <td align=""><?php echo $value['facility_name'];?></td>  

          <td align="center"><?php echo $ending_bal_s;?></td>     
          <td align="center"><?php echo $amc_s;?></td> 
          <td align="center"><?php echo $mmos_s;?></td>
          <td align="center"><input style="width:40px" class="screening_input" id="q_allocate_s<?php echo $count ?>" name="q_allocate_s[<?php echo $count ?>]" value = '<?php echo $value['allocate_s'];?>'/></td> 
          <td align="center"><input style="width:100px" class="screening_input" id="feedback_s<?php echo $count ?>" name="feedback_s[<?php echo $count ?>]" value = '<?php echo $value['remark_s'];?>'/></td> 
          <td align="center" <?php echo $style_s;?> > <?php echo $value['decision_s'];?></td> 

          <td align="center"><?php echo $ending_bal_c;?></td>     
          <td align="center"><?php echo $amc_c;?></td> 
          <td align="center"><?php echo $mmos_c;?></td> 
          <td align="center"><input style="width:40px" class="confirm_input" id="q_allocate_c<?php echo $count ?>"name="q_allocate_c[<?php echo $count ?>]" value = '<?php echo $value['allocate_c'];?>'/></td> 
          <td align="center"><input style="width:100px" class="confirm_input" id="feedback_c<?php echo $count ?>"name="feedback_c[<?php echo $count ?>]" value = '<?php echo $value['remark_c'];?>'/></td> 
          <td align="center"<?php echo $style_c;?>><?php echo $value['decision_c'];?></td> 

                    
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
  </form>
<?php form_close(); ?>

</div>
<br/>
<br/>
<br/>
    <div id="message" type="text" style="margin-left: 0%; width:200px;color:blue;font-size:120%">lalaaa</div>
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
   
  $("#allocation_table").tablecloth({theme: "paper",         
    bordered: true,
    condensed: true,
    striped: true,
    sortable: true,
    clean: true,
    cleanElements: "th td",
    customClass: "data-table"
  });
  function validate_input(){
    
  }
  function loadRemaining(){
    
           var message = 'Report has been Submitted Successfully.';          
           $('#next_modal').modal('show');   
           $('#report_status').html(message);
           $('#next_report_btn').attr('value',next_id); 
          
}
 
 function loadRemaining2(){
      var district_id = $('#district_id').val();
      var site_url= "<?php echo base_url() . 'rtk_management/get_remaining_districts/'; ?>";
      var url  = site_url+district_id;

    $.ajax({
        url: url,
        dataType: 'json',
        success: function(s){ 
           var next_id = s[0]; 
           if (next_id==''){

          var message = 'All Allocations have been Submitted.';          
           $('#next_modal').modal('show');   
           $('#report_status').html(message);
           $('#next_report_btn').hide();
           console.log(s);

         }else{

           var message = 'That Allocation has Already been Submitted.';          
           $('#next_modal').modal('show');   
           $('#report_status').html(message);
           $('#next_report_btn').attr('value',next_id);                                          
         }
        },
        error: function(e){
            console.log(e.responseText);
        }
    });
}
$('.screening_input').keyup(function() {
          
         var sc = $(this).val();

          if (isNaN(sc)){
              alert("Please enter numbers only");
              $(this).css("background-color", "pink");
            
          }

    });
    $('.confirm_input').keyup(function() {
          
         var sc = $(this).val();

          if (isNaN(sc)){
              alert("Please enter numbers only");
              $(this).css("background-color", "pink");
            
          }

    });
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

        if (sum_screening>screening_current_amount) {
         alert('The available amount of Screening is less the the amount you have allocated.</br>Available Amount: '+screening_current_amount+'<br/> Hint: Please check the values you entered');
        } else if (sum_confirm>confirmatory_current_amount) {
          alert('The available amount of Confirmatory is less the the amount you have allocated. <br/>Available Amount: '+confirmatory_current_amount+' <br/> Hint: Please check the values you entered');
        }
        // else if (sum_tiebreaker>tiebreaker_current_amount) {
        //   alert('The available amount of TieBreaker is less the the amount you have allocated. <br/>Available Amount: '+tiebreaker_current_amount+' <br/> Hint: Please check the values you entered');
        // }
        else{
          $('#message').html('The Allocation Report is Being Saved. Please Wait');                                         
          $('#message').css('font-size','13px');                                         
          $('#message').css('color','green'); 
        
          // save_allocation_report();
          var url = "<?php echo base_url() . 'rtk_management/edit_district_allocation_report'; ?>";
                    
          var data = $('#myform').serializeArray();
          data.push({name: 'new_screening_amount', value: new_screening_amount},{name: 'new_confirmatory_amount', value: new_confirmatory_amount}, {name: 'new_tiebreaker_amount', value: new_tiebreaker_amount});
          $.ajax({
            url : url, // or whatever
            type : 'POST',
            data : data,
                success : function (response) {                    
                                    
                    // if(response==1)
                    // {
                        loadRemaining();
                //     }else{
                //         loadRemaining2();
                //         console.log(response);

                //     }
                        console.log(data);
                }
            });
               
       }
        
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



<style type="text/css">
.row{
    font-size: 13px;
    font-family: calibri;
}
.user2{width: 40px;}
#example{width: 100% !important;}    
.nav{margin-bottom: 0px;} 
table,.dataTables_info{font-size: 11px;}
#example_filter>input{position: relative !important;margin-top: 1em;}
#example_wrapper>.DTTT_container>a>span{font-size: 10px;}
.DTTT_container{margin-top: 1em;}
#banner_text{width: auto;}
.divide{height: 2em;}
.span12{
    float: left;
    width: 60%;
    font-size: 14px;
    margin-top: 30px;
}

</style>
<script src="<?php echo base_url(); ?>assets/tagsinput/bootstrap-typeahead.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/tablecloth/assets/css/tablecloth.css">

<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>



<script type="text/javascript">
$(document).ready(function() {
    $("table").tablecloth({theme: "paper"});

    var mySource = <?php echo $facilities_in_district; ?>;

    $('#typeahead').typeahead({
        source: mySource,
        display: 'facility_name',
        KeyVal: 'facility_code'
    });

});


</script>

<script type="text/javascript">
$(function(){
    $('#edit_facility').click(function(){
       $('.modal').modal('show');
   })

    $('#update_facility_btn').click(function(){
      var facilityname = $('#facilityname').val();      
      var district = $('#district').val();
      var facility_code = $('#facility_code').val();
      $.post( "<?php echo base_url().'rtk_management/update_facility_county';?>", { 
        facilityname: facilityname,
        facility_code: facility_code,        
        district: district
    })
      .done(function( data ) {
    //alert( "Success : " + data );
  //$('#edit_facility').modal('hide');
//window.location = "<?php echo base_url().'rtk_management/county_admin/facilities';?>";
});
      


  })

});
</script>




<div class="" style="width:100%">
    <div class="" >
        <h1 align="center"> <b><?php echo $banner_text; ?></b></h1>
             
        <button class="btn btn-primary" style="align:right" data-target="#Edit_Facility" data-toggle="modal">Edit Facility</button>
     
    </div>   
    
    <div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; ">
        <div class="accordion" id="accordion2">
            <h3>RTK Reports</h3>
            <label style="color: green"> <i>Click to View the Consumption Summary</i></label>
            <?php 
            $count = count($reports['rtk_facility_arr']);
            if($count>=1){
                foreach ($reports['rtk_facility_arr'] as $key => $value) { ?>
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#rtk_report-<?php echo $value['id']; ?>">
                            <?php echo $value['facility_name'] . ' ( ' . $value['district'] . ', ' . $value['county'] . ')'; ?> Summary Report for <?php echo date('F, Y', strtotime('-1 Month',strtotime($value['order_date'])) ); ?> Compiled by <?php echo($value['compiled_by']); ?>
                        </a>
                    </div>
                    <div id="rtk_report-<?php echo $value['id']; ?>" class="accordion-body collapse" style="height: 0px;">
                        <div class="accordion-inner">
                            <table class="table" style="font-size:12px;">
                                <thead>
                                    <tr>
                                        <th>Kit</th>
                                        <th>Beginning Balance</th>
                                        <th>Received Quantity</th>
                                        <th>Used Total</th>
                                        <th>Total Tests</th>
                                        <th>Positive Adjustments</th>
                                        <th>Negative Adjustments</th>
                                        <th>Losses</th>
                                        <th>Closing Balance</th>
                                        <th>Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $a = 0;
                                    foreach ($value[$a] as  $values) {
                                       
                                        ?>
                                       <tr>
                                        <td><?php echo $values['commodity_name'];?></td>                                                               
                                        <td><?php echo $values['beginning_bal']; ?></td>
                                        <td><?php echo $values['q_received']; ?></td>
                                        <td><?php echo $values['q_used']; ?></td>
                                        <td><?php echo $values['no_of_tests_done']; ?></td>
                                        <td><?php echo $values['positive_adj']; ?></td>
                                        <td><?php echo $values['negative_adj']; ?></td>
                                        <td><?php echo $values['losses']; ?></td>
                                        <td><?php echo $values['closing_stock']; ?></td>
                                        <td><?php echo $values['q_requested']; ?></td>
                                    </tr> 
                                    <?php $a++; }
                                ?>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } 
                
                }else{
                    echo "There is no Data to Display. There are no records found for that facility";
                }         


            ?>

        </div>
    </div>
    <div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; ">
        <div class="accordion" id="accordion3">
            <h3>CD4 Reports</h3>
            <label style="color: green"> <i>Click to View the Consumption Summary</i></label>

            <?php 
            $count = count($reports['cd4_facility_arr']);
            if($count>=1){
                foreach ($reports['cd4_facility_arr'] as $key => $value) { ?>
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#cd4_report-<?php echo $value['order_id']; ?>">
                            <?php echo $value['facility_name'] . ' ( ' . $value['district'] . ', ' . $value['county'] . ')'; ?> Summary Report for <?php echo date('F, Y', strtotime('-1 Month',strtotime($value['order_date'])) ); ?> Compiled by <?php echo($value['compiled_by']); ?>
                        </a>
                    </div>
                    <div id="cd4_report-<?php echo $value['order_id']; ?>" class="accordion-body collapse" style="height: 0px;">
                        <div class="accordion-inner">
                            <table class="table" style="font-size:12px;">
                                <thead>
                                    <tr>
                                        <th>Kit</th>
                                        <th>Beginning Balance</th>
                                        <th>Received Quantity</th>
                                        <th>Used Total</th>
                                        <th>Total Tests</th>
                                        <th>Positive Adjustments</th>
                                        <th>Negative Adjustments</th>
                                        <th>Losses</th>
                                        <th>Closing Balance</th>
                                        <th>Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $a = 0;
                                    foreach ($value[$a] as  $values) {
                                       
                                        ?>
                                       <tr>
                                        <td><?php echo $values['commodity_name'];?></td>                                                               
                                        <td><?php echo $values['beginning_bal']; ?></td>
                                        <td><?php echo $values['q_received']; ?></td>
                                        <td><?php echo $values['q_used']; ?></td>
                                        <td><?php echo $values['no_of_tests_done']; ?></td>
                                        <td><?php echo $values['positive_adj']; ?></td>
                                        <td><?php echo $values['negative_adj']; ?></td>
                                        <td><?php echo $values['losses']; ?></td>
                                        <td><?php echo $values['closing_stock']; ?></td>
                                        <td><?php echo $values['q_requested']; ?></td>
                                    </tr> 
                                    <?php $a++; }
                                ?>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } 
                
                }else{
                    echo "There is no Data to Display. There are no records found for that facility";
                }         


            ?>

        </div>
    </div>
</div>



<!--Update the Facility -->
<script type="text/javascript">
$(document).ready( function () {
 $('#facilities_tlb').dataTable(); 

// functions may apply here


})
</script>



<div class="modal fade" id="Edit_Facility" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Facility</h4>
    </div>
    <div class="modal-body">       
        <form id="update_facility_form" method="POST" action="<?php echo base_url().'rtk_management/update_facility_county';?>">                        
            <h4>Facility Name: </h4>
            <p> <input type="text" name="facilityname" class="form-control" id="facilityname" value="<?php echo $facility_name ;?>"/></p>            
            <h4>Sub-County </h4>            
            <select name="district" id="district" class="form-control">
                <option value="<?php echo $district_id; ?>";> -- <?php echo $facility_district; ?> --</option>
                <?php 
                foreach ($districts as $dists) { ?>
                <option value="<?php echo $dists['id']; ?>"><?php echo $dists['district']; ?></option>
                <?php }?>
            </select>
            <p> <input type="hidden" class="form-control" name="facility_code" id="facility_code" value="<?php echo $mfl ;?>" /></p>
            <hr>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button id="update_facility_btn" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div> 

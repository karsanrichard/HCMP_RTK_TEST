<style type="text/css">

tr.header
{
    cursor:pointer;
}
.detailed_table{
    display: none;
}

/*[data-toggle="toggle"] {
    display: none;
}*/
</style>
<script src="<?php echo base_url(); ?>assets/tagsinput/bootstrap-typeahead.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/tablecloth/assets/css/tablecloth.css">

<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>



<script type="text/javascript">
$(document).ready(function() {
    $('.headers').click(function(row){

        $(this).nextUntil('table.headers').slideToggle();
    });

    $('#open_fcdrr').click(function(){
var cd4_reports_count = '<?php echo $cd4_report_count; ?>';
   // var cd4_reports_count = 1;
    if (cd4_reports_count > 0) {
        alert('CD4 Report already submitted. If you wish to change the report, click on the Edit link');
    }else{
       var url = "<?php echo base_url() . 'cd4_management/get_cd4_report/' . $mfl; ?>";

       // alert(url);
       window.location.href = url;
   }
    })
 $('#open_rtk_fcdrr').click(function(){
   var rtk_reports_count = '<?php echo $rtk_report_count; ?>';
   // var rtk_reports_count = 1;
    if (rtk_reports_count > 0) {
        alert('RTK Report already submitted.  If you wish to change the report, click on the Edit link');
    }else{
       var url = "<?php echo base_url() . 'rtk_management/get_report/' . $mfl; ?>";

       // alert(url);
       window.location.href = url;
   }
    })

    
    $('.detailed_btn').click(function(){
        alert('Oops! Still in progress, coming soon....')
    });

    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
 
        // Show/Hide Tabs
        jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();
 
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });
});

</script>

<style type="text/css">
    .tabs {
    width:100%;
    display:inline-block;
}
 
    /*----- Tab Links -----*/
    /* Clearfix */
    .tab-links:after {
        display:block;
        clear:both;
        content:'';
    }
 
    .tab-links li {
        float:left;
        list-style:none;
    }
 
    .tab-links a {
        padding:9px 15px;
        display:inline-block;
        border-radius:3px 3px 0px 0px;
        background: #fff ;
        font-size:16px;
        font-weight:600;
        color:#4c4c4c;
        transition:all linear 0.15s;
    }

    .tab-links a:hover {
        background:#e7fae8
;
        text-decoration:none;
    }
 
    li.active a, li.active a:hover {
        background:#b6e3b1 ;
        color:#4c4c4c;
    }
 
    /*----- Content of Tabs -----*/
    .tab-content {
        padding:15px;
        border-radius:3px;
        box-shadow:-1px 1px 1px rgba(0,0,0,0.15);
        background:#fff;
    }
 
    .tab {
        display:none;
    }

    .tab.active {
        display:block;
    }

</style>
<?php include ('cd4_scmlt_sidebar.php');?>
<div style="float:left; width:60%"> <h3 style=" text-align: center; color: #145a2b;"><strong>CD4 Consumption Reporting System</strong></h3>  </div> 
<div style="float:right; width:20%; margin-top:20px;"><button class="btn btn-primary" id="open_fcdrr" > <?php echo $cd4_button_text; ?></button></div>
<div style="float:right; width:20%; margin-top:20px;"><button class="btn btn-success" id="open_rtk_fcdrr" > <?php echo $rtk_button_text; ?></button></div>

<br/>
<br/>
<br/>
<h3 style="width: 80%; text-align: center "><?php echo $banner_text; ?></h3>
<br/>
<br/>


<div class="container" style="margin-left: 250px;background-color:   #fbf6f5">

<div class="tabs">
    <ul class="tab-links">
        <li class="active"><a href="#cd4_data">CD4 Reports</a></li>
        <li><a href="#rtk_data">RTK Reports</a></li>
    </ul>
<div class="tab-content">
    <div id="cd4_data" class = "tab active" style="margin-left:65px; width:60%;  ">
        <table  style="margin-left: 60px; font-size: 14px;" id="maintable" class="table">
            <thead>
                <tr>
                    <th><b>Month</b></th>
                    <th><b>Complied By</b></th>
                    <th><b>View Full Report</b></th> 
                    <th><b>Summary Report</b></th> 
                </tr>
            </thead>
            <tbody id="facilities_home" class="row_report">
            <?php 
                $count = count($reports);
                $a = 0;
                $i = 0;
                if($count>=1){
                foreach ($reports as $key => $value) { 
            ?>
                <tr >
                <td><?php echo date('F, Y', strtotime('-1 Month',strtotime($value['order_date'])) ); ?></td>
                <td><?php if ($value['compiled_by'] == '' || $value['compiled_by'] ==0) { echo "N/A"; } else { echo $value['compiled_by'];} ?></td>
                <td> <a class='link' href=" <?php echo site_url('cd4_management/fcdrr_details/'.$value['order_id']) ?>"> View Full Report </a>  <a href="<?php echo site_url('cd4_management/edit_lab_order_details/' . $value['order_id']); ?>"class="link report">| Edit</a> 
                </td>
                <td > Click to see Summary <button id ="parent_header_<?php echo $i;?>" class = "detailed_btn"> + </button> </td>
                </tr>
                <tr class = "detailed_table" id="header_<?php echo $i;?>">
                <td colspan = "4">
                    <table class="table " style="font-size:12px;">
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
                                        if(!(($values['beginning_bal']==0)&&
                                            ($values['q_received']==0)&&
                                            ($values['q_used']==0)&&
                                            ($values['no_of_tests_done']==0)&&
                                            ($values['positive_adj']==0)&&
                                            ($values['negative_adj']==0)&&
                                            ($values['losses']==0)&&
                                            ($values['closing_stock']==0)&&
                                            ($values['q_requested']==0))
                                        ){
                                       
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
                                }$i++;
                                ?>
                                   
                                </tbody>
                            </table>
                    </td>
                </tr>

                <?php }
                
                }else{
                    echo "There is no Data to Display. There are no records found for that facility";
                }  

                ?>
            </tbody>            
        </table>

    </div>
    <div id="rtk_data" class="tab">
        
        <table  style="margin-left: 60px; font-size: 14px;" id="maintable" class="table">
            <thead>
                <tr>
                    <th><b>Month</b></th>
                    <th><b>Complied By</b></th>
                    <th><b>View Full Report</b></th> 
                    <th><b>Summary Report</b></th> 
                </tr>
            </thead>
            <tbody id="facilities_home" class="row_report">
            <?php 
                $count = count($rtk_orders);
                $a = 0;
                $i = 0;
                if($count>=1){
                foreach ($rtk_orders as $key => $val) { 
            ?>
                <tr >
                <td><?php echo date('F, Y', strtotime('-1 Month',strtotime($val['order_date'])) ); ?></td>
                <td><?php if ($val['compiled_by'] == '' || $val['compiled_by'] ==0) { echo "N/A"; } else { echo $val['compiled_by'];} ?></td>
                <td> <a class='link' href=" <?php echo site_url('rtk_management/lab_order_details/'.$val['id']) ?>"> View Full Report </a> </td>
                <td > Click to see Summary <button id ="parent_header_<?php echo $i;?>" class = "detailed_btn"> + </button> </td>
                </tr>
                <?php }
                
                }else{
                    echo "There is no Data to Display. There are no records found for that facility";
                }  

                ?>
            </tbody>            
        </table>
    </div>
    </div></div></div>
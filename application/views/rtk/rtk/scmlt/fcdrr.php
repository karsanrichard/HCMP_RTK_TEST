<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/datatable/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/tablecloth/assets/css/tablecloth.css">
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>

<style>
    @import "<?php echo base_url(); ?>assets/datatable/media2/css/jquery.dataTables.css";
</style>
<style>
    .user{
        width:70px;
        background : none;
        border : none;
        text-align: center;
    }
    .user2{
        width:70px;

        text-align: center;
    }
    .col5{background:#D8D8D8;}
    table{
        font-size: 11px;   
    }    
    table td{
        line-height: 11px;
    }
</style>

<script type="text/javascript">

    $(window).load(function(){
        $('#fcdrr_changes_alert').modal('show');
    });
    
    $(function() {

        $('#user_order input').addClass("form-control");

    //Set the begining Balance for the Comodities    
    var begining_bal = <?php echo json_encode($beginning_bal);?>;
    var amcs = <?php echo json_encode($amcs);?>;

    for (var a = 0; a < begining_bal.length; a++) {            
        var current_bal = begining_bal[a];
        $('#b_balance_'+a).attr("value",current_bal); 
        $('#physical_count_'+a).attr("value",current_bal); 
    };             

    for (var a = 0; a < amcs.length; a++) {            
        var current_amc = amcs[a];
        $('#amc_'+a).attr("value",current_amc); 
         console.log(current_amc);
    };   
    //Set the first element uneditable i.e. Screening Determine
   // $('#tests_done_0').attr("readonly",'true');

    //Set the Datepickers
    $("#begin_date").datepicker({
        defaultDate: "",
        changeMontd: true,
        changeYear: true,
        numberOfMontds: 1,
    });
    $("#end_date").datepicker({
        defaultDate: "",
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        
    });

    /*Calculating the Value of the Number of tests done for Screening Determine*/

    /*end of triggering of the calculation of Values of the Number of tests done for Screening Determine*/
    $('#vct').change(function(){           
        validate_tests('vct');        
    })    
    $('#pitc').change(function(){        
        validate_tests('pitc');       
    })
    $('#pmtct').change(function(){        
        validate_tests('pmtct');        
    })
    $('#blood_screening').change(function(){        
        validate_tests('blood_screening');        
    })
    $('#other2').change(function(){        
       validate_tests('other2');        
   }) 

    /* end of triggering of the calculation of Values for Screening Determine */   
    /* Start of Validation for Tests for Screening Determine */
    function validate_tests(top_type){
        var input_value  = $('#'+top_type).val();
        if(isNaN(input_value)){            
           $('#'+top_type).css("border-color","red");
       }else{ 
            if(input_value<0){                
                $('#'+top_type).css("border-color","red");                
            }else{      
                $('#'+top_type).css("border-color","none");
                //compute_tests_done();
            }
        }

    }

/* --- Start of calculation for the no of tests done for Screening Determine  -- */
function compute_tests_done(){  
    var vct_no = parseInt($('#vct').val());
    var pitc_no = parseInt($('#pitc').val());
    var pmtct_no = parseInt($('#pmtct').val());
    var blood_screening_no = parseInt($('#blood_screening').val());
    var other = parseInt($('#other2').val());
    tests_done_no = vct_no + pitc_no + pmtct_no + blood_screening_no + other;
    $('#tests_done_0').attr("value",tests_done_no).change();
    $('#q_used_0').attr("value",tests_done_no).change();
}       
/* End of Validation for Tests for Screening Determine */


    /* ---- Compute the Losses New----*/    
    function validate_loss(row){
        var tests_done = $('#tests_done_'+row).val();        
        var quantity_used = $('#q_used_'+row).val();
        compute_loss(row,'q_used_','tests_done_');
    }

    function compute_loss(row,q_used,tests_done){        
        var loss = $('#q_used'+row).val() - $('#tests_done'+row).val();
        $('#losses_'+row).val(loss).change();        
    }

    $('.qty_used').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs_loss('q_used_',num);
        //validate_inputs('q_used_',num);
        
    })
    $('.tests_done').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs_loss('tests_done_',num);
        //validate_inputs('q_used_',num);
        
    })
    $('.bbal').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('b_balance_',num);
    })
    $('.qty_rcvd').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('q_received_',num);
    })
     $('.qty_rcvd_other').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('q_received_other',num);
    })


    $('.pos_adj').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('pos_adj_',num);
        $('#explanation').css("border-color","green"); 
    })
    $('.neg_adj').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('neg_adj_',num);
        $('#explanation').css("border-color","green"); 
    })  
    $('.phys_count').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('physical_count_',num);
    })  
    $('.losses').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);
        validate_inputs('losses_',num);
        $('#explanation').css("border-color","green"); 
    }) 
    
    $('.bbal').change(function() {
        row_id = $(this).closest("tr");
        number = row_id.attr("commodity_id");
        num = parseInt(number);        
        validate_inputs('b_balance_',num);
    })          
                  
    //  $('.bbal').load(function() {
    //     row_id = $(this).closest("tr");
    //     number = row_id.attr("commodity_id");
    //     num = parseInt(number);        
    //     validate_inputs('b_balance_',num);
    // })   



    /*  Check if a value is a number and not less than zero */
    function validate_inputs(input,row){        
        var input_value  = $('#'+input+row).val();
        if((isNaN(input_value))|| (input_value=='')){            
           $('#'+input+row).css("border-color","red");
           hide_save();
         }else{ 
                $('#'+input+row).css("border",'');                
                if(input_value<0){                
                    $('#'+input+row).css("border-color","red");                
                    hide_save();
                }else if(input_value>100000){
                    alert('Sorry, your values are above the expected limit. Please check them again');                
                    $('#'+input+row).css("border-color","red");                
                    hide_save();
                }else{      
                    $('#'+input+row).css("border",'');                
                    show_save();
                    compute_closing(row);
                }
            }

    }

    /*  End of Input Validations */

    /*  Check if a value is a number and not less than zero */
    function validate_inputs_loss(input,row){        
        var input_value  = $('#'+input+row).val();        
        if((isNaN(input_value))||(input_value='')){            
           $('#'+input+row).css("border-color","red");
         }else{ 
                input_value = parseInt(input_value);
                $('#'+input+row).css("border",'');                
                if(input_value<0){                
                    $('#'+input+row).css("border-color","red");
                    hide_save();                
                }else{      
                    $('#'+input+row).css("border",'');
                    var q_used = parseInt($('#q_used_'+row).val());
                    var tests_done = parseInt($('#tests_done_'+row).val());
                    if(q_used < tests_done){
                        $('#q_used_'+row).css("border-color","red"); 
                        $('#tests_done_'+row).css("border-color","red"); 
                        hide_save();
                    }else{
                        /*var loss = q_used - tests_done;
                        $('#q_used_'+row).css("border-color",""); 
                        $('#tests_done_'+row).css("border-color",""); 
                        $('#losses_'+row).val(loss).change();*/
                        show_save();
                        compute_closing(row);
                    }                        
                }
            }

    }

    /*  End of Input Validations */
    /* Compute Closing Balance */
    function compute_closing(row){
        var b_bal = parseInt($('#b_balance_' + row).val()); 
        var qty_rcvd = parseInt($('#q_received_' + row).val());
        var qty_rcvd_other = parseInt($('#q_received_other' + row).val());
        var q_used = parseInt($('#q_used_' + row).val());
        var tests_done = parseInt($('#tests_done_' + row).val());
        var loses = parseInt($('#losses_' + row).val());
        var pos_adj = parseInt($('#pos_adj_' + row).val());
        var neg_adj = parseInt($('#neg_adj_' + row).val());
        var closing = b_bal + qty_rcvd +qty_rcvd_other - q_used + pos_adj - neg_adj -loses;       
        if(((q_used+neg_adj)>(b_bal+qty_rcvd+qty_rcvd_other+pos_adj))||(closing<0)){
            alert('You cannot use more kits than what you have in Stock. Please check your computations again');
            $('#b_balance_' + row).css('border-color','red'); 
            $('#q_received_' + row).css('border-color','red'); 
            $('#q_received_other' + row).css('border-color','red'); 
            $('#q_used_' + row).css('border-color','red'); 
            $('#tests_done_' + row).css('border-color','red'); 
            $('#losses_' + row).css('border-color','red'); 
            $('#pos_adj_' + row).css('border-color','red'); 
            $('#neg_adj_' + row).css('border-color','red'); 
            $('#physical_count_' + row).css('border-color','red'); 
            hide_save();
        }else{
            $('#b_balance_' + row).css('border-color',''); 
            $('#q_received_' + row).css('border-color',''); 
            $('#q_received_other' + row).css('border-color',''); 
            $('#q_used_' + row).css('border-color',''); 
            $('#tests_done_' + row).css('border-color',''); 
            $('#losses_' + row).css('border-color',''); 
            $('#pos_adj_' + row).css('border-color',''); 
            $('#neg_adj_' + row).css('border-color',''); 
            $('#physical_count_' + row).css('border-color',''); 
            $('#physical_count_' + row).val(closing);
            show_save();
        }
    }  

    function hide_save() {
        $('#validate').show();
        $('#validate').html('NOTE: Please Correct all Input Fields with red border to Activate the Save Data Button');                                         
        $('#validate').css('font-size','13px'); 
        $('#validate').css('color','red'); 
        $('#save1').hide();
    }
    function show_save() {
        $('#validate').hide();       
        $('#save1').show();
    }


$('#save1')
.button()
.click(function() {               
    $('#message').html('The Report is Being Saved. Please Wait');                                         
    $('#message').css('font-size','13px');                                         
    $('#message').css('color','green'); 
    $(this).hide();

});
$("#dialog").dialog({
    height: 140,
    modal: true
});
function blinker() {
    $('.blinking').fadeOut(500);
    $('.blinking').fadeIn(500);
}
setInterval(blinker, 1000);


});


</script>

<style type="text/css">
    input{
        width: 70px;
    }
    #fixed-alert{
        height: 24px;
        background-color: #EEE;
        color: rgb(255, 166, 166);
        text-shadow: 0.5px 0.51px #949292;
        margin-bottom: 10px;
        position: fixed;
        top: 104px;
        width: 100%;
        padding: 10px 0px 0px 13px;
        border-bottom: 1px solid #ccc;
        font-size: 15px;
        text-align: center;
    }
    #banner_text{
        margin-top: 30px;
    }
    #dialog-form{
        margin-top: 30px;

    }
    .header_text{
        color:#337ab7;
        font-size: 16px
    }
     .header_values{
        font-size: 16px
    }
</style>

<?php

$attributes = array('name' => 'myform', 'id' => 'myform');
echo form_open('rtk_management/save_lab_report_data', $attributes);

foreach ($facilities as $facility) {
    $id = $facility['id'];
    $facility_name = $facility['facility_name'];
    $facility_code = $facility['facility_code'];
    $district_id = $facility['district'];
    $district = $facility['district_name'];
    $county = $facility['county'];
    $owner = $facility['owner'];
}
$month = date("m", strtotime("Month "));
$year = date("Y", strtotime(" Month "));
$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$lastdate = $year . '-' . $month . '-' . $num_days;
$firstdate = $year . '-' . $month . '-01';

$sql = "select * from lab_commodity_orders where facility_code =$facility_code and order_date between $firstdate and $lastdate";
$res = $this->db->query($sql)->result_array();
$count = count($res);
date_default_timezone_set('EUROPE/Moscow');
    $beg_date = date('Y-m-d', strtotime("first day of previous month"));
    $end_date = date('Y-m-d', strtotime("last day of previous month"));
?>

<div id="dialog-form" title="Enter the lab commodity details here">
    <form>
        <table id="user_order" width="10%" class="data-table">
            <input  type="hidden" name="facility_name" colspan = "3" style = "color:#000; border:none" value="<?php echo $facility_name ?>"></td>
            <input type="hidden" name="facility_code" colspan = "2" style = "color:#000; border:none" value="<?php echo $facility_code ?>"></td>
            <input type="hidden" name="district_name" colspan = "2" style = "color:#000; border:none" value="<?php echo $district ?>"></td>
            <input type="hidden" name="county" colspan = "3" style = "color:#000; border:none" value="<?php echo $county ?>"></td>

                <tr>
                    <td colspan = "4" class="header_text"><b>Name of Facility:</b></td>
                    <td colspan = "4" class="header_values"><?php echo $facility_name ?></td>
                    <td colspan = "4" class="header_text" align="right" ><b>MFL Code:</b></td>
                    <td colspan = "6" class="header_values"><?php echo $facility_code ?></td>
                   
 
                </tr>
                <tr >
                    <td colspan = "4" class="header_text"><b>County:</b></td>                        
                    <td colspan = "4" class="header_values"><?php echo $county ?></td>
                    <td colspan = "4" class="header_text" align="right"><b>Sub County:</b></td>
                    <td colspan = "6" class="header_values"><?php echo $district ?></td>
                                       
                </tr>
                <tr>
                    <td colspan = "4" class="header_text"><b>Report for Month Beginning:</b></td> 
                    <td colspan = "4" class="header_values" style="text-align:center;" ><?php echo $beg_date ?></td>
                    <td colspan = "4" class="header_text" align="right" ><b>Ending:</b></td>
                    <td colspan = "6" class="header_values"style="text-align:center;"><?php echo $end_date ?></td>
                    
                <tr>
                        <td colspan = "18" style = "text-align:center;" id="calc">
                            <b>The Ending Balance is Computed as follows: </b><i>Beginning Balance + Quantity Received + Positive Adjustments - Quantity Used - Negative Adjustments - Losses</i> 
                            <b><br/>Note:</b>
                            The Quantity Used Should Not be Less than the Tests Done
                        </td>            
                    </tr>
                <tr><td colspan = "18" height="60px"></td></tr>
                <tr >       
                    <td rowspan = "2" colspan = "2"><b>Commodity Name</b></td>
                    <td rowspan = "2"><b>Unit of Issue (e.g. Test)</b></td>
                    <td rowspan = "2"><b>Beginning Balance</b></td>
                    <td rowspan = "2"><b>Quantity Received This Month - from Central Warehouses (e.g. KEMSA)</b></td>
                    <td rowspan = "2"><b>Quantity Received from other sources (e.g. local suppliers)</b></td>
                    <td rowspan = "2"><b>Quantity Used</b></td>
                    <td rowspan = "2"><b>Number of Tests done (include Repeats, QA/QC)</b></td>
                    <td rowspan = "2"><b>Losses and Wastage</b></td>
                    <td colspan = "2"><b>Adjustments [indicate if (+) or (-)]</b></td>  
                    <td rowspan = "2"><b>End of Month Physical Count</b></td>
                    <td rowspan = "2"><b>Computed Ending Balance</b></td>
                    <td rowspan = "2"><b>Quantity Expriing in <u>less than</u> 6 Months</b></td>
                    <td rowspan = "2"><b>Days out of Stock</b></td> 
                    <td rowspan = "2"><b>Quantity Requested for&nbsp;Re-Supply</b></td>
                    <td rowspan = "2"><b>Average Monthly Consumption</b></td>
                </tr>
                <tr>
                    <td>Positive</td>
                    <td>Negative</td>
                </tr>
                <?php
                $checker = 0;
                foreach ($lab_categories as $lab_category) {
                //     echo "<pre>";
                // print_r($lab_commodities_categories);die;
                    ?>
                <tr>
                        <td colspan = "15" style = "text-align:left"><b><?php echo $lab_category->category_name; ?></b></td>            
                    </tr>                    
                    <?php foreach ($lab_category->category_lab_commodities as $lab_commodities) { ?>
                    <tr commodity_id="<?php echo $checker ?>"><input type="hidden" id="commodity_id_<?php echo $checker ?>" name="commodity_id[<?php echo $checker ?>]" value="<?php echo $lab_commodities['id']; ?>" >
                        <input type="hidden" id="facilityCode" name="facilityCode">
                        <input type="hidden" id="district" name="district" value="<?php echo $district_id; ?>">
                        <input type="hidden" id="unit_of_issue_<?php echo $checker ?>" name = "unit_of_issue[<?php echo $checker ?>]" value="<?php echo $lab_commodities['unit_of_issue']; ?>">
                        <td class="commodity_names" id="commodity_name_<?php echo $checker;?>" colspan = "2" style = "text-align:left"></b><?php echo $lab_commodities['commodity_name']; ?></td>
                        <td style = "color:#000; border:none; text; text-align:center" id="commodity_unit_type_<?php echo $checker;?>"><?php //echo $lab_commodities['unit_of_issue'];  ?></td>
                        <td><input id="b_balance_<?php echo $checker ?>" data-uiid="<?php echo $checker ?>" name = "b_balance[<?php echo $checker ?>]" class='bbal' size="10" type="text" value="0" style = "text-align:center"/></td>
                        
                        <td><input id="q_received_<?php echo $checker ?>" name = "q_received[<?php echo $checker ?>]" class='qty_rcvd' size="10" type="text" value="0" style = "text-align:center"/></td>
                         <td><input id="q_received_other<?php echo $checker ?>" name = "q_received_other[<?php echo $checker ?>]" class='qty_rcvd' size="10" type="text" value="0" style = "text-align:center"/></td>
                        <td><input id="q_used_<?php echo $checker ?>" name = "q_used[<?php echo $checker ?>]" class='qty_used' size="10" type="text" value="0" style = "text-align:center"/></td>
                        <td><input id="tests_done_<?php echo $checker ?>" name = "tests_done[<?php echo $checker ?>]" class='tests_done' size="10" value="0" type="text" style = "text-align:center"/></td>
                        <td><input id="losses_<?php echo $checker ?>" name = "losses[<?php echo $checker ?>]" class='losses' size="10" type="text" value="0" style = "text-align:center" /></td>
                        <td><input id="pos_adj_<?php echo $checker ?>" name = "pos_adj[<?php echo $checker ?>]" class='pos_adj' size="10" type="text" value="0" style = "text-align:center"/></td>  
                        <td><input id="neg_adj_<?php echo $checker ?>" name = "neg_adj[<?php echo $checker ?>]" class='neg_adj' size="10" type="text" value="0" style = "text-align:center"/></td>
                        <td><input id="fcdrr_physical_count_<?php echo $checker ?>"  name = "fcdrr_physical_count[<?php echo $checker ?>]" class='phys_count' value="0" size="10" type="text" style = "text-align:center"/></td>
                         <td><input id="physical_count_<?php echo $checker ?>"  name = "physical_count[<?php echo $checker ?>]" class='phys_count' value="0" size="10" type="text" style = "text-align:center"/></td>
                        <td><input id="q_expiring_<?php echo $checker ?>" name = "q_expiring[<?php echo $checker ?>]" class='user2' size="10" type="text" style = "text-align:center"/></td>
                        <td><input id="days_out_of_stock_<?php echo $checker ?>" name = "days_out_of_stock[<?php echo $checker ?>]" class='user2' size="10" type="text" style = "text-align:center"/></td>  
                        <td><input id="q_requested_<?php echo $checker ?>" data-uiid="<?php echo $checker ?>"name = "q_requested[<?php echo $checker ?>]" class='user2' size="10" type="text" style = "text-align:center"/></td> 
                        <td><input readonly id="amc_<?php echo $checker ?>" data-uiid="<?php echo $checker ?>" name = "amc[<?php echo $checker ?>]" class='amc' size="10" type="text" value="0" style = "text-align:center"/></td>                 
                    </tr>
                    <?php $checker++;
                }
            }
            ?>
            <tr>
                <td colspan = "18" height="60px"><br/></td>
            </tr>
            <tr>                    
                <td colspan = "18" style = "text-align:left;background: #EEE;">Explain Losses and Adjustments</td>
            </tr>
            <tr>                        
                <td colspan = "18"><input colspan = "16" id="explanation" name="explanation" size="210" type="text" value="" style=" width: 90%;"/></td>
            </tr>
            <tr></tr>


            <tr>
                <td colspan = "6" style = "text-align:left"><b>Order for Extra LMIS tools:<br/> To be requested only when your data collection or reporting tools are nearly full. Indicate quantity required for each tool type.</b></td>
                <td colspan = "4"><b>(1) Daily Activity Register for Laboratory Reagents and Consumables (MOH 642):</b></td>
                <td><input class='user2'id="moh_642" name="moh_642" size="10" type="text"/></td>
                <td colspan = "4"><b>(2) F-CDRR for Laboratory Commodities (MOH 643):</b></td>
                <td colspan = "4"><input class='user2'id="moh_643" name="moh_643" size="10" type="text"/></td>
            </tr>   


            <tr><td colspan = "4" style = "text-align:left">Compiled by:</td>
                <td colspan = "4" style = "text-align:left">Tel:</td>
                <td colspan = "10" style = "text-align:left">Designation:</td>
                
            </tr>
            <tr><td colspan = "4"><input class='user2'id="compiled_by" name="compiled_by" size="10" type="text" />
                <span style="color: #f33;font-size: 10px;">* Required Field</span></td>                
                <td colspan = "4"><input class='user2'id="compiled_tel" name="compiled_tel" size="10" type="text" /></td>               
                <td colspan = "10"><input class='user2'id="compiled_des" name="compiled_des" size="10" type="text" /></td>
                
            </tr>

            <tr></tr>

            <tr><td colspan = "4" style = "text-align:left">Approved by:</td>
                <td colspan = "4" style = "text-align:left">Tel:</td>
                <td colspan = "10" style = "text-align:left">Designation:</td>
                
            </tr>
            <tr><td colspan = "4"><input class='user2'id="approved_by" name="approved_by" size="10" type="text" colspan = "4"/>
                <span style="color:#f33;font-size: 10px;">* Required Field</span></td>            
                <td colspan = "4"><input class='user2'id="approved_tel" name="approved_tel" size="10" type="text" /></td>
                <td colspan = "10"><input class='user2'id="approved_des" name="approved_des" size="10" type="text" /></td>                
            </tr>

        </table>
        <div id="validate" type="text" style="margin-left: 0%; width:600px;color:blue;font-size:120%"></div>
        <div id="message" type="text" style="margin-left: 0%; width:200px;color:blue;font-size:120%"></div>
        <input class="btn btn-primary" type="submit"   id="save1"  value="Save" style="margin-left: 0%; width:100px" >
    </form>
</div>
<br />
<br />
<br />
<br />
<br />


<?php form_close(); ?>
<div class="modal fade " tabindex="-1" role="dialog" id="fcdrr_changes_alert_inactive">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title blinking" style="text-align: center; color: #C95A42;">New Alert!!</h4>
      </div>
      <div class="modal-body">
        <p>Due to difference in the values from the system and from the actual FCDRR, there are two fields for beginning and ending balances. <br/><br/>
        1. Enter the begining balance in the FCDRR in the cells under  <br/><b> Physical Count - Beginning Balance</b><br/><br/>
        2. Enter the ending balance in the FCDRR in the cells under  <br/><b>End of Month Physical Count</b></p>
        <br/>
        <br/>
        <p align="center"> <i>Remember: Enter the name of the person who compiled the FCDRR</i></p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">    
    $("table").tablecloth({
        bordered: true,
        condensed: true,
        striped: true,            
        clean: true,                
    });
    
    $('#calc').css('color','#0a6115');
    $('#calc').css('text-align','center');
    $('#calc').css('font-size','14px');
    $('#commodity_unit_type_0').append('Tests');
    $('#commodity_unit_type_1').append('Tests');
    $('#commodity_unit_type_2').append('Tests');
    $('#commodity_unit_type_3').append('Tests');
    $('#commodity_unit_type_4').append('Pieces');
    $('#commodity_unit_type_5').append('Pieces');
    $('#commodity_unit_type_6').append('Pieces');

</script>


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
</style>
<script type="text/javascript">
$(function() {
    jQuery(document).ready(function() {

        $("#begin_date").datepicker({
            defaultDate: "",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
                // onClose : function(selectedDate) {
                // 	$("#end_date").datepicker("option", "minDate", selectedDate);
                // }
            });
        $("#end_date").datepicker({
            defaultDate: "",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
                // onClose : function(selectedDate) {
                // 	$("#begin_date").datepicker("option", "minDate", selectedDate);
                // }
            });
    });


    $('#save1').click(function() {

        $('#myform').submit();
    });

});


</script>

<?php



$orderid = $order_id;

// foreach ($all_details as $detail) {
   
// }
$detail = $all_details[0];

$detail['total_tests'] = 
                $detail['calibur_pead'] +
                $detail['calibur_adults'] +
                $detail['cyflow_pead'] +
                $detail['cyflow_adults'] +
                $detail['count_adults'] +
                $detail['count_pead'] +
                $detail['pima_tests'] +
                $detail['presto_tests'] ;

?>
<style>
.converts{

}
.converts:hover{
    color: #fff;
}
table{
    font-size: 11px;
}
</style>

<div style="width:100%;color:#0000FF;float: left;margin-left:100px;">

    <!-- <div  class ="converts"  style="width:40px; float: left;">
        <a href="<?php echo site_url('rtk_management/get_lab_report/' . $this->uri->segment(3) . '/excel'); ?>">
            <img src="<?php echo site_url('assets/img/excel-icon.png'); ?>" style="margin-left:5px;width:100%;height:35px;" />
        </a>
    </div -->
<h1 style="color:green" align="center"><b>CD4 Commodity Orders for <?php echo $all_details[0]['facility_name']?></b></h1>
   
</div>
   
    <div id="dialog-form" title="Lab Commodities Order Report">
        

           
            <table id="user_order" width="90%" class="data-table" style="font-size: 14px;">


            <input  type="hidden" name="facility_name" colspan = "3" style = "color:#000; border:none" value="<?php echo $facility_name ?>"></td>
            <input type="hidden" name="facility_code" colspan = "2" style = "color:#000; border:none" value="<?php echo $facility_code ?>"></td>
            <input type="hidden" name="district_name" colspan = "2" style = "color:#000; border:none" value="<?php echo $district ?>"></td>
            <input type="hidden" name="county" colspan = "3" style = "color:#000; border:none" value="<?php echo $county ?>"></td>

            <tr>
                
                <td style = "text-align:right"><b>Name of Facility:</b></td>
                <td colspan = "2"><?php echo $all_details[0]['facility_name'] ?></td>
                <td rowspan = "8" style="background: #fff;"></td>
                <td colspan = "4"><b></b></td>
                <td rowspan = "8" style="background: #fff;"></td>
                <td colspan = "6" style="text-align:center"><b></b></td>
            </tr>
            <tr >
                <td colspan = "2" style = "text-align:right"><b>MFL Code:</b></td>
                <td><?php echo $all_details[0]['facility_code'] ?></td>
                <td colspan = "2" style="text-align:center"><b>CD4 Machine </b></td>
                <td colspan = "2" style="text-align:center"><b>No. of Tests Done <br/>[Adults &nbsp; &nbsp; |&nbsp; &nbsp; &nbsp; &nbsp; Pead] </b></td>
                <td colspan = "6"><b>Tests</b></td>                         
            </tr>
            <tr>
                <td colspan = "2" style = "text-align:right"><b>District:</b></td>
                <td><?php echo $all_details[0]['district_name'] ?></td>
                <td colspan = "2" align="right"><b>Facs Calibur</b></td>
                <td><?php echo $detail['calibur_pead']; ?></td>
                <td><?php echo $detail['calibur_adults']; ?></td>
                <td colspan = "3">Total Tests Done</td>   
                <td colspan="4"><?php echo $detail['total_tests']; ?></td>                    

            </tr>
            <tr>
                <td colspan = "2" style = "text-align:right"><b>County:</b></td>                     
                <td><?php echo $all_details[0]['county_name'] ?></td>
                <td colspan = "2" align="right"><b>Facs Count</b></td>
                <td><?php echo $detail['count_adults']; ?></td>
                <td><?php echo $detail['count_pead']; ?></td> 
                <td colspan = "3">Adults Below 500 CD4 count</td>   
                <td colspan="4"><?php echo $detail['adults_bel_cl']; ?></td>         

            </tr>
            <tr>
                <td colspan = "2" style = "text-align:right"><b>Beginning:</b></td> 
                <td><?php echo $beg_date ?></td>
                <td colspan = "2" align="right"><b>Cyflow Partec</b></td>
                <td><?php echo $detail['cyflow_pead']; ?></td>
                <td><?php echo $detail['cyflow_adults']; ?></td>
                <td colspan = "3">Peads Below 500 CD4 count</td>   
                <td colspan="4"><?php echo $detail['pead_bel_cl']; ?></td>  
            </tr>
            <tr>
                <td colspan = "2" style = "text-align:right"><b>Ending:</b></td>
                <td><?php echo $end_date ?></td>
                <td colspan = "2" align="right"><b>Alere PIMA</b></td>
                <td colspan = "2"><?php echo $detail['pima_tests']; ?> </td>  
                <td colspan = "8" style="text-align:center"><b></b></td>                     
            </tr>
            <tr >
                <td colspan = "3"></td>
                <td colspan = "2" align="right"><b>Facs Presto</b></td>
                <td colspan = "2"><?php echo $detail['presto_tests']; ?> </td>  
                <td colspan = "8" style="text-align:center"><b></b></td>
                
            </tr>
                <tr style="height:15px"><td colspan="16"></td></tr>
                
                <tr> 				
                    <th rowspan = "2" colspan = "2" style = "text-align:center;font-size:14"><b>Category Name</b></th>
                    <th rowspan = "2" colspan = "2" style = "text-align:center;font-size:14"><b>Commodity Name</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Beginning Balance</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Quantity Received</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Quantity Used</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Number of Tests Done</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Losses</b></th>
                    <th colspan = "2" style = "text-align:center;font-size:14"><b>Adjustments [indicate if (+) or (-)]</b></th>	
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>End of Month Physical Count</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Quantity Expiring in <u>less than</u> 6 Months</b></th>
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Days out of Stock</b></th>	
                    <th rowspan = "2" style = "text-align:center;font-size:14"><b>Quantity Requested for&nbsp;Re-Supply</b></th>
                </tr>
                <tr>
                    <th style = "text-align:center">Positive</th>
                    <th style = "text-align:center">Negative</th>
                </tr>
                <?php $checker = 0;
                foreach ($all_details as $detail) {
                    ?>
                    <tr>
                        <td colspan = "2" style = "text-align:left"><b><?php echo $detail['category_name']; ?></b></td>		    
                        <td class="commodity_names" id="commodity_name_<?php echo $checker;?>" colspan = "2" style = "text-align:left"></b><?php echo $detail['commodity_name']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['beginning_bal']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['q_received']; ?></td>
                        <!-- <td style = "text-align:center"><?php echo $detail['q_received_others']; ?></td> -->
                        <td style = "text-align:center"><?php echo $detail['q_used']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['no_of_tests_done']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['losses']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['positive_adj']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['negative_adj']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['closing_stock']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['q_expiring']; ?></td>
                        <td style = "text-align:center"><?php echo $detail['days_out_of_stock']; ?></td>	
                        <td style = "text-align:center"><?php echo $detail['q_requested']; ?></td>
                    </tr>
                    <?php $checker++;
                }
                ?>
                <tr>
                    <td colspan = "16"><br/></td>
                </tr>
                <tr>				
                    <td colspan = "16" style = "text-align:left">Explain Losses and Adjustments: <?php echo $explanation ?></td>
                </tr>
                <tr>				<td colspan = "4" style = "text-align:left"><b>Order for Extra LMIS tools:<br/> To be requested only when your data collection or reporting tools are nearly full. Indicate quantity required for each tool type.</b></td>
                    <td colspan = "4"><b>(1) Daily Activity Register for Laboratory Reagents and Consumables (MOH 642):</b></td>
                    <td colspan = "2" style="text-align:center;"><?php echo $moh_642 ?></td>
                    <td colspan = "4"><b>(2) F-CDRR for Laboratory Commodities (MOH 643):</b></td>
                    <td colspan = "2" style="text-align:center;"><?php echo $moh_643 ?></td>
                </tr>	


                <tr>					<td colspan = "4" style = "text-align:left">Compiled by: <?php echo $compiled_by ?></td>
                    <td colspan = "3" style = "text-align:left">Tel: <?php // echo $phone_no ?></td>
                    <td colspan = "3" style = "text-align:left">Designation: <?php echo $designation ?></td>
                    <td colspan = "3" style = "text-align:left">Sign:</td>
                    <td colspan = "3" style = "text-align:left">Date: <?php echo $order_date ?></td>
                </tr>

                <tr>					<td colspan = "4" style = "text-align:left">Approved by:</td>
                    <td colspan = "3" style = "text-align:left">Tel:</td>
                    <td colspan = "3" style = "text-align:left">Designation:</td>
                    <td colspan = "3" style = "text-align:left">Sign:</td>
                    <td colspan = "3" style = "text-align:left">Date:</td>
                </tr>


            </table></form>
        </div>
        <!--<input  class="btn btn-primary" id="save1" name="save1"  value="Edit Order" >-->
        <?php form_close(); ?>
        <script type="text/javascript">
        $("table").tablecloth({
            bordered: true,
            condensed: true,
            striped: true,            
            clean: false,            
        });
       
        </script>
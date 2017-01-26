
<div class="main-container" style="width: 60%; align:center;">

<div class="span12" style="align:center; font-size:16px;  width:100% margin-left: 10%"> 
	<b>Available amount of Kits </b><br/>
	Screening: <?php echo $result[0]['screening_current_amount']?>, Confirmatory: <?php echo $result[0]['confirmatory_current_amount']?>. <br/><br/>
</div>

<?php

	$attributes = array('name' => 'myform', 'id' => 'myform');
    echo form_open('rtk_management/submit_district_allocation_report', $attributes);

    
?>
<form id="myform">
<input type="hidden" id="countyid" name="county_id" value="<?php echo $countyid;?>"> 
  <input type="hidden" name="district_id" id = "district_id" value="<?php echo $districtid;?>"> 
  <input type="hidden" id="screening_current_amount" value="<?php echo $result[0]['screening_current_amount'];?>"> 
  <input type="hidden" id="confirmatory_current_amount" value="<?php echo $result[0]['confirmatory_current_amount'];?>"> 
  <input type="hidden" id="tiebreaker_current_amount" value="<?php echo $result[0]['tiebreaker_current_amount'];?>">

  <table id="edit_table" class="table" align="center">
  	<tr>
  		<th>Sub County</th>
  		<th>Drawing Rights: Screening</th>
  		<th>Drawing Rights: Confirmatory</th>
  	</tr>
  	<?php 
    foreach ($result as $key => $value) {
    	?>
  	<tr>
  		<td><?php echo $value['district'];?></td>
  		<td><input class="screening_input" type="text" name="sc_amount_s"></td>
  		<td><input class="confirm_input" type="text" name="sc_amount_c"></td>
  	</tr>
  	<?php } ?>
  </table>
</form>
<?php form_close(); ?>
<br/>
<br/>

<input class="btn btn-primary" type="submit"   id="confirm"  value="Edit" style="margin-left: 50%; " >

</div>
<script>
$(document).ready(function() {
 
  // $('#edit_table').dataTable({
  //    "sDom": "T lfrtip",
  //    "aaSorting": [],
  //    "bJQueryUI": false,
  //     "bPaginate": false,
  //     "oLanguage": {
  //       "sLengthMenu": "_MENU_ Records per page",
  //       "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
  //     },
  //     "oTableTools": {
  //     "aButtons": [      
      
  //     ],  
  //     "sSwfPath": "<?php echo base_url();?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
  //   }
  // });
  $("#edit_table").tablecloth({theme: "paper",         
    bordered: true,
    condensed: true,
    striped: true,
    sortable: true,
    clean: true,
    cleanElements: "th td",
    customClass: "data-table"
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
        // $('.tiebreaker_input').each(function() {
        //   sum_tiebreaker += Number($(this).val());

        // });
       
        var new_screening_amount = screening_current_amount - sum_screening;
        var new_confirmatory_amount = confirmatory_current_amount - sum_confirm;
        // var new_tiebreaker_amount = tiebreaker_current_amount - sum_tiebreaker;
           // alert(new_screening_amount);

        if (sum_screening>screening_current_amount) {
         alert('The available amount of Screening is less the the amount you have issued. Available Amount: '+screening_current_amount+'<br/> Hint: Please check the values you entered');
        } else if (sum_confirm>confirmatory_current_amount) {
          alert('The available amount of Confirmatory is less the the amount you have issued. Available Amount: '+confirmatory_current_amount+' <br/> Hint: Please check the values you entered');
        }
        // else if (sum_tiebreaker>tiebreaker_current_amount) {
        //   alert('The available amount of TieBreaker is less the the amount you have allocated. <br/>Available Amount: '+tiebreaker_current_amount+' <br/> Hint: Please check the values you entered');
        // }
        else{
          $('#message').html(' Please Wait');                                         
          $('#message').css('font-size','13px');                                         
          $('#message').css('color','green'); 
        
          
          var url = "<?php echo base_url() . 'rtk_management/edit_district_allocation_report'; ?>";
                    
          var data = $('#myform').serializeArray();
          data.push({name: 'new_screening_amount', value: new_screening_amount},{name: 'new_confirmatory_amount', value: new_confirmatory_amount});
          // $.ajax({
          //   url : url, 
          //   type : 'POST',
          //   data : data,
          //       success : function (response) {                  
                                    
          //               console.log(data);
          //       }
          //   });
               
       }
        
    });
});
</script>
<style type="text/css">
	input{
    border: 1px solid #C0C0C0 ; 
    border-radius: 15px;
    background: #F0F0F0 ;
    margin: 0 0 10px 0;
    width: auto;
}
</style>

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
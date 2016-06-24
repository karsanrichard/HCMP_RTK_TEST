
<?php 
include('rtk/rtk/clc/rca_sidabar.php'); ?>
<div class="dash_main" style="margin-left: 260px; margin-top: 10px;">
<h3> <?php echo $banner_text;?></h3><br/> <br/>
<div class="clc_contents">


				<div class="accordion-group" style="width: 70%;">
					<div id="bycons" class="table_divs panel panel-success">
					  <div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#facilties_details" style="font-size:13px;font-weight:bold;" >CD4 FAcilities in <?php echo $county_name;?> County</div>
					  <div id="facilties_details" class="accordion-body collapse">
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">
						
						<table id="reporting_summary_table" class="table"  style="width: 80%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-top:1px solid #ddd; border-left:1px solid #ddd;float: left; margin-top:10px; margin-left:7%;">
							<thead>
								<th>MFL Code</th>
							    <th>Name</th>
							    <th>Sub-County</th>
							    <th>Device Used</th>    
							    <th>Reporting Status</th>
							    <th>Action</th>
							</thead>
							<tbody>
								<?php foreach ($cd4_facilities as $row) { 
								   $code =$row['facility_code'];
								   ?>
								    <tr id="<?php echo $row['facil_id'];?>">    
								    <td><?php echo $code; ?></td>
								    <td><?php echo $row['facility_name'];?></td>
								    <td><?php echo $row['districtname'];?></td>
								    <td><?php echo $row['device_name'];?></td>
								    <td><?php if($row['cd4_enabled']==0)
								    {

								      echo "Non-Reporting";
								      echo ' <a href="../cd4_management/activate_facility/' . $row['facility_code'] . '" title="Add"><span class="glyphicon glyphicon-plus"></span> </i></a>';


								    }
								    else
								      {
								        echo "Reporting";
								        echo ' <a href="../cd4_management/deactivate_facility/' . $row['facility_code'] . '" title="Remove"><span class="glyphicon glyphicon-minus"></span> </i></a>';
								      }?></td>

								  <td><?php if($row['cd4_enabled']==0)
								    {      
								      echo 'N/A';


								    }
								    else
								      {        
								        echo ' <a href="../rtk_management/facility_profile/' . $code. '">View</a>';
								      }?></td>
								  </tr>
								<?php }?>
							</tbody>
			            </table>
             		</div>
					</div>
					</div>
				</div>

				<div class="accordion-group" style="width: 70%;">
					<div id="bycons" class="table_divs panel panel-success">
					  <div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#facilties_reporting_details" style="font-size:13px;font-weight:bold;" >Facilities Reporting Rate Details</div>
					  <div id="facilties_reporting_details" class="accordion-body collapse">
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">
						

						<table id="reporting_summary_table" class="table" style="width: 100%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;">
							<tr>
								<td> <b>Type</b></td>				
								<td> <b>Number</b></td>				
								<td> <b>Percentage</b></td>
							</tr>
							<tr>
								<td>Total Number of Facilities</td>				
								<td> <?php echo  $total_facilities;?></td>				
								<td> 100%</td>
							</tr>
							<tr>
								<td>Facilities which Reported </td>				
								<td> <?php echo  $reported_facilities;?></td>				
								<td> <?php echo  $reported_facilities_percentage;?>%</td>
							</tr>				
							<tr>
								<td>Facilities with no Reports</td>				
								<td> <?php echo  $nonreported_facilities;?></td>				
								<td> <?php echo  $nonreported_facilities_percentage;?>%</td>					
							</tr>
						</table>

			            <table class="table" style="width: 40%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left; margin-left: 15px;">
			               <tr>
			               	<td colspan="2" align="center"><b>Facilities with reports</b></td>               	
			               </tr>
			               <tr>
			               	<td>Facility Code</td>
			               	<td>Facility Name</td>               	
			               </tr>
			               <tr>
			               	<?php 
			               		foreach ($reported_facilities_text as $key => $value) {   ?>
					               	<td> <?php echo $value['facility_code']?></td>
					               	<td><?php echo $value['facility_name']?></td>
			               </tr>
							<?php } ?>
			            </table>

			            <table class="table" style="width: 40%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: right; margin-left: 15px;">
			               <tr>
			               	<td colspan="2" align="center"><b>Facilities with No reports</b></td>               	
			               </tr>
			               <tr>
			               	<td>Facility Code</td>
			               	<td>Facility Name</td>               	
			               </tr>
			               <tr>
			               	<?php 
			               		foreach ($reported_facilities_text as $key => $value) {   ?>
					               	<td> <?php echo $value['facility_code']?></td>
					               	<td><?php echo $value['facility_name']?></td>
			               </tr>
							<?php } ?>
			            </table>
             		</div>
					</div>
					</div>
				</div>	

		
	<div class="accordion-group" style="width: 70%;">
		<div id="by_dates" class="table_divs panel panel-success">
		<div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#by_date" style="font-size:13px;font-weight:bold" >Stock Card<i>(Click to View)</i></div>
		 <div id="by_date" class="accordion-body collapse">
			  <div class="accordion-inner div_height" >
					<?php 
					$commodity_count = 0;
        // echo "<pre>"; print_r($category_details);

					foreach ($category_details as $key => $all_details) {	?>
						<div class="accordion-group" style="width: 90%;float: left; margin-top: 3px;  ">
					<div id="bycons" class="table_divs panel panel-warning">
					  <div class="panel-heading accordion-heading" style="font-size:13px;font-weight:bold;" data-toggle="collapse"  href="#stock_card_<?php echo $commodity_count;?>"  ><?php echo $category_details[$commodity_count][0]['category_name'];?></div>
					  <div class="accordion-body collapse" id="stock_card_<?php echo $commodity_count;?>" >
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">
						<table class="table" style="font-size:13px;">
					<tr>
	                            <th>Kit</th>
	                            <th>Beginning Balance</th>
	                            <th>Received Quantity</th>
	                            <th>Used Total</th>
	                            <th>Total Tests</th>
	                            <th>Positive Adjustments</th>
	                            <th>Negative Adjustments</th>
	                            <th>Closing Balance</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <?php
						//foreach ($all_details as $keys => $commodity_details) {
				for ($i=0; $i < count($category_details); $i++) { ?>
					
	                        <tr>
	                            <td><?php echo $all_details[$i]['commodity_name']; ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_opening'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_received'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_used'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_tests'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_positive'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_negative'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($all_details[$i]['sum_closing_bal'], $decimals = 0); ?></td>
	                        </tr> 
	                        <?php 	} $commodity_count++;?>                                              
	                        </tbody>
	                        </table>
	                        </div>
             		</div>
					</div>
					</div>
			<?php 	
			 }//}?>
				</div>		
				

	
</div>
</div>
</div>
				

<style type="text/css">
	.table{
		font-size: 11px;
		/*margin: 1%;	*/
		width: 100%;	
	}
	.header_tr{
		background-color:#f5f5f5;
	}
	.table th{
		text-align: center;
	}

	.clc_contents  h6{		
		font-weight: bold;
		color: green;
		margin-top: 1%;
		margin-left: 1%;
	}
	.clc_contents {		
		
		width: 96%;
	}
	.inner_divs{
		margin-top: 5px;
		width: 90%;
		/*height: 20%;*/
	}
	.div_height{
		margin-left: 1%;
		margin-top: 5px;
		height: auto;

	}
	.inner_div_height{
		margin-left: 1%;
		margin-top: 3%;
		height: 50%;

	}
	.table_divs{
		/*margin-top: 1%;*/
		margin-left: 1%;
		/*border: 1px dotted green;*/
		width: 100%;
		height: auto;
	}
	.select_options{
		width:25%;
		float:left;
		height:30px;
		font-size:12px;
		 margin-left: 50px
	}
	
	
</style>
<script type="text/javascript">
	$(document).ready(function (e){
	
	county_id = <?php echo $county ?>;		
	
		$.ajax({

            url: "<?php echo base_url() . 'allocation_management/get_counties_districts/'; ?>" +county_id,
            dataType: 'json',
            success: function(s){
            	console.log(s);
                var districtslist = s.district_county_list;    
				$('#subcounty_select').html(districtslist);
                
            },
            error: function(e){
                console.log(e.responseText);
            }            
        }); 
	

	function requestData(){
		county_ids = <?php echo $county ?>;		

        $.ajax({

            url: "<?php echo base_url() . 'allocation_management/get_county_reporting_trend/'; ?>" +county_ids,
            dataType: 'json',
            success: function(s){
            	console.log(s);
                // var months_texts = s.months_texts;    
                // var percentages = s.percentages;    
                // var months_texts = s.months_texts;    
				
                chart.addSeries({
	              name: "New Rates",
	              data: s.percentages
	            });
            },
            error: function(e){
                console.log(e.responseText);
            }            
        }); 
    }

         $('#trend-chart').highcharts({
                chart: {
                    type: 'line'
                //     events: {
			             //    	load: requestData
			           		// }
                },
                title: {
                    text: 'RTKs Reporting Rate from <?php echo $first_month; ?> to <?php echo $last_month; ?>',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Live Data From RTK System',
                    x: -20
                },
                xAxis: {
                    categories: <?php echo $months_texts ?>
                },
                  yAxis: {
                      title: {
                          text: 'Reports Submission (%)'
                      },
                      plotLines: [{
                          value: 0,
                          width: 1,
                          color: '#009933'
                      }]
                  },
                  tooltip: {
                      valueSuffix: '%'
                  },
                 
                  plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                credits: false,
                series: [{
                    color: '#009933',
                    name: 'Reporting Rates',
                    data: <?php echo $percentages;?>
                }]
            });
       $('#btn_consumption_select').button().click(function(e) {               
         
			var type = $('#fcdrr_commodity_select').val();			
			var month = $('#date_select').val();			
			var district = $('#subcounty_select').val();			
			var county = <?php echo $county ?>;	
			var commodity = $('#commodity_select').val();
			var phpfunction = 'get_fcdrr_details';	

			if (type >0) {
				 phpfunction = 'get_one_fcdrr_details';
			}		

	        $('#message').html('The report for all FCDRR details is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/'; ?>"+phpfunction+'/'+type+'/'+month+'/'+county+'/'+district+'/'+commodity;
			// window.location = url;
       		alert(url);
		});
        $('#btn_all_facilities').button().click(function(e) {               
         
	        $('#message').html('The report for all Facilities AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_all_facilities_amcs'; ?>";
			window.location = url;
       
		});
		$('#btn_zone_a').button().click(function(e) {               
         
	        $('#message').html('The report for Zone A AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_zonal_facilities_amcs/A'; ?>";
			window.location = url;
       
		});
		$('#btn_zone_b').button().click(function(e) {               
         
	        $('#message').html('The report for Zone B AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_zonal_facilities_amcs/B'; ?>";
			window.location = url;
       
		});
		$('#btn_zone_c').button().click(function(e) {               
         
	        $('#message').html('The report for Zone C AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_zonal_facilities_amcs/C'; ?>";
			window.location = url;
       
		});
		$('#btn_zone_d').button().click(function(e) {               
         
	        $('#message').html('The report for Zone D AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_zonal_facilities_amcs/D'; ?>";
			window.location = url;
       
		});
		$('#btn_screening').button().click(function(e) {               
         
	        $('#message').html('The report for SCREENING-KHB AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_commodity_facilities_amcs/4'; ?>";
			window.location = url;
       
		});
		$('#btn_confirmatory').button().click(function(e) {               
         
	        $('#message').html('The report for CONFIRMATORY-First Response AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_commodity_facilities_amcs/5'; ?>";
			window.location = url;
       
		});
		$('#btn_tiebreaker').button().click(function(e) {               
         
	        $('#message').html('The report for Tie Breaker AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_commodity_facilities_amcs/5'; ?>";
			window.location = url;
       
		});
		$('#btn_dbsbundles').button().click(function(e) {               
         
	        $('#message').html('The report for DBS Bundles AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_commodity_facilities_amcs/22'; ?>";
			window.location = url;
       
		});

		// $('#btn_county_select').button().click(function(e) {               
		// 	var county_id = $('#county_select').val();			
         
	 //        $('#message').html('The report for County AMCs is being generated. Please Wait...');                                         
	 //        $('#message').css('font-size','13px');                                         
	 //        $('#message').css('color','green');  
	 //        var url = "<?php echo base_url() . 'allocation_management/get_county_facilities_amcs/'; ?>";
		// 	window.location = url+county_id;
       
		// });
		$('#btn_subcounty_select').button().click(function(e) {               
         
			var subcounty_id = $('#subcounty_select').val();			
	        $('#message').html('The report for Sub-County AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_subcounty_facilities_amcs/'; ?>";
			window.location = url+ subcounty_id;
       
		});
		$('#btn_monthly_select').button().click(function(e) {               
         
			var month = $('#date_select').val();			
	        $('#message').html('The report for Monthly AMCs is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/get_months_facilities_amcs/'; ?>";
			window.location = url+ month;
       
		});
		
		         

           
   
$('#consumption-chart').highcharts({
                chart: {
            type: 'line'
        },
        title: {
            text: '<?php echo ' Yearly Commodity Usage:  ' . $commodity_name.' from '. $from_date.' to '. $to_date; ?>'
        },
        subtitle: {
            text: 'Live data reports on RTK'
        },
        xAxis: {
            categories: <?php echo $graphdata['month']; ?>
        },
        yAxis: {
            min: 0,            
            title: {
                text: 'Quantity (Tests)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:12px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:10px">{series.name}: </td>' +
                '<td style="padding:0;font-size:10px"><b>{point.y:.1f}</b;></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Quantity Used',
            data: <?php echo $graphdata['qty_used']; ?>
       
        
        }]
            });
   
		
		// $('#subcounty_select').on('change', function() {
	 // 		region = 4;
	 // 		alert(region); // or $(this).val()
	 // 		// if(region=='zone'){
	 // 		// 	$('#combination_zone_select').show();
	 // 		// }
	 // 		// else if(region=='county'){
	 // 		// 	$('#combination_county_select').show();

	 // 		// }else if(region=='district'){
	 // 		// 	$('#combination_subcounty_select').show();

	 // 		// }
		// });

	});
// $(document).ajaxStart(function(){
// 	    $('#loading').show();
// 	 }).ajaxStop(function(){
// 	    $('#loading').hide();
// 	 });
</script>


	
</div>
<div class="modal" id="loading">
	
</div>
<style type="text/css">
	.modal
	{
	    display:    none;
	    position:   fixed;
	    z-index:    1000;
	    top:        0;
	    left:       0;
	    height:     100%;
	    width:      100%;
	    background: rgba( 255, 255, 255, .8 ) 
	                url('<?php echo base_url();?>assets/img/new_loader.gif') 
	                50% 50% 
	                no-repeat;
	}

	/* When the body has the loading class, we turn
	   the scrollbar off with overflow:hidden */
	body.loading {
	    overflow: hidden;   
	}

	/* Anytime the body has the loading class, our
	   modal element will be visible */
	body.loading .modal {
	    display: block;
	}

</style>

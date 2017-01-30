
<?php 
include('rca_sidabar.php');

    $county = $this->session->userdata('county_id');   
    $closing_stock_s = $county_data_s['endbal'];              
    $closing_stock_c = $county_data_t['endbal'];              
    $closing_stock_t = $county_data_c['endbal'];  
    $expiries_s = $county_data_s['expiries'];              
   	$expiries_c = $county_data_t['expiries'];              
   	$expiries_t = $county_data_c['expiries']; 
?>
<div class="dash_main" style="margin-left: 260px; margin-top: 10px;">
<h3> <?php echo $banner_text;?></h3><br/> <br/>
<div class="clc_contents">


		  <!-- <button id="btn_all_facilities" class=" btn btn-success">All Facilities</button> -->
		
<div class="accordion-group">
		<div id="consumption_details" class="table_divs panel panel-success">
			<div class="panel-heading accordion-heading " data-toggle="" data-parent="#accordion2" href="#kit_consumption" style="font-size:13px;font-weight:bold" >DOWNLOAD CONSUMPTION DATA: <i>(Click to View)</i></div>
			 <div id="kit_consumption" class="accordion-body  ">
				<div class="accordion-group inner_divs">
					<p style="margin-top: 10px; margin-left: 5%">Select Type of Report:</p>
				<div class="accordion-group">
			<select id="county_select" class="form-control select_options"> By County
				<option value=""> -- All Counties--</option>							
			</select>&nbsp;&nbsp;&nbsp;
			<select id="subcounty_select" class="form-control select_options"> By Region
				<option value=""> -- All Sub-Counties--</option>							
			</select>&nbsp;&nbsp;&nbsp;
			<select id="commodity_select" class="form-control select_options" > By Commodity
				<option value="0"> -- All Commodities--</option>							
				<option value="4"> Screening</option>							
				<option value="5"> Confirmatory</option>							
				<option value="6"> Tie Breaker</option>							
			</select>&nbsp;&nbsp;&nbsp;
			<select id="date_select" class="form-control select_options" > By Period
							
							<?php 

					            for ($i=1; $i <=12 ; $i++) { 
					            $month = date('m', strtotime("-$i month")); 
					            $year = date('Y', strtotime("-$i month")); 
					            $month_value = $month.$year;
					            $month_text =  date('F', strtotime("-$i month")); 
					            $month_text = $month_text." ".$year;
					         ?>

					        <option value="<?php echo $month_value ?>"><?php echo $month_text ?></option>;
						    <?php } ?>
						  </select>&nbsp;&nbsp;&nbsp;
		
			
	</div>	<br/>
					
					<select id="fcdrr_commodity_select" class="form-control select_options" > By Commodity
						<option value="0"> -- All FCDRR Details--</option>							
						<option value="1"> Ending Balances</option>							
						<option value="2"> Quantity Used</option>							
						<option value="3"> Tests Done</option>							
						<option value="4"> AMC</option>							
					</select>&nbsp;&nbsp;&nbsp;

		            <button type="button" id="btn_consumption_select" class="btn btn-primary my_navs">Download Consumption Report</button>
						  	
				</div>
			</div>
		</div>
	</div>
	<div class="accordion-group" style="margin-top: 50px; ">
		<div id="by_commodities" class="table_divs panel panel-success">
			  <div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#graphs_mainj" style="font-size:13px;font-weight:bold" >GRAPHS <i>(Still in progress)</i></div>
			  <div id="graphs_main" class="accordion-body collapse"  >
			  <div class="accordion-inner div_height">

				<div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; overflow-y: scroll; height: 350px">
					<div id="bycons" class="inner_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle=""  href="#trend_graphs" style="font-size:13px;font-weight:bold;" >Yearly Reporting Trend</div>
					  <div id="trend_graphs" class="accordion-body ">
					  <div class="accordion-inner ">
						 
						<div id="trend-chart" class="accordion-body "></div>
						
					  </div>
					  </div>
					</div>
				</div>
				<div class="accordion-group" style="width: 49%;float: left; margin-left: 5px; overflow-y: scroll; height: 350px">
					<div id="bycons" class="inner_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle=""  href="#cons_graphs" style="font-size:13px;font-weight:bold" >Yearly Consumption Trend</div>
					  <div id="cons_graphs" class="accordion-body  ">
					  <div class="accordion-inner ">
						 
						<div id="consumption-chart" class="accordion-body section_data"> </div>
							
					  </div>
					  </div>
					</div>
				</div>		
			</div>
			</div>
		</div>
	</div>		
	<div class="accordion-group" >
		<div id="by_dates" class="table_divs panel panel-success" style="overflow-y: scroll;">
		<div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#by_dateh" style="font-size:13px;font-weight:bold" >FACILITIES<i>(Still in progress)</i></div>
		 <div id="by_date" class="accordion-body collapse">
			  <div class="accordion-inner div_height" >
				
				<div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; ">
					<div id="bycons" class="table_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#facilties_details" style="font-size:13px;font-weight:bold;" >Reporting Rate Details</div>
					  <div id="facilties_details" class="accordion-body collapse">
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">
						<p style="font-size: 15px; color: #080f73"> <b><?php echo $reported_facilities ?> </b>facilities reported on time, <b><?php echo $late_reported_facilities[0]['late_facilities'] ?></b> reported past 15th and <b><?php echo $nonreported_facilities ?></b> did not report at all</p>

						<table id="reporting_summary_table" class="table" style="width: 50%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
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
             		</div>
					</div>
					</div>
				</div>	
				<div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; ">
					<div id="bycons" class="table_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle="collapse"  data-parent="#accordion2" href="#stock_card" style="font-size:13px;font-weight:bold;" >Stock Card</div>
					  <div id="stock_card" class="accordion-body collapse">
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">						

						<table class="table" style="font-size:13px;">
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
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <?php
	                        $count = count($county_summary);
	                        if($count==0){?>
	                        <tr>
	                            <td><?php echo "N/A" ?></td>
	                            <td>0</td>
	                            <td>0</td>
	                            <td>0</td>
	                            <td>0</td>
	                            <td>0</td>
	                            <td>0</td>
	                            <td>0</td>
	                            <td>0</td>
	                        </tr>                                               

	                        <?php }else{
	                        for ($i=0; $i <count($county_summary) ; $i++) {?>
	                        <tr>
	                            <td><?php echo $county_summary[$i]['commodity_name']; ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_opening'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_received'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_used'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_tests'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_positive'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_negative'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_losses'], $decimals = 0); ?></td>
	                            <td><?php echo number_format($county_summary[$i]['sum_closing_bal'], $decimals = 0); ?></td>
	                        </tr>                                               

	                        <?php }}
	                        ?>                   
	                </tbody>
	            	</table>
		            
             		</div>
					</div>
					</div>
				</div>		
				<div class="accordion-group" style="width: 49%;float: left; margin-left: 3px;  ">
					<div id="bycons" class="table_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle="collapse"  href="#high_stocks" style="font-size:13px;font-weight:bold;" >Facilities with High Stocks</div>
					  <div id="high_stocks" class="accordion-body collapse">
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">
						
						<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#screening_closing" style="margin-left:20px">Screening <p style="font-size:16px;"><b>+</b></p></button>
 						<div id="screening_closing" class="collapse">    
  
						<table class="table" style="width: 95%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
						<thead>
							<th>Sub County</th>
							<th>Facility Code</th>
							<th>Facility Name</th>
							<th>Stock at Hand (Tests)</th>
							</thead>
							<tbody>
							<tr>
								<?php     
		       					//ending balalnce Screening table      
		       
						        if(count($closing_stock_s) ==0){?>
						        	<td colspan = "4" align="center">No facilities have high stocks</td>
						        	</tr>
						        	<?php
						        }else{ ?>
						      
						        <tr>       
						          <?php
						        
						       		foreach ($closing_stock_s as $key => $value) {		?>

						           	<td><?php echo $value['district'];?></td>
						           	<td><?php echo $value['facility_code'];?></td>
						           	<td><?php echo $value['facility_name'];?></td>
						           	<td><?php echo $value['closing_stock'];?></td>
						        </tr>
						       <?php 
						       		 }
						     
						   		 }	?>
							</tbody>
				    	</table>    
						</div>

		            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#confirmatory_closing" style="margin-left:20px">Confirmatory <p style="font-size:16px;"><b>+</b></p></button>
 						<div id="confirmatory_closing" class="collapse">    
  
						<table class="table" style="width: 95%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
						<thead>
							<th>Sub County</th>
							<th>Facility Code</th>
							<th>Facility Name</th>
							<th>Stock at Hand (Tests)</th>
							</thead>
							<tbody>
							<tr>
								<?php     
		       					//ending balalnce Screening table      
		       
						        if(count($closing_stock_c) ==0){?>
						        	<td colspan = "4" align="center">No facilities have high stocks</td>
						        	</tr>
						        	<?php
						        }else{ ?>
						      
						        <tr>       
						          <?php
						        
						       		foreach ($closing_stock_c as $key => $value) {		?>

						           	<td><?php echo $value['district'];?></td>
						           	<td><?php echo $value['facility_code'];?></td>
						           	<td><?php echo $value['facility_name'];?></td>
						           	<td><?php echo $value['closing_stock'];?></td>
						        </tr>
						       <?php 
						       		 }
						     
						   		 }	?>
							</tbody>
				    	</table>    
						</div>
						<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#tiebreaker_closing" style="margin-left:20px">Tie Breaker <p style="font-size:16px;"><b>+</b></p></button>
 						<div id="tiebreaker_closing" class="collapse">    
  
						<table class="table" style="width: 95%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
						<thead>
							<th>Sub County</th>
							<th>Facility Code</th>
							<th>Facility Name</th>
							<th>Stock at Hand (Tests)</th>
							</thead>
							<tbody>
							<tr>
								<?php     
		       					//ending balalnce Screening table      
		       
						        if(count($closing_stock_t) ==0){?>
						        	<td colspan = "4" align="center">No facilities have high stocks</td>
						        	</tr>
						        	<?php
						        }else{ ?>
						      
						        <tr>       
						          <?php
						        
						       		foreach ($closing_stock_t as $key => $value) {		?>

						           	<td><?php echo $value['district'];?></td>
						           	<td><?php echo $value['facility_code'];?></td>
						           	<td><?php echo $value['facility_name'];?></td>
						           	<td><?php echo $value['closing_stock'];?></td>
						        </tr>
						       <?php 
						       		 }
						     
						   		 }	?>
							</tbody>
				    	</table>    
						</div>
             		</div>
					</div>
					</div>
				</div>		

				<div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; ">
					<div id="bycons" class="table_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle="collapse"  href="#high_expiries" style="font-size:13px;font-weight:bold;" >Facilities with High Expiries</div>
					  <div id="high_expiries" class="accordion-body collapse">
					  <div class="accordion-inner div_height" style="overflow-y: scroll;">
						<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#screening_ending" style="margin-left:20px">Screening <p style="font-size:16px;"><b>+</b></p></button>
 						<div id="screening_ending" class="collapse">    
  
						<table class="table" style="width: 95%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
						<thead>
							<th>Sub County</th>
							<th>Facility Code</th>
							<th>Facility Name</th>
							<th>Stock at Hand (Tests)</th>
							</thead>
							<tbody>
							<tr>
								<?php     
		       					//ending balalnce Screening table      
		       
						        if(count($expiries_s) ==0){?>
						        	<td colspan = "4" align="center">No facilities have high stocks</td>
						        	</tr>
						        	<?php
						        }else{ ?>
						      
						        <tr>       
						          <?php
						        
						       		foreach ($expiries_s as $key => $value) {		?>

						           	<td><?php echo $value['district'];?></td>
						           	<td><?php echo $value['facility_code'];?></td>
						           	<td><?php echo $value['facility_name'];?></td>
						           	<td><?php echo $value['closing_stock'];?></td>
						        </tr>
						       <?php 
						       		 }
						     
						   		 }	?>
							</tbody>
				    	</table>    
						</div>

		            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#confirmatory_ending"style="margin-left:20px">Confirmatory <p style="font-size:16px;"><b>+</b></p></button>
 						<div id="confirmatory_ending" class="collapse">    
  
						<table class="table" style="width: 95%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
						<thead>
							<th>Sub County</th>
							<th>Facility Code</th>
							<th>Facility Name</th>
							<th>Stock at Hand (Tests)</th>
							</thead>
							<tbody>
							<tr>
								<?php     
		       					//ending balalnce Screening table      
		       
						        if(count($expiries_c) ==0){?>
						        	<td colspan = "4" align="center">No facilities have high stocks</td>
						        	</tr>
						        	<?php
						        }else{ ?>
						      
						        <tr>       
						          <?php
						        
						       		foreach ($expiries_c as $key => $value) {		?>

						           	<td><?php echo $value['district'];?></td>
						           	<td><?php echo $value['facility_code'];?></td>
						           	<td><?php echo $value['facility_name'];?></td>
						           	<td><?php echo $value['closing_stock'];?></td>
						        </tr>
						       <?php 
						       		 }
						     
						   		 }	?>
							</tbody>
				    	</table>    
						</div>
						<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#tiebreaker_ending" style="margin-left:20px">Tie Breaker <p style="font-size:16px;"><b>+</b></p></button>
 						<div id="tiebreaker_ending" class="collapse">    
  
						<table class="table" style="width: 95%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left">
						<thead>
							<th>Sub County</th>
							<th>Facility Code</th>
							<th>Facility Name</th>
							<th>Stock at Hand (Tests)</th>
							</thead>
							<tbody>
							<tr>
								<?php     
		       					//ending balalnce Screening table      
		       
						        if(count($expiries_t) ==0){?>
						        	<td colspan = "4" align="center">No facilities have high stocks</td>
						        	</tr>
						        	<?php
						        }else{ ?>
						      
						        <tr>       
						          <?php
						        
						       		foreach ($expiries_t as $key => $value) {		?>

						           	<td><?php echo $value['district'];?></td>
						           	<td><?php echo $value['facility_code'];?></td>
						           	<td><?php echo $value['facility_name'];?></td>
						           	<td><?php echo $value['closing_stock'];?></td>
						        </tr>
						       <?php 
						       		 }
						     
						   		 }	?>
							</tbody>
				    	</table>    
						</div>
		            
             		</div>
					  </div>
					</div>
				</div>		
		</div>
		</div>
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
		height: 400px;

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
		width:18%;
		float:left;
		height:30px;
		font-size:12px;
		 margin-left: 50px
	}
	
	
</style>
<script type="text/javascript">
	$(document).ready(function (e){
	
		// alert('yes');
	
		$.ajax({

            url: "<?php echo base_url() . 'allocation_management/get_counties/'; ?>" ,
            dataType: 'json',
            success: function(s){
            	console.log(s);
                var countylist = s.counties_list;    
				$('#county_select').html(countylist);
                
            },
            error: function(e){
                console.log(e.responseText);
            }            
        }); 
        $.ajax({

            url: "<?php echo base_url() . 'allocation_management/get_districts/'; ?>" ,
            dataType: 'json',
            success: function(s){
            	console.log(s);
                var district_list = s.district_list;    
				$('#subcounty_select').html(district_list);
                
            },
            error: function(e){
                console.log(e.responseText);
            }            
        }); 
	

	function requestData(){
		county_ids = 1;		

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

         // $('#trend-chart').highcharts({
         //        chart: {
         //            type: 'line'
         //        //     events: {
			      //        //    	load: requestData
			      //      		// }
         //        },
         //        title: {
         //            text: 'RTKs Reporting Rate from <?php echo $first_month; ?> to <?php echo $last_month; ?>',
         //            x: -20 //center
         //        },
         //        subtitle: {
         //            text: 'Live Data From RTK System',
         //            x: -20
         //        },
         //        xAxis: {
         //            categories: <?php echo $months_texts ?>
         //        },
         //          yAxis: {
         //              title: {
         //                  text: 'Reports Submission (%)'
         //              },
         //              plotLines: [{
         //                  value: 0,
         //                  width: 1,
         //                  color: '#009933'
         //              }]
         //          },
         //          tooltip: {
         //              valueSuffix: '%'
         //          },
                 
         //          plotOptions: {
         //            column: {
         //                pointPadding: 0.2,
         //                borderWidth: 0
         //            }
         //        },
         //        credits: false,
         //        series: [{
         //            color: '#009933',
         //            name: 'Reporting Rates',
         //            data: <?php echo $percentages;?>
         //        }]
         //    });
       $('#btn_consumption_select').button().click(function(e) {               
         
			var type = $('#fcdrr_commodity_select').val();			
			var month = $('#date_select').val();			
			var district = $('#subcounty_select').val();			
			var county = $('#county_select').val();	
			var commodity = $('#commodity_select').val();
			var phpfunction = 'get_fcdrr_details';	

			if (type >0) {
				 phpfunction = 'get_one_fcdrr_details';
			}		

	        $('#message').html('The report for all FCDRR details is being generated. Please Wait...');                                         
	        $('#message').css('font-size','13px');                                         
	        $('#message').css('color','green');  
	        var url = "<?php echo base_url() . 'allocation_management/'; ?>"+phpfunction+'/'+type+'/'+month+'/'+county+'/'+district+'/'+commodity;
			window.location = url;
       		// alert(url);
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
		
		         

           
   
// $('#consumption-chart').highcharts({
//                 chart: {
//             type: 'line'
//         },
//         title: {
//             text: '<?php echo ' Yearly Commodity Usage:  ' . $commodity_name.' from '. $from_date.' to '. $to_date; ?>'
//         },
//         subtitle: {
//             text: 'Live data reports on RTK'
//         },
//         xAxis: {
//             categories: <?php echo $graphdata['month']; ?>
//         },
//         yAxis: {
//             min: 0,            
//             title: {
//                 text: 'Quantity (Tests)'
//             }
//         },
//         tooltip: {
//             headerFormat: '<span style="font-size:12px">{point.key}</span><table>',
//             pointFormat: '<tr><td style="color:{series.color};padding:0;font-size:10px">{series.name}: </td>' +
//                 '<td style="padding:0;font-size:10px"><b>{point.y:.1f}</b;></td></tr>',
//             footerFormat: '</table>',
//             shared: true,
//             useHTML: true
//         },
//         plotOptions: {
//             column: {
//                 pointPadding: 0.2,
//                 borderWidth: 0
//             }
//         },
//         series: [{
//             name: 'Quantity Used',
//             data: <?php echo $graphdata['qty_used']; ?>
       
        
//         }]
//             });
   
		
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

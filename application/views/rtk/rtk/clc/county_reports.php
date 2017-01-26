
<?php 
include('rca_sidabar.php');

        $county = $this->session->userdata('county_id');   

?>
<div class="dash_main" style="margin-left: 360px; margin-top: 10px;">

<div class="clc_contents">


		  <!-- <button id="btn_all_facilities" class=" btn btn-success">All Facilities</button> -->
	<div class="accordion-group">
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
					            // $month_text = $month_value;
					            $month_text =  date('F', strtotime("-$i month"));
					            // date('F', strtotime("-$i month")); 
					            $month_text = $month_text." ".$year;
					         ?>

					        <option value="<?php echo $month_value ?>"><?php echo $month_text ?></option>;
						    <?php } ?>
						  </select>&nbsp;&nbsp;&nbsp;
		
			
	</div>		

	<div class="accordion-group" style="margin-top: 50px; ">
		<div id="by_commodities" class="table_divs panel panel-success">
			  <div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#graphs_main" style="font-size:13px;font-weight:bold" >GRAPHS: <i>(Click to View)</i></div>
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
		<div id="by_dates" class="table_divs panel panel-success">
		<div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#by_date" style="font-size:13px;font-weight:bold" >FACILITIES<i>(Click to View)</i></div>
		 <div id="by_date" class="accordion-body collapse">
			  <div class="accordion-inner div_height" style="overflow-y: scroll ">
				
				<p style="font-size: 15px; color: #428bca"> <b><?php echo $reported_facilities ?> </b>facilities reported on time, <b><?php echo $late_reported_facilities[0]['late_facilities'] ?></b> reported past 15th and <b><?php echo $nonreported_facilities ?></b> did not report at all</p>

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

            <table class="table" style="width: 30%; font-size: 12px; border-right:1px solid #ddd; border-bottom:1px solid #ddd; border-left:1px solid #ddd;float: left; margin-left: 15px;">
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

	<div class="accordion-group">
		<div id="consumption_details" class="table_divs panel panel-success">
			<div class="panel-heading accordion-heading " data-toggle="collapse" data-parent="#accordion2" href="#kit_consumption" style="font-size:13px;font-weight:bold" >DOWNLOAD CONSUMPTION DATA: <i>(Click to View)</i></div>
			 <div id="kit_consumption" class="accordion-body collapse form-control">
				<div class="accordion-group inner_divs">
				
					<p style="margin-top: 10px;">Select Type of Report:</p>
					
					<select id="fcdrr_commodity_select" class="form-control select_options" > By Commodity
						<option value="0"> -- All FCDRR Details--</option>							
						<option value="1"> Ending Balances</option>							
						<option value="2"> Quantity Used</option>							
						<option value="3"> Tests Done</option>							
					</select>&nbsp;&nbsp;&nbsp;

		            <button type="button" id="btn_consumption_select" class="btn btn-primary my_navs">Download Consumption Report</button>
						  	
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
			window.location = url;
       		// alert(url);
		});
		         

 // var test_array = <?php echo $graphdata['qty_used']; ?>
 // console.log(test_array);         
   
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
   
		
		
});

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

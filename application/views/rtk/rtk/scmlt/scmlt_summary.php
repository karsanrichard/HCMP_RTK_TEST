<script src="<?php echo base_url().'Scripts/accordion.js'?>" type="text/javascript"></script> 
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo base_url();?>Scripts/FusionCharts/FusionCharts.js"></SCRIPT>

<script type="text/javascript">
$(function() {            

            $('#trend-chart').highcharts({
                chart: {
                    type: 'line'
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
                    categories: <?php echo $months_texts; ?>
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
                  legend: {
                      layout: 'horizontal',
                      align: 'right',
                      verticalAlign: 'middle',
                      borderWidth: 0
                  },
                  plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                credits: false,
                series: [{
                    color: '#009933',
                    name: 'Reporting Rates',
                    data: <?php echo $percentages; ?>
                }]
            });
   
});
</script>
<style>
.section_title{
	font-size:15px;
	font-weight:bold;

}
.sections_left{
	width: 40%;
	height: 400px;
	float: left;
}
.sections_right{
	width: 58%;
	height: 400px;
	float: right;
	overflow-x: scroll; 
}
table tr td{
		border: 1px solid #C8C9C8;
		border-collapse: collapse;
		padding: 8px;
		font-size: 13px;

}
.section_data{
	margin-top: 10px;
	margin-left: 10px;
	
}
</style>

<h3 style="margin-left: 300px;"><b><?php echo $d_name; ?> Sub County Summary </b></h3> <br/> <br/>

<?php include 'scmlt_sidebar.php'; ?>
	
<div class="container" style="margin-left: 260px;">
	 <div class="content">
	    <div class="table_divs panel panel-success sections_left" style="">
		 <div class="panel-heading accordion-heading section_title"> Reporting Summary</div>
		 <div id="reporting_summary" class="accordion-body section_data">
			
			<table id="reporting_summary_table" class="table ">
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
			<br/>
			<br/>
		    <button type="button" align ="center" class="btn btn-primary" data-toggle="modal" data-target="#reportedfacilities">Reported Facilities</button>
		    <button type="button" align ="center" class="btn btn-primary" data-toggle="modal" data-target="#nonreportedfacilities">Non-Reported Facilities</button>

	  	</div>
	  	</div>
	  	<div class="table_divs panel panel-success sections sections_right">
		 <div class="panel-heading accordion-heading section_title" style="width: 150%">Consumption Summary</div>					 	
	     <div id="consumption_summary" class="accordion-body section_data">
			
			<table id="consumption_summary_table " class="table" width="50%">
				<tr>
					<td> <b>Commodity Name</b></td>				
					<td> <b>Begining Balance</b></td>				
					<td> <b>Quantity Received</b></td>				
					<td> <b>Quantity Used</b></td>				
					<td> <b>Tests Done</b></td>
					<td> <b>Positve Adjustments</b></td>
					<td> <b>Negative Adjustments</b></td>
					<td> <b>Ending Balance</b></td>
					<td> <b>Quantity Requested</b></td>
					<td> <b>Days Out of Stock</b></td>
				</tr>
				<tr>
                                <?php 
                                $count = count($district_consumption_data);
                                if($count==0){?>
                                  <tr>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>                                        
                                    <td>N/A</td>                                        
                                  </tr>
                                <?php }else{
                                  foreach ($district_consumption_data as $key => $value) {?>
                                         <tr><td><?php echo $value['commodity_name'];?></td>
                                          <td><?php echo $value['sum_opening']; ?></td>
                                          <td><?php echo $value['sum_received']; ?></td>
                                          <td><?php echo $value['sum_used']; ?></td>
                                          <td><?php echo $value['sum_tests']; ?></td>
                                          <td><?php echo $value['sum_positive']; ?></td>
                                          <td><?php echo $value['sum_negative']; ?></td>
                                          <td><?php echo $value['sum_closing_bal']; ?></td>
                                          <td><?php echo $value['sum_requested']; ?></td>
                                          <td><?php echo $value['sum_days']; ?></td>
                                          </tr>
                                  <?php }
                                  }
                                ?>
                                    
                                    </tr>
				
			</table>
	     
	  	</div>
	  	</div>
	  	<div class="table_divs panel panel-success sections_left" style="overflow-y: scroll ">
		<div class="panel-heading accordion-heading section_title"> Facilities</div>
		  	<div class="table_divs panel panel-info" style="float: left; width: 48%; margin-top: 10px">
				<div id="facilities" class="panel-heading accordion-heading section_title">Active Facilities</div>
				<div id="active_facilities" class="accordion-body section_data" >
						
					<table id="acitve_facilities_table">
						<tr>
							<td> <b>Facility Code</b></td>				
							<td> <b>Facility Name</b></td>
						</tr>
						<tr>
						<?php foreach ($facilities_list[1] as $key => $value): ?>
							
							<td> <?php echo  $value['facility_code'];?></td>				
							<td> <?php echo  $value['facility_name'];?></td>
						</tr>
						<?php endforeach ?>
						
					</table>
			     
			  	</div>	</div>	     
			  	
		  	<div class="table_divs panel panel-info" style="float: right; width: 48%; margin-top: 10px">
			  	<div id="facilities" class="panel-heading accordion-heading section_title" >Inactive Facilities</div>
				<div id="inactive_facilities" class="accordion-body section_data">
					
					<table id="inacitve_facilities_table">
						<tr>
							<td> <b>Facility Code</b></td>				
							<td> <b>Facility Name</b></td>
						</tr>
						<tr>
						<?php foreach ($facilities_list[0] as $key => $value): ?>
							
							<td> <?php echo  $value['facility_code'];?></td>				
							<td> <?php echo  $value['facility_name'];?></td>
						</tr>
						<?php endforeach ?>
						
					</table>
			     
			  	</div>		     
			</div>
		</div>
	  	<div class="table_divs panel panel-success sections sections_right" >
		 <div class="panel-heading accordion-heading section_title">Yearly Reporting Trend</div>
			<div id="trend-chart" class="accordion-body section_data">
					 	
	     
	  	</div>  	

	 </div>	
</div>
<br/>
<div class="modal fade" id="reportedfacilities" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Reported</h4>
      </div>
      <div class="modal-body">        
        <p style="font-size: 15px; color: #428bca"> 10 facilities reported on time, 5 reported past 15th and 10 did not report at all</p>
            <table>
              
               <tr>
               
               	<td>Facility Code</td>
               	<td>Facility Name</td>
               	<td>View Report</td>
               </tr>
               <?php 
               $reported = $reporting_details['reported'];
	                $count = count($reported);
	                if($count==0){?>
	                  <tr>
	                    <td>N/A</td>
	                    <td>N/A</td>
	                    <td>N/A</td>	                                                        
	                  </tr>
	                <?php }else{
	                  foreach ($reported as $key => $value) {?>
               <tr>
               	<td> <?php echo  $value['facility_code'];?></td>				
				<td> <?php echo  $value['facility_name'];?></td>
               	<td> <a href="<?php echo site_url('rtk_management/lab_order_details/' . $value['order_id']); ?>"class="link">View</a></td>
               </tr>
 				<?php }
                                  }
                                ?>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="nonreportedfacilities" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Non -Reported Facilities</h4>
      </div>
      <div class="modal-body">        
            <table>
               <tr>
               	<td colspan="2" align="center"><b>Facilities with reports</b></td>
               </tr>
               <tr>
               	<td>Facility Code</td>
               	<td>Facility Name</td>
               </tr>
                <?php 

               		$nonreported = $reporting_details['non_reported'];
	                $count = count($reported);
	                
	              foreach ($nonreported as $key => $value) {?>
               <tr>
               	<td> <?php echo  $value['facility_code'];?></td>				
				<td> <?php echo  $value['facility_name'];?></td>
               </tr>
 				
 				<?php } ?>

            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>



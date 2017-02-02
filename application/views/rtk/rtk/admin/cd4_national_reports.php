<style type="text/css">
	.select_options{
		width:14%;
		float:left;
		height:30px;
		font-size:12px;
		 margin-left: 30px
	}
	

</style>

<div class="dash_main" style=" margin-top: 10px;">

<h3> <?php echo $banner_text;?></h3><br/> <br/>


<div class="clc_contents">

<div class="accordion-group">
	<select id="county_select" class="form-control select_options"> By County
		<option value=""> -- All Counties--</option>							
	</select>&nbsp;&nbsp;&nbsp;	
	
	<select id="commodity_select" class="form-control select_options" > By Commodity
		<option value="1"> -- All Commodities--</option>							
									
	</select>&nbsp;&nbsp;&nbsp;
	
	<select id="date_select" class="form-control select_options" > By Period
					
		<?php 

			for ($i=0; $i <=11; $i++) { 
	            $month_value =  date("mY", strtotime( date( 'Y-m-01' )." -$i months"));
	            $j = $i+1;            
	            $month_text =date("M Y", strtotime( date( 'Y-m-01' )." -$j months"));
            // echo $month_text." $month <br/>";
		?>

		<option value="<?php echo $month_value ?>"><?php echo $month_text ?></option>;
		<?php } ?>
	</select>&nbsp;&nbsp;&nbsp;

	<!-- <select id="fcdrr_commodity_select" class="form-control select_options" > By Commodity
		<option value="0"> -- All FCDRR Details--</option>							
		<option value="1"> Ending Balances</option>							
		<option value="2"> Quantity Used</option>							
		<option value="3"> Tests Done</option>							
		<option value="4"> AMC</option>							
	</select>&nbsp;&nbsp;&nbsp;

    <!-- <button type="button" id="btn_consumption_select" class="btn btn-primary my_navs">Download Consumption Report</button> -->
    <button type="button" id="update_button" class="btn btn-primary my_navs">Refresh</button>
				  	
</div>
<br/>
<div class="accordion-group" style="width: 49%;float: left; margin-left: 5px; margin-top: 20px; height: 350px">
					<div id="bycons" class="inner_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle=""  href="#trend_graphs" style="font-size:13px;font-weight:bold;" >Yearly Reporting Trend</div>
					  <div id="trend_graphs" class="accordion-body ">
					  <div class="accordion-inner ">
						 
						<div id="trend-chart" class="accordion-body "></div>
						
					  </div>
					  </div>
					</div>
				</div>
				<div class="accordion-group" style="width: 49%;float: left; margin-left: 3px; margin-top: 20px;overflow-y: scroll;height: 350px">
					<div id="bycons" class="table_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle=""  data-parent="#accordion2" href="#" style="font-size:13px;font-weight:bold;" >Stock Card</div>
					  <div id="" class="accordion-body ">
					  <div class="accordion-inner ">						

						<table class="table" id="stock_card" style="font-size:13px;">
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
	                    	
	                    </tbody>
	            	</table>
		            
             		</div>
					</div>
					</div>
				</div>	

				<div class="accordion-group" style="width: 49%;float: left; margin-left: 5px; margin-top: 120px; height: 350px">
					<div id="bycons" class="inner_divs panel panel-warning">
					  <div class="panel-heading accordion-heading " data-toggle=""  href="#cons_graphs" style="font-size:13px;font-weight:bold" >Monthly Reporting Summary (current month)</div>
					  <div id="cons_graphs" class="accordion-body  ">
					  <div class="accordion-inner ">
						 <p> <?php echo $reporting_summary['reported']; ?> Facilities reported, <?php echo $reporting_summary['nonreported']; ?> have not reported. </p> <button class="btn btn-success" id="" data-toggle="modal" data-target="#reported_facilities">Reported Facilities</button> <button class="btn btn-success" id="" data-toggle="modal" data-target="#nonreported_facilities" style="margin-left: 5%">Non Reported Facilities</button>
						<div id="reporting-piechart" class="accordion-body section_data"> </div>
							
					  </div>
					  </div>
					</div>
				</div>
				

				
	<div class="modal fade" id="reported_facilities" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><b>Reported Facilities</b> </h4>
      </div>
      <div class="modal-body">
       
      <table class="table" id="reported_table">
      	<thead>
      		<th>County</th>
      		<th>Sub County</th>
      		<th>Facility Code</th>
      		<th>Facility Name</th>
      		<th>FCDRR</th>
      	</thead>
      	<tbody>
      		
      		
		</tbody>
      </table>
    </div>
     
    </div>
  </div>
</div>			
	<div class="modal fade" id="nonreported_facilities" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><b>Non Reported Facilities</b> </h4>
      </div>
      <div class="modal-body">
       
        <table class="table" id="nonreported_table">
      	<thead>
      		<th>County</th>
      		<th>Sub County</th>
      		<th>Facility Code</th>
      		<th>Facility Name</th>
      	</thead>
      	<tbody>
      		
		</tbody>
      </table>
    </div>
     
    </div>
  </div>
</div>
</div>

</div>
</div>

<script type="text/javascript">
	$(document).ready(function(e){
		$.fn.dataTable.ext.errMode = 'none'; $('#table-id').on('error.dt', function(e, settings, techNote, message) { console.log( 'An error occurred: ', message); })
		populate_stock_card();
		populate_reporting_trend();
		populate_montlhy_reporting_summary();
		populate_nonreported_list();
		populate_reported_list();

	$('#update_button').click(function(){
    	
    	populate_stock_card();
		populate_reporting_trend();
		populate_montlhy_reporting_summary();
		populate_nonreported_list();
		populate_reported_list();

    });
	function populate_montlhy_reporting_summary(){

    var county = $('#county_select').val();
    var month = $('#date_select').val();
    
	$.ajax({
    url: "<?php echo base_url() . 'allocation_management/get_cd4_reporting_summary/'; ?>"+month+'/'+county,
    dataType: 'json',
    success: function(s){
    	console.log(s);
        var reported_percentage = s.reported_percentage;
        var nonreported_percentage = s.nonreported_percentage;

    $('#reporting-piechart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Reported and Non Reported Facilities'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Facilities Reporting Status',
            colorByPoint: true,
            data: [{
                name: 'Reported Facilities',
                y: reported_percentage
            }, {
                name: 'Not Reported',
                y: nonreported_percentage,
                sliced: true,
                selected: true
            }]
        }]
    });

    },
    error: function(e){
        console.log(e.responseText);
    }            
});
}

    // $('#trend-chart').highcharts({
    //     title: {
    //         text: 'Yearly Reporting Rates',
    //         x: -20 //center
    //     },
    //     subtitle: {
    //         text: 'Source: HCMP',
    //         x: -20
    //     },
    //     xAxis: {
    //         categories: <?php echo $trend_details['months_texts']?>
    //     },
    //     yAxis: {
    //         title: {
    //             text: 'Percentages'
    //         },
    //         plotLines: [{
    //             value: 0,
    //             width: 1,
    //             color: '#808080'
    //         }]
    //     },
    //     tooltip: {
    //         valueSuffix: '%'
    //     },
    //     legend: {
    //         layout: 'vertical',
    //         align: 'right',
    //         verticalAlign: 'middle',
    //         borderWidth: 0
    //     },
    //     series: [{
    //         name: 'Percentages',
    //         data: <?php echo $trend_details['percentages']?>
    //     }]
    // });

   

    $.ajax({
	    url: "<?php echo base_url() . 'allocation_management/get_counties'; ?>",
	    dataType: 'json',
	    success: function(s){
	    	console.log(s);
	        var countieslist = s.counties_list;
			$('#county_select').html(countieslist);
	        
	    },
	    error: function(e){
	        console.log(e.responseText);
	    }            
	}); 

	$.ajax({
	    url: "<?php echo base_url() . 'allocation_management/get_cd4_commodities'; ?>",
	    dataType: 'json',
	    success: function(s){
	    	console.log(s);
	        var commodity_list = s.cd4_commodities;
			$('#commodity_select').html(commodity_list);
	        
	    },
	    error: function(e){
	        console.log(e.responseText);
	    }            
	});
function populate_reporting_trend(){

    var county = $('#county_select').val();
    
	$.ajax({
    url: "<?php echo base_url() . 'allocation_management/get_cd4_reporting_percentage2/'; ?>"+county,
    dataType: 'json',
    success: function(s){
    	console.log(s);
        var percentages = s.percentages;
        var months_texts = s.months_texts;

        	$('#trend-chart').highcharts({
        title: {
            text: 'Yearly Reporting Rates',
            x: -20 //center
        },
        subtitle: {
            text: 'Source: HCMP',
            x: -20
        },
        xAxis: {
            categories: months_texts,
            
        },
        yAxis: {
            title: {
                text: 'Percentages'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: '%'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'Percentages',
            data: percentages
        }]
    });
    },
    error: function(e){
        console.log(e.responseText);
    }            
});
}
	var oTable = $('#stock_card').dataTable({	
		"bPaginate":false,				
	    "bFilter": false,
	    "bSearchable":false,
	    "bInfo":false
	});
	var reported_table = $('#reported_table').dataTable({	
		"bPaginate":false,				
	    "bFilter": false,
	    "bSearchable":false,
	    "bInfo":false
	});var nonreported_table = $('#nonreported_table').dataTable({	
		"bPaginate":false,				
	    "bFilter": false,
	    "bSearchable":false,
	    "bInfo":false
	});

	var stock_card_url = '';

	 
function populate_stock_card(){
    	var commodity = $('#commodity_select').val();
    	var month = $('#date_select').val();
    	var county = $('#county_select').val();
    	var stock_card_url = "<?php echo base_url() . 'allocation_management/get_cd4_stock_card2/'; ?>"+commodity+'/'+month+'/'+county;

    	// alert(stock_card_url);

	$.ajax({

		url: stock_card_url,
		dataType: 'json',
		success: function(s){
		// console.log(s);
		oTable.fnClearTable();
		for(var i = 0; i < s.length; i++) {
			oTable.fnAddData([
			s[i][0],
			s[i][1],
			s[i][2],
			s[i][3],
			s[i][4],
			s[i][5],
			s[i][7],
			s[i][8],
			s[i][9],
			s[i][10]

			]);
			} // End For
		},
		error: function(e){
			console.log(e.responseText);
		}
	});
	}
 
	function populate_reported_list(){
		
    	var commodity = $('#commodity_select').val();
    	var month = $('#date_select').val();
    	var county = $('#county_select').val();
    	var reported_url = "<?php echo base_url() . 'allocation_management/get_cd4_reporting_facility_details/'; ?>"+month+'/'+county;

    	// alert(stock_card_url);

	$.ajax({

		url: reported_url,
		dataType: 'json',
		success: function(s){
		console.log(s);
		var reported_list = s.reported;
		reported_table.fnClearTable();
		for(var i = 0; i < reported_list.length; i++) {
			
			var fcdrr_link = "<?php echo base_url() . 'cd4_management/fcdrr_details/'; ?>"+reported_list[i][4];
			var link = '<a href "'+fcdrr_link+'> View FCDRR</a>';
			alert (fcdrr_link);
			
			reported_table.fnAddData([
			reported_list[i][0],
			reported_list[i][1],
			reported_list[i][2],
			reported_list[i][3],
			fcdrr_link
			// reported_list[i][4]
			]);
			} // End For
		},
		error: function(e){
			console.log(e.responseText);
		}
	});
	}

	function populate_nonreported_list(){

    	var commodity = $('#commodity_select').val();
    	var month = $('#date_select').val();
    	var county = $('#county_select').val();
    	var reported_url = "<?php echo base_url() . 'allocation_management/get_cd4_reporting_facility_details/'; ?>"+month+'/'+county;



	$.ajax({

		url: reported_url,
		dataType: 'json',
		success: function(s){
		console.log(s);
		var non_reported_list = s.nonreported;
		nonreported_table.fnClearTable();
		for(var i = 0; i < non_reported_list.length; i++) {
			nonreported_table.fnAddData([
			non_reported_list[i][0],
			non_reported_list[i][1],
			non_reported_list[i][2],
			non_reported_list[i][3]
			]);
			} // End For
		},
		error: function(e){
			console.log(e.responseText);

		}
	});
	}

});
	
</script>
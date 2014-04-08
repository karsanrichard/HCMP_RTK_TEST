<div class="container-fluid" style="">
      <div class="row row-offcanvas row-offcanvas-right" id="sidebar" >
      	<p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Click to view Side Menu</button>
          </p>
        <div class="col-sm-3 col-md-2 sidebar-offcanvas"  id="bar" role="navigation" style="margin-left:0.5%">

           <div class="panel-group " id="accordion" style="padding: 0;">
             <!--Reports Accordion -->   
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"><span class="glyphicon glyphicon-file">
                            </span>Reports</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-usd"></span><a href="#">Expiries</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-user"></span><a href="#">Stock Control Card</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-tasks"></span><a href="#">Order Report</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-shopping-cart"></span><a href="#">Commodities Issue</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!--Divisional Reports Accordion-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"><span class="glyphicon glyphicon-file">
                            </span>Divisional Reports</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-saved"></span><a href="<?php echo base_url().'divisional_reports/view_malaria_report'?>">Malaria Reports</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-saved"></span><a href="<?php echo base_url().'divisional_reports/view_TB_report'?>">TB Reports</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-saved"></span><a href="<?php echo base_url().'divisional_reports/view_RH_report'?>">Reproductive Health Reports</a>
                                    </td>
                                </tr>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!--Submit Divisional Reports-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"><span class="glyphicon glyphicon-pencil">
                            </span>Submit Divisional Reports</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-edit"></span><a href="<?php echo base_url().'divisional_reports/malaria_report'?>">Malaria Reports</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-edit"></span><a href="<?php echo base_url().'divisional_reports/TB_report'?>">TB Reports</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-edit"></span><a href="<?php echo base_url().'divisional_reports/RH_report'?>">Reproductive Health Reports</a>
                                    </td>
                                </tr>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 " style="padding:0;border-radius: 0 ">
          <h1 class="page-header" style="margin: 0;font-size: 1.6em;"> 
          	<!--Echoes the name of the report user views -->
	          	<?php
	          	if (isset($report_title))
				{
					echo $report_title;
				} else {
					echo "Report";
				}
	          	?>
          	</h1>

        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 " style="padding:0;border-radius: 0 ">
         <?php $this -> load -> view($report_view);?>
        </div>
      </div>
    </div>
<script>
    	$(document).ready(function () {
  $('[data-toggle=offcanvas]').click(function () {
    $('.row-offcanvas').toggleClass('active')
  });
  
  $(window).resize(function() {
    if (($(window).width() < 768))
    {
        $( ".col-md-2,.col-md-10" ).css( "position", "" );
    };
});
$('#exp_datatable,#potential_exp_datatable,#potential_exp_datatable2').dataTable( {
     "sDom": "T lfrtip",
  
       "sScrollY": "377px",
       
                    "sPaginationType": "bootstrap",
                    "oLanguage": {
                        "sLengthMenu": "_MENU_ Records per page",
                        "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
                    },
            "oTableTools": {
                 "aButtons": [
        "copy",
        "print",
        {
          "sExtends":    "collection",
          "aButtons":    [ "csv", "xls", "pdf" ]
        }
      ],
      "sSwfPath": "<?php echo base_url(); ?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
    }
  } ); 

});
    </script>
<style>     
 
    .label {
        font-size: 11px;
        padding: 3px;
    }
    
    
    #fixed-topbar{
       position: fixed;
       margin-left:16.5%; 
       top: 80px;
       background: #36BB24; 
       width: 40%;
       padding: 7px 1px 0px 13px;
       border-bottom: 1px solid #ccc;
       border-bottom: 1px solid #ccc;
       border-radius: 4px; 
    }
    #cd4-topbar{
       position: fixed;
       margin-left:76.5%; 
       top: 80px;
       background: #36BB24; 
       width: 18%;
       padding: 7px 1px 0px 13px;
       border-bottom: 1px solid #ccc;
       border-bottom: 1px solid #ccc;
       border-radius: 4px; 
    }
</style>

<?php if ($this->session->userdata('switched_from') == 'rtk_manager') { ?>
<div id="fixed-topbar" style="">
    <span class="lead" style="color: #fff; margin-left: 100px;">Switch back to RTK Manager</span>
    &nbsp;
    &nbsp;
    <a href="<?php echo base_url(); ?>rtk_management/switch_district/0/rtk_manager/0/home_controller/0/" class="btn btn-primary" id="switch_idenity" style="margin-top: -5px;margin-left: 50px; margin-bottom: 5px;">Go</a>
</div><?php } ?>

<div id="cd4-topbar" style="">
    <span  style="color: #fff; margin-left: 20px; font-size: 16px; ">Switch to RTK Reports</span>
    &nbsp;
    &nbsp;
    <a href="<?php echo base_url(); ?>rtk_management/scmlt_home" class="btn btn-primary" style="margin-top: -5px;margin-left: 10px; margin-bottom: 5px;">Go</a>
</div>

<?php include ('scmlt_sidebar.php');?>

<div class="container" style="margin-left: 250px;background-color:   #fbf6f5">
   
    <div class="dash_main" id = "dash_main" >
        <div style="font-size: 13px; margin-top: 20px;">           
            <br/>
            <div  style=" margin-left:10px; margin-top:30px; text-align:center;">
                <h3> <strong><?php echo $district_name; ?> Sub County - <?php echo date('F, Y') ; ?> </strong> </h3>
                <h4> <strong>CD4 Reports </strong> </h4>

            </div>

            <div class="alert alert-<?php echo $alertype ?>" style="margin-top:10px; margin-left:40px; width: 500px; text-align:center; float:left;"><?php echo $alertmsg; ?></div>
            <div class="alert alert-success" style="margin-top:10px; margin-right: 105px; width: 200px; text-align:center; float: right;">Countdown <br/><?php echo $remainingdays; ?><br/>days to go</div>

        </div>
        <div id="tablediv" style="margin-left:5px; width:80%;">
            <table  style="margin-left: 15%;" id="maintable" class="table">
                <thead>
                    <tr>
                        <th><b>MFL Code</b></th>
                        <th><b>Facility Name</b></th>
                        <th ><b>FCDRR Reports</b></th> 
                    </tr>
                </thead>
                <tbody id="facilities_home">
                    <?php echo $table_body; ?>
                </tbody>            
            </table>

        </div>
    </div>

    <div style="position:fixed; bottom: 0;margin-bottom: 25px;margin-left:5px;width: 80%;font-size: 118%; background: #fff;">
        <span>Reports Progress: <?php echo $percentage_complete; ?>% </span>

        <div class="progress">
            <div class="progress-bar progress-bar-<?php echo $progress_class; ?>" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage_complete; ?>%">
                <span class="sr-only"><?php echo $percentage_complete; ?>% Complete </span>
            </div>
        </div>



    </div>
</div>


<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/tablecloth/assets/css/tablecloth.css">
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>
<script type="text/javascript">
    $(document).ready(function() {      


        // $("table").tablecloth({theme: "paper"});
        var deadline = '<?php echo $deadline_date;?>';
        var date = '<?php echo $date;?>';           
        if(date>deadline){
            $('.report').hide();
        }

        $('#maintable').dataTable({
            "sDom": "T lfrtip",
            "bPaginate": false,
            "aaSorting": [[3, "asc"]],
            "sScrollY": "377px",
            "sScrollX": "100%",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
            }
            
        });

        //Slider for Report Saved
        $.fn.slideFadeToggle = function(speed, easing, callback) {
            return this.animate({
                opacity: 'toggle',
                height: 'toggle'
            }, speed, easing, callback);
        };
        $(".notif").delay(20000).slideUp(1000);
        $("#tablediv").delay(15000).css("height", '450px');
        $(".dataTables_filter").delay(15000).css("color", '#ccc');        

        //Switch Districts
        $('#switch_district').change(function() {
            var value = $('#switch_district').val();
            var path = "<?php echo base_url() . 'rtk_management/switch_district/'; ?>" + value + "/scmlt";
            window.location.href = path;
        });

    });

</script>

<?php
    $option = '';
    $id = $this->session->userdata('user_id');
    $q = 'SELECT * from dmlt_districts,districts 
    where dmlt_districts.district=districts.id
    and dmlt_districts.dmlt=' . $id;
    
    $res = $this->db->query($q)->result_array();
    
    foreach ($res as $key => $value) {
        $option .= '<option value = "' . $value['id'] . '">' . $value['district'] . '</option>';
    }
    
    if(count($res)>0){
        $style = 'display:block';
    }else{
        $style = 'display:none';
    }
    
?>

<div class="col-md-2" style="padding-right: 20px;margin-left:-5px; top: 100px; position: fixed; width:18%">


    <span style="<?php echo $style ?>;font-size: 16px;" class="label label-info">Switch Sub-Counties</span>
    <br />
    <br />
   
    <select id="switch_district" class="form-control select_switch" style="<?php echo $style ?>;">
        <option>-- Select Sub-County --</option>
        <?php echo $option; ?>
    </select>
    
    <br/>

    <select id="switch_month" class="form-control select_switch">       
        <?php 

            for ($i=1; $i <=21 ; $i++) { 
            $month = date('m', strtotime("-$i month")); 
            $year = date('Y', strtotime("-$i month")); 
            $month_value = $month.$year;
            $month_text =  date('F', strtotime("-$i month")); 
            $month_text = "-- ".$month_text." ".$year." --";
         ?>
        <option value="<?php echo $month_value ?>"><?php echo $month_text ?></option>;
        <?php } ?>
    </select>

    <br/>

    <ul class="main_list" style="font-size:100%;border:ridge 1px #ccc">
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url('Home')?>">>> Home</a></li>        
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/scmlt_summary'?>">>> Summary</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/scmlt_orders'?>">>> Reports</a></li>     
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/scmlt_allocation_details'?>">>> Allocation</a></li>       
       <!--  <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/allocation_csv_interface'?>">>> Upload Allocation CSV</a></li>  
        <li> -->
            <!-- <a href="" class="allocation-excel"><h5>Allocation via excel</h5></a> -->
        </li>
    </ul>

    <?php if ($this->session->userdata('switched_from') == 'rtk_manager') { ?>
    <br/>
    <div class = "switch-bar">
        <a class="btn btn-primary" id="switch_idenity" style=" margin-left: 2%; margin-bottom: 5%; width:80%; color:#032e05;" href="<?php echo base_url(); ?>rtk_management/switch_district/0/rtk_manager/0/home_controller/0/" >Switch back to RTK Manager</a>
    </div><?php } ?>

    <br/>

    <div class = "switch-bar"> 
        <a href="<?php echo base_url(); ?>cd4_management/scmlt_home" class="btn btn-primary" style="margin-left: 2%; margin-bottom: 5%; width:65%; color:#032e05;">CD4 Reports</a>
    </div>
</div>

<style type="text/css">    

    .main_list {
        list-style-type: none;
        margin: 0;
        padding: 0;
        border: 1px ridge #000;
        width: 100%;
        background-color: #36BB24;
        border-radius: 5px;
    }
    .side_links{
        display: block;
        color: #000;
        padding: 8px 0 8px 16px;
        text-decoration: none;
        font-size: 16px
    }
    .side_links:hover {
        background-color: #EAF5E6  ;
        color: #184906;
    }

    .side_links_a{
        color:  #032e05 ;
        font-size: 90%;
    }
    .select_switch{
        max-width: 220px;
        background-color: #ffffff;
        border: 1px solid #cccccc; 
        font-size: 14px;
    }

    .switch-bar{      
       background: #36BB24; 
       width: 100%;
       padding: 7px 1px 0px 13px;
       border-bottom: 1px solid #ccc;
       border-bottom: 1px solid #ccc;
       border-radius: 4px; 
    }    

</style>

<script type="text/javascript">
$(document).ready(function() {
    $('#switch_district').change(function() {
        var value = $('#switch_district').val();
        var path = "<?php echo base_url() . 'rtk_management/switch_district/'; ?>" + value + "/scmlt";
        window.location.href = path;
    });

    $(".allocation-excel").on('click', function(e) {
                  e.preventDefault(); 
        var body_content='<?php  $att=array("name"=>'myform','id'=>'myform');
        echo form_open_multipart('orders/facility_order#',$att)?>'+
    '<input type="file" name="file" id="file" required="required" class="form-control"><br>'+
    '<button class="upload">Upload</button>'+
    '</form>';
       //hcmp custom message dialog
        dialog_box(body_content,'');        
    });

    function dialog_box(body_html_data,footer_html_data){
    
            $('#communication_dialog .modal-body').html("");
            $('#communication_dialog .modal-footer').html("");
            //set message dialog box 
            $('#communication_dialog .modal-footer').html(footer_html_data);
            $('#communication_dialog .modal-body').html(body_html_data);
            $('#communication_dialog').modal('show');
            $(".clone_datepicker").datepicker({
    beforeShowDay: function(date)
    {
        // getDate() returns the day [ 0 to 31 ]
     if (date.getDate() ==
         getLastDayOfYearAndMonth(date.getFullYear(), date.getMonth()))
        {
            return [true, ''];
        }
        return [false, ''];
    },              
    dateFormat: 'd My', 
    changeMonth: true,
    changeYear: true,
    buttonImage: baseUrl,       }); 
    
}

});
</script>
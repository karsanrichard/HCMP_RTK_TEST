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
});
</script>
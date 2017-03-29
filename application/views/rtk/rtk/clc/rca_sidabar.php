<?php
    // $month = $this->session->userdata('Month');
    // if ($month==''){
    //    $month = date('mY', strtotime('-1 month'));
    // }
    // $year= substr($month, -4);
    // $month= substr_replace($month,"", -4);
    // $monthyear = $year . '-' . $month . '-1';        
    // $englishdate1 = date('F, Y', strtotime('next month'));
    // $englishdate = date('F, Y', strtotime($monthyear));
    
    $option = '';
    $id = $this->session->userdata('user_id');
    $q = 'SELECT counties.id AS countyid, counties.county
            FROM rca_county, counties
            WHERE rca_county.county = counties.id
            AND rca_county.rca =' . $id;
    $res = $this->db->query($q)->result_array();
    
    foreach ($res as $key => $value) {
        $option .= '<option value = "' . $value['countyid'] . '">' . $value['county'] . '</option>';
    }

    $current_month = date('mY', strtotime('-1 month'));    

?>
<div class="col-md-2" style="border-right: solid 1px #ccc;padding-right: 20px;margin-left:-5px">
   
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <select id="switch_county" class="form-control" style="max-width: 220px;background-color: #ffffff;border: 1px solid #cccccc;">
        <option>-- Select County --</option>
        <?php echo $option; ?>
    </select>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <select id="switch_month" class="form-control" style="max-width: 220px;background-color: #ffffff;border: 1px solid #cccccc;">       
        <?php 
            $all_month_text = '';
            $new_all_month_text = '';

            for ($i=0; $i <=21 ; $i++) { 
            // $month = date('m', strtotime("-$i month")); 
            // $year = date('Y', strtotime("-$i month")); 
            // $month_value = $month.$year;
            // $month_text =  date('F', strtotime("-$i month")); 
            // $month_text = "-- ".$month_text." ".$year." --";  
            // $all_month_text .= $month_text."\n";

            // echo "<pre>";print_r($month_text);exit;

            $tmp = date('Y-m-15'); // Get the middle of the month to avoid PHP date bug.
            $begin_date = date('Y-m-01', strtotime($tmp . '-'.$i.' month')); // First day of calendar month in future.
            $end_date = date('Y-m-t', strtotime($begin_date)); // Last day of calendar months in future.
            $monthTitle = date('F Y', strtotime($begin_date));

            $month = date('F', strtotime($begin_date));
            $month_num = date('m', strtotime($begin_date));
            $year = date('Y', strtotime($begin_date));
            $month_text = "-- ".$month." ".$year." --"; 
            $month_value = $month_num.$year;

            // $new_all_month_text .= $month_value."\n";

         ?>
        <option value="<?php echo $month_value ?>"><?php echo $month_text ?></option>;
        <?php } ?>
    </select>
    <?php 
        //echo "<pre>";print_r($new_all_month_text);exit;
     ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

 <div id="switch"><button id="switch_back" class="btn btn-primary">Switch to Current Month</button></div>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <ul class="main_list" style="font-size:100%;border:ridge 1px #ccc">
        <li class = "side_links "><a class = "side_links_a" href="<?php echo base_url().'rtk_management/county_home'?>">Summary</a></li>        
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'allocation_management/county_reports' ?>"> RTK Reports</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'cd4_management/county_cd4_reports' ?>"> CD4 Reports</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/rca_districts'?>">Sub-Counties</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/county_stock'?>">Stock Card</a></li> 
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/rca_pending_facilities'?>">Non-Reported Facilities</a></li> 
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/county_admin/users' ?>">Users</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/county_admin/facilities' ?>">Facilities</a></li>
        
        <!-- <?php
        $county_id = $this->session->userdata('county_id');

        $sql ="select * from counties where id = '$county_id'";
        $result = $this->db->query($sql)->result_array();
        $zone = $result[0]['zone'];
        
        $month = date('m');
        $day =date('d');
        
        $zone_a_months = array(01,04,07,10);
        $zone_b_months = array(02,05,08,11);
        $zone_c_months = array(03,06,09,12);
        // $zone_d_months = array(01,04,07,10);

            if ($day>=12 && $day <=23) { 
            
                if (in_array($month, $zone_a_months) && $zone == 'A'){
                // else {
                    echo '<li class = "side_links"><a href="'.base_url().'rtk_management/cmlt_allocation_dashboard">RTK Allocation ongoing</a></li>';
               } else if (in_array($month, $zone_b_months) && $zone == 'B'){

                    echo '<li class = "side_links"><a href="'.base_url().'rtk_management/cmlt_allocation_dashboard">RTK Allocation ongoing</a></li>';
               } else if (in_array($month, $zone_c_months) && $zone == 'C'){

                    echo '<li class = "side_links"><a href="'.base_url().'rtk_management/cmlt_allocation_dashboard">RTK Allocation ongoing</a></li>';
               } else{

                    echo  '<li class = "side_links">RTK Allocation ongoing   </a></li>';
               }

            }else{
                    
              echo  '<li class = "side_links">RTK Allocation </a></li>';

            }

        ?> -->
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/county_drawing_rights'?>">Drawing Rights </a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/cmlt_allocation_list/'.$county_id?>">RTK Allocation List</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/county_trend' ?>">Trends</a></li>
    </ul>
</div>

<style type="text/css">
    

    .main_list {
        list-style-type: none;
        margin: 0;
        padding: 0;
        border: 1px ridge #000;
        width: 100%;
        background-color: #428bca;
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
    background-color:  #d3ecfd   ;
    color: #428bca;
    }
    .side_links_a{
        color:  #2c306f;
    }
</style>
<script type="text/javascript">
$(function() {   
    $('#switch_month').change(function() {
            var value = $('#switch_month').val();
            var path = "<?php echo base_url() . 'rtk_management/switch_district/0/rtk_county_admin/'; ?>" + value + "/";
             // alert (path);
window.location.href = path;
});

        $('#switch_county').change(function() {
            var value = $('#switch_county').val();
            var path = "<?php echo base_url() . 'rtk_management/switch_district/0/rtk_county_admin/0/home_controller/'; ?>" + value + "";
//              alert (path);
window.location.href = path;
});
    });

</script>
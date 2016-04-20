<?php
            $option = '';
            $id = $this->session->userdata('user_id');
            $q = 'SELECT * from dmlt_districts,districts 
            where dmlt_districts.district=districts.id
            and dmlt_districts.dmlt=' . $id;
            $q1 = 'SELECT * from user,districts 
            where user.district=districts.id
            and user.id=' . $id;
            $res = $this->db->query($q);
            $res1 = $this->db->query($q1);
            foreach ($res->result_array() as $key => $value) {
                $option .= '<option value = "' . $value['id'] . '">' . $value['district'] . '</option>';
            }
            foreach ($res1->result_array() as $key => $value) {
                $option .= '<option value = "' . $value['id'] . '">' . $value['district'] . '</option>';
            }
            ?>
<div class="col-md-2" style="border-right: solid 1px #ccc;padding-right: 20px;margin-left:-5px">
   
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <select id="switch_county" class="form-control" style="max-width: 220px;background-color: #ffffff;border: 1px solid #cccccc;">
        <option>-- Select Sub County --</option>
        <?php echo $option; ?>
    </select>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <select id="switch_month" class="form-control" style="max-width: 220px;background-color: #ffffff;border: 1px solid #cccccc;">       
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
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <ul class="main_list" style="font-size:100%;border:ridge 1px #ccc">
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/scmlt_home'?>">>> Home</a></li>        
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/scmlt_summary'?>">>> Summary</a></li>
        <li class = "side_links"><a class = "side_links_a" href="<?php echo base_url().'rtk_management/scmlt_orders'?>">>> Reports</a></li>       
    </ul>
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
        color: #fff;
    }

</style>
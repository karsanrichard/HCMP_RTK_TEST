<?php
/*

*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once ('home_controller.php');

class Facility extends Home_controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        ini_set('memory_limit', '-1');
        ini_set('max_input_vars', 3000);
    }

    public function index() {
        echo "Facilities";
    }

    //FACILITY FUNCTUONS

    public function facility_home(){
        
    ini_set('memory_limit', '-1');
    
   
    $facility = $this->session->userdata('facility_id'); 
    $district = $this->session->userdata('district_id'); 
    $countyid = $this->session->userdata('county_id');

    date_default_timezone_set('EUROPE/moscow');
    // echo "Facility $facilityname County $countyid District $district";
    $conditions = '';
    

    $sql = 'SELECT facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,
            lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
            FROM lab_commodity_orders, facilities
            WHERE lab_commodity_orders.facility_code = facilities.facility_code AND lab_commodity_orders.facility_code = '.$facility.'
            ORDER BY  lab_commodity_orders.id DESC ';       
             
    $result = $this->db->query($sql)->result_array();
    // echo "$sql"; // echo "<pre>"; print_r($result);
       
    $data['lab_order_list'] = $result;
    $data['d_name'] = $result[0]['facility_name'];
    $data['countyid'] = $countyid;

    $data['title'] = "Orders";
    $data['content_view'] = "rtk/rtk/facilities/facility_orders_list";
    $data['banner_text'] = $result[0]['facility_name'] . "Orders";

    $this->load->view("rtk/template", $data);   


    //     $facilities = Facilities::get_total_facilities_rtk_in_district($district);       
    //     $district_name = districts::get_district_name_($district);                    
    //     $table_body = '';
    //     $reported = 0;
    //     $nonreported = 0;
    //     $date = date('d', time());

    //     $msg = $this->session->flashdata('message');
    //     if(isset($msg)){
    //         $data['notif_message'] = $msg;
    //     }
    //     if(isset($popout)){
    //         $data['popout'] = $popout;
    //     }
        
    //     $sql = "select distinct rtk_settings.* 
    //     from rtk_settings, facilities 
    //     where facilities.zone = rtk_settings.zone 
    //     and facilities.rtk_enabled = 1";
    //     $res_ddl = $this->db->query($sql);
    //     $deadline_date = null;
    //     $settings = $res_ddl->result_array();
    //     foreach ($settings as $key => $value) {
    //         $deadline_date = $value['deadline'];
    //         $five_day_alert = $value['5_day_alert'];
    //         $report_day_alert = $value['report_day_alert'];
    //         $overdue_alert = $value['overdue_alert'];
    //     }
    //     date_default_timezone_set("EUROPE/Moscow");

    //     foreach ($facilities as $facility_detail) {

    //        $lastmonth = date('F', strtotime("last day of previous month"));
    //        if($date>$deadline_date){
    //         $report_link = "<span class='label label-danger'>  Pending for $lastmonth </span> <a href=" . site_url('rtk_management/get_report/' . $facility_detail['facility_code']) . " class='link report'></a></td>";
    //     }else{
    //         $report_link = "<span class='label label-danger'>  Pending for $lastmonth </span> <a href=" . site_url('rtk_management/get_report/' . $facility_detail['facility_code']) . " class='link report'> Report</a></td>";
    //     }


    //     $table_body .="<tr><td><a class='ajax_call_1' id='county_facility' name='" . base_url() . "rtk_management/get_rtk_facility_detail/$facility_detail[facility_code]' href='#'>" . $facility_detail["facility_code"] . "</td>";
    //     $table_body .="<td>" . $facility_detail['facility_name'] . "</td><td>" . $district_name['district'] . "</td>";
    //     $table_body .="<td>";

    //     $lab_count = lab_commodity_orders::get_recent_lab_orders($facility_detail['facility_code']);
    //     if ($lab_count > 0) {
    //         $reported = $reported + 1;              
    //         $table_body .="<span class='label label-success'>Submitted  for    $lastmonth </span><a href=" . site_url('rtk_management/rtk_orders') . " class='link'> View</a></td>";
    //     } else {
    //         $nonreported = $nonreported + 1;
    //         $table_body .=$report_link;
    //     }

    //     $table_body .="</td>";
    // }   
    // $county = $this->session->userdata('county_name');
    // $countyid = $this->session->userdata('county_id');
    // $data['countyid'] = $countyid;
    // $data['county'] = $county;
    // $data['table_body'] = $table_body;
    // $data['content_view'] = "rtk/rtk/scmlt/dpp_home_with_table";
    // $data['title'] = "Home";
    // $data['link'] = "home";
    // $total = $reported + $nonreported;
    // $percentage_complete = ceil($reported / $total * 100);
    // $percentage_complete = number_format($percentage_complete, 0);
    // $data['percentage_complete'] = $percentage_complete;
    // $data['reported'] = $reported;
    // $data['nonreported'] = $nonreported;
    // $data['facilities'] = Facilities::get_total_facilities_rtk_in_district($district);
    // $this->load->view('rtk/template', $data);

}
}
?>
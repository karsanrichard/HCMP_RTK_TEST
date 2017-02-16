<?php
/*

*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once ('home_controller.php');

class Rtk_Management extends Home_controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        ini_set('memory_limit', '-1');
        ini_set('max_input_vars', 3000);

        // error_reporting(E_ALL);
        // ini_set('display_errors', TRUE);
        // ini_set('display_startup_errors', TRUE);

        $this->load->library('Excel');
    }

    public function index() {
        echo "|";
    }

        //SCMLT FUNCTUONS

    public function scmlt_home(){
        $district = $this->session->userdata('district_id');                
        $user_id = $this->session->userdata('user_id');                
        $facilities = Facilities::get_total_facilities_rtk_in_district($district);       
        $district_name = districts::get_district_name_($district);                    
        $table_body = '';
        $reported = 0;
        $nonreported = 0;
        $date = date('d', time());

        // echo "<pre>";print_r($facilities);exit;

        $msg = $this->session->flashdata('message');
        if(isset($msg)){
            $data['notif_message'] = $msg;
        }
        if(isset($popout)){
            $data['popout'] = $popout;
        }

        $sql = "select distinct rtk_settings.* from rtk_settings, facilities where facilities.zone = rtk_settings.zone and facilities.rtk_enabled = 1";
        $settings = $this->db->query($sql)->result_array();
        $deadline_date = null;

        foreach ($settings as $key => $value) {
            $deadline_date = $value['deadline'];
            $five_day_alert = $value['5_day_alert'];
            $report_day_alert = $value['report_day_alert'];
            $overdue_alert = $value['overdue_alert'];
        }
        date_default_timezone_set("EUROPE/Moscow");

        foreach ($facilities as $facility_detail) {

            $lastmonth = date('F', strtotime("last day of previous month"));
            if($date>$deadline_date){
                $report_link = "<span class='label label-danger'>  Pending for $lastmonth </span>  <a href=" . site_url('rtk_management/get_report/' . $facility_detail['facility_code']) . " class='link report'></a></td>";
            }else{
                $report_link = "<span class='label label-danger'>  Pending for $lastmonth </span> <a href=" . site_url('rtk_management/get_report/' . $facility_detail['facility_code']) . " class='link report'> Report</a></td>";
            }

                // echo "$deadline_date"; die;
            $table_body .="<tr><td><a class='ajax_call_1' id='county_facility' name='" . base_url() . "rtk_management/get_rtk_facility_detail/$facility_detail[facility_code]' href='#'>" . $facility_detail["facility_code"] . "</td>";
            $table_body .="<td>" . $facility_detail['facility_name'] . "</td>";
            $table_body .="<td>";

            $lab_count = lab_commodity_orders::get_recent_lab_orders($facility_detail['facility_code']);

            // echo "<pre>";print($lab_count);
            if ($lab_count > 0) {
                $reported = $reported + 1;              
                $table_body .="<span class='label label-success'>Submitted for $lastmonth </span> <a href=" . site_url('rtk_management/rtk_orders') . " class='link'> View</a></td>";
            } else {
                $nonreported = $nonreported + 1;
                $table_body .=$report_link;
            }

            $table_body .="</td>";
        }  
        $total = $reported + $nonreported;
        $percentage_complete = ceil($reported / $total * 100);
        $percentage_complete = number_format($percentage_complete, 0);

        $progress_class = " ";
        if ($percentage_complete <= 100) {
            $progress_class = 'success';
            $alertype = 'success';
        }
        if ($percentage_complete < 75) {
            $progress_class = 'warning';
            $alertype = 'warning';
        }
        if ($percentage_complete < 50) {
            $progress_class = 'info';
            $alertype = 'info';
        }
        if ($percentage_complete < 25) {
            $progress_class = 'danger';
            $alertype = 'danger';
        }

        $date = date('d', time());

        $remainingdays = $deadline_date - $date;


        if($date>0 && $date <=$deadline_date && $percentage_complete<100){
            $alertmsg = 'Click on <u>Report</u> for all Facilities with the red label within the table below<br > '. $deadline_date;   

        } else if($date>0 && $date <=$deadline_date && $percentage_complete==100){
            $alertmsg = '<strong>Congratulations!</strong> <br/> You have reported for all facilities in your district. You can cross-check and edit your reports';     

        } else if($date>0 && $date >$deadline_date && $percentage_complete==100){
            $alertmsg = '<strong>Congratulations!</strong> <br/> You have reported for all facilities in your district.';     

        } else if($date>0 && $date >$deadline_date && $percentage_complete < 100){
            $alertmsg = 'Not all facilities were reported for on time';     

        }    

        $sql2 = "select * from user where id = $user_id";
        $result2 = $this->db->query($sql2)->result_array();


        $data['district_name'] = $district_name['district'];
        $data['percentage_complete'] = $percentage_complete;
        $data['report_day_alert'] = $report_day_alert;
        $data['deadline_date'] = $deadline_date;
        $data['remainingdays'] = $remainingdays;
        $data['alertype'] = $alertype;
        $data['alertmsg'] = $alertmsg;
        $data['table_body'] = $table_body;
        $data['district_id'] = $district;
        $data['content_view'] = "rtk/rtk/scmlt/dpp_home_with_table";
        $data['title'] = "Home";
        $data['link'] = "home";    

        // echo "<pre>";print_r($data);exit;
        $this->load->view('rtk/template', $data);

    }
    
    public function scmlt_orders($msg = NULL) {
        $district = $this->session->userdata('district_id');        
        $district_name = Districts::get_district_name($district)->toArray();        
        $d_name = $district_name[0]['district'];
        $countyid = $this->session->userdata('county_id');

        $data['countyid'] = $countyid;

        $data['title'] = "Orders";
        $data['content_view'] = "rtk/rtk/scmlt/rtk_orders_listing_v";
        $data['banner_text'] = $d_name . "Orders";
        //        $data['fcdrr_order_list'] = Lab_Commodity_Orders::get_district_orders($district);
        ini_set('memory_limit', '-1');

        date_default_timezone_set('EUROPE/moscow');
        $last_month = date('m');
        //            $month_ago=date('Y-'.$last_month.'-d');
        $month_ago = date('Y-m-d', strtotime("last day of previous month"));
        $sql = 'SELECT  
        facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
        FROM lab_commodity_orders, facilities
        WHERE lab_commodity_orders.facility_code = facilities.facility_code 
        AND lab_commodity_orders.order_date between ' . $month_ago . ' AND NOW()
        AND facilities.district =' . $district . '
        ORDER BY  lab_commodity_orders.id DESC ';
        /*$query = $this->db->query("SELECT  
        facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
        FROM lab_commodity_orders, facilities
        WHERE lab_commodity_orders.facility_code = facilities.facility_code 
        AND lab_commodity_orders.order_date between '$month_ago ' AND NOW()
        AND lab_commodity_orders.district_id =' . $district . '
        ORDER BY  lab_commodity_orders.id DESC");*/
        $query = $this->db->query($sql);

        $data['lab_order_list'] = $query->result_array();
        $data['all_orders'] = Lab_Commodity_Orders::get_district_orders($district);
        $myobj = Doctrine::getTable('districts')->find($district);
                //$data['district_incharge']=array($id=>$myobj->district);
        $data['myClass'] = $this;
        $data['d_name'] = $d_name;
        $data['msg'] = $msg;

        $this->load->view("rtk/template", $data);
    }
    public function scmlt_allocations($msg = NULL) {
        $district = $this->session->userdata('district_id');
        $district_name = Districts::get_district_name($district)->toArray();
        $countyid = $this->session->userdata('county_id');
        $data['countyid'] = $countyid;
        $d_name = $district_name[0]['district'];     
        $data['title'] = "Allocations";
        $data['content_view'] = "rtk/rtk/scmlt/rtk_allocation_v";
        $data['banner_text'] = $d_name . "Allocation";
        //        $data['lab_order_list'] = Lab_Commodity_Orders::get_district_orders($district);
        ini_set('memory_limit', '-1');

        $start_date = date("Y-m-", strtotime("-3 Month "));
        $start_date .='1';

        $end_date = date('Y-m-d', strtotime("last day of previous month"));      
        $allocations = $this->_allocation(NULL, NULL, $district,NUll, NULL,NULL);
        $data['lab_order_list'] = $allocations;
        $data['all_orders'] = Lab_Commodity_Orders::get_district_orders($district);
        $myobj = Doctrine::getTable('districts')->find($district);
        $data['myClass'] = $this;
        $data['msg'] = $msg;        
        $data['d_name'] = $d_name;

        $this->load->view("rtk/template", $data);
    }
    public function scmlt_summary( $year, $month) {

        $district = $this->session->userdata('district_id');   
        $district_name = Districts::get_district_name($district)->toArray();
        $d_name = $district_name[0]['district'];
        $month = $this->session->userdata('Month');

        if ($month == '') {
            $month = date('mY', time());
        }
        $year = substr($month, -4);
        $month_number = substr($month, 0, 2);
        // echo "$month_number";

        $data['reporting_details'] = $this->rtk_facilities_not_reported(NULL, null,$district,NULL, $year,$month_number,null);

        $sql3 = "select *  from rtk_district_percentage where month ='$month' and district_id = '$district' ";
        $result3 = $this->db->query($sql3)->result_array();  

        $total_facilities = $result3[0]['facilities'];
        $reported_facilities=$result3[0]['reported'];
        $nonreported_facilities= $total_facilities - $reported_facilities;
        $data['reported_facilities'] = $reported_facilities;
        $data['reported_facilities_percentage'] = $result3[0]['percentage'];
        $data['total_facilities'] = $total_facilities;
        $data['nonreported_facilities'] =$nonreported_facilities;
        $data['nonreported_facilities_percentage'] = round(($nonreported_facilities/$total_facilities)*100);

        $today = date('Y-m-d');
        $end_date = date('Y-m-t', strtotime($today));
        $beg_date = date('Y-m-');
        $beg_date .= '01';
        $q1 = "SELECT DISTINCT
        facilities.facility_code, facilities.facility_name
        FROM
        lab_commodity_orders,
        facilities,
        districts
        WHERE
        facilities.district = districts.id
        AND facilities.facility_code = lab_commodity_orders.facility_code
        AND districts.id = '$district'
        AND lab_commodity_orders.order_date BETWEEN '$beg_date' AND '$end_date'
        group by lab_commodity_orders.facility_code";

        $q_res1 = $this->db->query($q1)->result_array();
        foreach ($q_res1 as $vals) {
            if ($vals['order_date'] >$day15 ) {
                $late_reporting += 1;
            }
        }

        $sql = "select * from facilities where district = '$district'";
        $result = $this->db->query($sql)->result_array();

        $facilities = array();
        foreach($result as $value) {
            $facilities[$value['rtk_enabled']][] = $value;
        }
        // echo '<pre>';print_r($data['reporting_details']);die;

        $months_texts = array();
        $percentages = array();

        for ($i=11; $i >=0; $i--) { 
            $months =  date("mY", strtotime( date( 'Y-m-01' )." -$i months"));
            $j = $i+1;            
            $month_text =  date("M Y", strtotime( date( 'Y-m-01' )." -$j months")); 
            array_push($months_texts,$month_text);
            $sql2 = "select sum(reported) as reported, sum(facilities) as total, month from rtk_district_percentage where month ='$months' and district_id = '$district'";

            $result2 = $this->db->query($sql2)->result_array();            
            foreach ($result2 as $key => $value) {
                $reported = $value['reported'];
                $total = $value['total'];
                $percentage = round(($reported/$total)*100);
                if($percentage>100){
                    $percentage = 100;
                }
                array_push($percentages, $percentage);
                $trend_details[$month] = array('reported'=>$reported,'total'=>$total,'percentage'=>$percentage);
            }
        }
        $data['trend_details'] = json_encode($trend_details);        
        $data['months_texts'] = str_replace('"',"'",json_encode($months_texts));        
        $data['percentages'] = str_replace('"',"'",json_encode($percentages));                
        $data['first_month'] = date("M Y", strtotime( date( 'Y-m-01' )." -12 months")); 
        $data['last_month'] = date("M Y", strtotime( date( 'Y-m-01' )." -1 months")); 
        $data['district_consumption_data'] = $this->district_totals($year, $month_number, $district,$commodity_id);
        // echo '<pre>';print_r($data['district_consumption_data']);die;
        $data['d_name'] = $d_name;
        $data['facilities_list'] = $facilities;
        $data['title'] = "Summary";
        $data['link'] = "home";    
        $data['content_view'] = "rtk/rtk/scmlt/scmlt_summary";      
        $this->load->view('rtk/template', $data);
    }

        //Load FCDRR
    public function get_report($facility_code) {       

        $data['title'] = "Lab Commodities 3 Report";
        $data['content_view'] = "rtk/rtk/scmlt/fcdrr";
        $data['banner_text'] = "Lab Commodities 3 Report";
        $data['link'] = "rtk_management";
        $data['quick_link'] = "commodity_list";
        $my_arr = $this->_get_begining_balance($facility_code);
        // print_r($my_arr);die;
        $my_count = count($my_arr);
        // $data['beginning_bal'] = $my_arr; 
        $data['beginning_bal'] = $my_arr['ending_bal'];         
        $data['amcs'] = $my_arr['amcs'];         
        $data['facilities'] = Facilities::get_one_facility_details($facility_code);            
        $data['lab_categories'] = Lab_Commodity_Categories::get_active();
        $this->load->view("rtk/template", $data);
    }public function get_report_test($facility_code) {       

        $data['title'] = "Lab Commodities 3 Report";
        $data['content_view'] = "rtk/rtk/scmlt/fcdrrv2";
        $data['banner_text'] = "FACILITY CONSUMPTION DATA REPORT and REQUEST (F-CDRR) for LABORATORY COMMODITIES (MoH 643)";
        $data['link'] = "rtk_management";
        $data['quick_link'] = "commodity_list";
        $my_arr = $this->_get_begining_balance($facility_code);
        $my_count = count($my_arr);
        $data['beginning_bal'] = $my_arr;         
        $data['facilities'] = Facilities::get_one_facility_details($facility_code);            
        $data['lab_categories'] = Lab_Commodity_Categories::get_active();
        $this->load->view("rtk/template", $data);
    }

        //Load CD4 FCDRR
    public function get_cd4_report($facility_code) {    

        $data['title'] = "Lab Commodities 3 Report";
        $data['content_view'] = "rtk/rtk/scmlt/cd4_fcdrr";
        $data['banner_text'] = "CD4 Lab Commodities Report";
        $data['link'] = "rtk_management";
        $data['quick_link'] = "commodity_list";
        $my_arr = $this->_get_cd4_begining_balance($facility_code);

        // print_r($my_arr);die;

        $my_count = count($my_arr);
        $data['beginning_bal'] = $my_arr;         
        $data['facilities'] = Facilities::get_one_facility_details($facility_code);            
        $data['lab_categories'] = Cd4_Lab_Commodity_Categories::get_active();

        // echo "<pre>"; print_r($data['lab_categories']);die;s


        $this->load->view("rtk/template", $data);
    }
    function update_lab_details($month){
        $current_month = substr($month, 0,2);
        $year = substr($month, -4);
        $firstdate = $year.'-'.$current_month.'-01';
        $lastdate = $year.'-'.$current_month.'-31';

        echo "$firstdate and $lastdate";
        $sql = "SELECT 
        facilities.facility_code, lab_commodity_orders.id
        FROM
        facilities,
        lab_commodity_orders
        WHERE
        order_date BETWEEN '2015-11-01' AND '2015-11-31'
        AND facilities.facility_code = lab_commodity_orders.facility_code";
        $result = $this->db->query($sql)->result_array();

        foreach ($result as $key => $value) {

            $order_id = $value['id'];
            $mfl = $value['facility_code'];

            $sql2 = "SELECT 
            lab_commodity_details.commodity_id,
            lab_commodity_details.q_used
            FROM
            lab_commodity_details,
            lab_commodity_orders
            WHERE
            lab_commodity_details.order_id = lab_commodity_orders.id
            AND lab_commodity_orders.id = '$order_id'";

            $result2 = $this->db->query($sql2)->result_array();
            $khb = $result2[0]['q_used'];
            $confirm = $result2[1]['q_used'];
            $tie = $result2[2]['q_used'];
            $dbs = $result2[3]['q_used'];
            $determine = $result2[4]['q_used'];;
            $new_screening = $khb + $determine;

            echo "$mfl order id = $order_id determine $determine + khb $khb = $new_screening, confrim = $confirm, tie = $tie <br/>";

            $sql3 ="UPDATE lab_commodity_details
            SET newqused = CASE commodity_id
            WHEN '4' THEN '$new_screening'
            WHEN '5' THEN '$confirm'
            WHEN '6' THEN '$tie'
            WHEN '7' THEN '$dbs'
            END
            WHERE commodity_id IN ('4','5','6','7') and order_id = $order_id";

            $this->db->query($sql3);

        // echo "<pre>"; print_r($result2);

        }
    }
    function get_commodity_amcs($zone, $month){
        // $sql3 ="UPDATE lab_commodity_details
        //             SET amc=0";

        //     $this->db->query($sql3);
        //     die;
        $current_month = substr($month, 0,2);
        $last_month = $current_month - 1;
        $three_months_ago = $current_month - 2;
        $year = substr($month, -4);

        $firstdate = $year.'-'.$current_month.'-01';
        $lastdate = $year.'-'.$current_month.'-31';

        $firstdate2 = $year.'-'.$last_month.'-01';
        $lastdate2 = $year.'-'.$last_month.'-31';

        $firstdate3 = $year.'-'.$three_months_ago.'-01';
        $lastdate3 = $year.'-'.$three_months_ago.'-31';

        // echo "current month dates: $firstdate to $lastdate <br/> <br/>";
        // echo "last month dates: $firstdate2 to $lastdate2 <br/> <br/>";
        // echo "three months ago dates: $firstdate3 to $lastdate3 <br/> <br/>";

        // die;
        $sql = "SELECT 
        facilities.facility_code
        FROM
        facilities,
        districts,
        counties
        WHERE
        facilities.district = districts.id
        and facilities.rtk_enabled = 1
        AND counties.id = districts.county";
        $result = $this->db->query($sql)->result_array();

        //     foreach ($result as $key => $value) {
        //         # code...
        //     }

        foreach ($result as $key => $value) {
            $mfl = $value['facility_code'];
            // echo "works"; die;

            for ($i=4; $i <=6 ; $i++) { 
            //     # code...

                $sql2 = "SELECT 
                commodity_id, q_used as q_used
                FROM
                lab_commodity_details
                WHERE
                commodity_id = '$i'
                AND created_at BETWEEN '2017-01-01' AND '2017-01-31'
                and facility_code = '$mfl'";
            // echo "$sql2";die;
                $sql3 = "SELECT 
                commodity_id, q_used as q_used
                FROM
                lab_commodity_details
                WHERE
                commodity_id = '$i'
                AND created_at BETWEEN '2016-12-01' AND '2016-12-31'
                and facility_code = '$mfl'";
                $sql4 = "SELECT 
                commodity_id, q_used as q_used
                FROM
                lab_commodity_details
                WHERE
                commodity_id = '$i'
                AND created_at BETWEEN '2016-11-01' AND '2016-11-31'
                and facility_code = '$mfl'"; 

                $result2 = $this->db->query($sql2)->result_array();
                $result3 = $this->db->query($sql3)->result_array();
                $result4 = $this->db->query($sql4)->result_array();
                $count =0;
                if (empty($result2)) {
                    $count = $count;
                }else{
                    $count =$count+1;
                }

                if (empty($result3)) {
                    $count = $count;
                }else{
                    $count =$count +1;
                }

                if (empty($result4)) {
                    $count = $count;
                }else{
                    $count =$count+1;
                }

                echo $mfl.' commodity '.$i .'  first month:'.$result2[0]['q_used'].' second month:'.$result3[0]['q_used'].' third month:'.$result4[0]['q_used'].'<br/>';

        // echo $count;
        // die;
                $amc = ceil(($result2[0]['q_used'] + $result3[0]['q_used']+ $result4[0]['q_used']) /$count);
                echo "AMC becomes ".$amc."<br/>";

                $sql5 = "update lab_commodity_details set amc = '$amc' where commodity_id = '$i' AND created_at BETWEEN '$firstdate' AND '$lastdate' and facility_code = '$mfl'";
                $this->db->query($sql5);        
        // echo $sql5.'<br/>';
            }
        }
    }
        //Begining Balances
    function _get_begining_balance($facility_code) {
        // $facility_code = '12865';
        $result_bal = array();
        $all_amc =array();

        $start_date_bal = date('Y-m-d', strtotime("first day of previous month"));
        $end_date_bal = date('Y-m-d', strtotime("last day of previous month"));

        $current_month = substr($end_date_bal, 5,2);
        $last_month = $current_month - 1;
        $three_months_ago = $current_month - 2;
        $year = substr($end_date_bal, 0,4);

        $firstdate = $year.'-'.$current_month.'-01';
        $lastdate = $year.'-'.$current_month.'-31';

        $firstdate2 = $year.'-0'.$last_month.'-01';
        $lastdate2 = $year.'-0'.$last_month.'-31';

        $firstdate3 = $year.'-0'.$three_months_ago.'-01';
        $lastdate3 = $year.'-0'.$three_months_ago.'-31';


        for ($i=4; $i <=6 ; $i++) { 
        //     # code...

            $sql2 = "SELECT 
            commodity_id, q_used
            FROM
            lab_commodity_details
            WHERE
            commodity_id = '$i'
            AND created_at BETWEEN '$firstdate' AND '$lastdate'
            and facility_code = '$facility_code'";
            $sql3 = "SELECT 
            commodity_id, q_used
            FROM
            lab_commodity_details
            WHERE
            commodity_id = '$i'
            AND created_at BETWEEN '$firstdate2' AND '$lastdate2'
            and facility_code = '$facility_code'";

            $sql4 = "SELECT 
            commodity_id, q_used
            FROM
            lab_commodity_details
            WHERE
            commodity_id = '$i'
            AND created_at BETWEEN '$firstdate3' AND '$lastdate3'
            and facility_code = '$facility_code'"; 

            $result2 = $this->db->query($sql2)->result_array();
            $result3 = $this->db->query($sql3)->result_array();
            $result4 = $this->db->query($sql4)->result_array();

            $count =0;
            if (empty($result2)) {
                $count = $count;
            }else{
                $count =$count+1;
            }

            if (empty($result3)) {
                $count = $count;
            }else{
                $count =$count +1;
            }

            if (empty($result4)) {
                $count = $count;
            }else{
                $count =$count+1;
            }

            $amc = ceil(($result2[0]['q_used'] + $result3[0]['q_used']+ $result4[0]['q_used']) /$count);
        // echo $firstdate2. 'and'.$lastdate2.'</br>';
        // echo $sql3.'</br>';
            array_push($all_amc, $amc);
        }

        $sql_bal = "SELECT lab_commodity_details.closing_stock from lab_commodity_orders, lab_commodity_details 
        where lab_commodity_orders.id = lab_commodity_details.order_id 
        and lab_commodity_orders.order_date between '$start_date_bal' and '$end_date_bal' 
        and lab_commodity_orders.facility_code='$facility_code'";

        $res_bal = $this->db->query($sql_bal)->result_array();

        foreach ($res_bal as $row_bal) {
            array_push($result_bal, $row_bal['closing_stock']);
        }

        $all_values = array('ending_bal' =>$result_bal, 'amcs'=>$all_amc);
        // print_r($all_values); die;
        return $all_values;
    }

        //CD4 Begining Balances
    function _get_cd4_begining_balance($facility_code) {
        $result_bal = array();
        $start_date_bal = date('Y-m-d', strtotime("first day of previous month"));
        $end_date_bal = date('Y-m-d', strtotime("last day of previous month"));
        $sql_bal = "SELECT cd4_fcdrr_commodities.closing_stock from cd4_fcdrr, cd4_fcdrr_commodities 
        where cd4_fcdrr.id = cd4_fcdrr_commodities.fcdrr_id 
        and cd4_fcdrr.order_date between '$start_date_bal' and '$end_date_bal' 
        and cd4_fcdrr.facility_code='$facility_code'";

        $res_bal = $this->db->query($sql_bal)->result_array();

        foreach ($res_bal as $row_bal) {
            array_push($result_bal, $row_bal['closing_stock']);
        }
        return $result_bal;
    }

        //Save FCDRR
    public function save_lab_report_data() {

        date_default_timezone_set("EUROPE/Moscow");
        $firstday = date('D dS M Y', strtotime("first day of previous month"));
        $lastday = date('D dS M Y', strtotime("last day of previous month"));
        $lastmonth = date('F', strtotime("last day of previous month"));

        $month = $lastmonth;
        $district_id = $_POST['district'];
        $facility_code = $_POST['facility_code'];
        $drug_id = $_POST['commodity_id'];
        $unit_of_issue = $_POST['unit_of_issue'];
        $b_balance = $_POST['b_balance'];
        $physical_b_balance = $_POST['phisical_b_balance'];
        $q_received = $_POST['q_received'];
        $q_received_other = $_POST['q_received_other'];
        $q_used = $_POST['q_used'];
        $tests_done = $_POST['tests_done'];
        $losses = $_POST['losses'];
        $pos_adj = $_POST['pos_adj'];
        $neg_adj = $_POST['neg_adj'];
        $physical_count = $_POST['physical_count'];
        $fcdrr_physical_count = $_POST['fcdrr_physical_count'];
        $q_expiring = $_POST['q_expiring'];
        $days_out_of_stock = $_POST['days_out_of_stock'];
        $q_requested = $_POST['q_requested'];
        $amc = $_POST['amc'];
        $commodity_count = count($drug_id);

        $vct = $_POST['vct'];
        $pitc = $_POST['pitc'];
        $pmtct = $_POST['pmtct'];
        $b_screening = $_POST['blood_screening'];
        $other = $_POST['other2'];
        $specification = $_POST['specification'];
        $rdt_under_tests = $_POST['rdt_under_tests'];
        $rdt_under_pos = $_POST['rdt_under_positive'];
        $rdt_btwn_tests = $_POST['rdt_to_tests'];
        $rdt_btwn_pos = $_POST['rdt_to_positive'];
        $rdt_over_tests = $_POST['rdt_over_tests'];
        $rdt_over_pos = $_POST['rdt_over_positive'];
        $micro_under_tests = $_POST['micro_under_tests'];
        $micro_under_pos = $_POST['micro_under_positive'];
        $micro_btwn_tests = $_POST['micro_to_tests'];
        $micro_btwn_pos = $_POST['micro_to_positive'];
        $micro_over_tests = $_POST['micro_over_tests'];
        $micro_over_pos = $_POST['micro_over_positive'];
        $beg_date = $_POST['begin_date'];
        $end_date = $_POST['end_date'];
        $explanation = $_POST['explanation'];
        $compiled_by = $_POST['compiled_by'];
        $moh_642 = $_POST['moh_642'];
        $moh_643 = $_POST['moh_643'];

        date_default_timezone_set('EUROPE/Moscow');
        $beg_date = date('Y-m-d', strtotime("first day of previous month"));
        $end_date = date('Y-m-d', strtotime("last day of previous month"));

        $user_id = $this->session->userdata('user_id');        

        $order_date = date('y-m-d');
        $count = 1;
        $data = array('facility_code' => $facility_code, 'district_id' => $district_id, 'compiled_by' => $compiled_by, 'order_date' => $order_date, 'vct' => $vct, 'pitc' => $pitc, 'pmtct' => $pmtct, 'b_screening' => $b_screening, 'other' => $other, 'specification' => $specification, 'rdt_under_tests' => $rdt_under_tests, 'rdt_under_pos' => $rdt_under_pos, 'rdt_btwn_tests' => $rdt_btwn_tests, 'rdt_btwn_pos' => $rdt_btwn_pos, 'rdt_over_tests' => $rdt_over_tests, 'rdt_over_pos' => $rdt_over_pos, 'micro_under_tests' => $micro_under_tests, 'micro_under_pos' => $micro_under_pos, 'micro_btwn_tests' => $micro_btwn_tests, 'micro_btwn_pos' => $micro_btwn_pos, 'micro_over_tests' => $micro_over_tests, 'micro_over_pos' => $micro_over_pos, 'beg_date' => $beg_date, 'end_date' => $end_date, 'explanation' => $explanation, 'moh_642' => $moh_642, 'moh_643' => $moh_643, 'report_for' => $lastmonth, 'user_id' =>$user_id);
        $u = new Lab_Commodity_Orders();
        $u->fromArray($data);
        $u->save();
        $object_id = $u->get('id');
        $this->logData('13', $object_id);
        $this->update_amc($facility_code);

        $lastId = Lab_Commodity_Orders::get_new_order($facility_code);
        $new_order_id = $lastId->maxId;
        $count++;

        for ($i = 0; $i < $commodity_count; $i++) {            
            $mydata = array('order_id' => $new_order_id, 'facility_code' => $facility_code, 'district_id' => $district_id, 'commodity_id' => $drug_id[$i], 'unit_of_issue' => $unit_of_issue[$i], 'beginning_bal' => $b_balance[$i],'physical_beginning_bal' => $physical_b_balance[$i], 'q_received' => $q_received[$i], 'q_recieved_others' => $q_received_other[$i], 'q_used' => $q_used[$i], 'no_of_tests_done' => $tests_done[$i], 'losses' => $losses[$i], 'positive_adj' => $pos_adj[$i], 'negative_adj' => $neg_adj[$i], 'closing_stock' => $physical_count[$i],'physical_closing_stock' => $fcdrr_physical_count[$i], 'q_expiring' => $q_expiring[$i], 'days_out_of_stock' => $days_out_of_stock[$i], 'q_requested' => $q_requested[$i], 'amc' => $amc[$i]);
            Lab_Commodity_Details::save_lab_commodities($mydata); 
        // print_r($mydata); die;          
        }
        // die;
        $q = "select county from districts where id='$district_id'";
        $res = $this->db->query($q)->result_array();
        foreach ($res as $key => $value) {
            $county = $value['county'];
        }

        $r = "select partner from facilities where facility_code='$facility_code'";
        $resr = $this->db->query($r)->result_array();
        foreach ($resr as $key => $value) {
            $partner = $value['partner'];
        }
        if($partner=0){
            $partner = null;
        }
        $this->_update_reports_count('add',$county,$district_id,$partner);
        $this->session->set_flashdata('message', 'The report has been saved');
        // redirect('rtk_management/scmlt_home');
        if ($usertype_id ==8) {
            redirect("rtk_management/scmlt_home");

        }else{
            redirect("Home");

        }

    }
        //Save cd4 FCDRR
    public function save_cd4_report_data() {

        date_default_timezone_set("EUROPE/Moscow");
        $firstday = date('D dS M Y', strtotime("first day of previous month"));
        $lastday = date('D dS M Y', strtotime("last day of previous month"));
        $lastmonth = date('F', strtotime("last day of previous month"));



        $month = $lastmonth;
        $district_id = $_POST['district'];
        $facility_code = $_POST['facility_code'];
        $drug_id = $_POST['commodity_id'];
        $unit_of_issue = $_POST['unit_of_issue'];
        $b_balance = $_POST['b_balance'];
        $q_received = $_POST['q_received'];
        $q_used = $_POST['q_used'];
        $tests_done = $_POST['tests_done'];
        $losses = $_POST['losses'];
        $pos_adj = $_POST['pos_adj'];
        $neg_adj = $_POST['neg_adj'];
        $physical_count = $_POST['physical_count'];
        $q_expiring = $_POST['q_expiring'];
        $days_out_of_stock = $_POST['days_out_of_stock'];
        $q_requested = $_POST['q_requested'];
        $commodity_count = count($drug_id);

        $calibur_pead   =   $_POST['calibur_pead'];
        $calibur_adult  =   $_POST['calibur_adult'];
        $caliburs       =   $calibur_pead + $calibur_adult;
        $count_pead     =   $_POST['count_pead'];
        $count_adult    =   $_POST['count_adult'];
        $counts         =   $count_pead + $count_adult;
        $partec_pead    =   $_POST['partec_pead'];
        $partec_adult   =   $_POST['partec_adult'];
        $cyflows        =   $partec_pead + $partec_adult;
        $adults_bel_cl  =   $_POST['adults_bel_cl'];
        $pead_bel_cl    =   $_POST['pead_bel_cl'];
        $pima   =   $_POST['pima'];
        $presto =   $_POST['presto'];
        $total_tests    =   $caliburs+$counts+$cyflows+$pima+$presto;

        $beg_date = $_POST['begin_date'];
        $end_date = $_POST['end_date'];
        $explanation = $_POST['explanation'];
        $compiled_by = $_POST['compiled_by'];
        $moh_642 = $_POST['moh_642'];
        $moh_643 = $_POST['moh_643'];

        date_default_timezone_set('EUROPE/Moscow');
        $beg_date = date('Y-m-d', strtotime("first day of previous month"));
        $end_date = date('Y-m-d', strtotime("last day of previous month"));

        $user_id = $this->session->userdata('user_id');        

        $order_date = date('y-m-d');
        $count = 1;
        $data = array(
            'facility_code' => $facility_code, 
            'district_id' => $district_id, 
            'compiled_by' => $compiled_by, 
            'order_date' => $order_date, 
            'calibur_pead' => $calibur_pead, 
            'calibur_adults' => $calibur_adult, 
            'caliburs' => $caliburs, 
            'count_pead' => $count_pead, 
            'count_adults' => $count_adult, 
            'counts' => $counts, 
            'cyflow_pead' => $partec_pead, 
            'cyflow_adults' => $partec_adult, 
            'cyflows' => $cyflows, 
            'total_tests' => $total_tests, 
            'pima_tests' => $pima, 
            'presto_tests' => $presto, 
            'adults_bel_cl' => $adults_bel_cl, 
            'pead_bel_cl' => $pead_bel_cl, 
            'beg_date' => $beg_date, 
            'end_date' => $end_date, 
            'explanation' => $explanation, 
            'moh_642' => $moh_642, 
            'moh_643' => $moh_643, 
            'report_for' => $lastmonth
            );
        $u = new Cd4_Fcdrr();
        $u->fromArray($data);
        $u->save();
        $object_id = $u->get('id');
        // $this->logData('13', $object_id);
        // $this->update_amc($facility_code);

        $lastId = Cd4_Fcdrr::get_new_order($facility_code);
        $new_fcdrr_id = $lastId->maxId;
        $count++;

        for ($i = 0; $i < $commodity_count; $i++) {            
            $mydata = array(
                'fcdrr_id' => $new_fcdrr_id, 
                'facility_code' => $facility_code, 
                'district_id' => $district_id, 
                'commodity_id' => $drug_id[$i], 
                'unit_of_issue' => $unit_of_issue[$i], 
                'beginning_bal' => $b_balance[$i], 
                'q_received' => $q_received[$i], 
                'q_used' => $q_used[$i], 
                'no_of_tests_done' => $tests_done[$i], 
                'losses' => $losses[$i], 
                'positive_adj' => $pos_adj[$i], 
                'negative_adj' => $neg_adj[$i], 
                'closing_stock' => $physical_count[$i], 
                'q_expiring' => $q_expiring[$i], 
                'days_out_of_stock' => $days_out_of_stock[$i], 
                'q_requested' => $q_requested[$i]
                );
            Cd4_Fcdrr_Commodities::save_lab_commodities($mydata);           
        }
        // $q = "select county from districts where id='$district_id'";
        // $res = $this->db->query($q)->result_array();
        // foreach ($res as $key => $value) {
        //     $county = $value['county'];
        // }

        // $r = "select partner from facilities where facility_code='$facility_code'";
        // $resr = $this->db->query($r)->result_array();
        // foreach ($resr as $key => $value) {
        //     $partner = $value['partner'];
        // }
        // if($partner=0){
        //     $partner = null;
        // }
        //         // $this->_update_reports_count('add',$county,$district_id,$partner);
        // $this->session->set_flashdata('message', 'The report has been saved');
        redirect('cd4_management/facility_home');

    }


        //Edit FCDRR
    public function edit_lab_order_details($order_id, $msg = NULL) {
        $delivery = $this->uri->segment(3);
        $district = $this->session->userdata('district_id');
        $data['title'] = "Lab Commodity Order Details";    
        ini_set('memory_limit', '-1');
        $data['order_id'] = $order_id;
        $data['content_view'] = "rtk/rtk/scmlt/fcdrr_edit";
        $data['banner_text'] = "Lab Commodity Order Details";
        $data['lab_categories'] = Lab_Commodity_Categories::get_all();
        $data['detail_list'] = Lab_Commodity_Details::get_order($order_id);
        $result = $this->db->query('SELECT * 
            FROM lab_commodity_details, counties, facilities, districts, lab_commodity_orders, lab_commodity_categories, lab_commodities
            WHERE lab_commodity_details.facility_code = facilities.facility_code
            AND counties.id = districts.county
            AND facilities.facility_code = lab_commodity_orders.facility_code
            AND lab_commodity_details.commodity_id = lab_commodities.id
            AND lab_commodity_categories.id = lab_commodities.category
            AND facilities.district = districts.id
            AND lab_commodity_details.order_id = lab_commodity_orders.id
            AND lab_commodity_orders.id = ' . $order_id . '');
        $data['all_details'] = $result->result_array();      
        $this->load->view("rtk/template", $data);
    }

        //Update the FCDRR Online
    public function update_lab_commodity_orders() {
        $rtk = new Rtk_Management();
        $order_id = $_POST['order_id'];
        $detail_id = $_POST['detail_id'];
        $district_id = $_POST['district'];
        $facility_code = $_POST['facility_code'];
        $drug_id = $_POST['commodity_id'];
        $unit_of_issue = $_POST['unit_of_issue'];
        $b_balance = $_POST['b_balance'];
        $q_received = $_POST['q_received'];
        $q_used = $_POST['q_used'];
        $tests_done = $_POST['tests_done'];
        $losses = $_POST['losses'];
        $pos_adj = $_POST['pos_adj'];
        $neg_adj = $_POST['neg_adj'];
        $physical_count = $_POST['physical_count'];
        $q_expiring = $_POST['q_expiring'];
        $days_out_of_stock = $_POST['days_out_of_stock'];
        $q_requested = $_POST['q_requested'];
        $commodity_count = count($drug_id);
        $detail_count = count($detail_id);

        $vct = $_POST['vct'];
        $pitc = $_POST['pitc'];
        $pmtct = $_POST['pmtct'];
        $b_screening = $_POST['blood_screening'];
        $other = $_POST['other2'];
        $specification = $_POST['specification'];
        $rdt_under_tests = $_POST['rdt_under_tests'];
        $rdt_under_pos = $_POST['rdt_under_positive'];
        $rdt_btwn_tests = $_POST['rdt_to_tests'];
        $rdt_btwn_pos = $_POST['rdt_to_positive'];
        $rdt_over_tests = $_POST['rdt_over_tests'];
        $rdt_over_pos = $_POST['rdt_over_positive'];
        $micro_under_tests = $_POST['micro_under_tests'];
        $micro_under_pos = $_POST['micro_under_positive'];
        $micro_btwn_tests = $_POST['micro_to_tests'];
        $micro_btwn_pos = $_POST['micro_to_positive'];
        $micro_over_tests = $_POST['micro_over_tests'];
        $micro_over_pos = $_POST['micro_over_positive'];
        date_default_timezone_set('EUROPE/Moscow');
        $beg_date = date('y-m-d', strtotime($_POST['begin_date']));
        $end_date = date('y-m-d', strtotime($_POST['end_date']));
        $explanation = $_POST['explanation'];
        $compiled_by = $_POST['compiled_by'];

        $moh_642 = $_POST['moh_642'];
        $moh_643 = $_POST['moh_643'];

        $myobj = Doctrine::getTable('Lab_Commodity_Orders')->find($order_id);

        $myobj->vct = $vct;
        $myobj->pitc = $pitc;
        $myobj->pmtct = $pmtct;
        $myobj->b_screening = $b_screening;
        $myobj->other = $other;
        $myobj->specification = $specification;
        $myobj->rdt_under_tests = $rdt_under_tests;
        $myobj->rdt_under_pos = $rdt_under_pos;
        $myobj->rdt_btwn_tests = $rdt_btwn_tests;
        $myobj->rdt_btwn_pos = $rdt_btwn_pos;
        $myobj->rdt_over_tests = $rdt_over_tests;
        $myobj->rdt_over_pos = $rdt_over_pos;
        $myobj->micro_under_tests = $micro_under_tests;
        $myobj->micro_under_pos = $micro_under_pos;
        $myobj->micro_btwn_tests = $micro_btwn_tests;
        $myobj->micro_btwn_pos = $micro_btwn_pos;
        $myobj->micro_over_tests = $micro_over_tests;
        $myobj->micro_over_pos = $micro_over_pos;
        $myobj->beg_date = $beg_date;
        $myobj->end_date = $end_date;
        $myobj->explanation = $explanation;
        $myobj->compiled_by = $compiled_by;
        $myobj->moh_642 = $moh_642;
        $myobj->moh_643 = $moh_643;
        $myobj->save();
        $object_id = $myobj->get('id');
        $this->logData('14', $object_id);
        $q = "select id from lab_commodity_details where order_id = $order_id";
        $res = $this->db->query($q);
        $ids = $res->result_array();  

        for ($i = 0; $i < $detail_count; $i++) {

            $id = $ids[$i]['id'];           
            $sql = "UPDATE `lab_commodity_details` SET `beginning_bal`=$b_balance[$i],
            `q_received`='$q_received[$i]',`q_used`=$q_used[$i],`no_of_tests_done`=$tests_done[$i],`losses`=$losses[$i],
            `positive_adj`=$pos_adj[$i],`negative_adj`=$neg_adj[$i],`closing_stock`=$physical_count[$i],
            `q_expiring`=$q_expiring[$i],`days_out_of_stock`=$days_out_of_stock[$i],`q_requested`=$q_requested[$i] WHERE id= $id ";
            $this->db->query($sql);
        }

        redirect('rtk_management/scmlt_orders');
    }

    public function rtk_orders($msg = NULL) {
        $district = $this->session->userdata('district_id');        
        $district_name = Districts::get_district_name($district)->toArray();        
        $d_name = $district_name[0]['district'];
        $countyid = $this->session->userdata('county_id');

        $data['countyid'] = $countyid;

        $data['title'] = "Orders";
        $data['content_view'] = "rtk/rtk/dpp/rtk_orders_listing_v";
        $data['banner_text'] = $d_name . "Orders";
        //        $data['fcdrr_order_list'] = Lab_Commodity_Orders::get_district_orders($district);
        ini_set('memory_limit', '-1');

        date_default_timezone_set('EUROPE/moscow');
        $last_month = date('m');
        //            $month_ago=date('Y-'.$last_month.'-d');

        /*$query = $this->db->query("SELECT  
        facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
        FROM lab_commodity_orders, facilities
        WHERE lab_commodity_orders.facility_code = facilities.facility_code 
        AND lab_commodity_orders.order_date between '$month_ago ' AND NOW()
        AND lab_commodity_orders.district_id =' . $district . '
        ORDER BY  lab_commodity_orders.id DESC");*/
                // $query = $this->db->query($sql);

        $data['lab_order_list'] = $this->get_lab_orders($district);
        $data['all_orders'] = Lab_Commodity_Orders::get_district_orders($district);
        $myobj = Doctrine::getTable('districts')->find($district);
                //$data['district_incharge']=array($id=>$myobj->district);
        $data['myClass'] = $this;
        $data['d_name'] = $d_name;
        $data['msg'] = $msg;

        $this->load->view("rtk/template", $data);
}

function get_lab_orders($district, $facility_code=null){
    $month_ago = date('Y-m-d', strtotime("last day of previous month"));
    $conditions = '';

    if (isset($facility_code)) {

        $conditions = ' and facilities.facility_code = '.$facility_code ;
    }
    $sql = 'SELECT  
    facilities.facility_code,facilities.facility_name,lab_commodity_orders.id,lab_commodity_orders.order_date,lab_commodity_orders.district_id,lab_commodity_orders.compiled_by,lab_commodity_orders.facility_code
    FROM lab_commodity_orders, facilities
    WHERE lab_commodity_orders.facility_code = facilities.facility_code '.$conditions.' 
    AND lab_commodity_orders.order_date between ' . $month_ago . ' AND NOW()
    AND facilities.district =' . $district . '
    ORDER BY  lab_commodity_orders.id DESC ';

    $query = $this->db->query($sql)->result_array();

    return $query;
}

        //VIew FCDRR Report
public function lab_order_details($order_id, $msg = NULL) {
    $delivery = $this->uri->segment(3);
    $district = $this->session->userdata('district_id');
    $data['title'] = "Lab Commodity Order Details";       
    $data['order_id'] = $order_id;
    $data['content_view'] = "rtk/rtk/scmlt/fcdrr_report";
    $data['banner_text'] = "Lab Commodity Order Details";

    $data['lab_categories'] = Lab_Commodity_Categories::get_all();
    $data['detail_list'] = Lab_Commodity_Details::get_order($order_id);

    $result = $this->db->query('SELECT * 
        FROM lab_commodity_details, counties, facilities, districts, lab_commodity_orders, lab_commodity_categories, lab_commodities
        WHERE lab_commodity_details.facility_code = facilities.facility_code
        AND counties.id = districts.county
        AND facilities.facility_code = lab_commodity_orders.facility_code
        AND lab_commodity_details.commodity_id = lab_commodities.id
        AND lab_commodity_categories.id = lab_commodities.category
        AND facilities.district = districts.id
        AND lab_commodity_details.order_id = lab_commodity_orders.id
        AND lab_commodity_orders.id = ' . $order_id . '');
    $data['all_details'] = $result->result_array();
    $this->load->view("rtk/template", $data);
}

        //Print the FCDRR
public function get_lab_report($order_no, $report_type) {
    $table_head = '<style>table.data-table {border: 1px solid #DDD;margin: 10px auto;border-spacing: 0px;}
    table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
    table.data-table td, table th {padding: 4px;}
    table.data-table td {border: none;border-left: 1px solid #DDD;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
    .col5{background:#D8D8D8;}</style></table>
    <table class="data-table" width="100%">
        <thead>
            <tr>
                <th><strong>Category</strong></th>
                <th><strong>Description</strong></th>
                <th><strong>Unit of Issue</strong></th>
                <th><strong>Beginning Balance</strong></th>
                <th><strong>Quantity Received</strong></th>
                <th><strong>Quantity Used</strong></th>
                <th><strong>Number of Tests Done</strong></th>
                <th><strong>Losses</strong></th>
                <th><strong>Positive Adjustments</strong></th>
                <th><strong>Negative Adjustments</strong></th>
                <th><strong>Closing Stock</strong></th>
                <th><strong>Quantity Expiring in 6 Months</strong></th>
                <th><strong>Days Out of Stock</strong></th>
                <th><strong>Quantity Requested</strong></th>
            </tr>
        </thead>
        <tbody>';
            $detail_list = Lab_Commodity_Details::get_order($order_no);
            $table_body = '';
            foreach ($detail_list as $detail) {
                $table_body .= '<tr><td>' . $detail['category_name'] . '</td>';
                $table_body .= '<td>' . $detail['commodity_name'] . '</td>';
                $table_body .= '<td>' . $detail['unit_of_issue'] . '</td>';
                $table_body .= '<td>' . $detail['beginning_bal'] . '</td>';
                $table_body .= '<td>' . $detail['q_received'] . '</td>';
                $table_body .= '<td>' . $detail['q_used'] . '</td>';
                $table_body .= '<td>' . $detail['no_of_tests_done'] . '</td>';
                $table_body .= '<td>' . $detail['losses'] . '</td>';
                $table_body .= '<td>' . $detail['positive_adj'] . '</td>';
                $table_body .= '<td>' . $detail['negative_adj'] . '</td>';
                $table_body .= '<td>' . $detail['closing_stock'] . '</td>';
                $table_body .= '<td>' . $detail['q_expiring'] . '</td>';
                $table_body .= '<td>' . $detail['days_out_of_stock'] . '</td>';
                $table_body .= '<td>' . $detail['q_requested'] . '</td></tr>';
            }
            $table_foot = '</tbody></table>';
            $report_name = "Lab Commodities Order " . $order_no . " Details";
            $title = "Lab Commodities Order " . $order_no . " Details";
            $html_data = $table_head . $table_body . $table_foot;

            switch ($report_type) {
                case 'excel' :
                $this->_generate_lab_report_excel($report_name, $title, $html_data);
                break;
                case 'pdf' :
                $this->_generate_lab_report_pdf($report_name, $title, $html_data);
                break;
            }
        }


        //Generate the FCDRR PDF

        function _generate_lab_report_pdf($report_name, $title, $html_data) {

            /*         * ******************************************setting the report title******************** */

            $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms-resized.png' height='70' width='70'style='vertical-align: top;' > </img></div>
            <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>$title</div>
            <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>
                Ministry of Health</div>
                <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold;display: block; font-size: 13px;'>Health Commodities Management Platform</div><hr />";

                /*         * ********************************initializing the report ********************* */
                $this->load->library('mpdf');
                $this->mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
                $this->mpdf->SetTitle($title);
                $this->mpdf->WriteHTML($html_title);
                $this->mpdf->simpleTables = true;
                $this->mpdf->WriteHTML('<br/>');
                $this->mpdf->WriteHTML($html_data);
                $report_name = $report_name . ".pdf";
                $this->mpdf->Output($report_name, 'D');
            }

        //Generate the FCDRR Excel
            function _generate_lab_report_excel($report_name, $title, $html_data) {
                $data = $html_data;
                $filename = $report_name;
                header("Content-type: application/excel");
                header("Content-Disposition: attachment; filename=$filename.xls");
                echo "$data";
            }







        ///*** CLC Functions ***        ///
            public function county_home() {
                $lastday = date('Y-m-d', strtotime("last day of previous month"));
                $countyid = $this->session->userdata('county_id');
                $districts = districts::getDistrict($countyid);
                $county_name = counties::get_county_name($countyid);
                $County = $county_name[0]['county'];
        //$reports = array();
                $tdata = ' ';
        // foreach ($districts as $value) {
        //     $q = $this->db->query('SELECT lab_commodity_orders.id, lab_commodity_orders.facility_code, lab_commodity_orders.compiled_by, lab_commodity_orders.order_date, lab_commodity_orders.district_id, districts.id as distid, districts.district, facilities.facility_name, facilities.facility_code FROM districts, lab_commodity_orders, facilities WHERE lab_commodity_orders.district_id = districts.id AND facilities.facility_code = lab_commodity_orders.facility_code AND districts.id = ' . $value['id'] . '');
        //     $res = $q->result_array();
        //             // foreach ($res as $values) {
        //             //     date_default_timezone_set('EUROPE/Moscow');
        //             //     $order_date = date('F', strtotime($values['order_date']));
        //             //     $tdata .= '<tr>
        //             //     <td>' . $order_date . '</td>
        //             //     <td>' . $values['facility_code'] . '</td>
        //             //     <td>' . $values['facility_name'] . '</td>
        //             //     <td>' . $values['district'] . '</td>
        //             //     <td>' . $values['order_date'] . '</td>
        //             //     <td>' . $values['compiled_by'] . '</td>
        //             //     <td><a href="' . base_url() . 'rtk_management/lab_order_details/' . $values['id'] . '">View</a></td>
        //             //     <tr>';
        //             //     }
        //             //     if (count($res) > 0) {
        //             //         array_push($reports, $res);
        //             //     }
        //     }
                $month = $this->session->userdata('Month');

                if ($month == '') {
                    $month = date('mY', strtotime('-1 month'));

                }
                $m =substr($month, 0,2);
                $y = substr($month, 2);
                $new_month = $y.'-'.$m.'-01';
                $d = new DateTime("$new_month");    
                $d->modify( 'last day of next month' );
                $month_db =  $d->format( 'mY' );                  

                $sql ="select rtk_district_percentage.percentage,districts.district from rtk_district_percentage,districts,counties
                where rtk_district_percentage.district_id = districts.id and districts.county = counties.id and counties.id = '$countyid' 
                and rtk_district_percentage.month = '$month_db'";

        //echo "$sql";
                $year = substr($month, -4);
                $month = substr_replace($month, "", -4);
                $reporting_rates = $this->db->query($sql)->result_array();

                $districts = array();
                $reported = array();
                $nonreported = array();
        //$query = $this->db->query($q);
                foreach ($reporting_rates as $key => $value) {
                    array_push($districts, $value['district']);  
                    $percentage_reported = intval($value['percentage']);
                    if($percentage_reported >100){
                        $percentage_reported=100;
                    }else{
                        $percentage_reported = intval($value['percentage']);
                    }

                    array_push($reported, $percentage_reported);
                    $percentage_non_reported = 100 - $percentage_reported;
                    array_push($nonreported, $percentage_non_reported);
                }


                $districts = json_encode($districts);
                $districts = str_replace('"', "'", $districts);
                $reported = json_encode($reported);
                $nonreported = json_encode($nonreported);


                $reporting_rates = array('districts'=>$districts,'reported'=>$reported,'nonreported'=>$nonreported);       

                $data['graphdata'] = $reporting_rates;
                $data['county_perc'] = $this->get_county_reporting_percentage($month_db,$countyid);


        //$data['graphdata'] = $this->county_reporting_percentages($countyid, $year, $month);        
        //$data['county_summary'] = $this->_requested_vs_allocated($year, $month, $countyid); 

        //$data['tdata'] = $tdata;
                $data['county'] = $County;
                $data['active_month'] = $month.$year;
                $data['title'] = 'RTK County Admin';
                $data['banner_text'] = 'RTK County Admin';
                $data['content_view'] = "rtk/rtk/clc/home";
                $this->load->view("rtk/template", $data);
            }



            public function get_county_reporting_percentage($month=null,$countyid){
                if(isset($month)){           
                    $year = substr($month, -4);
                    $month = substr($month, 0,2);            
                    $monthyear = $month.$year;                    
                }

                $sql = "select percentage from rtk_county_percentage where month='$monthyear' and county_id='$countyid'";
                $result = $this->db->query($sql)->result_array();
                foreach ($result as $key => $value) {
                    $percentage = number_format($value['percentage']);
                }
                return $percentage;
            }
            public function county_stock() {
                $lastday = date('Y-m-d', strtotime("last day of previous month"));
                $countyid = $this->session->userdata('county_id');
                $districts = districts::getDistrict($countyid);
                $county_name = counties::get_county_name($countyid);
                $County = $county_name[0]['county'];

                $month = $this->session->userdata('Month');
                if ($month == '') {
                    $month = date('mY', strtotime('-1 month'));
                }
                $year = substr($month, -4);
                $month = substr($month, 0,2);

                $month_db = date("mY", strtotime("$month +0 month"));        
        // echo "$month , $year, $countyid";die();
                $data['county_summary'] = $this->_requested_vs_allocated($year, $month, $countyid); 
        // echo "<pre>";
        // print_r($data['county_summary']);die();

                $data['county'] = $County;
                $data['active_month'] = $month.$year;
                $data['title'] = 'RTK County Admin';
                $data['banner_text'] = 'RTK County Stocks';
                $data['content_view'] = "rtk/rtk/clc/stock_card";
                $this->load->view("rtk/template", $data);
            }
            public function county_profile($county) {
                $data = array();
                $lastday = date('Y-m-d', strtotime("last day of previous month"));
                $County = $this->session->userdata('county_name');
                $Countyid = $this->session->userdata('county_id');
                $districts = districts::getDistrict($Countyid);
                $facility = facilities::get_facility_name_($mfl);

                $data['county'] = $County;
                $data['countyid'] = $Countyid;
                $data['title'] = 'Facility Profile: ' . $facility['facility_name'];
                $data['banner_text'] = 'Facility Profile: ' . $facility['facility_name'];
                $data['content_view'] = "rtk/rtk/clc/county_profile_view";

                $this->load->view("rtk/template", $data);
            }

            public function rca_districts() {
                $county = $this->session->userdata('county_id');
                date_default_timezone_set('EUROPE/moscow');
                $districts = districts::getDistrict($county);
                $county_name = counties::get_county_name($county);
                $County = $county_name[0]['county'];
                $table_data_facilities = array();
                $res_district = $this->_districts_in_county($county);
                $data['districts_list'] = $res_district;

                $count_districts = count($res_district);
                $data['count_districts'] = $count_districts;
                for ($i = 0; $i < $count_districts; $i++) {
                    $district_id = $res_district[$i]['id'];
                    $sql1 = "SELECT * FROM facilities WHERE facilities.district = '$district_id' and facilities.rtk_enabled = '1'";
                    $q = $this->db->query($sql1);
                    $res = $q->result_array();
                    $count = count($res);
                    array_push($table_data_facilities, $count);
                }
                $data['facilities_count'] = $table_data_facilities;
                $data['county'] = $County;
                $data['title'] = 'RTK County Admin';
                $data['banner_text'] = "RTK County Admin: Sub-Counties in $County County";
                $data['content_view'] = "rtk/rtk/clc/districts_v";
                $this->load->view("rtk/template", $data);
            }

            public function rca_facilities_reports() {

                $county = $this->session->userdata('county_id');

                date_default_timezone_set('EUROPE/moscow');
                $lastday = date('Y-m-d', strtotime("last day of previous month"));
                $districts = districts::getDistrict($county);
                $county_name = counties::get_county_name($county);         
                $County = $county_name['county'];        
                $sql = "SELECT lab_commodity_orders.id, lab_commodity_orders.facility_code, lab_commodity_orders.compiled_by, lab_commodity_orders.order_date, lab_commodity_orders.district_id, districts.district, facilities.facility_name, facilities.facility_code
                FROM lab_commodity_orders,  facilities, districts, counties
                WHERE districts.county = counties.id
                AND facilities.district = districts.id
                AND lab_commodity_orders.facility_code = facilities.facility_code
                AND counties.id = $county 
                ORDER BY   `lab_commodity_orders`.`order_date` DESC ,`lab_commodity_orders`.`district_id` ASC";
                $res = $this->db->query($sql)->result_array();
                $data['reports'] = $res;
                $data['county'] = $County;
                $data['title'] = 'RTK County Admin';
                $data['banner_text'] = "RTK County Admin: Available Reports for $County County";
                $data['content_view'] = "rtk/rtk/clc/facilities_reports_v";
                $this->load->view("rtk/template", $data);
            }

            public function county_admin($sk = null) {
                $data = array();
                $lastday = date('Y-m-d', strtotime("last day of previous month"));
                $County = $this->session->userdata('county_name');
                $Countyid = $this->session->userdata('county_id');
                $districts = districts::getDistrict($Countyid);

                $facilities = $this->_facilities_in_county($Countyid);
                $users = $this->_users_in_county($Countyid, 7);        
                $data['facilities'] = $facilities;
                $data['users'] = $users;
                $data['districts'] = $this->_districts_in_county($Countyid);

                $data['sk'] = $sk;
                $data['county'] = $County;
                $data['countyid'] = $Countyid;
                $data['title'] = 'RTK County Admin';
                $data['banner_text'] = 'RTK County Admin';
                $data['content_view'] = "rtk/rtk/clc/admin_dashboard_view";
                $this->load->view("rtk/template", $data);
            }

            public function create_facility_county() {
                $facilityname = $_POST['facilityname'];
                $facilitycode = $_POST['facilitycode'];
                $facilityowner = $_POST['facilityowner'];
                $facilitytype = $_POST['facilitytype'];
                $district = $_POST['district'];
                $time = date('Y-m-d', time());
                $facilityname = addslashes($facilityname);
                $sql = "INSERT INTO `facilities` (`id`, `facility_code`, `facility_name`, `district`, `drawing_rights`, `owner`, `type`, `level`, `rtk_enabled`, `cd4_enabled`, `drawing_rights_balance`, `using_hcmp`, `date_of_activation`) 
                VALUES (NULL, '$facilitycode', '$facilityname', '$district', '0', '$facilityowner', '$facilitytype', '', '1', '0', '0', '0', '$time')";
                $this->db->query($sql);
                $object_id = $this->db->insert_id();
                $this->logData('20', $object_id);
        //$this->_update_facility_count('add',$county,$district);        
                redirect('rtk_management/county_admin/facilities');
            }
            public function update_facility_county() {
                $facilityname = $_POST['facilityname'];
                $district = $_POST['district'];
                $mfl = $_POST['facility_code'];
                $time = date('Y-m-d', time());
                $facilityname = addslashes($facilityname);

                $sql = "UPDATE `facilities` SET `facility_name` = '$facilityname',  `district` = '$district' WHERE `facility_code`='$mfl' ";
                $this->db->query($sql);
                $q = $this->db->query("select id from `facilities`WHERE `facility_code`='$mfl' ");
                $facil = $q->result_array();
                $object_id = $facil[0]['id'];
                $this->logData('21', $object_id);

                redirect('rtk_management/facility_profile/' . $mfl);
            }
            public function county_trend($month=null) { 
                if(isset($month)){           
                    $year = substr($month, -4);
                    $month = substr($month, 0,2);            
                    $monthyear = $year . '-' . $month . '-1';         

                }else{
                    $month = $this->session->userdata('Month');
                    if ($month == '') {
                        $month = date('mY', strtotime('-1 month'));

                    }
                    $m =substr($month, 0,2);
                    $y = substr($month, 2);
                    $new_month = $y.'-'.$m.'-01';
                    $d = new DateTime("$new_month");    
                    $d->modify( 'last day of next month' );
                    $month_db =  $d->format( 'mY' );                  

        // if ($month == '') {
        //     $month = date('mY', time());
        // }else{
        //     echo "$month";        //die();                            
        // }
        // $year = substr($month, -4);
        // $month = substr_replace($month, "", -4);
                    $monthyear = $y . '-' . $m . '-1';
                }
                $active_month = $month.$year;
                $current_month = date('mY', strtotime("-1 month"));

                $countyid = $this->session->userdata('county_id');       
                $county_name = counties::get_county_name($countyid);        
                $County = $county_name['county'];
                $res = $this->db->query("select facilities as total_facilities,percentage as total_percentage from rtk_county_percentage 
                    where county_id='$countyid' and month='$month_db'");
                $result = $res->result_array();       

                $data['total_facilities'] = $result[0]['total_facilities'];             
                $data['total_percentage'] = $result[0]['total_percentage'];             

                $englishdate = date('F, Y', strtotime("-0 month",strtotime($monthyear)));
                $reporting_rates = $this->reporting_rates($countyid,$year,$month);        
                $xArr = array();
                $yArr = array();
                $xArr1 = array();
                $cumulative_result = 0;
                foreach ($reporting_rates as $value) {
                    $count = $value['count'];
                    $order_date = substr($value['order_date'], -2);
                    $order_date = date('jS', strtotime($value['order_date']));

                    $cumulative_result +=$count;
                    array_push($xArr1, $cumulative_result);

                    array_push($yArr, $order_date);
                    array_push($xArr, $count);
                }

                $data['cumulative_result'] = $cumulative_result;
                $data['jsony'] = json_encode($yArr);
                $data['jsonx'] = str_replace('"', "", json_encode($xArr));
                $data['jsonx1'] = str_replace('"', "", json_encode($xArr1));
                $data['englishdate'] = $englishdate;              
                $data['county'] = $County;
                $data['active_month'] = $active_month;
                $data['current_month'] = $current_month;
                $Countyid = $this->session->userdata('county_id');
                $data['user_logs'] = $this->rtk_logs();
                $data['content_view'] = "rtk/rtk/clc/trend";
                $data['banner_text'] = "$County County Monthly Reporting Trends";
                $data['title'] = "RTK County Admin Trends";
                $this->load->view('rtk/template', $data);
            }
            public function district_profile($district,$com_id=null) {
                if(isset($com_id)){
                    $commodity_id = $com_id;
                }else{
                    $commodity_id = 4;
                }
                $data = array();
                $lastday = date('Y-m-d', strtotime("last day of previous month"));

                $current_month = $this->session->userdata('Month');

                if ($current_month == '') {
                    $current_month = date('mY', time());
                }
                $previous_month = date('m', strtotime("last day of previous month"));
                $previous_month_1 = date('mY', strtotime('-2 month'));
                $previous_month_2 = date('mY', strtotime('-3 month'));


                $year_current = substr($current_month, -4);

                $year_previous = date('Y', strtotime("last day of previous month"));
                $year_previous_1 = substr($previous_month_1, -4);
                $year_previous_2 = substr($previous_month_2, -4);

                $current_month = substr_replace($current_month, "", -4);        
                $previous_month_1 = substr_replace($previous_month_1, "", -4);
                $previous_month_2 = substr_replace($previous_month_2, "", -4);

                $monthyear_current = $year_current . '-' . $current_month . '-1';
                $monthyear_previous = $year_previous . '-' . $previous_month . '-1';
                $monthyear_previous_1 = $year_previous_1 . '-' . $previous_month_1 . '-1';
                $monthyear_previous_2 = $year_previous_2 . '-' . $previous_month_2 . '-1';

                $englishdate = date('F, Y', strtotime($monthyear_current));

                $m_c = date("F", strtotime($monthyear_current));
        //first month               
                $m0 = date("F", strtotime($monthyear_previous));
                $m1 = date("F", strtotime($monthyear_previous_1));
                $m2 = date("F", strtotime($monthyear_previous_2));

                $month_text = array($m2, $m1, $m0);

                $district_summary = $this->rtk_summary_district($district, $year_current, $current_month);
                $district_summary_prev = $this->rtk_summary_district($district, $year_previous, $previous_month);
                $district_summary1 = $this->rtk_summary_district($district, $year_previous_1, $previous_month_1);
                $district_summary2 = $this->rtk_summary_district($district, $year_previous_2, $previous_month_2);


                $sql_c = "SELECT commodity_name FROM lab_commodities where category='1' and id='$commodity_id'";
                $result_c = $this->db->query($sql_c)->result_array();   
                $sql_all_c = "SELECT * FROM lab_commodities where category='1'";
                $result_all_c = $this->db->query($sql_all_c)->result_array();   
                $county_id = districts::get_county_id($district);
                $county_name = counties::get_county_name($county_id['county']);

                $cid = $this->db->select('districts.county')->get_where('districts', array('id' =>$district))->result_array();

                foreach ($cid as $key => $value) {
                    $myres = $cid[0]['county'];
                }
                $mycounties = $this->db->select('districts.district,districts.id')->get_where('districts', array('county' =>$myres))->result_array(); 

                $data['district_balances_current'] = $this->district_totals($year_current, $current_month, $district,$commodity_id);
                $data['district_balances_previous'] = $this->district_totals($year_previous, $previous_month, $district,$commodity_id);
                $data['district_balances_previous_1'] = $this->district_totals($year_previous_1, $previous_month_1, $district,$commodity_id);
                $data['district_balances_previous_2'] = $this->district_totals($year_previous_2, $previous_month_2, $district,$commodity_id);


                $data['district_summary'] = $district_summary;
        // echo "<pre>";
        // print_r($data['district_balances_previous_1'] );die();
                $data['districts'] = $mycounties;
                $data['facilities'] = $this->_facilities_in_district($district);

                $data['district_name'] = $district_summary['district'];
                $data['district_id'] = $district;
                $data['commodity_name'] = $result_c[0]['commodity_name'];
                $data['commodities'] = $result_all_c;
                $data['county_id'] = $county_name['id'];
                $data['county_name'] = $county_name['county'];     

                $data['title'] = 'RTK County Admin - Sub-County Profile: ' . $district_summary['district'];
                $data['banner_text'] = 'Sub-County Profile: ' . $district_summary['district'];
                $data['content_view'] = "rtk/rtk/shared/district_profile_view";
                $data['months'] = $month_text;

                $this->load->view("rtk/template", $data);
            }
            public function rca_pending_facilities() {
                $countyid = $this->session->userdata('county_id');        
                $districts = districts::getDistrict($countyid);
                $county_name = counties::get_county_name($countyid);

                $County = $county_name['county'];
                $month = $this->session->userdata('Month');
                if ($month == '') {
                    $month = date('mY', strtotime('-1 month'));
                }
                $year = substr($month, -4);
                $month = substr_replace($month, "", -4);
                $date = date('F-Y', mktime(0, 0, 0, $month, 1, $year));       
                $reporting_details = $this->rtk_facilities_not_reported(NULL, $countyid,NULL,NULL, $year,$month);
        // print_r($reporting_details); die;
                $pending_facilities = $reporting_details['non_reported'];
                $new_pending_facilities = array();                
                $data['county'] = $County;
                $data['pending_facility'] = $pending_facilities;
                $data['title'] = 'RTK County Admin';
                $data['banner_text'] = 'RTK County Admin: Non-Reported Facilities';
                $data['content_view'] = "rtk/rtk/clc/pending_facilities_v";
                $this->load->view("rtk/template", $data);
            }


        //        //        //        ///RTK ADMIN FUNCTIONS
            public function rtk_manager_home() {
                $data = array();
                $data['title'] = 'RTK Manager';
                $data['banner_text'] = 'RTK Manager';
                $data['content_view'] = "rtk/rtk/admin/home_v";
                $counties = $this->_all_counties();
                $county_arr = array();
                foreach ($counties as $county) {
                    array_push($county_arr, $county['county']);
                }
                $counties_json = json_encode($county_arr);
                $counties_json = str_replace('"', "'", $counties_json);
                $data['counties_json'] = $counties_json;

                $thismonth = date('m', time());
                $thismonth_year = date('Y', time());
                $this_month_full = $thismonth.$thismonth_year;

                $previous_month = date('m', strtotime("-1 month", time()));
                $previous_month_year = date('Y', strtotime("-1 month", time()));
                $previous_month_full = $previous_month.$previous_month_year;

                $prev_prev = date('m', strtotime("-2 month", time()));
                $prev_prev_year = date('Y', strtotime("-2 month", time()));
                $prev_prev_month_full = $prev_prev.$prev_prev_year;

                $thismonth_arr1 = array();

                foreach ($counties as $key => $value) {
                    $id = $value['id'];
                    $q = "select percentage from rtk_county_percentage where month='$this_month_full' and county_id=$id";
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $key => $value) {            
                        $percentage = intval($value['percentage']);  
                        if( $percentage >100){
                            $percentage = 100;
                        }else{
                            $percentage = intval($value['percentage']);
                        }
                    }        
        // echo "This". print_r($result);exit;
                    array_push($thismonth_arr1, $percentage);
                }     

                $previous_month_arr1 = array();

                foreach ($counties as $key => $value) {
                    $id = $value['id'];
                    $q = "select percentage from rtk_county_percentage where month='$previous_month_full' and county_id=$id";
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $key => $value) {            
                        $percentage = intval($value['percentage']);                               
                    } 
                    array_push($previous_month_arr1, $percentage);
                }  

                $prev_prev_month_arr1 = array();

                foreach ($counties as $key => $value) {
                    $id = $value['id'];
                    $q = "select percentage from rtk_county_percentage where month='$prev_prev_month_full' and county_id=$id";
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $key => $value) {            
                        $percentage = intval($value['percentage']);                               
                    } 
                    array_push($prev_prev_month_arr1, $percentage);
                }         
                $thismonthjson = json_encode($thismonth_arr1);
                $thismonthjson = str_replace('"', "", $thismonthjson);
                $data['thismonthjson'] = $thismonthjson;

                $previous_monthjson = json_encode($previous_month_arr1);
                $previous_monthjson = str_replace('"', "", $previous_monthjson);
                $data['previous_monthjson'] = $previous_monthjson;

                $prev_prev_monthjson = json_encode($prev_prev_month_arr1);
                $prev_prev_monthjson = str_replace('"', "", $prev_prev_monthjson);
                $data['prev_prev_monthjson'] = $prev_prev_monthjson;
                $this->load->view('rtk/template', $data);
            }

            public function partner_super_home() {
                $data = array();
                $data['title'] = 'RTK Partner Admin';
                $data['banner_text'] = 'RTK Partner Admin';
                $data['content_view'] = "rtk/rtk/partner/partner_admin";
                $partners = $this->_all_partners();
                $partner_arr = array();
                foreach ($partners as $partner) {
                    array_push($partner_arr, $partner['name']);
                }
                $partners_json = json_encode($partner_arr);
                $partners_json = str_replace('"', "'", $partners_json);
                $data['partners_json'] = $partners_json;
                $session_month = $this->session->userdata('Month');    
                if($session_month!=''){
                    $month = $this->session->userdata('Month');
                    $thismonth = substr($month,0,2);       
                    $thismonth_year = substr($month,-4);          
                    $this_month_full = $thismonth.$thismonth_year;
        //echo "$this_month_full";die();

                    $m_prev = substr($this_month_full, 0,2);
                    $y_prev = substr($this_month_full, -4);
                    $f_prev = $y_prev.'-'.$m_prev.'-01';

                    $previous_month = new DateTime($f_prev);
                    $previous_month->modify('-1 month');
                    $previous_month_full =  $previous_month->format('mY');

                    $m_prev1 = substr($previous_month_full, 0,2);
                    $y_prev1 = substr($previous_month_full, -4);
                    $f_prev1 = $y_prev1.'-'.$m_prev1.'-01';

                    $prev_prev = new DateTime($f_prev1);
                    $prev_prev->modify('-1 month');
                    $prev_prev_month_full =  $prev_prev->format('mY');

                    $thismonthname  = date('F',strtotime("$thismonth_year-$thismonth-01"));
                    $prevmonthname  = $previous_month->format('F');
                    $englishdate  = date('F,Y',strtotime("$thismonth_year-$thismonth-01"));
                    $prev_prevmonthname  = $prev_prev->format('F');

                }else{

                    $thismonth = date('m', time());
                    $thismonth_year = date('Y', time());
                    $this_month_full = $thismonth.$thismonth_year;

                    $previous_month = date('m', strtotime("-1 month", time()));
                    $previous_month_year = date('Y', strtotime("-1 month", time()));
                    $previous_month_full = $previous_month.$previous_month_year;

                    $prev_prev = date('m', strtotime("-2 month", time()));
                    $prev_prev_year = date('Y', strtotime("-2 month", time()));
                    $prev_prev_month_full = $prev_prev.$prev_prev_year;

                    $new_prev = date('m', strtotime("-3 month", time()));
                    $new_prev_year = date('Y', strtotime("-3 month", time()));

                    $englishdate  = date('F,Y',strtotime("$previous_month_year-$previous_month-01"));

                    $thismonthname  = date('F',strtotime("$previous_month_year-$previous_month-01"));
                    $prevmonthname  = date('F',strtotime("$prev_prev_year-$prev_prev-01"));
                    $prev_prevmonthname  = date('F',strtotime("$new_prev_year-$new_prev-01"));
                }






        //echo"Current $thismonthname, Previous $prevmonthname ,Previous1 $prev_prevmonthname";die();
                $thismonth_arr1 = array();

                foreach ($partners as $key => $value) {
                    $id = $value['ID'];
                    $q = "select percentage from rtk_partner_percentage where month='$this_month_full' and partner_id=$id";       
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $key => $value) {            
                        $percentage = intval($value['percentage']);  
                        if( $percentage >100){
                            $percentage = 100;
                        }else{
                            $percentage = intval($value['percentage']);
                        }
                    }        
                    array_push($thismonth_arr1, $percentage);
                }     

                $previous_month_arr1 = array();

                foreach ($partners as $key => $value) {
                    $id = $value['ID'];
                    $q = "select percentage from rtk_partner_percentage where month='$previous_month_full' and partner_id=$id";
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $key => $value) {            
                        $percentage = intval($value['percentage']);                               
                    } 
                    array_push($previous_month_arr1, $percentage);
                }  

                $prev_prev_month_arr1 = array();    
                foreach ($partners as $key => $value) {
                    $id = $value['ID'];
                    $q = "select percentage from rtk_partner_percentage where month='$prev_prev_month_full' and partner_id=$id";
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $key => $value) {            
                        $percentage = intval($value['percentage']);                                           
                    } 
                    array_push($prev_prev_month_arr1, $percentage);
                }         

                $thismonthjson = json_encode($thismonth_arr1);
                $thismonthjson = str_replace('"', "", $thismonthjson);
                $data['thismonthjson'] = $thismonthjson;

                $previous_monthjson = json_encode($previous_month_arr1);
                $previous_monthjson = str_replace('"', "", $previous_monthjson);
                $data['previous_monthjson'] = $previous_monthjson;   

                $data['thismonthname'] = $thismonthname;
                $data['prevmonthname'] = $prevmonthname;
                $data['prev_prevmonthname'] = $prev_prevmonthname;

                $data['englishdate'] = $englishdate;

                $prev_prev_monthjson = json_encode($prev_prev_month_arr1);
                $prev_prev_monthjson = str_replace('"', "", $prev_prev_monthjson);
                $data['prev_prev_monthjson'] = $prev_prev_monthjson;
                $this->load->view('rtk/template', $data);
            } 
            public function rtk_manager($month=null) {
                if(isset($month)){           
                    $year = substr($month, -4);
                    $month = substr($month, 0,2);            
                    $monthyear = $year . '-' . $month . '-1';         
                    $db_month = $month.$year;
                }else{
                    $month = $this->session->userdata('Month');
                    if ($month == '') {
                        $month = date('mY', time());
                    }
                    $year = substr($month, -4);
                    $month = substr_replace($month, "", -4);
                    $monthyear = $year . '-' . $month . '-1';
                    $db_month = $month.$year;
                }

                $res = $this->db->query("SELECT sum(facilities) as facilities, sum(reported) as reported FROM `rtk_county_percentage` WHERE month='$db_month'");
                $result = $res->result_array();    
                $data['total_facilities'] = $result[0]['facilities'];
                $englishdate = date('F, Y', strtotime($monthyear));
                $reporting_rates = $this->reporting_rates(null,$year,$month);       
                $xArr = array();
                $yArr = array();
                $xArr1 = array();
                $cumulative_result = 0;
                foreach ($reporting_rates as $value) {
                    $count = $value['count'];
                    $order_date = substr($value['order_date'], -2);
                    $order_date = date('jS', strtotime($value['order_date']));
                    $cumulative_result +=$count;
                    array_push($xArr1, $cumulative_result);
                    array_push($yArr, $order_date);
                    array_push($xArr, $count);
                }  
        // echo "$cumulative_result";die();
                $data['cumulative_result'] = $cumulative_result;
                $data['reported'] = $result[0]['reported'];
                $data['jsony'] = json_encode($yArr);
                $data['jsonx'] = str_replace('"', "", json_encode($xArr));
                $data['jsonx1'] = str_replace('"', "", json_encode($xArr1));
        // echo "<pre>";
        // print_r($data['jsony']);die();
                $data['englishdate'] = $englishdate;
                $County = $this->session->userdata('county_name');
                $data['county'] = $County;
                $Countyid = $this->session->userdata('county_id');   
                $data['active_month'] = $month.$year;
                $data['content_view'] = "rtk/rtk/admin/admin_home";
                $data['banner_text'] = "RTK Manager";
                $data['title'] = "RTK Manager";
                $this->load->view('rtk/template', $data);
            } 

            public function rtk_manager_activity($all=null) {
                $month = $this->session->userdata('Month');
                if ($month == '') {
                    $month = date('mY', time());
                }

                $year = substr($month, -4);
                $month = substr_replace($month, "", -4);
                $monthyear = $year . '-' . $month . '-01';
                $monthyear1 = $year . '-' . $month . '-31';

                $timestamp = strtotime($monthyear);
                if(isset($all)){
                    $data['user_logs'] = $this->rtk_logs();
                }else{
                    $limit = 'LIMIT 0,30';
                    $data['user_logs'] = $this->rtk_logs(null, NULL, NULL, $timestamp, $timestamp1,$limit);
                }
                $data['englishdate'] = $englishdate;
                $County = $this->session->userdata('county_name');
                $data['county'] = $County;
                $Countyid = $this->session->userdata('county_id');
                $data['user_logs'] = $this->rtk_logs(null, NULL, NULL, $timestamp, $timestamp1);

                $data['active_month'] = $month.$year;
                $data['content_view'] = "rtk/rtk/admin/admin_logs";
                $data['banner_text'] = "RTK Manager";
                $data['title'] = "RTK Manager";
                $this->load->view('rtk/template', $data);
            } 
            public function rtk_manager_users() {
                $data['title'] = 'RTK Manager';
                $data['banner_text'] = 'RTK Manager';
                $data['content_view'] = "rtk/rtk/admin/admin_users";
                $users = $this->_get_rtk_users();        
                $data['users'] = $users;
                $this->load->view('rtk/template', $data);
            }

            public function delete_user($user, $district, $redirect_url = null) {
                $sql = 'DELETE FROM `user` WHERE `id` =' . $user
                . ' AND  `usertype_id` =7'
                . ' AND  `district` =' . $district;

                $object_id = $user;
                $this->logData('2', $object_id);
                $this->db->query($sql);


                if ($redirect_url == 'county_user') {
                    redirect('rtk_management/county_admin/users');
                }
                if ($redirect_url == 'rtk_manager') {
                    redirect('rtk_management/rtk_manager_admin');
                } else {
                    redirect('home_controller');
                }
            }

            public function delete_user_gen($user, $redirect_url = null) {
                $sql = 'DELETE FROM `user` WHERE `id` =' . $user;

                $object_id = $user;
                $this->logData('2', $object_id);
                $this->db->query($sql);

                if ($redirect_url == 'county_user') {
                    redirect('rtk_management/county_admin/users');
                }
                if ($redirect_url == 'rtk_manager') {
                    redirect('rtk_management/rtk_manager_users');
                } else {
                    redirect('home_controller');
                }
            }

            public function rtk_manager_facilities($zone = null){
                if(isset($zone)){
                    $facilities = $this->_get_rtk_facilities($zone);
                }else{
                    $zone = 'A';
                    $facilities = $this->_get_rtk_facilities('A');
                }


                $data['title'] = 'RTK Manager';
                $data['banner_text'] = 'RTK Manager : Facilities in Zone '.$zone;
                $data['content_view'] = "rtk/rtk/admin/admin_facilities";
        // /$facilities = $this->_get_rtk_facilities();
                $q = "select * from partners where flag='1' order by ID asc";
                $res = $this->db->query($q)->result_array();  
                $partners_array = array();  
                foreach ($res as $key => $value) {
                    $id = $value['ID'];
                    $name = $value['name'];        
                    $partners_array[$id]=  $name;

                }    

                $data['facilities'] = $facilities;
                $data['partners_array'] = $partners_array;
                $this->load->view('rtk/template', $data);
            }

            public function rtk_manager_facilities_data($zone = null){
        // if(isset($zone)){
        //      $facilities = $this->_get_rtk_facilities_data($zone);
        //      $data['banner_text'] = 'RTK Manager : Facilities Data for Zone '.$zone;
        // }else{         
        //      $facilities = $this->_get_rtk_facilities_data();
        //      $data['banner_text'] = 'RTK Manager : All Facilities Data';
        // }
                $sql = "SELECT facilities.facility_code,facilities.facility_name,districts.district,counties.county
                from districts,counties,facilities where facilities.district = districts.id 
                AND districts.county = counties.id and facilities.rtk_enabled = '1' ORDER BY counties.county,facilities.facility_code";

                $facilities = $this->db->query($sql)->result_array();
                $screening = array();
                $screening_det = array();
                $screening_khb = array();
                $confirmatory = array();
                $confirmatory_det = array();
                $confirmatory_fr = array();
                $tiebreaker = array();
                foreach ($facilities as $key => $value) {
                    $fcode = $value['facility_code'];
                    $q = "select sum(facility_amc.amc), facility_amc.commodity_id from  facility_amc 
                    where  facility_amc.facility_code = '$fcode' and month = '112014' group by facility_amc.commodity_id";
                    $results = $this->db->query($q)->result_array();
                    foreach ($results as $key => $value) {
# code...
                    }
                }
                $data['title'] = 'RTK Manager Facilities Data';    
                $data['content_view'] = "rtk/rtk/admin/admin_facilities_data";        
                $data['facilities'] = $facilities;    
                $this->load->view('rtk/template', $data);
            }
            function calculate_drwing_rights(){
                $sql6 = "select * from counties";
                $res6 = $this->db->query($sql6)->result_array();

                foreach ($res6 as $key => $value) {
                    $screening = str_replace(',', '',$value['screening_drawing_rights']);
                    $county_id = $value['id'];
                    $confirmatory = ceil($screening*0.10);
                    $tiebreaker = ceil($screening*0.03);
                    echo $screening.' '.$confirmatory.' '.$tiebreaker.' '.$value['county'].'<br/>';
        //screening_current_amount
        // $sql = "update counties set screening_drawing_rights = '$screening' where id = $county_id";

        // $sql = "update counties set screening_drawing_rights='$screening', confimatory_drawing_rights = '$confirmatory', tiebreaker_current_amount ='$tiebreaker' where id = $county_id";

                    $sql = "update counties set screening_current_amount='$screening', confirmatory_current_amount = '$confirmatory', tiebreaker_drawing_rights ='$tiebreaker' where id = $county_id";
                    $this->db->query($sql);
                }
            }
            public function rtk_manager_settings() {

                $sql = "select rtk_settings.*, user.fname,user.lname from rtk_settings, user where rtk_settings.user_id = user.id ";
                $res = $this->db->query($sql);
                $deadline_data = $res->result_array();

                $sql1 = "select * from rtk_alerts_reference ";
                $res1 = $this->db->query($sql1);
                $alerts_to_data = $res1->result_array();


                $sql3 = "select rtk_alerts.*,rtk_alerts_reference.id as ref_id,rtk_alerts_reference.description as description from rtk_alerts,rtk_alerts_reference where rtk_alerts.reference=rtk_alerts_reference.id order by id ASC,status ASC";
                $res3 = $this->db->query($sql3);
                $alerts_data = $res3->result_array();

                $sql4 = "select lab_commodities.*,lab_commodity_categories.category_name, lab_commodity_categories.id as cat_id from lab_commodities,lab_commodity_categories where lab_commodities.category=lab_commodity_categories.id and lab_commodity_categories.active = '1'";
                $res4 = $this->db->query($sql4);
                $commodities_data = $res4->result_array();

                $sql5 = "select * from lab_commodity_categories";
                $res5 = $this->db->query($sql5);
                $commodity_categories = $res5->result_array();

                $sql6 = "select * from counties";
                $res6 = $this->db->query($sql6);
                $drawing_rights = $res6->result_array();

                $data['deadline_data'] = $deadline_data;
                $data['alerts_to_data'] = $alerts_to_data;
                $data['alerts_data'] = $alerts_data;
                $data['commodities_data'] = $commodities_data;
                $data['commodity_categories'] = $commodity_categories;
                $data['drawing_rights'] = $drawing_rights;

                $data['title'] = 'RTK Manager Settings';
                $data['banner_text'] = 'RTK Manager Settings';
        //$data['content_view'] = "rtk/admin/admin_home_view";
                $data['content_view'] = "rtk/rtk/admin/admin_settings";
                $users = $this->_get_rtk_users();
                $data['users'] = $users;
                $this->load->view('rtk/template', $data);
            }
            public function rtk_manager_stocks($month=null) {
                if(isset($month)){           
                    $year = substr($month, -4);
                    $month = substr($month, 0,2);            
                    $monthyear = $year . '-' . $month . '-01';         

                }else{
                    $month = $this->session->userdata('Month');
                    if ($month == '') {
                        $month = date('mY', time());
                    }
                    $year = substr($month, -4);
                    $month = substr_replace($month, "", -4);
                    $monthyear = $year . '-' . $month . '-01';
                }

                $englishdate = date('F, Y', strtotime($monthyear));

                $data['stock_status'] = $this->_national_reports_sum($year, $month);           
                $data['englishdate'] = $englishdate;
                $County = $this->session->userdata('county_name');
                $data['county'] = $County;
                $Countyid = $this->session->userdata('county_id');   
                $data['active_month'] = $month.$year;
                $data['content_view'] = "rtk/rtk/admin/stocks_v";
                $data['banner_text'] = "RTK Manager";
                $data['title'] = "RTK Manager";
                $this->load->view('rtk/template', $data);
            } 

            public function rtk_manager_messages() {

                $data['title'] = 'RTK Manager Messages';
                $data['banner_text'] = 'RTK Manager';        
                $data['content_view'] = "rtk/rtk/admin/admin_messages";        
                $this->load->view('rtk/template', $data);
            }

            public function send_message($count,$sql,$array){
                $a = 0;
                $b = 98;
                for ($i=$a; $i <=$count ; $i+$b) { 


                }

            }

            public function rtk_send_message() {
                $receipient_id = mysql_real_escape_string($_POST['id']);
                $subject = mysql_real_escape_string($_POST['subject']);
                $raw_message = mysql_real_escape_string($_POST['message']);             
                $attach_file = null;
                $bcc_email = null;
                $bcc_email = 'ttunduny@gmail.com,tngugi@clintonhealthaccess.org,annchemu@gmail.com';
                $message = str_replace(array('\\n', "\r", "\n"), "<br />", $raw_message);             
                include 'rtk_mailer.php';
                $newmail = new rtk_mailer();

        // $newmail->send_email('ttunduny@gmail.com', $message, $subject, $attach_file, $bcc_email);
        // die();

                $receipient = array();
                $month = date('mY');       
                if($receipient_id==1){
        //all users
                    $q = "SELECT email FROM user WHERE usertype_id in (0,7,8,11,13,14,15) and status=1 ORDER BY id DESC";
                    $count = $this->db->query($q)->num_rows();
                    $a = 0;
                    $b = 98;
                    $increment = 98;
                    for ($i=$a; $a <=$count ; $i+$increment) { 
                        $sql = "SELECT email FROM user WHERE usertype_id in (0,7,8,11,13,14,15) and status=1 ORDER BY id DESC LIMIT $a,$b";                    
                        $res = $this->db->query($sql)->result_array();                                      
                        $to ="";
                        foreach ($res as $key => $value) {
                            $one = $value['email'];
                            $to.= $one.',';                        
                        } 
                        $newmail->send_email($to, $message, $subject, $attach_file, $bcc_email);
                        $a +=$increment;
                        $b += $increment;
                    }
                    die();               

                }elseif($receipient_id==2){
        //All SCMLTs
                    $q = "SELECT email FROM user WHERE usertype_id = 7 and status = 1 ORDER BY id DESC";
                    $count = $this->db->query($q)->num_rows();
                    $a = 0;
                    $b = 98;
                    $increment = 98;
                    for ($i=$a; $a <=$count ; $i+$increment) { 
                        $sql = "SELECT email FROM user WHERE usertype_id = 7 and status = 1 ORDER BY id DESC LIMIT $a,$b";                    
                        $res = $this->db->query($sql)->result_array();                                      
                        $to ="";
                        foreach ($res as $key => $value) {
                            $one = $value['email'];
                            $to.= $one.',';                        
                        } 
                        $newmail->send_email($to, $message, $subject, $attach_file, $bcc_email);
                        $a +=$increment;
                        $b += $increment;
                    }                         

                }elseif($receipient_id==3){
        //All CLCs
                    $q = "SELECT email FROM user WHERE usertype_id =13 and status =1 ORDER BY id DESC";
                    $count = $this->db->query($q)->num_rows();
                    $a = 0;
                    $b = 98;
                    $increment = 98;
                    for ($i=$a; $a <=$count ; $i+$increment) { 
                        $sql = "SELECT email FROM user WHERE usertype_id =13 and status =1 ORDER BY id DESC LIMIT $a,$b";                    
                        $res = $this->db->query($sql)->result_array();                                      
                        $to ="";
                        foreach ($res as $key => $value) {
                            $one = $value['email'];
                            $to.= $one.',';                        
                        } 
                        $newmail->send_email($to, $message, $subject, $attach_file, $bcc_email);
                        $a +=$increment;
                        $b += $increment;
                    }    

        // $sql = "SELECT email FROM user WHERE usertype_id =13 and status =1 ORDER BY id DESC";
        // $res = $this->db->query($sql)->result_array();                  
        // $to =array();
        // foreach ($res as  $value) {
        //     $one = $value['email'];
        //     array_push($to,$one);
        // }          

                }elseif($receipient_id==4){
        //Sub C with more than 75% reporting
                    $sql = "select distinct district_id from  rtk_district_percentage  where  month = '$month' and percentage > 75";
                    $districts = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($districts as $value) {
                        $dist = $value['district_id'];
                        $q = "select email from user where district = $dist and usertype_id=7 and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }              

                }elseif($receipient_id==5){
        //Sub C with less than 75% reporting            
                    $sql = "select distinct district_id from  rtk_district_percentage  where  month = '$month' and percentage < 75";
                    $districts = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($districts as $value) {
                        $dist = $value['district_id'];
                        $q = "select email from user where district = $dist and usertype_id=7 and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }      
                }elseif($receipient_id==6){
        //Sub C with less than 50% reporting   
                    $sql = "select distinct district_id from  rtk_district_percentage  where  month = '$month' and percentage  > 50";
                    $districts = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($districts as $value) {
                        $dist = $value['district_id'];
                        $q = "select email from user where district = $dist and usertype_id=7 and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }               

                }elseif($receipient_id==7){
        //Sub C with less than 25% reporting   
                    $sql = "select distinct district_id from  rtk_district_percentage  where  month = '$month' and percentage > 25";
                    $districts = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($districts as $value) {
                        $dist = $value['district_id'];
                        $q = "select email from user where district = $dist and usertype_id=7 and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }               

                }elseif($receipient_id==8){
        //C with more than 75% reporting   
                    $sql = "select distinct county_id from  rtk_county_percentage  where  month = '$month' and percentage  >75";
                    $counties = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($counties as $value) {
                        $county = $value['county_id'];
                        $q = "select email from user where countyid = 'county' and usertype_id='13' and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }                

                }elseif($receipient_id==9){
        //C with less than 75% reporting
                    $sql = "select distinct county_id from  rtk_county_percentage  where  month = '$month' and percentage <75";
                    $counties = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($counties as $value) {
                        $county = $value['county_id'];
                        $q = "select email from user where countyid = 'county' and usertype_id='13' and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }               
                }elseif($receipient_id==10){
        //C with less than 50% reporting 
                    $sql = "select distinct county_id from  rtk_county_percentage  where  month = '$month' and percentage  <50";
                    $counties = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($counties as $value) {
                        $county = $value['county_id'];
                        $q = "select email from user where countyid = 'county' and usertype_id='13' and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }                    
                }elseif($receipient_id==11){
        //C with less than 25% reporting  
                    $sql = "select distinct county_id from  rtk_county_percentage  where  month = '$month' and percentage  >75";
                    $counties = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($counties as $value) {
                        $county = $value['county_id'];
                        $q = "select email from user where countyid = 'county' and usertype_id='13' and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }          
                }elseif($receipient_id==12){
        //C with 0% reporting 
                    $sql = "select distinct county_id from  rtk_county_percentage  where  month = '$month' and percentage  = 0";
                    $districts = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($districts as $value) {
                        $county = $value['county_id'];
                        $q = "select email from  user where user.county.id = '$county' and user.usertype_id = '13' and user.status = '1' order by user.id asc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }                 

                }elseif($receipient_id==13){
        //Sub C with 0% reporting   
                    $sql = "select distinct district_id from  rtk_district_percentage  where  month = '$month' and percentage  = 0";
                    $districts = $this->db->query($sql)->result_array();
                    $emails = array();
                    foreach ($districts as $value) {
                        $dist = $value['district_id'];
                        $q = "select email from user where district = $dist and usertype_id=7 and status = 1 order by id desc";
                        $res = $this->db->query($q)->result_array();      
                        array_push($emails, $res);
                    }       
                    foreach ($emails AS $key => $value) {
                        $new_emails[] = $value[0];
                    }
                    $to = array();
                    foreach ($new_emails as $key => $value) {
                        $one = $value['email'];
                        array_push($to,$one);
                    }               

                }

        //echo "$to";die();
        //$msg = $this->trigger_emails($message);

        //Convert to get individual emails in a format suitable for sending
        //echo nl2br($desc);
        // $message = nl2br($raw_message);

        //          //$message = str_replace('\\n', '', $raw_message); 
        //  echo "$message";die();          
        //          //$receipient = 'ttunduny@gmail.com';        
        //          //$receipient = implode($to, ',');     
        //          //echo "$bcc_email";die();

                $bcc_email = 'ttunduny@gmail.com,tngugi@clintonhealthaccess.org,annchemu@gmail.com';
        //         // $receipient = array();
        //         // $receipient =$to; 

        //          //parse_str($_POST['receipients'], $receipient);
        //          //$receipient = $receipient['hidden-receipients'];
        // include 'rtk_mailer.php';
        // $newmail = new rtk_mailer();
        // $response = $newmail->send_email($to, $message, $subject, null, $bcc_email);

                echo $msg;
            }
            function user_details($user = NULL){
                $conditions = '';
                $conditions = (isset($user)) ? $conditions . " AND user.id = $user" : $conditions . ' ';

                $sql = "select * from user, access_level where user.usertype_id = access_level.id and access_level.id BETWEEN 7 AND  13 $conditions";
                $res = $this->db->query($sql);
                $returnable = $res->result_array();
                if ($res->num_rows()<1){ 
                    echo "user does not exist";}
                    else {
                        $main_county = $this->db->query("select counties.id as county_id, counties.county from  counties, user where   user.county_id = counties.id AND user.id = $user");
                        $user_county = $main_county->result_array();
                        $rca_res = $this->db->query("select counties.id as county_id, counties.county from counties where counties.id in (select rca_county.county from rca_county where rca_county.rca = $user)");
                        $other_counties = $rca_res->result_array();
                        $counties = array_merge($user_county,$other_counties);       
                        array_push($returnable, $counties);

                        $main_dist = $this->db->query("select districts.id as district_id, districts.district  from  districts, user where  user.district = districts.id AND user.id = $user");
                        $user_dist = $main_dist->result_array();
                        $other_dist = $this->db->query("select districts.id as district_id, districts.district from districts where districts.id in (select dmlt_districts.district from dmlt_districts where dmlt_districts.dmlt = $user)");
                        $other_districts = $other_dist->result_array();
                        $districts = array_merge($user_dist,$other_districts);

                        array_push($returnable, $districts);
                        return $returnable;}

                    }
                    function user_profile($user_id){
                        $user_details = $this->user_details($user_id);
        //        echo "<pre>";print_r($user_details);die;
                        $full_name = $arr[0]['fname'].' '.$user_details[0]['lname'];
                        $status = $user_details[0]['status'];
                        $data['all_counties'] = $this->all_counties();
                        $data['all_subcounties'] = $this->all_districts();


                        $data['user_logs'] = $this->rtk_logs($user_id);
                        $data['user_id'] = $user_id;
                        $data['full_name'] = $full_name;
                        $data['status'] = $status;
                        $data['user_details'] = $user_details;
                        $data['title'] = 'User Profile : '.$full_name;
                        $data['banner_text'] = 'User Profile : '.$full_name;
                        $data['content_view'] = 'rtk/rtk/admin/user_profile_view';

                        $this->load->view('rtk/template',$data);

                    }

                    function all_counties(){
                        $counties = $this->db ->query("select * from counties");
                        return ($counties->result_array());
                    }
                    function all_districts(){
                        $districts = $this->db ->query("select * from districts");
                        return ($districts->result_array());
                    }

                    public function dmlt_district_action1() {
                        $action = $_POST['action'];
                        $dmlt = $_POST['dmlt_id'];
                        $district = $_POST['dmlt_district'];

                        if ($action == 'add') {
                            $this->_add_dmlt_to_district($dmlt, $district);
                        } elseif ($action == 'remove') {
                            $this->_remove_dmlt_from_district($dmlt, $district);
                        }
        //echo "Sub-County Added Successfully";
                        redirect('rtk_management/user_profile/'.$dmlt);
                    }

                    public function add_rca_to_county() {
                        $rca = $_POST['rca_id'];

                        $county = $_POST['county'];       
                        $this->_add_rca_to_county($rca, $county);
                        redirect('rtk_management/user_profile/'.$rca);
                    }
                    function _add_rca_to_county($rca, $county, $redirect_url) {
                        $sql = "INSERT INTO `rca_county` (`id`, `rca`, `county`) VALUES (NULL, '$rca', '$county')";
                        $this->db->query($sql);
                        $object_id = $this->db->insert_id();
                        $this->logData('1', $object_id);
                    }

                    public function allocation_dashboard() {  

                        $sql_cd4 = "select count(distinct MFLCode) as cd4 from cd4_facility where rolloutstatus='1'";
                        $res_cd4 = $this->db->query($sql_cd4)->result_array();

                        $sql_rtk = "select count(distinct facility_code) as rtk from facilities where rtk_enabled='1'";
                        $res_rtk = $this->db->query($sql_rtk)->result_array();

                        $sql_eid = "select count(ID) as eid from eid_labs";
                        $res_eid = $this->db->query($sql_eid)->result_array();

                        $data['cd4'] = $res_cd4;
                        $data['rtk'] = $res_rtk;

                        $data['eid'] = $res_eid;


                        $data['banner_text'] = 'National Allocation Dashboard';
                        $data['content_view'] = 'rtk/rtk/allocation/allocation_dashboard';
                        $data['title'] = 'National Dashboard: ';
                        $this->load->view("rtk/template", $data);
                    }

        ///Allocation Functions
                    public function allocation_home() {

                        $data['zone_a_stats'] = $this->zone_allocation_stats('a');
                        $data['zone_b_stats'] = $this->zone_allocation_stats('b');
                        $data['zone_c_stats'] = $this->zone_allocation_stats('c');
                        $data['zone_d_stats'] = $this->zone_allocation_stats('d');

                        $data['banner_text'] = 'RTK National';
                        $data['content_view'] = 'rtk/rtk/allocation/allocation_home_view';
                        $data['title'] = 'National RTK Allocations: ';
                        $this->load->view("rtk/template", $data);
                    }
                    public function allocation_trend() {        

                        $months_texts = array();
                        $percentages = array();

                        for ($i=11; $i >=0; $i--) { 
                            $month =  date("mY", strtotime( date( 'Y-m-01' )." -$i months"));
                            $j = $i+1;            
                            $month_text =  date("M Y", strtotime( date( 'Y-m-01' )." -$j months")); 
                            array_push($months_texts,$month_text);
                            $sql = "select sum(reported) as reported, sum(facilities) as total, month from rtk_county_percentage 
                            where month ='$month'";

                            $res_trend = $this->db->query($sql)->result_array();            
                            foreach ($res_trend as $key => $value) {
                                $reported = $value['reported'];
                                $total = $value['total'];
                                $percentage = round(($reported/$total)*100);
                                if($percentage>100){
                                    $percentage = 100;
                                }
                                array_push($percentages, $percentage);
                                $trend_details[$month] = array('reported'=>$reported,'total'=>$total,'percentage'=>$percentage);
                            }
                        }   
                        $trend_details = json_encode($trend_details);        
                        $months_texts = str_replace('"',"'",json_encode($months_texts));        
                        $percentages = str_replace('"',"'",json_encode($percentages));                
                        $data['first_month'] = date("M Y", strtotime( date( 'Y-m-01' )." -12 months")); 
                        $data['last_month'] = date("M Y", strtotime( date( 'Y-m-01' )." -1 months")); 
                        $data['percentages'] = $percentages;
                        $data['months_texts'] = $months_texts;
                        $data['trend_details'] = $trend_details;
                        $data['banner_text'] = 'RTK National Allocation Trend';
                        $data['content_view'] = 'rtk/rtk/allocation/allocation_trend';
                        $data['title'] = 'National RTK Trend: ';
                        $this->load->view("rtk/template", $data);
                    }
                    public function allocation_stock_card() {  

                        if (!isset($month)) {
                            $month = date('mY', strtotime('-0 month'));
                            $month_1 = date('mY', strtotime('-1 month'));
                        }
                        $year = substr($month, -4);
                        $year_1 = substr($month_1, -4);
                        $month = substr_replace($month, "", -4);              
                        $month_1 = substr_replace($month_1, "", -4);              
                        $firstdate = $year . '-' . $month . '-01';
                        $firstdate1 = $year_1 . '-' . $month_1 . '-01';
                        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $lastdate = $year . '-' . $month .'-'. $num_days;        
                        $month_text =  date("F Y", strtotime($firstdate1));

        // $sql_amcs = "SELECT 
        //                 lab_commodities.id,
        //                 lab_commodities.commodity_name,
        //                 SUM(facility_amc.amc) AS amc
        //             FROM
        //                 lab_commodities,
        //                 facility_amc
        //             WHERE
        //                 lab_commodities.id = facility_amc.commodity_id
        //                     AND lab_commodities.category = '1'
        //             GROUP BY lab_commodities.id
        //             ORDER BY lab_commodities.id ASC"; 

                        $sql_endbals = "SELECT 
                        lab_commodities.id,
                        lab_commodities.commodity_name,
                        SUM(lab_commodity_details.closing_stock) AS end_bal,
                        SUM(lab_commodity_details.amc) AS amc
                        FROM
                        lab_commodities,
                        lab_commodity_details
                        WHERE
                        lab_commodities.category = '1'
                        AND lab_commodity_details.commodity_id = lab_commodities.id
                        AND lab_commodity_details.created_at BETWEEN '$firstdate' AND '$lastdate'
                        GROUP BY lab_commodities.id
                        ORDER BY lab_commodities.id ASC";

        // $facil_amcs = $this->db->query($sql_amcs)->result_array();
                        $facil_endbals = $this->db->query($sql_endbals)->result_array();
        // $count = count($facil_amcs);
        // $stock_details = array();
        // for ($i=0; $i < $count; $i++) { 
        //     $comm_id = $facil_amcs[$i]['id'];
        //     $comm_name = $facil_amcs[$i]['commodity_name'];
        //     $amc = $facil_amcs[$i]['amc'];
        //     $endbal = $facil_endbals[$i]['end_bal'];
        //     $ratio = 'N/A';
        //             //$ratio = round(($endbal/$amc),0);
        //     $stock_details[$i] = array('id'=>$comm_id,'commodity_name'=>$comm_name,'amc'=>$amc,'endbal'=>$endbal,'ratio'=>$ratio);
        // }      

                        $sql_counties = "select * from counties";
                        $option_counties = "";
                        $option_counties .='<option value="0">--Select a County--</option>';
                        $res_counties = $this->db->query($sql_counties)->result_array();
                        foreach ($res_counties as $key => $value) {
                            $option_counties .='<option value="'.$value['id'].'">'.$value['county'].'</option>';
                        }

                        $data['month_text'] = $month_text;
                        $data['stock_details'] = $facil_endbals;
                        $data['option_counties'] = $option_counties;
                        $data['banner_text'] = 'RTK National Allocation Stock Card for All Counties';
                        $data['content_view'] = 'rtk/rtk/allocation/allocation_stock_card';
                        $data['title'] = 'National RTK Stock Card';
                        $this->load->view("rtk/template", $data);
                    }
                    public function allocation_stock_card_county($county = null) {  
                        $conditions_endbal = "";
                        $conditions_amc = "";       
                        if (!isset($month)) {
                            $month = date('mY', strtotime('-0 month'));
                            $month_1 = date('mY', strtotime('-1 month'));
                        }
                        $year = substr($month, -4);
                        $year_1 = substr($month_1, -4);
                        $month = substr_replace($month, "", -4);              
                        $month_1 = substr_replace($month_1, "", -4);              
                        $firstdate = $year . '-' . $month . '-01';
                        $firstdate1 = $year_1 . '-' . $month_1 . '-01';
                        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $lastdate = $year . '-' . $month .'-'. $num_days;        
                        $month_text =  date("F Y", strtotime($firstdate1)); 

                        $sql_amcs = "select lab_commodities.id,lab_commodities.commodity_name,sum(facility_amc.amc) as amc 
                        from  lab_commodities,facility_amc,facilities,districts,counties 
                        where  lab_commodities.id = facility_amc.commodity_id and lab_commodities.category = '1' 
                        and facility_amc.facility_code = facilities.facility_code and facilities.district = districts.id 
                        and districts.county = counties.id and counties.id = '$county'
                        group by lab_commodities.id order by lab_commodities.id asc";  



                        $sql_endbals = "select lab_commodities.id,lab_commodities.commodity_name, sum(lab_commodity_details.closing_stock) as end_bal
                        from   lab_commodities, lab_commodity_details,districts,counties 
                        where lab_commodities.category = '1' and lab_commodity_details.commodity_id = lab_commodities.id
                        and lab_commodity_details.created_at between '$firstdate' and '$lastdate' 
                        and lab_commodity_details.district_id = districts.id and districts.county = counties.id and counties.id = '$county'
                        group by lab_commodities.id order by lab_commodities.id asc";

                        $facil_amcs = $this->db->query($sql_amcs)->result_array();
                        $facil_endbals = $this->db->query($sql_endbals)->result_array();
                        $count = count($facil_amcs);
                        $stock_details = array();
                        for ($i=0; $i < $count; $i++) { 
                            $comm_id = $facil_amcs[$i]['id'];
                            $comm_name = $facil_amcs[$i]['commodity_name'];
                            $amc = $facil_amcs[$i]['amc'];
                            $endbal = $facil_endbals[$i]['end_bal'];
                            $ratio = round(($endbal/$amc),0);
                            $stock_details[$i] = array('id'=>$comm_id,'commodity_name'=>$comm_name,'amc'=>$amc,'endbal'=>$endbal,'ratio'=>$ratio);
                        }      

                        $county_dets = counties::get_county_name($county);
                        $county_name = $county_dets['county'];

                        $sql_counties = "select * from counties";
                        $option_counties = "";
                        $option_counties .='<option value="'.$county.'">'.$county_name.'</option>';
                        $option_counties .='<option value="0">--Select a County--</option>';
                        $res_counties = $this->db->query($sql_counties)->result_array();
                        foreach ($res_counties as $key => $value) {
                            $option_counties .='<option value="'.$value['id'].'">'.$value['county'].'</option>';
                        }   

                        $data['month_text'] = $month_text;
                        $data['county_name'] = $county_name;
                        $data['county_id'] = $county;
                        $data['stock_details'] = $stock_details;
                        $data['option_counties'] = $option_counties;
                        $data['banner_text'] = "RTK National Allocation Stock Card for $county_name County";
                        $data['content_view'] = 'rtk/rtk/allocation/allocation_stock_card_county';
                        $data['title'] = "National Allocation Stock Card for $county_name County";
                        $this->load->view("rtk/template", $data);
                    }

                    public function download_county_mos($county = null,$report_type) {  
                        $conditions_endbal = "";
                        $conditions_amc = "";       
                        if (!isset($month)) {
                            $month = date('mY', strtotime('-0 month'));
                            $month_1 = date('mY', strtotime('-1 month'));
                        }
                        $year = substr($month, -4);
                        $year_1 = substr($month_1, -4);
                        $month = substr_replace($month, "", -4);              
                        $month_1 = substr_replace($month_1, "", -4);              
                        $firstdate = $year . '-' . $month . '-01';
                        $firstdate1 = $year_1 . '-' . $month_1 . '-01';
                        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $lastdate = $year . '-' . $month .'-'. $num_days;        
                        $month_text =  date("F Y", strtotime($firstdate1)); 
                        $county_dets = counties::get_county_name($county);
                        $county_name = $county_dets['county'];

                        $sql_amcs = "SELECT districts.district,facilities.facility_code,facilities.facility_name,lab_commodities.id,
                        lab_commodities.commodity_name,SUM(facility_amc.amc) AS amc FROM lab_commodities,facility_amc,
                        facilities,districts,counties WHERE lab_commodities.id = facility_amc.commodity_id AND 
                        lab_commodities.category = '1' AND facility_amc.facility_code = facilities.facility_code
                        AND facilities.district = districts.id AND districts.county = counties.id AND counties.id = '$county'
                        GROUP BY districts.district,facilities.facility_code,lab_commodities.id
                        ORDER BY districts.district,facilities.facility_code,lab_commodities.id ASC";   

                        $sql_endbals = "SELECT districts.district,facilities.facility_code,facilities.facility_name,lab_commodities.id,
                        lab_commodities.commodity_name,SUM(lab_commodity_details.closing_stock) AS end_bal FROM
                        lab_commodities,lab_commodity_details,facility_amc,facilities,districts,counties WHERE 
                        lab_commodities.id = facility_amc.commodity_id AND lab_commodity_details.commodity_id = lab_commodities.id
                        AND lab_commodity_details.created_at BETWEEN '2014-11-01' AND '2014-11-30' AND lab_commodities.category = '1'
                        AND lab_commodity_details.facility_code = facilities.facility_code AND facility_amc.facility_code = facilities.facility_code AND facilities.district = districts.id 
                        AND districts.county = counties.id AND counties.id = '$county' 
                        GROUP BY districts.district,facilities.facility_code,lab_commodities.id
                        ORDER BY districts.district,facilities.facility_code,lab_commodities.id ASC";



                        $facil_amcs = $this->db->query($sql_amcs)->result_array();
                        $facil_endbals = $this->db->query($sql_endbals)->result_array();
                        $count = count($facil_amcs);
                        $stock_details = array();
                        for ($i=0; $i < $count; $i++) { 
                            $comm_id = $facil_amcs[$i]['id'];
                            $district = $facil_amcs[$i]['district'];
                            $fcode = $facil_amcs[$i]['facility_code'];
                            $fname = $facil_amcs[$i]['facility_name'];
                            $comm_name = $facil_amcs[$i]['commodity_name'];
                            $amc = $facil_amcs[$i]['amc'];
                            $endbal = $facil_endbals[$i]['end_bal'];
                            $ratio = round(($endbal/$amc),0);
                            $stock_details[$i] = array('id'=>$comm_id,
                                'commodity_name'=>$comm_name,
                                'amc'=>$amc,
                                'endbal'=>$endbal,
                                'ratio'=>$ratio,
                                'district'=>$district,
                                'facility_code'=>$fcode,
                                'facility_name'=>$fname);
                        } 



                        foreach ($stock_details as $key=> $value) {        
                            $dist = $value['district'];
                            $fcode = $value['facility_code'];
                            $fname = $value['facility_name'];
                            $cname = $value['commodity_name'];
                            $amc = $value['amc'];
                            $endbal = $value['endbal'];
                            $ratio = $value['ratio'];        
                            $table_body .= '<tr>';
                            $table_body .='<td>'.$dist.'</td>';
                            $table_body .='<td>'.$fcode.'</td>';
                            $table_body .='<td>'.$fname.'</td>';
                            $table_body .='<td>'.$cname.'</td>';
                            $table_body .='<td>'.$amc.'</td>';
                            $table_body .='<td>'.$endbal.'</td>';
                            $table_body .='<td>'.$ratio.'</td>';             
                            $table_body .='</tr>';

                        }    

                        $table_head = '<style>table.data-table {border: 1px solid #DDD;margin: 10px auto;border-spacing: 0px;}
                        table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;font-weight:bold}
                        table.data-table td, table th {padding: 4px;}
                        table.data-table td {border: none;border-left: 1px solid #DDD;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;font-size:13px;}
                        .col5{background:#D8D8D8;}</style></table>
                        <table id="stock_card_table" class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="7" id="th-banner">
                                        HIV Rapid Test Kit Stock Status as at end of '.$month_text.' for '.$county_name.' County
                                    </th>
                                </tr>
                                <tr>
                                    <th>Sub-County</th>
                                    <th>Facility Code</th>
                                    <th>Facility Name</th>
                                    <th>Commodity Name</th>
                                    <th>AMC</th>
                                    <th>Ending Balance</th>
                                    <th>MOS Central</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: solid 1px #828274;">
                                '.$table_body.'
                            </tbody>';
                            $table_foot = '</table>';
                            $report_name = "Stock Status as at end of $month_text for $county_name County";
                            $title = "HIV Rapid Test Kit Stock Status as at end of $month_text for $county_name County";
                            $html_data = $table_head . $table_body . $table_foot;
        //echo "$html_data";die();
                            switch ($report_type) {
                                case 'excel' :
                                $this->_generate_lab_report_excel($report_name, $title, $html_data);
                                break;
                                case 'pdf' :
                                $this->_generate_lab_report_pdf($report_name, $title, $html_data);
                                break;
                            };
        // $title = 'HIV Rapid Test Kit Stock Status as at end of '.$month_text.' for '.$county_name.' County';
        // $report_name = 'Stock Status for '.$month_text.' for '.$county_name.' County';
        // $this-> _generate_lab_report_excel($report_name, $title, $html_data) { 

        // $county_dets = counties::get_county_name($county);
        // $county_name = $county_dets['county'];

        // $sql_counties = "select * from counties";
        // $option_counties = "";
        // $option_counties .='<option value="'.$county.'">'.$county_name.'</option>';
        // $option_counties .='<option value="0">--Select a County--</option>';
        // $res_counties = $this->db->query($sql_counties)->result_array();
        // foreach ($res_counties as $key => $value) {
        //    $option_counties .='<option value="'.$value['id'].'">'.$value['county'].'</option>';
        // }   

        // $data['month_text'] = $month_text;
        // $data['county_name'] = $county_name;
        // $data['stock_details'] = $stock_details;
        // $data['option_counties'] = $option_counties;
        // $data['banner_text'] = "RTK National Allocation Stock Card for $county_name County";
        // $data['content_view'] = 'rtk/rtk/allocation/download_mos';
        // $data['title'] = "National Allocation Stock Card for $county_name County";
        // $this->load->view("rtk/template", $data);
                        }



                        public function test_php(){
                            phpinfo();
                        }

                        public function sess(){

                            print_r($this->session->all_userdata());
                        }
public function cmlt_allocation_dashboard(){        //karsan
    $month = date('m');
    $current_month = date('mY', time());
    $year_current = substr($current_month, -4);
    $county = (int) $this->session->userdata("county_id");

    $sql = "select * from counties where id = $county";
    $result = $this->db-> query($sql)->result_array();

    $sql2 = "select * from districts where county = $county";
    $result2 = $this->db-> query($sql2)->result_array();

    $facilities_data = '';
    foreach ($result2 as $key => $value) {
        $district_id = $value['id'];
        $district_name = $value['district'];

        $sql3 = "select count(*) as count from facilities where district = $district_id and rtk_enabled = 1";
        $result3 = $this->db-> query($sql3)->result_array();

        $district_summary = $this->rtk_summary_district($district_id, $year_current, $current_month);
        $facilities_data[$district_id]['reported_facilities'] = $district_summary['reported'];
        $facilities_data[$district_id]['total_facilities'] = $district_summary['total_facilities'];
        $facilities_data[$district_id]['reporting_month'] = $district_summary['reporting_month'];

        // $allocated =$this-> get_remaining_districts($district_id);
        // var_dump($allocated);
        // die;


        // array_push($facilities_data, $district_array);
    }

    $sql4 = "SELECT DISTINCT  districts.id FROM  districts, counties WHERE districts.county = counties.id and counties.id = '$county' 
    and districts.id not in (select distinct district_id from allocation_details where month(month) = '$month') ";               
    $result4 = $this->db->query($sql4)->result_array();  

    $district_array = array();

    foreach ($result4 as $value4) {
        $district_array[] = $value4['id'];
    }    
        //         print_r($district_array);
        // die;
        //         echo "<pre>";
        // array_push($district_array2, $district_array);

        // $sql4 = "";
        // $result4 = $this->db->query($sql4)->result_array();

    $county_name = $result[0]['county'];

    $data['title'] = "County Allocation";
    $data['banner_text'] = "$county_name County Allocation";
    $data['content_view'] = 'rtk/rtk/clc/cmlt_allocation_dashboard';     
    $data['county_details'] = $result;
    $data['district_details'] = $result2;
    $data['facilities_data'] = $facilities_data;
    $data['district_array'] = $district_array;
    $this->load->view('rtk/template', $data); 
}

public function district_allocation_table(){        //karsan slow
    $county = (int) $this->session->userdata("county_id");
    $district_id = $this->uri->segment(3);

    $sql4 = "select * from counties where id = '$county'";
    $result4 = $this->db->query($sql4)->result_array();

    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    districts.id as districtid,
    counties.county,
    counties.id as countyid,
    counties.screening_current_amount,
    counties.confirmatory_current_amount,
    counties.tiebreaker_current_amount
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND districts.id = '$district_id'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";
    $result = $this->db->query($sql)->result_array();
    $final_dets = array();
    // echo "<pre>"; print_r($result4);die;

    foreach ($result as $key => $id_details) {

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $districtid = $id_details['districtid'];
        $countyid = $id_details['countyid'];
        $facilityname = $id_details['facility_name'];

        // $sql2 = "SELECT 
        //             facility_amc.amc as amc
        //         FROM
        //             facilities,
        //             facility_amc
        //         WHERE
        //             facilities.facility_code = facility_amc.facility_code
        //                 AND facilities.facility_code = '$fcode'
        //                 AND facility_amc.commodity_id between 4 and 22            
        //         ORDER BY facility_amc.commodity_id ASC";

        // $result2 = $this->db->query($sql2)->result_array();
        // echo "$sql2"; die;
        $sql3 = "SELECT amc,
        commodity_id,
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result3 = $this->db->query($sql3)->result_array();
        // echo "<pre>"; print_r($result3); die;

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['district_id'] = $districtid;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['county_id'] = $countyid;
        // $final_dets[$fcode]['screening_current_amount'] = $screening_current_amount;
        // $final_dets[$fcode]['confirmatory_current_amount'] = $confirmatory_current_amount;
        // $final_dets[$fcode]['tiebreaker_current_amount'] = $tiebreaker_current_amount;
        // $final_dets[$fcode]['amcs'] = $result2;
        $calculated_amc = $this->calculate_amc($fcode);

        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
        $final_dets[$fcode]['amc'] = $calculated_amc;
    }

        // echo "$sql3";die;
    // echo "<pre>";print_r($final_dets);exit;
    $data['title'] = "Sub-County Allocation";
    $data['banner_text'] = '<h2 align="center"> RTK Allocation '.$result[0]['county'].' ---- '.$result[0]['district'].'</h2>';
    $data['content_view'] = 'rtk/rtk/clc/cmlt_district_allocation';        
    $data['final_dets'] = $final_dets;
    $data['county_name'] = $result4[0]['county'];
    $data['countyid'] = $result4[0]['id'];
    $data['districtid'] = $district_id;
    $data['district_name'] = $result['district'];
    $data['screening_current_amount'] = $result4[0]['screening_current_amount'];
    $data['confirmatory_current_amount'] = $result4[0]['confirmatory_current_amount'];
    $data['tiebreaker_current_amount'] = $result4[0]['tiebreaker_current_amount'];

    // echo "<pre>";print_r($data);exit;
    $this->load->view('rtk/template', $data); 
}

public function scmlt_allocation_table($district_id = NULL){        //karsan slow
    $county = (int) $this->session->userdata("county_id");
    $district_id = (int) $this->session->userdata("district_id");

    // echo "<pre>";print_r($district_id);exit;
    $district_id = $this->uri->segment(3);

    $sql4 = "select * from counties where id = '$county'";
    $result4 = $this->db->query($sql4)->result_array();

    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    districts.id as districtid,
    counties.county,
    counties.id as countyid,
    counties.screening_current_amount,
    counties.confirmatory_current_amount,
    counties.tiebreaker_current_amount
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND districts.id = '$district_id'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";
    $result = $this->db->query($sql)->result_array();
    $final_dets = array();
    // echo "<pre>"; print_r($result4);die;

    foreach ($result as $key => $id_details) {

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $districtid = $id_details['districtid'];
        $countyid = $id_details['countyid'];
        $facilityname = $id_details['facility_name'];

        // $sql2 = "SELECT 
        //             facility_amc.amc as amc
        //         FROM
        //             facilities,
        //             facility_amc
        //         WHERE
        //             facilities.facility_code = facility_amc.facility_code
        //                 AND facilities.facility_code = '$fcode'
        //                 AND facility_amc.commodity_id between 4 and 22            
        //         ORDER BY facility_amc.commodity_id ASC";

        // $result2 = $this->db->query($sql2)->result_array();
        // echo "$sql2"; die;
        $sql3 = "SELECT amc,
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result3 = $this->db->query($sql3)->result_array();
        // echo "<pre>"; print_r($result3); die;

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['district_id'] = $districtid;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['county_id'] = $countyid;
        // $final_dets[$fcode]['screening_current_amount'] = $screening_current_amount;
        // $final_dets[$fcode]['confirmatory_current_amount'] = $confirmatory_current_amount;
        // $final_dets[$fcode]['tiebreaker_current_amount'] = $tiebreaker_current_amount;
        // $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }

        // echo "$sql3";die;
    // echo "<pre>";print_r($result);exit;
    $data['title'] = "Sub-County Allocation";
    $data['banner_text'] = '<h2 align="center"> RTK Allocation '.$result[0]['county'].' ---- '.$result[0]['district'].'</h2>';
    $data['content_view'] = 'rtk/rtk/clc/scmlt_district_allocation';        
    $data['final_dets'] = $final_dets;
    $data['county_name'] = $result4[0]['county'];
    $data['countyid'] = $result4[0]['id'];
    $data['districtid'] = $district_id;
    $data['district_name'] = $result['district'];
    $data['screening_current_amount'] = $result4[0]['screening_current_amount'];
    $data['confirmatory_current_amount'] = $result4[0]['confirmatory_current_amount'];
    $data['tiebreaker_current_amount'] = $result4[0]['tiebreaker_current_amount'];

    // echo "<pre>";print_r($data);exit;
    $this->load->view('rtk/template', $data); 
}

public function calculate_amc($facility_code)
{
    $query_old = "SELECT 
            commodity_id, 
            AVG(amc) AS amc, 
            created_at, 
            days_out_of_stock
            FROM
                lab_commodity_details
            WHERE
                facility_code = '$facility_code'
            AND created_at > (NOW() -INTERVAL 4 MONTH)
            AND created_at < (NOW() -INTERVAL 1 MONTH)
            AND commodity_id IN (4 , 5, 6)
            GROUP BY commodity_id";//Screening and confirmatory

    $query = "SELECT 
            commodity_id, 
            AVG(q_used) AS amc, 
            created_at, 
            days_out_of_stock
            FROM
                lab_commodity_details
            WHERE
                facility_code = '$facility_code'
            AND created_at > (NOW() -INTERVAL 4 MONTH)
            AND created_at < (NOW() -INTERVAL 1 MONTH)
            AND commodity_id IN (4 , 5, 6)
            GROUP BY commodity_id";//Screening and confirmatory
            
    $result = $this->db->query($query)->result_array();
    // echo "<pre>";print_r($result);exit;

    $final_amc_s = $final_amc_c = array();
    //Was to build an array for this, chose to leave the bulk to the query
    return $result;
}

function get_remaining_districts($district_id){
    $county_id = $this->session->userdata('county_id');
    $month = date('m');

    $sql = "SELECT DISTINCT  districts.id FROM  districts, counties WHERE districts.county = counties.id and counties.id = '$county_id' 
    and districts.id not in (select distinct district_id from allocation_details where month(month) = '$month') ";               
    $result = $this->db->query($sql)->result_array();  

    $output = array();

    foreach ($result as $key => $value) {
        $district = $value['id'];
        array_push($output, $district);
    }           
    echo json_encode($output);
        // echo $sql;
}

function submit_district_allocation_report(){
    // echo "<pre>";print_r($this->input->post());exit;

    $district_id = $_POST['district_id'];

    $month = date('Y-m-d');

    $sql = "select distinct district_id, month from allocation_details where district_id = '$district_id' and  month = '$month'";
    $result = $this->db->query($sql)->result_array();
    $count_submitted = count($result);
    // echo "<pre>";print_r($count_submitted);exit;
    if($count_submitted==0) {
        $county_id = $_POST['county_id'];
        $facility_name = $_POST['facility_name'];
        $facility_code = $_POST['facility_code'];
        $q_allocate_s = $_POST['q_allocate_s'];
        $q_allocate_c = $_POST['q_allocate_c'];
        $ending_bal_c= $_POST['ending_bal_c'];
        $ending_bal_s = $_POST['ending_bal_s'];
        $decision_s = $_POST['decision_s'];
        $decision_c = $_POST['decision_c'];
        $remark_s = $_POST['feedback_s'];
        $remark_c = $_POST['feedback_c'];
        $mmos_s = $_POST['mmos_s'];
        $mmos_c = $_POST['mmos_c'];
        $amc_c = $_POST['amc_c'];
        $amc_s = $_POST['amc_s'];
        $drug_id = $_POST['commodity_id'];

        $new_screening_amount = $_POST['new_screening_amount'];
        $new_confirmatory_amount = $_POST['new_confirmatory_amount'];
        $new_tiebreaker_amount = $_POST['new_tiebreaker_amount'];

        $commodity_count = count($drug_id);
        // echo $commodity_count;exit;

        $user_id = $this->session->userdata('user_id');        

        $allocation_date = date('Y-m-d');
        $new_data = array();

        for ($i = 0; $i < $commodity_count; $i++) {            

            $mydata = array('county_id'=>$county_id, 
                'district_id'=>$district_id,
                'facility_code' => $facility_code[$i], 
                'facility_name' => $facility_name[$i], 
                'amc_s' => $amc_s[$i],
                'ending_bal_s' => $ending_bal_s[$i],
                'allocate_s' => $q_allocate_s[$i],                    
                'mmos_s' => $mmos_s[$i],
                'remark_s' => $remark_s[$i],
                'decision_s' => $decision_s[$i],
                'amc_c' => $amc_c[$i],
                'ending_bal_c' => $ending_bal_c[$i],
                'allocate_c' => $q_allocate_c[$i],
                'mmos_c' => $mmos_c[$i],
                'remark_c' => $remark_c[$i],
                'decision_c' => $decision_c[$i],
                'month'=>$allocation_date, 
                'user_id'=>$user_id);
            array_push($new_data,$mydata);
        }
        // echo "<pre>";print_r($new_data);exit;
        $res = $this->db->insert_batch('allocation_details',$new_data);
        // echo "<pre>";print_r($res);exit;

        $sql2 = "update counties set screening_current_amount = '$new_screening_amount', confirmatory_current_amount = '$new_confirmatory_amount' where id = '$county_id'";   
        $this->db->query($sql2);

        echo "1";
        // echo $sql2;
        // print_r($mydata) ;

    }else{
        echo "2";           

    }          
}
public function edit_county_allocation_report($district_id){
    $month = date('mY');
    $year = substr($month, -4);
    $month = substr_replace($month, "", -4);      
    $monthyear = $year . '-' . $month . '-1';
    $firstdate = $year . '-' . $month . '-01';
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $sql = "select * from allocation_details where district_id = $district_id and created_at between '$firstdate' and '$lastdate'";
    $result = $this->db->query($sql)->result_array();
    $countyid = $result[0]['county_id'];

    $sql2 = "select *, counties.county as county_name from counties, districts where counties.id = $countyid and districts.id = $district_id and districts.county = counties.id";
    $result2 = $this->db->query($sql2)->result_array();

    $data['districtid'] = $district_id;
    $data['district_name'] = $result2[0]['district'];
    $data['county_name'] = $result2[0]['county_name'];
    $data['allocation_details'] = $result;
    $data['screening_current_amount'] = $result2[0]['screening_current_amount'];
    $data['confirmatory_current_amount'] = $result2[0]['confirmatory_current_amount'];
    $data['tiebreaker_current_amount'] = $result2[0]['tiebreaker_current_amount'];

    $data['title'] = "Sub-County Allocation";
    $data['banner_text'] = '<h2 align="center"> RTK Allocation '.$result2[0]['county_name'].' ---- '.$result2[0]['district'].'Sub County</h2>';
    $data['content_view'] = 'rtk/rtk/clc/cmlt_district_allocation_edit'; 

    $this->load->view('rtk/template', $data); 
}
function edit_district_allocation_report(){
    $district_id = $_POST['district_id'];

    $month = date('Y-m-d');

        // $sql = "select distinct district_id, month from allocation_details where district_id = '$district_id' and  month = '$month'";
        // $result = $this->db->query($sql)->result_array();
        // $count_submitted = count($result);

    if($count_submitted==0) {
        $county_id = $_POST['county_id'];
        $facility_name = $_POST['facility_name'];
        $facility_code = $_POST['facility_code'];
        $q_allocate_s = $_POST['q_allocate_s'];
        $q_allocate_c = $_POST['q_allocate_c'];
        $ending_bal_c= $_POST['ending_bal_c'];
        $ending_bal_s = $_POST['ending_bal_s'];
        $decision_s = $_POST['decision_s'];
        $decision_c = $_POST['decision_c'];
        $remark_s = $_POST['feedback_s'];
        $remark_c = $_POST['feedback_c'];
        $mmos_s = $_POST['mmos_s'];
        $mmos_c = $_POST['mmos_c'];
        $amc_c = $_POST['amc_c'];
        $amc_s = $_POST['amc_s'];
        $drug_id = $_POST['commodity_id'];

        $new_screening_amount = $_POST['new_screening_amount'];
        $new_confirmatory_amount = $_POST['new_confirmatory_amount'];
        $new_tiebreaker_amount = $_POST['new_tiebreaker_amount'];

        $commodity_count = count($drug_id);

        $user_id = $this->session->userdata('user_id');        

        $allocation_date = date('Y-m-d');

        for ($i = 0; $i < $commodity_count; $i++) {            

            $mydata = array('county_id'=>$county_id, 'district_id'=>$district_id,'facility_code' => $facility_code[$i], 'facility_name' => $facility_name[$i], 'amc_s' => $amc_s[$i],
                'ending_bal_s' => $ending_bal_s[$i],
                'allocate_s' => $q_allocate_s[$i],                    
                'mmos_s' => $mmos_s[$i],
                'remark_s' => $remark_s[$i],
                'decision_s' => $decision_s[$i],

                'amc_c' => $amc_c[$i],
                'ending_bal_c' => $ending_bal_c[$i],
                'allocate_c' => $q_allocate_c[$i],
                'mmos_c' => $mmos_c[$i],
                'remark_c' => $remark_c[$i],
                'decision_c' => $decision_c[$i],

                'month'=>$allocation_date, 
                'user_id'=>$user_id);
            $this->db->update('allocation_details', $mydata); 
            print_r($mydata) ;

        }

        $sql2 = "update counties set screening_current_amount = '$new_screening_amount', confirmatory_current_amount = '$new_confirmatory_amount' where id = '$county_id'";   
        $this->db->query($sql2);

        echo "1";          

    }else{
        echo "2";           

    }          
}
public function edit_drawing_rights(){
    $county_id = $this->session->userdata('county_id');

    $sql = "select *, counties.county as county_name from counties, districts where districts.county = counties.id and counties.id = $county_id";
    $data['result'] = $this->db->query($sql)->result_array();
        // print_r($data['result']); die;
    $data['title'] = "County Allocation";
    $data['banner_text'] = '<h2 align="center"> Edit Drawing Rights for Each Sub County in '.$data['result'][0]['county_name'].'</h2>';
    $data['content_view'] = 'rtk/rtk/clc/edit_drawing_rights';  

    $this->load->view('rtk/template', $data); 
}

function update_drawiing_rights_sc(){

    $month = date('Y-m-d');

    $county_id = $_POST['county_id'];
    $district_id = $_POST['district_id'];
    $sc_amount_s = $_POST['sc_amount_s'];
    $sc_amount_c = $_POST['sc_amount_c'];

    $new_screening_amount = $_POST['new_screening_amount'];
    $new_confirmatory_amount = $_POST['new_confirmatory_amount'];
    $new_tiebreaker_amount = $_POST['new_tiebreaker_amount'];

    $user_id = $this->session->userdata('user_id');    

    $mydata = array('county_id'=>$county_id,
        'district_id'=>$district_id,
        'screening_current_amount' => $sc_amount_s, 
        'confirmatory_current_amount' => $sc_amount_c,
        'month'=>$month, 
        'user_id'=>$user_id);

    $this->db->update('district_drawing_rights', $mydata); 

    print_r($mydata) ;           

    $sql2 = "update counties set screening_current_amount = '$new_screening_amount', confirmatory_current_amount = '$new_confirmatory_amount' where id = '$county_id'";   
    $this->db->query($sql2);            

}

public function cmlt_allocation_report(){
    $county_id = $this->session->userdata('county_id');
    $firstdate = date('Y-m-01');
    $lastdate = date('Y-m-31');

        // echo "$firstdate and $num_days"; die;
    $sql = "select allocation_details.*, counties.county as county_name, districts.district as district_name from allocation_details, counties, districts where counties.id = $county_id and created_at between '$firstdate' and '$lastdate' and counties.id = districts.county and allocation_details.district_id = districts.id";
    $data['result'] = $this->db->query($sql)->result_array();

    $sql2 = "select * from counties where id = $county_id";
    $data['county_data'] = $this->db->query($sql2)->result_array();
        // echo "$sql";
        // echo "<pre>"; print_r($result);    die;
    $data['title'] = "County Allocation";
    $data['banner_text'] = '<h2 align="center"> RTK Allocation Report ('.$data['county_data'][0]['county'].')</h2>';
    $data['content_view'] = 'rtk/rtk/clc/cmlt_allocation_report';  

    $this->load->view('rtk/template', $data); 
}
public function download_allocation_county($county_id){
    $firstdate = date('Y-m-01');
    $lastdate = date('Y-m-31');

    $sql = "select allocation_details.*, counties.county as county_name, districts.district as district_name from allocation_details, counties, districts where counties.id = $county_id and created_at between '$firstdate' and '$lastdate' and counties.id = districts.county and allocation_details.district_id = districts.id";
    $result = $this->db->query($sql)->result_array();
    foreach ($result as $key => $value) {
        $county_name = $value['county_name'];
        $district_name = $value['district_name'];              
        $facility_code = $value['facility_code'];
        $facility_name = $value['facility_name'];  

        $ending_bal_s = $value['ending_bal_s'];     
        $amc_s = $value['amc_s']; 
        $mmos_s  = $value['mmos_s']; 
        $allocate_s = $value['allocate_s']; 
        $remark_s = $value['remark_s']; 
        $decision_s = $value['decision_s']; 

        $ending_bal_c = $value['ending_bal_c'];     
        $amc_c = $value['amc_c']; 
        $mmos_c = $value['mmos_c']; 
        $allocate_c = $value['allocate_c']; 
        $remark_c = $value['remark_c'];  
        $decision_c = $value['decision_c'];

        $allocate_s_kits = round($allocate_s /100); 
        $allocate_c_kits = round($allocate_c/30); 


        $allocation_details[] = array($county_name, $district_name, $facility_code, $facility_name, $ending_bal_s, $amc_s, $mmos_s, $allocate_s, $allocate_s_kits, $remark_s, $decision_s,$ending_bal_c, $amc_c, $mmos_c, $allocate_c, $allocate_c_kits, $remark_c, $decision_c);
    }
        // echo "<pre>";
        // print_r($allocation_details); die;
    $this->excel->setActiveSheetIndex(0);
        //name the worksheet
    $this->excel->getActiveSheet()->setTitle(' Counties');
        //set cell A1 content with some text
    $this->excel->getActiveSheet()->setCellValue('A1', ' RTK Commodity Allocation Data (in Tests)');
    $this->excel->getActiveSheet()->setCellValue('A4', 'County');
    $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
    $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
    $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
    $this->excel->getActiveSheet()->setCellValue('E4', 'Screening');
    $this->excel->getActiveSheet()->setCellValue('L4',  'Confirmatory');

    $this->excel->getActiveSheet()->setCellValue('E5', 'Ending Balance');
    $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
    $this->excel->getActiveSheet()->setCellValue('G5', 'Number of Months of Stock');
    $this->excel->getActiveSheet()->setCellValue('H5', 'Quantity to Allocate');
    $this->excel->getActiveSheet()->setCellValue('I5', 'In Kits Quantity to Allocate ');
    $this->excel->getActiveSheet()->setCellValue('J5', 'Remarks');
    $this->excel->getActiveSheet()->setCellValue('K5', 'Decision');

    $this->excel->getActiveSheet()->setCellValue('L5', 'Ending Balance');
    $this->excel->getActiveSheet()->setCellValue('M5', 'AMC');
    $this->excel->getActiveSheet()->setCellValue('N5', 'Number of Months of Stock');
    $this->excel->getActiveSheet()->setCellValue('O5', 'Quantity to Allocate');
    $this->excel->getActiveSheet()->setCellValue('P5', 'In Kits Quantity to Allocate');
    $this->excel->getActiveSheet()->setCellValue('Q5', 'Remarks');
    $this->excel->getActiveSheet()->setCellValue('R5', 'Decision');



        //merge cell A1 until C1
    $this->excel->getActiveSheet()->mergeCells('A1:F1');
    $this->excel->getActiveSheet()->mergeCells('E4:K4');
    $this->excel->getActiveSheet()->mergeCells('L4:R4');
        //set aligment to center for that merged cell (A1 to C1)
    $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $this->excel->getActiveSheet()->getStyle('L4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
    $this->excel->getActiveSheet()->getStyle('A4:R4')->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

    for($col = ord('A'); $col <= ord('P'); $col++){
        //set column dimension
        $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
        //change the font size
        $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);

    }

    foreach ($allocation_details as $row){
        $exceldata[] = $row;
    }        

        //Fill data 
    $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');

$filename= $result[0]['county_name'].' County ('.$result[0]['month'].').xlsx';         //save our workbook as this file name
header('Content-Type: application/vnd.ms-excel');         //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"');         //tell browser what's the file name
header('Cache-Control: max-age=0');         //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
        //force user to download the Excel file without writing it to server's HD
ob_end_clean();
$objWriter->save('php:        //output');
        // echo "yes";
}
public function send_allocation_county($county_id){

    $firstdate = date('Y-m-01');
    $lastdate = date('Y-m-31');

    $sql = "select allocation_details.*, counties.county as county_name, districts.district as district_name from allocation_details, counties, districts where counties.id = $county_id and created_at between '$firstdate' and '$lastdate' and counties.id = districts.county and allocation_details.district_id = districts.id";
    $result = $this->db->query($sql)->result_array();
    $created_at = $result[0]['created_at'];

    $allocation_month = date('F, Y', strtotime($created_at));
        //  echo  $allocation_month;
        //  echo "<pre>";
        // print_r($result);
        // die();


    $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

    <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>
    <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold;display: block; font-size: 13px;'>Health Commodities Management Platform</div>
    <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>Rapid Test Kits (RTK) System</div>   
    <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>".$result[0]['county_name']." Allocation Details (".$allocation_month.")</div><hr />    

    <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
        table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
        table.data-table td, table th {padding: 4px;}
        table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
        .col5{background:#D8D8D8;}</style>";
        $table_head = '
        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
            <thead border="0" style="margin: 10px auto;font-weight:900">
                <tr>
                    <th>County</th>
                    <th>Sub-County</th>
                    <th>Facility Code</th>
                    <th>Facility Name</th>
                    <th colspan="6">Screening</th>
                    <th colspan="6">Confirmatory</th>                           
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>

                    <th>Ending Balance</th>
                    <th>AMC</th>
                    <th>Month of Stock</th>
                    <th>Quantity to Allocate</th>
                    <th>Remarks</th>
                    <th>Decision</th>

                    <th>Ending Balance</th>
                    <th>AMC</th>
                    <th>Month of Stock</th>
                    <th>Quantity to Allocate</th>
                    <th>Remarks</th>
                    <th>Decision</th>
                </thead>
                <tbody>';      
                    $table_body = '';
                    $count = count($result);
        // for ($i=0; $i<$count; $i++){
                    foreach ($result as $key => $value) {
        // $allocation_month = date('m, Y', $value['month']);


                        $table_body .= '<tr><td>' . $value['county_name'] . '</td>';
                        $table_body .= '<td>' . $value['district_name'] . '</td>';
                        $table_body .= '<td>' . $value['facility_code'] . '</td>';
                        $table_body .= '<td>' . $value['facility_name'] . '</td>';
                        $table_body .= '<td>' . $value['ending_bal_s'] . '</td>';
                        $table_body .= '<td>' . $value['amc_s'] . '</td>';
                        $table_body .= '<td>' . $value['mmos_s'] . '</td>';
                        $table_body .= '<td>' . $value['q_allocate_s'] . '</td>';
                        $table_body .= '<td>' . $value['remark_s'] . '</td>';
                        $table_body .= '<td>' . $value['decision_s'] . '</td>';
                        $table_body .= '<td>' . $value['ending_bal_c'] . '</td>';
                        $table_body .= '<td>' . $value['amc_c'] . '</td>';
                        $table_body .= '<td>' . $value['mmos_c'] . '</td>';
                        $table_body .= '<td>' . $value['q_allocate_c'] . '</td>';
                        $table_body .= '<td>' . $value['remark_c'] . '</td>';
                        $table_body .= '<td>' . $value['decision_c'] . '</td></tr>';
                    }
        // }
                    $table_foot = '</tbody></table>';


                    $email_address = 'annchemu@gmail.com';
                    $message = 'Dear National Team,<br/></br/>Please find attached the Allocation Report for '.$result[0]['county_name'].'as at end of '.$allocation_month; 
                    $html_data = $html_title . $table_head . $table_body . $table_foot;
        // echo "$html_data";die();
                    $reportname = $result[0]['county_name'].' County Report ('.$allocation_month.')';
        // $this->create_pdf($html_data,$reportname);
        // echo $reportname;
                    $this->sendmail($html_data,$message, $reportname, $email_address);  
                }
public function cmlt_allocation_details($county= 1 ){

    $county = (int) $this->session->userdata("county_id");
    // echo "<pre>";print_r($this->session->all_userdata());exit;

    ini_set('max_execution_time',-1);
    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND counties.id = '$county'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";

    $result = $this->db->query($sql)->result_array();

// echo "<pre>";print_r($result);exit;
    $final_dets = array();

    foreach ($result as $key => $id_details) {

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];



        $sql2 = "SELECT 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";

        $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result3 = $this->db->query($sql3)->result_array();

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }

// echo "$sql3";die;
// echo "<pre>";
// print_r($result3);die;

    $data['title'] = "";
    $data['banner_text'] = $result[0]['county'];
    $data['content_view'] = 'rtk/allocation_committee/cmlt_allocation';        
    $data['final_dets'] = $final_dets;
    $this->load->view('rtk/template', $data); 
}

public function scmlt_allocation_details($county= 1 ){
//karsan

    $county = (int) $this->session->userdata("county_id");
    $district = (int) $this->session->userdata("district_id");
    // echo "<pre>";print_r($this->session->all_userdata());exit;

    ini_set('max_execution_time',-1);
    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND districts.id = '$district'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";

    $result = $this->db->query($sql)->result_array();

    // echo "<pre>";print_r($result);exit;
    $final_dets = array();

    foreach ($result as $key => $id_details) {

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];

        $sql2 = "SELECT 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";

        $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result3 = $this->db->query($sql3)->result_array();
        // echo "<pre>";print_r($result3);exit;

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }
    // echo "<pre>";print_r($final_dets);exit;

// echo "$sql3";die;
// echo "<pre>";
// print_r($result3);die;

    $data['title'] = "";
    $data['banner_text'] = $result[0]['county'];
    $data['content_view'] = 'rtk/allocation_committee/scmlt_allocation';        
    $data['final_dets'] = $final_dets;
    $this->load->view('rtk/template', $data); 
}

public function scmlt_allocation_details_json($county ){    
    ini_set('max_execution_time',-1);

    $county = (int)$this->session->userdata("county_id");
    $district = (int) $this->session->userdata("district_id");

    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND districts.id = '$district'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";
        // echo "$sql";die();
    $result = $this->db->query($sql)->result_array();

    // echo "<pre>";print_r($result);exit;
    $final_dets = array();

    foreach ($result as $key => $id_details) { 

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];

        $sql2 = "SELECT 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc,
        lab_commodities
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id = lab_commodities.id
        AND lab_commodities.category = 1
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";
        // echo "$sql2";die();
        $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";
        // echo "$sql3";die();
        $result3 = $this->db->query($sql3)->result_array();

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }

    // echo "<pre>";print_r($final_dets);exit;
    if(count($final_dets)>0){
        foreach ($final_dets as $value) {
        //$zone = str_replace(' ', '-',$value['zone']);
            $facil = $value['code'];

            $ending_bal_s =ceil(($value['end_bal'][0]['closing_stock'])/50); 
            $ending_bal_c =ceil(($value['end_bal'][1]['closing_stock'])/30); 
            $ending_bal_t =ceil(($value['end_bal'][2]['closing_stock'])/20);

            $amc_s = str_replace(',', '',$value['amcs'][0]['amc']);
            $amc_c = str_replace(',', '',$value['amcs'][1]['amc']);
            $amc_t = str_replace(',', '',$value['amcs'][2]['amc']);

            if($amc_s==''){
                $amc_s = 0;
            }

            if($amc_c==''){
                $amc_c = 0;
            }

            if($amc_t==''){
                $amc_t = 0;
            }

            $mmos_s = ceil(($amc_s * 4)/50);
            $mmos_c = ceil(($amc_c * 4)/30);
            $mmos_t = ceil(($amc_t * 4)/20);

            if($mmos_s < $ending_bal_s){
                $qty_to_alloc_s = 0;
            }else{
                $qty_to_alloc_s = $mmos_s - $ending_bal_s;
            }

            if($mmos_c < $ending_bal_c){
                $qty_to_alloc_c = 0;
            }else{
                $qty_to_alloc_c = $mmos_c - $ending_bal_c;
            }

            if($mmos_t < $ending_bal_t){
                $qty_to_alloc_t = 0;
            }else{
                $qty_to_alloc_t = $mmos_t - $ending_bal_t;
            }

            $county = $value['county'];
            $district = $value['district'];            
            $facility_name = $value['name'];   

            $sql_dets = 'INSERT INTO `allocation_details`
            (`county`, `district`, `facility_code`, `facility_name`,`zone`,
            `end_bal_s3`, `end_bal_s6`, `amc_s3`, `amc_s6`, `mmos_s3`, `mmos_s6`, `allocate_s3`,`allocate_s6`, 
            `end_bal_c3`, `end_bal_c6`, `amc_c3`, `amc_c6`, `mmos_c3`, `mmos_c6`, `allocate_c3`, `allocate_c6`,
            `end_bal_t3`, `end_bal_t6`, `amc_t3`, `amc_t6`, `mmos_t3`, `mmos_t6`, `allocate_t3`, `allocate_t6`)
            VALUES 
            ("$county","$district","$facil","$facility_name","$zone",
            "$ending_bal_s",0,"$amc_s",0,"$mmos_s",0,"$qty_to_alloc_s",0,
            "$ending_bal_c",0,"$amc_c",0,"$mmos_c",0,"$qty_to_alloc_c",0,
            "$ending_bal_t",0,"$amc_t",0,"$mmos_t",0,"$qty_to_alloc_t",0)';
        // echo "$sql_dets";die();
            $this->db->query($sql_dets);
        }


        $data['title'] = "Zone A";
        $data['banner_text'] = "Facilities in Zone A";
        $data['content_view'] = 'rtk/allocation_committee/zone_a';        
        $data['final_dets'] = $final_dets;
        $this->load->view('rtk/template', $data); 
    }
} 

public function allocation_csv_interface($success=NULL)
{
// error_reporting(E_ALL);
// echo $success;exit;
    $success = (isset($success) && $success>0)? $success:NULL;

    $county = (int) $this->session->userdata("county_id");
    $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
    $month_data = array();

    $month_4 = date('F Y', strtotime('-4 month'));
    $month_3 = date('F Y', strtotime('-3 month'));
    $month_2 = date('F Y', strtotime('-2 month'));
    $month_1 = date('F Y', strtotime('-1 month'));
    $month_0 = date('F Y', strtotime('-0 month'));

    $allowed_months = array($month_4,$month_3,$month_2,$month_1,$month_0);

// echo "<pre>";print_r($months);exit;
    $data['title'] = "Allocation CSV";
    $data['banner_text'] = '';
    $data['success'] = $success;
    $data['months'] = $allowed_months;
    $data['content_view'] = 'rtk/allocation_committee/allocation_csv';        
// $data['final_dets'] = $final_dets;
    $this->load->view('rtk/template', $data); 
}

public function allocation_csv($value='')
{
// error_reporting(1);

    $data = $this->input->post();
    $selected_month = $data['month'];
    // echo "<pre>";print_r($data);exit;
    $user_id = $this->session->userdata('user_id');


    $month = date('F',strtotime($selected_month));
    $year = date('Y',strtotime($selected_month));
// echo "<pre>";print_r($year);exit;

    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $ext = pathinfo($_FILES["file"]['name'], PATHINFO_EXTENSION);
// echo "THIS ".$ext;exit;
//echo $_FILES["file"]["tmp_name"];exit;
        if ($ext == 'xls') {
            $excel2 = PHPExcel_IOFactory::createReader('Excel5');
        } else if ($ext == 'xlsx') {
            $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        } else if ($ext == 'csv') {
            $excel2 = PHPExcel_IOFactory::createReader('CSV');
        } else {
            die('Invalid file format given' . $_FILES['file']);
        }

        $excel2 = $objPHPExcel = $excel2 -> load($_FILES["file"]["tmp_name"]);
// Empty Sheet

        $sheet = $objPHPExcel -> getSheet(0);
        $highestRow = $sheet -> getHighestRow();

        $highestColumn = $sheet -> getHighestColumn();

// echo $highestColumn.' '.$highestRow;exit;
        $rowData_temp = array();

        for ($row = 2; $row <= $highestRow; $row++) {
//  Read a row of data into an array
            $rowData_temp = $objPHPExcel -> getActiveSheet() -> rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData_final[] = array_pop($rowData_temp);
        }

    // echo "<pre>";print_r($rowData_final);exit;

        $rowData_final_count = count($rowData_final);
        $blank_cells = 0;

        $screening = $confirmatory = $allocation_data_array = array();
        foreach ($rowData_final as $row_data => $data) {
            // echo "<pre>";print_r($data);
            $facility_code = $data[2];


            if ($facility_code>0 && $facility_code!='') {
                $q = "select district from facilities where facility_code = $facility_code";
                $district_id = $this->db->query($q)->result_array();

                // echo "<pre> ";print_r($district_id);exit;

                if($district_id !='' && $district_id>0 && !empty($district_id)):
                $district_id = $district_id[0]['district'];
                
                $c = "select county from districts where id = $district_id";
                $county_id = $this->db->query($c)->result_array();
                $county_id = $county_id[0]['county'];
                
                // echo "<pre> ";print_r($county_id);exit;

                $screening['district_id'] = $district_id;
                $screening['facility_code'] = $facility_code;
                $screening['screening_beg_bal'] = $data[4];
                $screening['screening_days_out_of_stock'] = $data[5];
                $screening['screening_end_month_phyc_count'] = $data[6];
                $screening['screening_losses'] = $data[7];
                $screening['screening_neg_adj'] = $data[8];
                $screening['screening_no_of_tests'] = $data[9];
                $screening['screening_pos_adj'] = $data[10];
                $screening['screening_qtt_received_other'] = $data[11];
                $screening['screening_qtt_received'] = $data[12];
                $screening['screening_qtt_requested'] = $data[13];
                $screening['screening_qtt_used'] = $data[14];
                $screening['screening_qtt_expiring_6_months'] = $data[15];

                $screening_data[] = $screening;

                $confirmatory['district_id'] = $district_id;
                $confirmatory['facility_code'] = $facility_code;
                $confirmatory['confirmatory_beg_bal'] = $data[16];
                $confirmatory['confirmatory_days_out_of_stock'] = $data[17];
                $confirmatory['confirmatory_end_month_phyc_count'] = $data[18];
                $confirmatory['confirmatory_neg_adj'] = $data[19];
                $confirmatory['confirmatory_losses'] = $data[20];
                $confirmatory['confirmatory_no_of_tests'] = $data[21];
                $confirmatory['confirmatory_pos_adj'] = $data[22];
                $confirmatory['confirmatory_qtt_received_other'] = $data[23];
                $confirmatory['confirmatory_qtt_received'] = $data[24];
                $confirmatory['confirmatory_qtt_requested'] = $data[25];
                $confirmatory['confirmatory_qtt_used'] = $data[26];
                $confirmatory['confirmatory_qtt_expiring_6_months'] = $data[27];

                $confirmatory_data[] = $confirmatory;


            //Assumed that the end month physical stock count is the ending balance of the commodity
            $ending_bal_s =ceil(($data[6])/50); 
            $ending_bal_c =ceil(($data[18])/30); 
            // $ending_bal_t =ceil(($value['end_bal'][2]['closing_stock'])/20);

            // $amc_s = str_replace(',', '',$value['amcs'][0]['amc']);
            // $amc_c = str_replace(',', '',$value['amcs'][1]['amc']);
            // $amc_t = str_replace(',', '',$value['amcs'][2]['amc']);

            $amc_s ='';
            $amc_c = '';
            $amc_t = '';


            if($amc_s==''){
                $amc_s = 0;
            }

            if($amc_c==''){
                $amc_c = 0;
            }

            if($amc_t==''){
                $amc_t = 0;
            }

            $mmos_s = ceil(($amc_s * 6)/50);
            $mmos_c = ceil(($amc_c * 6)/30);
            $mmos_t = ceil(($amc_t * 6)/20);

            if($mmos_s < $ending_bal_s){
                $qty_to_alloc_s = 0;
            }else{
                $qty_to_alloc_s = $mmos_s - $ending_bal_s;
            }

            if($mmos_c < $ending_bal_c){
                $qty_to_alloc_c = 0;
            }else{
                $qty_to_alloc_c = $mmos_c - $ending_bal_c;
            }

            if($mmos_t < $ending_bal_t){
                $qty_to_alloc_t = 0;
            }else{
                $qty_to_alloc_t = $mmos_t - $ending_bal_t;
            }

            $county = $county_id;
            $district = $district_id;            
            $facility_name = '';   
            // echo "<pre>";print_r($screening_data_array);exit;
            $allocation_details['county_id'] = $county_id;
            $allocation_details['district_id'] = $district_id;
            $allocation_details['facility_code'] = $facility_code;
            $allocation_details['ending_bal_s'] = $ending_bal_s;
            $allocation_details['amc_s'] = $amc_s;
            $allocation_details['mmos_s'] = $mmos_s;
            $allocation_details['allocate_s'] = $qty_to_alloc_s;

            $allocation_details['ending_bal_c'] = $ending_bal_c;
            $allocation_details['amc_c'] = $amc_c;
            $allocation_details['mmos_c'] = $mmos_c;
            $allocation_details['allocate_c'] = $qty_to_alloc_c;
            $allocation_details['month'] = date('Y-m-d');
            $allocation_details['user_id'] = $user_id;

            array_push($allocation_data_array, $allocation_details);
            endif;

            }else{
                $blank_cells = $blank_cells + 1;
            }
        }

        $screening_count = count($screening_data);
        $confirmatory_count = count($confirmatory_data);

        // echo "<pre>";print_r($allocation_data_array);exit;
        $screening_data_array = array();
        $confirmatory_data_array = array();
        $explanation = "Upload via excel";
        // echo "<pre>";print_r($user_id);exit;

        foreach ($screening_data as $data => $value) {
            //4 Screening
            //5 Confirmatory
            $facility_code = $value['facility_code'];
            $q = "select district from facilities where facility_code = $facility_code";
            $district_id = $this->db->query($q)->result_array();

            $new_order_id = $this->labs_order_check($facility_code,$month,$year);
            // echo "<pre> Screening: ";print_r($new_order_id);

            // echo "<pre>";print_r($value);exit;
            $screening_insert_data = array(
                'order_id' => $new_order_id,
                'facility_code' => $facility_code,
                'commodity_id' => 4,
                'unit_of_issue' => 1,
                'beginning_bal' => $value['screening_beg_bal'],
                'physical_beginning_bal' => 0,
                'q_received' => $value['screening_qtt_received'],
                'q_recieved_others' => $value['screening_qtt_received_other'],
                'q_used' => $value['screening_qtt_used'],
                'newqused' => 0,
                'no_of_tests_done' => $value['screening_no_of_tests'],
                'losses' => $value['screening_losses'],
                'positive_adj' => $value['screening_pos_adj'],
                'negative_adj' => $value['screening_neg_adj'],
                'physical_closing_stock' => $value['screening_end_month_phyc_count'],
                'closing_stock' => $value['screening_end_month_phyc_count'],
                'newclosingstock' => 0,

                'q_expiring' => $value['screening_qtt_expiring_6_months'],
                'days_out_of_stock' => $value['screening_days_out_of_stock'],
                'q_requested' => $value['screening_qtt_requested'],
                'amc' => 0,
                'allocated' => 0,
                'allocated_date' => 0,
                );
            array_push($screening_data_array, $screening_insert_data);
        }

        foreach ($confirmatory_data as $data => $value) {
            //4 Screening
            //5 Confirmatory
            $facility_code = $value['facility_code'];

            $new_order_id = $this->labs_order_check($facility_code,$month,$year);
            // echo "<pre> Confirmatory: ";print_r($new_order_id);exit;

            // echo "<pre>";print_r($new_order_id);exit;
            $confirmatory_insert_data = array(
                'order_id' => $new_order_id,
                'facility_code' => $facility_code,
                'commodity_id' => 5,
                'unit_of_issue' => 1,
                'beginning_bal' => $value['screening_beg_bal'],
                'physical_beginning_bal' => 0,
                'q_received' => $value['screening_qtt_received'],
                'q_recieved_others' => $value['screening_qtt_received_other'],
                'q_used' => $value['screening_qtt_used'],
                'newqused' => 0,
                'no_of_tests_done' => $value['screening_no_of_tests'],
                'losses' => $value['screening_losses'],
                'positive_adj' => $value['screening_pos_adj'],
                'negative_adj' => $value['screening_neg_adj'],
                'physical_closing_stock' => $value['screening_end_month_phyc_count'],
                'closing_stock' => $value['screening_end_month_phyc_count'],
                'newclosingstock' => 0,
                'q_expiring' => $value['screening_qtt_expiring_6_months'],
                'days_out_of_stock' => $value['screening_days_out_of_stock'],
                'q_requested' => $value['screening_qtt_requested'],
                'amc' => 0,
                'allocated' => 0,
                'allocated_date' => 0,
                );
            array_push($confirmatory_data_array, $screening_insert_data);
        }

        //INSERT FOR SCREENING DATA
        $result_scr = $this -> db -> insert_batch('lab_commodity_details', $screening_data_array);

        //INSERT FOR CONFIRMATORY DATA
        $result_conf = $this -> db -> insert_batch('lab_commodity_details', $confirmatory_data_array);

        //INSERT FOR ALLOCATION DETAILS
        $result_alloc = $this -> db -> insert_batch('allocation_details', $allocation_data_array);

    // echo "<pre>";print_r($result_alloc);echo"</pre>"; exit;

}        //end of file input if
else{
    echo "NO FILE";
}

redirect('rtk_management/allocation_csv_interface/1');
}

public function get_facility_amc($facility_code)
{
    $amc_s ='';
    $amc_c = '';
    $amc_t = '';

}

public function labs_order_check($facility_code=NULL,$month=NULL,$year=NULL)
{
        // echo $facility_code.' '.$month.' '.$year;exit;

    $q = "SELECT * FROM lab_commodity_orders WHERE facility_code='$facility_code' AND report_for='$month' AND created_at > NOW() - INTERVAL 1 MONTH";
    $check = $this->db->query($q)->result_array();
    $count_check = count($check);
        // echo "<pre>";print_r($check);exit;

    if($count_check == 0):
        // echo "LAUNCHED";exit;
        $start_date = date('Y-m-d', strtotime("first day of previous month"));
    $start_date_given = date('Y-m-d', strtotime('first day of this month',$month));
    $month_no = date('m',strtotime($month));
    $last_day_month = date('t',strtotime($month));

    $start_month_day = $year.'-'.$month_no.'-01';
    $end_month_day = $year.'-'.$month_no.'-'.$last_day_month;

        // echo $end_month;exit;
    $end_date = date('Y-m-d', strtotime("last day of previous month"));
    $end_date_given = date('Y-m-d', strtotime($month));
    $order_date = date('Y-m-d');

    $q = "select district from facilities where facility_code = $facility_code";
    $district = $this->db->query($q)->result_array();
    $district_id = $district[0]['district'];

        // echo "<pre>"; print_r($district_id);exit;

    $explanation = "Upload via excel";

    $current_month = substr($end_date, 5,2);
        // echo $current_month;exit;
    $last_month = $current_month - 1;
        // echo $last_month;exit;

    $three_months_ago = $current_month - 2;
    $year = substr($end_date, 0,4);

    $firstdate = $year.'-'.$current_month.'-01';
    $lastdate = $year.'-'.$current_month.'-31';

    $user_id = $this->session->userdata('user_id');

        // echo $lastdate;exit;

    $order_data = array(
        'facility_code' => $facility_code, 
        'district_id' => $district_id,
        'compiled_by' => $user_id, 
        'order_date' => $order_date, 
        'vct' => 0, 
        'pitc' => 0, 
        'pmtct' => 0, 
        'b_screening' => 0, 
        'other' => 0, 
        'specification' => 0, 
        'rdt_under_tests' => 0, 
        'rdt_under_pos' => 0, 
        'rdt_btwn_tests' => 0, 
        'rdt_btwn_pos' => 0, 
        'rdt_over_tests' => 0, 
        'rdt_over_pos' => 0, 
        'micro_under_tests' => 0, 
        'micro_under_pos' => 0, 
        'micro_btwn_tests' => 0, 
        'micro_btwn_pos' => 0, 
        'micro_over_tests' => 0, 
        'micro_over_pos' => 0, 
        'beg_date' => $start_month_day, 
        'end_date' => $end_month_day, 
        'explanation' => $explanation, 
        'moh_642' => 0, 
        'moh_643' => 0, 
        'report_for' => $month, 
        'user_id' =>$user_id);


    $u = new Lab_Commodity_Orders();
    $u->fromArray($order_data);
    $u->save();
    $order_id = $object_id = $u->get('id');
    else:
        $lastId = Lab_Commodity_Orders::get_new_order($facility_code);
    $new_order_id = $lastId->maxId;

    $order_id = $new_order_id;
    endif;

        // echo "<pre>";print_r($object_id);exit;
    return $order_id;
}

public function cmlt_allocation_details_json($county ){    
    ini_set('max_execution_time',-1);

    $county = (int)$this->session->userdata("county_id");

    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND counties.id = '$county'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";
        // echo "$sql";die();
    $result = $this->db->query($sql)->result_array();

    $final_dets = array();

    foreach ($result as $key => $id_details) { 

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];

        $sql2 = "SELECT 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc,
        lab_commodities
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id = lab_commodities.id
        AND lab_commodities.category = 1
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";
        // echo "$sql2";die();
        $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";
        // echo "$sql3";die();
        $result3 = $this->db->query($sql3)->result_array();

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }
    if(count($final_dets)>0){
        foreach ($final_dets as $value) {
        //$zone = str_replace(' ', '-',$value['zone']);
            $facil = $value['code'];

            $ending_bal_s =ceil(($value['end_bal'][0]['closing_stock'])/50); 
            $ending_bal_c =ceil(($value['end_bal'][1]['closing_stock'])/30); 
            $ending_bal_t =ceil(($value['end_bal'][2]['closing_stock'])/20);

            $amc_s = str_replace(',', '',$value['amcs'][0]['amc']);
            $amc_c = str_replace(',', '',$value['amcs'][1]['amc']);
            $amc_t = str_replace(',', '',$value['amcs'][2]['amc']);

            if($amc_s==''){
                $amc_s = 0;
            }

            if($amc_c==''){
                $amc_c = 0;
            }

            if($amc_t==''){
                $amc_t = 0;
            }

            $mmos_s = ceil(($amc_s * 4)/50);
            $mmos_c = ceil(($amc_c * 4)/30);
            $mmos_t = ceil(($amc_t * 4)/20);

            if($mmos_s < $ending_bal_s){
                $qty_to_alloc_s = 0;
            }else{
                $qty_to_alloc_s = $mmos_s - $ending_bal_s;
            }

            if($mmos_c < $ending_bal_c){
                $qty_to_alloc_c = 0;
            }else{
                $qty_to_alloc_c = $mmos_c - $ending_bal_c;
            }

            if($mmos_t < $ending_bal_t){
                $qty_to_alloc_t = 0;
            }else{
                $qty_to_alloc_t = $mmos_t - $ending_bal_t;
            }

            $county = $value['county'];
            $district = $value['district'];            
            $facility_name = $value['name'];   

            $sql_dets = 'INSERT INTO `allocation_details`
            (`county`, `district`, `mfl`, `facility_name`,`zone`,
            `end_bal_s3`, `end_bal_s6`, `amc_s3`, `amc_s6`, `mmos_s3`, `mmos_s6`, `allocate_s3`,`allocate_s6`, 
            `end_bal_c3`, `end_bal_c6`, `amc_c3`, `amc_c6`, `mmos_c3`, `mmos_c6`, `allocate_c3`, `allocate_c6`,
            `end_bal_t3`, `end_bal_t6`, `amc_t3`, `amc_t6`, `mmos_t3`, `mmos_t6`, `allocate_t3`, `allocate_t6`)
            VALUES 
            ("$county","$district","$facil","$facility_name","$zone",
            "$ending_bal_s",0,"$amc_s",0,"$mmos_s",0,"$qty_to_alloc_s",0,
            "$ending_bal_c",0,"$amc_c",0,"$mmos_c",0,"$qty_to_alloc_c",0,
            "$ending_bal_t",0,"$amc_t",0,"$mmos_t",0,"$qty_to_alloc_t",0)';
        // echo "$sql_dets";die();
            $this->db->query($sql_dets);
        }


        $data['title'] = "Zone A";
        $data['banner_text'] = "Facilities in Zone A";
        $data['content_view'] = 'rtk/allocation_committee/zone_a';        
        $data['final_dets'] = $final_dets;
        $this->load->view('rtk/template', $data); 
    }
} 

public function cmlt_allocation_details_amcs($county){    
    ini_set('max_execution_time',-1);

    $county = (int)$this->session->userdata("county_id");
    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities
    inner JOIN   districts
    ON    facilities.district = districts.id
    inner JOIN counties
    ON  districts.county = counties.id
    WHERE
    facilities.rtk_enabled = 1
    AND counties.id = '$county'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";
        // echo "$sql";die();
    $result = $this->db->query($sql)->result_array();
        // print_r($result); die;

    $final_dets = array();
    $result2 = array();
        // $my_new_result = array();
    $count = 0;
    foreach ($result as $key => $id_details) {

        // $my_new_result = array();

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];

        //         // echo "$facilityname";die();
        // $sqlm1 = "SELECT distinct 
        //             facility_amc.amc as amc
        //         FROM
        //             facilities,
        //             facility_amc
        //         WHERE
        //             facilities.facility_code = facility_amc.facility_code
        //                 AND facilities.facility_code = '$fcode'
        //                 AND facility_amc.commodity_id = '4'";
        // $result_m1amcs = $this->db->query($sqlm1)->result_array();
        // $sqlm2 = "SELECT distinct 
        //         facility_amc.amc as amc
        //     FROM
        //         facilities,
        //         facility_amc
        //     WHERE
        //         facilities.facility_code = facility_amc.facility_code
        //             AND facilities.facility_code = '$fcode'
        //             AND facility_amc.commodity_id = '5'";
        // $result_m2amcs = $this->db->query($sqlm2)->result_array();
        // $sqlm3= "SELECT distinct 
        //         facility_amc.amc as amc
        //     FROM
        //         facilities,
        //         facility_amc
        //     WHERE
        //         facilities.facility_code = facility_amc.facility_code
        //             AND facilities.facility_code = '$fcode'
        //             AND facility_amc.commodity_id = '6'";
        // $result_m3amcs = $this->db->query($sqlm3)->result_array();
        // $result2[$count] = array($result_m1amcs[0]['amc'],$result_m2amcs[0]['amc'],$result_m3amcs[0]['amc']);
        // die();


        $sql2 = "SELECT distinct 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";
        $result2 = $this->db->query($sql2)->result_array();

        // echo "<pre>";
        // print_r($result2);die();
        $sql3 = "SELECT distinct
        closing_stock
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";
        // echo "$sql3";die();
        $result3 = $this->db->query($sql3)->result_array();


        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
        $final_dets[$fcode]['amcs'] = $result2;

        $count++;
    }


        // $final_dets[$fcode]['amcs'] = $result2;
        // echo "<pre>";
        // print_r($final_dets);die();
    $data['title'] = "Zone A";
    $data['my_amcs'] = $result2;
    $data['banner_text'] = "Facilities in Zone A";
    $data['content_view'] = 'rtk/allocation_committee/zone_a';        
    $data['final_dets'] = $final_dets;
    $this->load->view('rtk/template', $data); 
}  


public function allocation_details($zone, $a,$b ){         // to browser view
    ini_set('max_execution_time',-1);
    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county

    FROM
    facilities,
    districts,
    counties
    WHERE
    facilities.district = districts.id
    AND districts.county = counties.id
    AND facilities.rtk_enabled = 1
    and counties.zone = '$zone'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC limit $a, $b";

    $result = $this->db->query($sql)->result_array();

    $final_dets = array();

    foreach ($result as $key => $id_details) {

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];



        $sql2 = "SELECT distinct
        facility_amc_b.amc as amc,
        facility_amc_b.amc_6 as amc_6,
        facility_amc_b.commodity_id
        FROM
        facilities,
        facility_amc_b
        WHERE
        facilities.facility_code = facility_amc_b.facility_code
        AND facilities.facility_code = '$fcode'           
        ORDER BY facility_amc_b.commodity_id ASC";
        ;
        $result2 = $this->db->query($sql2)->result_array();
        // $sql2 = "SELECT distinct
        //             facility_amc_b.amc as amc,
        //             facility_amc_b.commodity_id
        //         FROM
        //             facilities,
        //             facility_amc_b
        //         WHERE
        //             facilities.facility_code = facility_amc_b.facility_code
        //                 AND facilities.facility_code = '$fcode'     
        //         ORDER BY facility_amc_b.commodity_id ASC";
        //         ;
        // $result2 = $this->db->query($sql2)->result_array();
        // $sql2 = "SELECT distinct
        //             facility_amc.amc as amc,
        //             facility_amc.commodity_id
        //         FROM
        //             facilities,
        //             facility_amc
        //         WHERE
        //             facilities.facility_code = facility_amc.facility_code
        //                 AND facilities.facility_code = '$fcode'     
        //         ORDER BY facility_amc.commodity_id ASC";
        //         ;
        // $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock,
        days_out_of_stock,
        q_requested,
        created_at

        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result3 = $this->db->query($sql3)->result_array();

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }

        // echo "$sql3";die;
        // echo "<pre>"; print_r($final_dets);die;

    $data['title'] = "Zone $zone";
    $data['banner_text'] = "Facilities in Zone $zone";
    $data['content_view'] = 'rtk/allocation_committee/month_of_stock_counties';        
    $data['final_dets'] = $final_dets;
    $this->load->view('rtk/template', $data); 
} 

public function allocation_details_json($zone, $a,$b ){          //to database  
    ini_set('max_execution_time',-1);
    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities,
    districts,
    counties
    WHERE
    facilities.district = districts.id
    AND districts.county = counties.id
    AND facilities.rtk_enabled = 1
    and facilities.zone='Zone $zone'
    and counties.id =14
    ORDER BY counties.county asc,facilities.facility_code ASC limit $a, $b";
        // echo "$sql";die();
    $result = $this->db->query($sql)->result_array();

    $final_dets = array();

    foreach ($result as $key => $id_details) { 

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];

        $sql2 = "SELECT 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc,
        lab_commodities
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id = lab_commodities.id
        AND lab_commodities.category = 1
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";
        // echo "$sql2";die();
        $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";
        // echo "$sql3";die();
        $result3 = $this->db->query($sql3)->result_array();

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }
    if(count($final_dets)>0){
        foreach ($final_dets as $value) {
        //$zone = str_replace(' ', '-',$value['zone']);
            $facil = $value['code'];

            $ending_bal_s =ceil(($value['end_bal'][0]['closing_stock'])/50); 
            $ending_bal_c =ceil(($value['end_bal'][1]['closing_stock'])/30); 
            $ending_bal_t =ceil(($value['end_bal'][2]['closing_stock'])/20);

            $amc_s = str_replace(',', '',$value['amcs'][0]['amc']);
            $amc_c = str_replace(',', '',$value['amcs'][1]['amc']);
            $amc_t = str_replace(',', '',$value['amcs'][2]['amc']);

            if($amc_s==''){
                $amc_s = 0;
            }

            if($amc_c==''){
                $amc_c = 0;
            }

            if($amc_t==''){
                $amc_t = 0;
            }

            $mmos_s = ceil(($amc_s * 4)/50);
            $mmos_c = ceil(($amc_c * 4)/30);
            $mmos_t = ceil(($amc_t * 4)/20);

        //screening

            if($mmos_s < $ending_bal_s){
                $qty_to_alloc_s = 0;
            }else{
                $qty_to_alloc_s = $mmos_s - $ending_bal_s;
            }
        // confirmatory
            if($mmos_c < $ending_bal_c){
                $qty_to_alloc_c = 0;
            }else{
                $qty_to_alloc_c = $mmos_c - $ending_bal_c;
            }
        //tiebreaker
            if($mmos_t < $ending_bal_t){
                $qty_to_alloc_t = 0;
            }else{
                $qty_to_alloc_t = $mmos_t - $ending_bal_t;
            }

            $county = $value['county'];
            $district = $value['district'];            
            $facility_name = $value['name'];   

            $sql_dets = "INSERT INTO `allocation_details`
            (`county`, `district`, `mfl`, `facility_name`,`zone`,
            `end_bal_s3`, `end_bal_s6`, `amc_s3`, `amc_s6`, `mmos_s3`, `mmos_s6`, `allocate_s3`,`allocate_s6`, 
            `end_bal_c3`, `end_bal_c6`, `amc_c3`, `amc_c6`, `mmos_c3`, `mmos_c6`, `allocate_c3`, `allocate_c6`,
            `end_bal_t3`, `end_bal_t6`, `amc_t3`, `amc_t6`, `mmos_t3`, `mmos_t6`, `allocate_t3`, `allocate_t6`)
            VALUES 
            ('$county','$district','$facil','$facility_name','$zone',
            '$ending_bal_s',0,'$amc_s',0,'$mmos_s',0,'$qty_to_alloc_s',0,
            '$ending_bal_c',0,'$amc_c',0,'$mmos_c',0,'$qty_to_alloc_c',0,
            '$ending_bal_t',0,'$amc_t',0,'$mmos_t',0,'$qty_to_alloc_t',0)";
        // echo "$sql_dets";die();
            $this->db->query($sql_dets);
        }


        // $data['title'] = "Zone A";
        // $data['banner_text'] = "Facilities in Zone A";
        // $data['content_view'] = 'rtk/allocation_committee/zone_a';        
        // $data['final_dets'] = $final_dets;
        // $this->load->view('rtk/template', $data); 
    }
} 
public function allocation_details_amcs($zone, $a,$b ){    
    ini_set('max_execution_time',-1);
    $sql = "SELECT distinct 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities,
    districts,
    counties
    WHERE
    facilities.district = districts.id
    AND districts.county = counties.id
    AND facilities.rtk_enabled = 1
    and facilities.zone='Zone $zone'
    ORDER BY counties.county asc, districts.district asc,facilities.facility_code ASC limit $a, $b";
        // echo "$sql";die();
    $result = $this->db->query($sql)->result_array();
        // print_r($result); die;

    $final_dets = array();
    $result2 = array();
        // $my_new_result = array();
    $count = 0;
    foreach ($result as $key => $id_details) {

        // $my_new_result = array();

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];

        //         // echo "$facilityname";die();
        // $sqlm1 = "SELECT distinct 
        //             facility_amc.amc as amc
        //         FROM
        //             facilities,
        //             facility_amc
        //         WHERE
        //             facilities.facility_code = facility_amc.facility_code
        //                 AND facilities.facility_code = '$fcode'
        //                 AND facility_amc.commodity_id = '4'";
        // $result_m1amcs = $this->db->query($sqlm1)->result_array();
        // $sqlm2 = "SELECT distinct 
        //         facility_amc.amc as amc
        //     FROM
        //         facilities,
        //         facility_amc
        //     WHERE
        //         facilities.facility_code = facility_amc.facility_code
        //             AND facilities.facility_code = '$fcode'
        //             AND facility_amc.commodity_id = '5'";
        // $result_m2amcs = $this->db->query($sqlm2)->result_array();
        // $sqlm3= "SELECT distinct 
        //         facility_amc.amc as amc
        //     FROM
        //         facilities,
        //         facility_amc
        //     WHERE
        //         facilities.facility_code = facility_amc.facility_code
        //             AND facilities.facility_code = '$fcode'
        //             AND facility_amc.commodity_id = '6'";
        // $result_m3amcs = $this->db->query($sqlm3)->result_array();
        // $result2[$count] = array($result_m1amcs[0]['amc'],$result_m2amcs[0]['amc'],$result_m3amcs[0]['amc']);
        // die();


        $sql2 = "SELECT distinct 
        facility_amc.amc as amc
        FROM
        facilities,
        facility_amc
        WHERE
        facilities.facility_code = facility_amc.facility_code
        AND facilities.facility_code = '$fcode'
        AND facility_amc.commodity_id between 4 and 6            
        ORDER BY facility_amc.commodity_id ASC";
        $result2 = $this->db->query($sql2)->result_array();

        // echo "<pre>";
        // print_r($result2);die();
        $sql3 = "SELECT distinct
        closing_stock
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";
        // echo "$sql3";die();
        $result3 = $this->db->query($sql3)->result_array();


        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
        $final_dets[$fcode]['amcs'] = $result2;

        $count++;
    }


        // $final_dets[$fcode]['amcs'] = $result2;
        // echo "<pre>";
        // print_r($final_dets);die();
    $data['title'] = "Zone A";
    $data['my_amcs'] = $result2;
    $data['banner_text'] = "Facilities in Zone A";
    $data['content_view'] = 'rtk/allocation_committee/zone_a';        
    $data['final_dets'] = $final_dets;
    $this->load->view('rtk/template', $data); 
} 

public function national_expiries(){
    ini_set('max_execution_time',-1);
    $previous_month = date('F-Y', strtotime('-1 month',time()));    
    $sql = "SELECT distinct counties.county,districts.district,lab_commodity_details.facility_code,facilities.facility_name
    FROM
    lab_commodity_details,
    facilities,
    districts,
    counties
    WHERE
    facilities.facility_code = lab_commodity_details.facility_code
    AND facilities.district = districts.id
    AND districts.county = counties.id
    AND created_at BETWEEN '2015-02-01' AND '2015-02-30'
    AND facilities.facility_code = lab_commodity_details.facility_code
    AND lab_commodity_details.q_expiring > 0                       
    ORDER BY counties.county ASC , districts.district ASC , facilities.facility_name ASC";           
    $facilities = $this->db->query($sql)->result_array();          
    $new_commodities = array();
    foreach ($facilities as $key => $value) {
        $fcode = $value['facility_code'];
        $sql2 = "SELECT lab_commodities.commodity_name,lab_commodity_details.commodity_id,lab_commodity_details.q_expiring
        FROM
        lab_commodity_details, lab_commodities
        WHERE
        lab_commodity_details.facility_code = '$fcode'                               
        AND created_at BETWEEN '2015-02-01' AND '2015-02-30'
        AND lab_commodity_details.q_expiring > 0
        and lab_commodity_details.commodity_id = lab_commodities.id
        HAVING lab_commodity_details.commodity_id BETWEEN 4 AND 6
        ORDER BY lab_commodity_details.commodity_id ASC";
        $commodities = $this->db->query($sql2)->result_array();
        $new_commodities[$fcode] = $commodities;


    }                  
    $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

    <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>        
    <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>National HIV Rapid Test Kit (RTK) Expiries for $previous_month</div><hr />    

    <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
        table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
        table.data-table td, table th {padding: 4px;}
        table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
        .col5{background:#D8D8D8;}</style>";
        $table_head = '
        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
            <thead border="0" style="margin: 10px auto;font-weight:900">
                <tr>
                    <th align="">County</th>
                    <th align="">Sub-County</th>
                    <th align="">MFL</th>
                    <th align="">Facility Name</th>     
                    <th align="">Commodity Name</th>           
                    <th align="">Quantity Expiring (Tests)</th>               
                </tr>
            </thead>
            <tbody>';      
                $table_body = '';
                if(count($facilities)>0){
                    foreach ($facilities as $key =>$value) {
                        $count = 0;        
                        $facility_code = $value['facility_code'];
                        $facility_name = $value['facility_name'];
                        $district = $value['district'];
                        $county = $value['county'];

                        $count = count($new_commodities[$facility_code])+1; 
                        $table_body .= '<tr>';
                        $table_body .= '<td rowspan="'.$count.'">' . $county . '</td>';                
                        $table_body .= '<td rowspan="'.$count.'">' . $district . '</td>';                
                        $table_body .= '<td rowspan="'.$count.'">' . $facility_code . '</td>';                
                        $table_body .= '<td rowspan="'.$count.'">' . $facility_name . '</td></tr>';


                        for ($i=0; $i<$count-1 ; $i++) { 

                            $commodity_name = $new_commodities[$facility_code][$i]['commodity_name'];
                            $q_expiring = $new_commodities[$facility_code][$i]['q_expiring'];
                            $table_body .= '<tr>';
                            $table_body .= '<td>'. $commodity_name . '</td>';                
                            $table_body .= '<td>'. $q_expiring . '</td></tr>';                
                        }                
                    }}
        // $email_address = 'jodek@usaid.gov,jbatuka@usaid.gov,omarabdi2@yahoo.com,njebungeibowen@gmail.com,colwande@yahoo.com,hoy4@cdc.gov,
        //             uys0@cdc.gov,japhgituku@yahoo.co.uk,onjathi@clintonhealthaccess.org,bedan.wamuti@kemsa.co.ke,bnmuture@gmail.com,ttunduny@gmail.com,annchemu@gmail.com';

                    $message = "Dear National Team,<br/></br/>Please find attached the County Percentages for $previous_month.<br/></br>Sent From the RTK System";  
                    $table_foot = '</tbody></table>';          
                    $html_data = $html_title . $table_head . $table_body . $table_foot;
        // echo "$html_data";die();
                    $email_address = 'ttunduny@gmail.com';
                    $reportname = 'RTK Expiries for '.$previous_month;
                    $reportname = 'National Expiries for '.$previous_month;
                    $this->sendmail($html_data,$message,$email_address);
        // $this->create_pdf($html_data,$reportname);
                }  
                function zone_allocation_stats($zone) {

                    $last_allocation_sql = "SELECT lab_commodity_details.allocated_date 
                    FROM facilities, lab_commodity_details
                    WHERE facilities.facility_code = lab_commodity_details.facility_code
                    AND lab_commodity_details.commodity_id
                    BETWEEN 1 
                    AND 3 
                    AND facilities.Zone =  'Zone $zone'
                    AND lab_commodity_details.allocated >0
                    ORDER BY  `lab_commodity_details`.`allocated_date` DESC 
                    LIMIT 0,1";

                    $last_allocation_res = $this->db->query($last_allocation_sql);
                    $last_allocation = $last_allocation_res->result_array();

                    $last_allocation_date = $last_allocation[0]['allocated_date'];

                    $three_months_ago = date("Y-m-", strtotime("-3 Month "));
                    $three_months_ago .='1';

                    $total_facilities_sql = "SELECT count(*) as total_facilities
                    FROM facilities, districts, counties
                    WHERE facilities.district = districts.id
                    AND districts.county = counties.id
                    AND facilities.zone = 'Zone $zone' 
                    AND facilities.rtk_enabled =1";

                    $res = $this->db->query($total_facilities_sql);
                    $total_facilities_res = $res->result_array();
                    $total_facilities = $total_facilities_res[0]['total_facilities'];

                    $sql1 = "SELECT count(DISTINCT facilities.facility_code) as facilities_allocated
                    FROM lab_commodity_orders, lab_commodity_details, facilities, counties, districts
                    WHERE lab_commodity_orders.id = lab_commodity_details.order_id
                    AND districts.county = counties.id
                    AND lab_commodity_details.allocated > 0
                    AND facilities.zone = 'Zone $zone'
                    AND districts.id = facilities.district
                    AND facilities.facility_code = lab_commodity_orders.facility_code
                    AND facilities.rtk_enabled = 1
                    AND lab_commodity_orders.order_date BETWEEN  '$three_months_ago'AND  NOW()";

                    $res = $this->db->query($sql1);
                    $facilities_allocated = $res->result_array();

                    $facilities_allocated = $facilities_allocated[0]['facilities_allocated'];
                    $allocation_percentage = $facilities_allocated / $total_facilities * 100;
                    $allocation_percentage = number_format($allocation_percentage, $decimals = 0);

                    $facilities_allocated;
                    $zone_stats = array(
                        'total_facilities' => $total_facilities,
                        'facilities_allocated' => $facilities_allocated,
                        'allocation_percentage' => $allocation_percentage,
                        'last_allocation' => $last_allocation_date
                        );
                    return $zone_stats;
                }

        // public function allocation_zone($zone = null) {
        //     if (!isset($zone)) {
        //         redirect('rtk_management/allocation_home');
        //     }
        //     $data['counties_in_zone'] = $this->_zone_counties($zone);
        //     $data['banner_text'] = 'National';
        //     $data['active_zone'] = "$zone";
        //     $data['content_view'] = 'rtk/rtk/allocation/allocation_zone_view';
        //     $data['title'] = 'National Summary: ';
        //     $this->load->view("rtk/template", $data);
        // }

                public function allocation_zone($zone = null) {
                    if (!isset($zone)) {
                        redirect('rtk_management/allocation_home');
                    }
                    $sql = "SELECT 
                    COUNT(facilities.facility_code) AS count, counties.zone
                    FROM
                    facilities, districts, counties
                    WHERE
                    facilities.rtk_enabled = 1
                    AND facilities.district = districts.id
                    AND districts.county = counties.id
                    GROUP BY counties.zone";
                    $data['facilities'] = $this->db->query($sql)->result_array();
        // echo "<pre>";
        // print_r($data['facilities']);die();

                    $data['banner_text'] = 'National Allocations: Zone: '.$zone;
                    $data['active_zone'] = "$zone";
                    $data['content_view'] = 'rtk/rtk/allocation/allocation_zone_view';
                    $data['title'] = 'National Summary: ';
                    $this->load->view("rtk/template", $data);
                }
                function _zone_counties($zone) {
                    $returnable = array();
                    $sql = "select Distinct counties.county, counties.id
                    FROM  facilities,counties,districts
                    WHERE  facilities.Zone = 'Zone $zone'
                    AND facilities.district = districts.id
                    AND districts.county = counties.id
                    order by counties.county";

                    $res = $this->db->query($sql);
                    foreach ($res->result_array() as $value) {
                        $allocation_stats = $this->_county_allocation_stats($value['id']);

                        array_push($allocation_stats, $value['county']);
                        array_push($allocation_stats, $value['id']);
                        array_push($returnable, $allocation_stats);
                    }

                    return $returnable;
                }
                private function _county_allocation_stats($county) {
/*
* We'd like to know the county allocation status for the month
*/

        // Total Facilities in the county

$three_months_ago = date("Y-m-", strtotime("-3 Month "));
$three_months_ago .='1';
$sql = "SELECT count(*) as total_facilities
FROM facilities, districts, counties
WHERE facilities.district = districts.id
AND districts.county = counties.id
AND counties.id = $county
AND facilities.rtk_enabled =1";

$sql1 = "SELECT count(DISTINCT facilities.facility_code) as facilities_allocated,
lab_commodity_details.allocated_date as last_allocation
FROM lab_commodity_orders, lab_commodity_details, facilities, counties, districts
WHERE lab_commodity_orders.id = lab_commodity_details.order_id
AND districts.county = counties.id
AND counties.id = $county
AND lab_commodity_details.allocated > 0
AND districts.id = facilities.district
AND facilities.facility_code = lab_commodity_orders.facility_code
AND facilities.rtk_enabled = 1
AND lab_commodity_orders.order_date BETWEEN  '$three_months_ago'AND  NOW()
ORDER BY lab_commodity_details.allocated_date DESC";

$res = $this->db->query($sql1);
$facilities_allocated = $res->result_array();
$last_allocation = $facilities_allocated[0]['last_allocation'];
$facilities_allocated = $facilities_allocated[0]['facilities_allocated'];

$res1 = $this->db->query($sql);
$total_facilities = $res1->result_array();
$total_facilities = $total_facilities[0]['total_facilities'];
$allocation_percentage = $facilities_allocated / $total_facilities * 100;
$allocation_percentage = number_format($allocation_percentage, 0);

$returnable = array('facilities' => $total_facilities,
    'allocated_facilities' => $facilities_allocated,
    'allocation_percentage' => $allocation_percentage,
    'last_allocation' => $last_allocation);
return $returnable;
}
public function allocation_county_dashboard($zone){
        // if (!isset($zone)) {
        //         redirect('rtk_management/allocation_home');
        //     }
    $sql = "SELECT 
    COUNT(facilities.facility_code) AS count, counties.county, counties.id
    FROM
    facilities, districts, counties
    WHERE
    facilities.rtk_enabled = 1
    AND facilities.district = districts.id
    AND districts.county = counties.id
    and counties.zone = '$zone'
    GROUP BY counties.county";
    $data['facilities'] = $this->db->query($sql)->result_array();
        // echo "<pre>";
        // print_r($data['facilities']);die();

    $data['banner_text'] = 'National Allocations: Zone: '.$zone;
    $data['active_zone'] = "$zone";
    $data['content_view'] = 'rtk/rtk/allocation/allocation_county_view';
    $data['title'] = 'National Summary: ';
    $this->load->view("rtk/template", $data);
}
public function allocation_county_detail_zoom($county) {
        // $county = (int) $this->session->userdata("county_id");

    ini_set('max_execution_time',-1);
    $sql = "SELECT 
    facilities.facility_code,
    facilities.facility_name,
    districts.district,
    counties.county
    FROM
    facilities,
    districts,
    counties
    WHERE     
    facilities.rtk_enabled = 1
    AND facilities.district = districts.id
    AND districts.county = counties.id
    AND counties.id = '$county'
    ORDER BY counties.county asc, districts.district asc, facilities.facility_code ASC ";
        // echo "$sql";die;;
    $result = $this->db->query($sql)->result_array();
        // echo "<pre>"; print_r($result); die;

    $final_dets = array();

    foreach ($result as $key => $id_details) {

        $fcode = $id_details['facility_code'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facilityname = $id_details['facility_name'];



        // $sql2 = "SELECT 
        //             facility_amc.amc as amc
        //         FROM
        //             facilities,
        //             facility_amc
        //         WHERE
        //             facilities.facility_code = facility_amc.facility_code
        //                 AND facilities.facility_code = '$fcode'
        //                 AND facility_amc.commodity_id between 4 and 6            
        //         ORDER BY facility_amc.commodity_id ASC";

        // $result2 = $this->db->query($sql2)->result_array();

        $sql3 = "SELECT 
        closing_stock,
        days_out_of_stock,
        q_requested
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $fcode
        AND commodity_id between 4 and 6
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result3 = $this->db->query($sql3)->result_array();

        $final_dets[$fcode]['name'] = $facilityname;
        $final_dets[$fcode]['district'] = $district;
        $final_dets[$fcode]['county'] = $county;
        $final_dets[$fcode]['amcs'] = $result2;
        $final_dets[$fcode]['code'] = $fcode;
        $final_dets[$fcode]['end_bal'] = $result3;
    }

        // echo "$sql3";die;
        // echo "<pre>";
        // print_r($result3);die;

        // $data['title'] = "";
        // $data['banner_text'] = $result[0]['county'];
        // $data['content_view'] = 'rtk/allocation_committee/cmlt_allocation';        
    $data['final_dets'] = $final_dets;
        // $this->load->view('rtk/template', $data); 
        //                 // $ish;
        //                 // $county = counties::get_county_name($county_id);
        //                 // $county_name = Counties::get_county_name($county_id);
        //                 // $data['countyname'] =$county_name['county'];

        //         $htm = '';
        //         $table_body = '';

        //                 // $districts_in_county = districts::getDistrict($county_id);
        //                 // $data['districts_in_county'] = $districts_in_county;
        //                 // $htm .= '<ul class="facility-list">';
        //                 // foreach ($districts_in_county as $key => $district_arr)
        //                 //     $district = $district_arr['id'];
        //                 // $district_name = $district_arr['district'];
        //                 // $htm .= '<li>' . $district_name . '</li>';
        //                 // $htm .= '<ul class="sub-list">';


        //         $beg_date = date('Y-m', strtotime("-3 Month")).'-01';
        //         $beg_date_current = date('Y-m', strtotime("-1 Month")).'-01';        
        //         $end_date = date('Y-m-d', strtotime("last day of previous Month"));
        //         $end_date_current = date('Y-m-d', strtotime("last day of previous Month"));
        //                 //echo "begin $beg_date_current </br> end $end_date_current";die;

        //         $sql = "SELECT DISTINCT
        //             facilities.*,districts.district,counties.county
        //         FROM
        //             facilities,districts,counties,lab_commodity_orders
        //         WHERE
        //             rtk_enabled = 1
        //             and facilities.zone = 'Zone $zone'
        //             and districts.id = facilities.district 
        //             and counties.id  = districts.county
        //             and facilities.facility_code = lab_commodity_orders.facility_code
        //             and lab_commodity_orders.order_date between '$beg_date' and '$end_date' ORDER BY facilities.facility_code ASC" ;        
        //                        // echo($sql);die;
        //         $facilities = $this->db->query($sql)->result_array();
        //              //echo "<pre>";print_r($facilities);die;
        //        foreach ($facilities as $key => $value) {
        //                     $fcode = $value['facility_code'];
        //                     $q = "SELECT DISTINCT
        //     lab_commodities.*,
        //     facility_amc.*,
        //     lab_commodity_details.closing_stock,
        //     lab_commodity_details.order_id
        // FROM
        //     lab_commodities,
        //     facility_amc,
        //     lab_commodity_details
        // WHERE
        //     lab_commodities.id = facility_amc.commodity_id
        //         AND facility_amc.facility_code = '$fcode'
        //         AND lab_commodity_details.commodity_id = lab_commodities.id
        //         AND lab_commodity_details.facility_code = facility_amc.facility_code
        //         AND lab_commodity_details.commodity_id BETWEEN 0 AND 6
        //         AND lab_commodity_details.created_at BETWEEN '$beg_date_current' AND '$end_date_current'";


        //                     $amc_details = $this->db->query($q)->result_array();                                        

        //                     $order_id = $value['order_id'];
        //                     $county = $value['county'];
        //                     $district = $value['district'];
        //                     $facility_name = $value['facility_name'];
        //                     $facility_code = $value['facility_code'];
        //                     $contactperson = $value['contactperson'];
        //                     $phone_no = $value['cellphone'];

        //                     $amcs[$fcode] = $amc_details;


        //                      $ending_bal_s = $amcs[$fcode][2]['closing_stock'];
        //                      $ending_bal_c = $amcs[$fcode][3]['closing_stock'];
        //                      $ending_bal_t = $amcs[$fcode][4]['closing_stock'];

        //                      $amc_s = $amcs[$fcode][2]['amc'];
        //                         $amc_s = str_replace(',', '', $amc_s);
        //                         $comm_s = $amcs[$fcode][2]['commodity_id'];

        //                         $amc_t = $amcs[$fcode][4]['amc'];
        //                         $amc_t = str_replace(',', '', $amc_t);
        //                         $comm_t = $amcs[$fcode][4]['commodity_id'];

        //                         $amc_c = $amcs[$fcode][3]['amc'];
        //                         $amc_c = str_replace(',', '', $amc_c);
        //                         $comm_c = $amcs[$fcode][3]['commodity_id'];
        //                                 //$allocation = '<span class=\"label label-important\">Pending Allocation for  ' . $lastmonth . '</span>';
        //                         if($amc_s==null||$amc_s==''){
        //                             $amc_s =0;
        //                         }
        //                         if($amc_c==null||$amc_c==''){
        //                             $amc_c =0;
        //                         }
        //                         if($amc_t==null||$amc_t==''){
        //                             $amc_t =0;
        //                         }            


        //                         $mmos_s = ceil(($amc_s * 4)/50);
        //                         $mmos_c = ceil(($amc_c * 4)/30);
        //                         $mmos_t = ceil(($amc_t * 4)/20);

        //                         if($mmos_s < $ending_bal_s){
        //                           $qty_to_alloc_s = 0;
        //                         }else{
        //                           $qty_to_alloc_s = $mmos_s - $ending_bal_s;
        //                         }

        //                         if($mmos_c < $ending_bal_c){
        //                           $qty_to_alloc_c = 0;
        //                         }else{
        //                           $qty_to_alloc_c = $mmos_c - $ending_bal_c;
        //                         }

        //                         if($mmos_t < $ending_bal_t){
        //                           $qty_to_alloc_t = 0;
        //                         }else{
        //                           $qty_to_alloc_t = $mmos_t - $ending_bal_t;
        //                         }

        //                         $qty_of_issue_s = ceil($amc_s/$amcs[$fcode][2]['unit_of_issue']);
        //                         $qty_of_issue_c = ceil($amc_c/$amcs[$fcode][3]['unit_of_issue']);
        //                         $qty_of_issue_t = ceil($amc_t/$amcs[$fcode][4]['unit_of_issue']);

        //                         if($qty_of_issue_t==0){
        //                             $qty_of_issue_t +=1;
        //                         }
        //                         if($qty_of_issue_c==0){
        //                             $qty_of_issue_c +=1;
        //                         }
        //                         if($qty_of_issue_s==0){
        //                             $qty_of_issue_s +=1;
        //                         }


        //                      $table_body .= "
        //                         <tr id=''>            
        //                         <input type='hidden' name='order_id' value='$order_id' />
        //                         <input type='hidden' name='fcode' value='$fcode' />
        //                         <input type='hidden' name='screening_id' value='$comm_s' />
        //                         <input type='hidden' name='confirm_id' value='$comm_c' />
        //                         <input type='hidden' name='tiebreaker_id' value='$comm_t' />
        //                         <td>$county</td>
        //                         <td>$district</td>
        //                         <td>$facility_code</td>                        
        //                         <td>$facility_name</td>
        //                         <td>$contactperson</td>
        //                         <td>$phone_no</td>

        //                         <td>$ending_bal_s</td>
        //                         <td>$mmos_s</td>
        //                         <td>$amc_s</td>
        //                         <td><input type ='text' size = '5' name ='allocate_screening_khb' value ='$qty_of_issue_s'></td>

        //                         <td>$ending_bal_c</td>
        //                         <td>$mmos_c</td>
        //                         <td>$amc_c</td>
        //                         <td><input type ='text' size = '5' name ='allocate_confirm_first' value ='$qty_of_issue_c'></td>

        //                         <td>$ending_bal_t</td>
        //                         <td>$mmos_t</td>
        //                         <td>$amc_t</td>
        //                         <td><input type ='text' size = '5' name ='allocate_tie_breaker' value ='$qty_of_issue_t'></td>                                                
        //                         </tr>";




        //                 }

        //echo "$table_body";die();
        //    echo "<pre>"; print_r($amcs[$fcode]);die;
        // print_r($amcs[$fcode]);die();

        //      $facility_code = $orders_arr['facility_code'];
        //      $facility_name = $orders_arr['facility_name'];
        //      $district_name = $orders_arr['district'];

        //      $order_detail_id = $amcs[$fcode][0]['order_id'];

        //      $last_allocated = $amcs[$fcode][4]['allocated_date'];
        //      if($last_allocated == 0){
        //          $allocation_status = 'Pending Allocation';
        //      }else{

        //          $last_allocated = date('d-m-Y',$last_allocated);

        //          $beg_date_ts = strtotime($beg_date);
        //          $end_date_ts = strtotime($end_date);

        //          if(($last_allocated>$beg_date_ts)){
        //              $allocation_status = 'Pending Allocation';
        //          }else{
        //              $allocation_status = 'Allocated on '.$last_allocated;
        //          }
        //      }

        //      $commodity_s = $amcs[$fcode][2]['commodity_name'];
        //      $commodity_t = $amcs[$fcode][4]['commodity_name'];
        //      $commodity_c = $amcs[$fcode][3]['commodity_name'];

        //      $amc_s = $amcs[$fcode][2]['amc'];
        //      $amc_s = str_replace(',', '', $amc_s);
        //      $comm_s = $amcs[$fcode][2]['commodity_id'];

        //      $amc_t = $amcs[$fcode][4]['amc'];
        //      $amc_t = str_replace(',', '', $amc_t);
        //      $comm_t = $amcs[$fcode][4]['commodity_id'];

        //      $amc_c = $amcs[$fcode][3]['amc'];
        //      $amc_c = str_replace(',', '', $amc_c);
        //      $comm_c = $amcs[$fcode][3]['commodity_id'];
        //              //$allocation = '<span class=\"label label-important\">Pending Allocation for  ' . $lastmonth . '</span>';
        //      if($amc_s==null||$amc_s==''){
        //          $amc_s =0;
        //      }
        //      if($amc_c==null||$amc_c==''){
        //          $amc_c =0;
        //      }
        //      if($amc_t==null||$amc_t==''){
        //          $amc_t =0;
        //      }            


        //      $mmos_s = ceil(($amc_s * 4)/50);
        //      $mmos_c = ceil(($amc_c * 4)/30);
        //      $mmos_t = ceil(($amc_t * 4)/20);

        //      if($mmos_s < $ending_bal_s){
        //        $qty_to_alloc_s = 0;
        //      }else{
        //        $qty_to_alloc_s = $mmos_s - $ending_bal_s;
        //      }

        //      if($mmos_c < $ending_bal_c){
        //        $qty_to_alloc_c = 0;
        //      }else{
        //        $qty_to_alloc_c = $mmos_c - $ending_bal_c;
        //      }

        //      if($mmos_t < $ending_bal_t){
        //        $qty_to_alloc_t = 0;
        //      }else{
        //        $qty_to_alloc_t = $mmos_t - $ending_bal_t;
        //      }

        //      $qty_of_issue_s = ceil($amc_s/$amcs[$fcode][2]['unit_of_issue']);
        //      $qty_of_issue_c = ceil($amc_c/$amcs[$fcode][3]['unit_of_issue']);
        //      $qty_of_issue_t = ceil($amc_t/$amcs[$fcode][4]['unit_of_issue']);

        //      if($qty_of_issue_t==0){
        //          $qty_of_issue_t +=1;
        //      }
        //      if($qty_of_issue_c==0){
        //          $qty_of_issue_c +=1;
        //      }
        //      if($qty_of_issue_s==0){
        //          $qty_of_issue_s +=1;
        //      }

        //      $table_body .= "
        //      <tr id=''>            
        //      <input type='hidden' name='order_detail_id' value='$order_detail_id' />
        //      <input type='hidden' name='screening_id' value='$comm_s' />
        //      <input type='hidden' name='confirm_id' value='$comm_c' />
        //      <input type='hidden' name='tiebreaker_id' value='$comm_t' />
        //      <td>$district_name</td>
        //      <td>$facility_code</td>
        //      <td>$facility_name</td>
        //      <td>$amc_s </td>
        //      <td><input type ='text' size = '5' name ='allocate_screening_khb' value ='$qty_of_issue_s'></td>
        //      <td>$amc_c</td>
        //      <td><input type ='text' size = '5' name ='allocate_confirm_first' value ='$qty_of_issue_c'></td>
        //      <td>$amc_t</td>
        //      <td><input type ='text' size = '5' name ='allocate_tie_breaker' value ='$qty_of_issue_t'></td>
        //      <td>$allocation_status</td>
        //      <input type='hidden' name='fcode' value='$fcode' />
        //      </tr>";
        //  }
        //         // die();

        // $data['county_id'] = $county_id;
        // $data['table_body'] = $table_body;
    $data['title'] = "County Allocation View";
    $data['banner_text'] = 'Allocate ' . $result['county'].'County';
        //$data['content_view'] = "rtk/allocation_committee/ajax_view/rtk_county_allocation_datatableonly_v";
    $data['content_view'] = "rtk/rtk/allocation/rtk_county_allocation_datatableonly_v";
    $this->load->view("rtk/template", $data);
}

public function rtk_allocation_data() {
    if ($_POST['form_data'] == '') {
        echo 'No data was found';           
    }

    $data = $_POST['form_data'];       
        // echo "<pre>";
        // print_r($data);die();
    $now = time();

    $data = array_chunk($data,8);

    $beg_date = date('Y-m', strtotime("-3 Month"));
    $beg_date.='-01';
    $end_date = date('Y-m-d', strtotime("last day of previous Month"));

    foreach ($data as $key => $value) {
        $order_id = $value[0]['value'];
        $facilitycode = $value[8]['value'];
        $screening_id = $value[1]['value'];
        $confirm_id = $value[2]['value'];
        $tiebreaker_id = $value[3]['value'];

        $screening_allocate = $value[4]['value'];
        $confirm_allocate = $value[5]['value'];
        $tiebreaker_allocate = $value[6]['value'];

        $query_s = "UPDATE  `lab_commodity_details` SET  `allocated` =  '$screening_allocate',`allocated_date` =  '$now' WHERE  `lab_commodity_details`.`created_at` between '$beg_date' and '$end_date' and `lab_commodity_details`.`commodity_id`='$screening_id'";
        $query_c = "UPDATE  `lab_commodity_details` SET  `allocated` =  '$confirm_allocate',`allocated_date` =  '$now' WHERE  `lab_commodity_details`.`order_id` ='$order_id' and `lab_commodity_details`.`commodity_id`='$confirm_id'";
        $query_t = "UPDATE  `lab_commodity_details` SET  `allocated` =  '$tiebreaker_allocate',`allocated_date` =  '$now' WHERE  `lab_commodity_details`.`order_id` ='$order_id' and `lab_commodity_details`.`commodity_id`='$tiebreaker_id'";

        // $query_s = "UPDATE  `lab_commodity_details` SET  `allocated` =  '$screening_allocate',`allocated_date` =  '$now' WHERE  `lab_commodity_details`.`created_at` between '$beg_date' and '$end_date' and`lab_commodity_details`.`facility_code`='$facilitycode' and `lab_commodity_details`.`commodity_id`='$screening_id'";
        // $query_c = "UPDATE  `lab_commodity_details` SET  `allocated` =  '$confirm_allocate',`allocated_date` =  '$now' WHERE  `lab_commodity_details`.`created_at` between '$beg_date' and '$end_date' and`lab_commodity_details`.`facility_code`='$facilitycode' and `lab_commodity_details`.`commodity_id`='$confirm_id'";
        // $query_t = "UPDATE  `lab_commodity_details` SET  `allocated` =  '$tiebreaker_allocate',`allocated_date` =  '$now' WHERE  `lab_commodity_details`.`created_at` between '$beg_date' and '$end_date' and`lab_commodity_details`.`facility_code`='$facilitycode' and `lab_commodity_details`.`commodity_id`='$tiebreaker_id'";
        $this->db->query($query_s);
        $this->db->query($query_c);
        $this->db->query($query_t);
        // $id = $value[1];
        // $val = $value[3];
        // $query = 'UPDATE  `lab_commodity_details` SET  `allocated` =  ' . $val . ',`allocated_date` =  ' . $now . ' WHERE  `lab_commodity_details`.`id` =' . $id . '';
        // $this->db->query($query);
    }


        // $object_id = $id;
        //$this->logData('16',$object_id);
    echo("allocations saved");
        //redirect('rtk_management/allocation_zone/a');
}
function county_allocation($county_id) {
    $county = Counties::get_county_name($county_id);
    $countyname = $county['county'];
    $data['county_name'] = $countyname;
    $data['banner_text'] = "Allocations in " . $countyname;
    $data['title'] = $countyname . " County RTK Allocations";
    $data['content_view'] = "rtk/allocation_committee/ajax_view/county_allocations_v";
    $data['county_allocation'] = $this->_allocation_county($county_id);

    $this->load->view("rtk/template", $data);
}

public function national_rtk_allocation($county) {

    $current_month = date('m');
    $current_year = date('Y');

    $firstdate = $current_year.'-'.$current_month.'-01';
    $lastdate = $current_year.'-'.$current_month.'-31';
    $final_dets = array();
    $sql3 = "select sum(screening_current_amount) as screening, sum(confirmatory_current_amount) as confirmatory, sum(tiebreaker_current_amount) as tiebreaker from counties";
    $result3 = $this->db->query($sql3)->result_array();
        // print_r($result3);die;
        // echo $firstdate.' and '.$lastdate;die;

    $sql = "SELECT distinct
    counties.county,
    districts.district,
    facilities.facility_code,
    facilities.facility_name,
    allocation_details.amc_s,
    allocation_details.allocate_s,
    allocation_details.amc_c,
    allocation_details.allocate_c,
    allocation_details.amc_t,
    allocation_details.allocate_t,
    allocation_details.amc_d,
    allocation_details.allocate_d
    FROM
    counties,
    districts,
    facilities,
    allocation_details
    WHERE
    counties.id = districts.county
    AND districts.id = facilities.district
    AND facilities.rtk_enabled = 1
    AND counties.id = '$county'
    and facilities.facility_code = allocation_details.facility_code
    and allocation_details.timestamp between '$firstdate' and '$lastdate'";
    $result = $this->db->query($sql)->result_array();
        // echo "<pre>"; print_r($result);die;                 

    foreach ($result as $key => $value) {

        $facility_code = $value['facility_code'];

        $sql2 = "SELECT 
        closing_stock
        FROM
        lab_commodity_details AS a
        WHERE
        facility_code = $facility_code
        AND commodity_id between 4 and 7
        AND created_at in (SELECT 
        MAX(created_at)
        FROM
        lab_commodity_details AS b
        WHERE
        a.facility_code = b.facility_code)";

        $result2 = $this->db->query($sql2)->result_array();

        $final_dets[$facility_code]['county'] = $value['county'];
        $final_dets[$facility_code]['district'] = $value['district'];
        $final_dets[$facility_code]['facility_name'] = $value['facility_name'];
        $final_dets[$facility_code]['facility_code'] = $facility_code;
        $final_dets[$facility_code]['amc_s'] = $value['amc_s'];
        $final_dets[$facility_code]['allocate_s'] = $value['allocate_s'];
        $final_dets[$facility_code]['amc_s'] = $value['amc_c'];
        $final_dets[$facility_code]['allocate_c'] = $value['allocate_c'];
        $final_dets[$facility_code]['amc_t'] = $value['amc_t'];
        $final_dets[$facility_code]['allocate_t'] = $value['allocate_t'];
        $final_dets[$facility_code]['amc_d'] = $value['amc_d'];
        $final_dets[$facility_code]['allocate_d'] = $value['allocate_d'];
        $final_dets[$facility_code]['ending_bal_s'] = $result2[0]['closing_stock'];
        $final_dets[$facility_code]['ending_bal_c'] = $result2[1]['closing_stock'];
        $final_dets[$facility_code]['ending_bal_t'] = $result2[2]['closing_stock'];
        $final_dets[$facility_code]['ending_bal_d'] = $result2[3]['closing_stock'];

    }
        // echo "<pre>"; print_r($final_dets);die;                 



    $data['title'] = "National RTK Allocations";
    $data['banner_text'] = "National RTK Allocations";
    $data['title'] = " National RTK Allocations ".$result['county']." County";
    $data['content_view'] = "rtk/allocation_committee/national_rtk_allocations";
    $data['allocations'] = $final_dets;
    $data['drawing_rights'] = $result3;

    $this->load->view("rtk/template", $data);
}
function _allocation_county($county_id) {
    $three_months_ago = date("Y-m-", strtotime("-3 Month"));
    $three_months_ago .='1';


    $beg_date = date('Y-m', strtotime("-3 Month"));
    $beg_date.='-01';
    $end_date = date('Y-m-d', strtotime("last day of previous Month"));
        // echo "begin $beg_date </br> end $end_date";die;

    $sql = "SELECT facilities.facility_code,facilities.facility_name,districts.district
    FROM facilities, districts, counties
    WHERE facilities.district = districts.id
    AND facilities.rtk_enabled = 1
    AND counties.id = districts.county
    AND counties.id = $county_id
    ORDER BY districts.district,facilities.facility_code  ASC LIMIT 0,10";
    $orders = $this->db->query($sql);
        //echo "<pre>";print_r($orders->result_array());die;
    foreach ($orders->result_array() as $orders_arr) {
        $fcode = $orders_arr['facility_code'];

        $q = "SELECT DISTINCT
        lab_commodities.*,        
        lab_commodity_details.order_id,
        lab_commodity_details.q_requested,    
        lab_commodity_details.commodity_id,        
        lab_commodity_details.allocated,
        lab_commodity_details.q_used,    
        facility_amc.amc,    
        lab_commodity_details.allocated_date
        FROM
        lab_commodities,
        facility_amc,
        facilities,        
        lab_commodity_orders,
        lab_commodity_details
        WHERE
        lab_commodities.id = facility_amc.commodity_id
        AND facility_amc.facility_code = '$fcode'                
        AND facilities.facility_code = facility_amc.facility_code
        AND facility_amc.commodity_id = lab_commodity_details.commodity_id        
        AND lab_commodity_orders.facility_code = facilities.facility_code
        AND lab_commodity_orders.id = lab_commodity_details.order_id
        AND lab_commodity_details.commodity_id = lab_commodities.id
        AND lab_commodity_details.commodity_id BETWEEN 0 AND 6
        AND lab_commodity_orders.order_date BETWEEN '$beg_date' AND '$end_date'
        group by lab_commodity_orders.facility_code,lab_commodity_details.commodity_id
        ORDER BY facilities.facility_code  ASC,lab_commodity_details.commodity_id ASC ";

        $amc_details = $this->db->query($q)->result_array();
        $amcs[$fcode] = $amc_details;
        //echo "<pre>"; print_r($amcs[$fcode]);die();

        $facility_code = $orders_arr['facility_code'];
        $facility_name = $orders_arr['facility_name'];
        $district_name = $orders_arr['district'];

        $order_detail_id = $amcs[$fcode][0]['order_id'];

        $last_allocated = $amcs[$fcode][4]['allocated_date'];
        if($last_allocated == 0){
            $allocation_status = 'Pending Allocation';
        }else{

            $last_allocated = date('d-m-Y',$last_allocated);

            $beg_date_ts = strtotime($beg_date);
            $end_date_ts = strtotime($end_date);

            if(($last_allocated>$beg_date_ts)){
                $allocation_status = 'Pending Allocation';
            }else{
                $allocation_status = 'Allocated on '.$last_allocated;
            }
        }

        $commodity_s = $amcs[$fcode][2]['commodity_name'];
        $commodity_t = $amcs[$fcode][4]['commodity_name'];
        $commodity_c = $amcs[$fcode][3]['commodity_name'];

        $amc_s = $amcs[$fcode][2]['amc'];
        $amc_s = str_replace(',', '', $amc_s);
        $comm_s = $amcs[$fcode][2]['commodity_id'];

        $amc_t = $amcs[$fcode][4]['amc'];
        $amc_t = str_replace(',', '', $amc_t);
        $comm_t = $amcs[$fcode][4]['commodity_id'];

        $amc_c = $amcs[$fcode][3]['amc'];
        $amc_c = str_replace(',', '', $amc_c);
        $comm_c = $amcs[$fcode][3]['commodity_id'];
        //$allocation = '<span class=\"label label-important\">Pending Allocation for  ' . $lastmonth . '</span>';

        $qty_of_issue_s = ceil($amc_s/$amcs[$fcode][2]['unit_of_issue']);
        $qty_of_issue_c = ceil($amc_c/$amcs[$fcode][3]['unit_of_issue']);
        $qty_of_issue_t = ceil($amc_t/$amcs[$fcode][4]['unit_of_issue']);

        if($qty_of_issue_t==0){
            $qty_of_issue_t +=1;
        }
        if($qty_of_issue_c==0){
            $qty_of_issue_c +=1;
        }
        if($qty_of_issue_s==0){
            $qty_of_issue_s +=1;
        }

        $table_body .= "
        <tr id=''>                        
            <td>$district_name</td>
            <td>$facility_code</td>
            <td>$facility_name</td>
            <td>$amc_s </td>
            <td>$qty_of_issue_s</td>
            <td>$amc_c</td>
            <td>$qty_of_issue_c</td>
            <td>$amc_t</td>
            <td>$qty_of_issue_t</td>
            <td>$allocation_status</td>
        </tr>";

        echo $table_body;die;
    }
}



public function trigger_emails() {
    $subject = 'RTK DATA VALIDITY';
    $message = "Dear All,<br/>We would like to bring to your notice the following changes to the system:<br/><ol>
    <li>The autocalculating feature for the Screening - Determine has been removed, and you will be required to type in the values, the only calculation done is the ending balance</li>
    <li>Please enter the begining balances of all commmodities as per the FCDRR where there is a zero (to enable data validity)</li>
    <li>Where there are losses, positive adjustments and/or negative adjustments, please ensure that you fill out the explanation for the same, otherwise you wil not be able to save the report</li></ol><br/>
    All these changes have been made in order to ensure that the system will serve you better.<br/>
    Please use the remaining time to ensure that all the reports submitted for this month fulfil the above requirements. <br/>
    Use the edit link on the orders page to edit the reports.<br/>
    <b>With Regards,<br/>Titus Tunduny</b><br/>
    for: The RTK Development Team<br/>";
        $attach_file = null;
    $bcc_email = 'ttunduny@gmail.com';
    include 'rtk_mailer.php';

    $sql = "select distinct email from user where usertype_id='7' and status =1";
    $res = $this->db->query($sql)->result_array();
    $count = count($res);
    $a = 0;
    $b = 99;

    for ($i=$a; $i < $b ; $i++) {             
        $sql1 = "select distinct email from user where usertype_id='7' and status =1 limit $a,$b";
        $res1 = $this->db->query($sql1)->result_array();
        $to ="";
        foreach ($res1 as $key => $value) {
            $one = $value['email'];
            $to.= $one.',';
        }
        $newmail = new rtk_mailer();
        $response = $newmail->send_email('titus.tunduny@strathmore.edu', $message, $subject, $attach_file, $bcc_email); 
        $a = $b;
        if($b<$count){
            $b+=99;
        }else{
            break;
        }           

    }  
        // $sql = "select distinct email from user where usertype_id='7' and status =1";
        // $res = $this->db->query($sql)->result_array();

        // $subject = 'RTK DATA VALIDITY';
        // $message = "Dear All,<br/>We would like to bring to your notice the following changes to the system:<br/><ol>
        // <li>The autocalculating feature for the Screening - Determine has been removed, and you will be required to type in the values, the only calculation done is the ending balance</li>
        // <li>Please enter the begining balances of all commmodities as per the FCDRR where there is a zero (to enable data validity)</li>
        // <li>Where there are losses, positive adjustments and/or negative adjustments, please ensure that you fill out the explanation for the same, otherwise you wil not be able to save the report</li></ol><br/>
        // All these changes have been made in order to ensure that the system will serve you better.<br/>
        // Please use the remaining time to ensure that all the reports submitted for this month fulfil the above requirements. <br/>
        // Use the edit link on the orders page to edit the reports.<br/>
        // <b>With Regards,<br/>Titus Tunduny</b><br/>
        // for: The RTK Development Team<br/>";

        // $attach_file = null;
        // $bcc_email = 'ttunduny@gmail.com';
        // include 'rtk_mailer.php';
        // $to ="";
        // foreach ($res as $key => $value) {
        //     $one = $value['email'];
        //     $to.= $one.',';
        // }       
        //$max = count($res);
        // $a = 0;
        // $b = 100;
        // for ($i=$a; $i < $b ; $i++) {             
        //     $one = $res[$i]['email'];
        //     $to.= $one.',';            
        //     $newmail = new rtk_mailer();
        //     $response = $newmail->send_email('titus.tunduny@strathmore.edu', $message, $subject, $attach_file, $bcc_email);
        //     }  
        // }        



        //$sql = "INSERT INTO `rtk_messages`(`id`, `sender`, `subject`, `message`, `receipient`, `state`) VALUES (NULL,'$sender','$subject','$message','$receipient','0')";
        //$this->db->query($sql);
        //$object_id = $this->db->insert_id();
        // $this->logData('23', $object_id);
    echo "Email Sent";
}



        //        //        ///Partner Account
public function partner_home() {
    $lastday = date('Y-m-d', strtotime("last day of previous month"));
    $countyid = $this->session->userdata('county_id');
    $partner_id = $this->session->userdata('partner_id');     

    $partner_details = Partners::get_one_partner($partner_id);        
    $partner_name = $partner_details['name'];        
    $districts = districts::getDistrict($countyid);
    $county_name = counties::get_county_name($countyid);
    $County = $county_name[0]['county'];

    $reports = array();
    $tdata = ' ';
    foreach ($districts as $value) {
        $q = $this->db->query('SELECT lab_commodity_orders.id, lab_commodity_orders.facility_code, lab_commodity_orders.compiled_by, lab_commodity_orders.order_date, lab_commodity_orders.district_id, districts.id as distid, districts.district, facilities.facility_name, facilities.facility_code FROM districts, lab_commodity_orders, facilities WHERE lab_commodity_orders.district_id = districts.id AND facilities.facility_code = lab_commodity_orders.facility_code AND districts.id = ' . $value['id'] . '');
        $res = $q->result_array();
        foreach ($res as $values) {
            date_default_timezone_set('EUROPE/Moscow');
            $order_date = date('F', strtotime($values['order_date']));
            $tdata .= '<tr>
            <td>' . $order_date . '</td>
            <td>' . $values['facility_code'] . '</td>
            <td>' . $values['facility_name'] . '</td>
            <td>' . $values['district'] . '</td>
            <td>' . $values['order_date'] . '</td>
            <td>' . $values['compiled_by'] . '</td>
            <td><a href="' . base_url() . 'rtk_management/lab_order_details/' . $values['id'] . '">View</a></td>
            <tr>';
            }
            if (count($res) > 0) {
                array_push($reports, $res);
            }
        }
        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);


        $monthyear = $year . '-' . $month . '-1';
        $englishdate = date('F, Y', strtotime($monthyear));
        $data['graphdata'] = $this->partner_reporting_percentages($partner_id, $year, $month);
        //$data['county_summary'] = $this->_requested_vs_allocated($year, $month, $countyid);
        $data['tdata'] = $tdata;
        $data['county'] = $County;
        $data['title'] = 'RTK Partner';
        $data['banner_text'] = "RTK Partner : $partner_name";
        $data['content_view'] = "rtk/rtk/partner/partner_dashboard";
        $this->load->view("rtk/template", $data);
    }
    public function partner_facilities() {
        $lastday = date('Y-m-d', strtotime("last day of previous month"));        
        $partner_id = $this->session->userdata('partner_id');       
        $partner_details = Partners::get_one_partner($partner_id);        
        $partner_name = $partner_details['name'];                         
        $sql = "select distinct counties.id, counties.county from  counties, facilities, districts where
        facilities.district = districts.id and facilities.partner = '$partner_id'  and districts.county = counties.id
        and facilities.rtk_enabled = '1'";
        $res_counties = $this->db->query($sql)->result_array();        
        $table_data_district = array();
        $table_data_facilities = array();
        //$res_district = $this->_districts_in_county($county);

        $count_counties = count($res_counties);
        $count = count($res_counties);       
        for ($i = 0; $i < $count; $i++) {
            $county_id = $res_counties[$i]['id'];
            $sql1 = "select count(distinct facilities.id) as facilities, count(distinct districts.id) as districts
            from  counties,  facilities,  districts where  
            facilities.district = districts.id  and facilities.partner = '$partner_id'
            and districts.county = counties.id and facilities.rtk_enabled = '1'
            and counties.id = '$county_id'";
        // echo "sql1<br/>";
            $res = $this->db->query($sql1)->result_array();

            foreach ($res as $key => $value) {
                $facilities_count  = $value['facilities'];
                $district_count  = $value['districts'];
                array_push($table_data_facilities, $facilities_count);
                array_push($table_data_district, $district_count);
            }            

        }

        $data['counties_list'] = $res_counties;
        $data['facilities_count'] = $table_data_facilities; 
        $data['districts_count'] = $table_data_district;        
        $data['title'] = 'RTK Partner Admin';
        $data['banner_text'] = "RTK Partner Admin: Counties in $partner_name";
        $data['content_view'] = "rtk/rtk/partner/districts_v";
        $this->load->view("rtk/template", $data);        
    }


    public function partner_pending_facilities() {
        $partner = $this->session->userdata('partner_id');         
        $partner_details = Partners::get_one_partner($partner);        
        $partner_name = $partner_details['name'];  

        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }
        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);
        $date = date('F-Y', mktime(0, 0, 0, $month, 1, $year));       
        $pending_facilities = $this->rtk_facilities_not_reported(NULL, $countyid,NULL,NULL, $year,$month,$partner);
        $new_pending_facilities = array();                        
        $data['pending_facility'] = $pending_facilities;
        $data['title'] = 'RTK Partner Admin Non-Reports Facilities';
        $data['banner_text'] = "RTK Partner Admin: Facilties that did not Report under $partner_name Region";
        $data['content_view'] = "rtk/rtk/partner/partner_non_reporting";
        $this->load->view("rtk/template", $data);
    }



    public function partner_facilities_reports() {

        $partner = $this->session->userdata('partner_id');          
        $partner_details = Partners::get_one_partner($partner);        
        $partner_name = $partner_details['name'];                   
        date_default_timezone_set('EUROPE/moscow');
        $lastday = date('Y-m-d', strtotime("last day of previous month"));            
        $sql = "SELECT 
        lab_commodity_orders.id,
        lab_commodity_orders.facility_code,
        lab_commodity_orders.compiled_by,
        lab_commodity_orders.order_date,
        lab_commodity_orders.district_id,
        districts.district,
        facilities.facility_name,
        facilities.facility_code
        FROM
        lab_commodity_orders,
        facilities,
        districts   
        WHERE
        facilities.partner = '$partner'    
        AND facilities.district = districts.id
        AND lab_commodity_orders.facility_code = facilities.facility_code        
        ORDER BY `lab_commodity_orders`.`order_date` DESC , `lab_commodity_orders`.`district_id` ASC";            
        $res = $this->db->query($sql)->result_array();
        $data['reports'] = $res;            
        $data['title'] = 'RTK Partner Admin Reports';
        $data['banner_text'] = "RTK Partner Admin: Available Reports for $partner_name Region";
        $data['content_view'] = "rtk/rtk/partner/partner_reports";
        $this->load->view("rtk/template", $data);
    }

    public function partner_users($sk = null) {
        $partner = $this->session->userdata('partner_id');          
        $partner_details = Partners::get_one_partner($partner);        
        $partner_name = $partner_details['name'];     

        $q = "SELECT user.fname,user.lname,user.email,user.id AS id,user.telephone,districts.id AS district_id,districts.district
        FROM
        user,
        districts
        WHERE
        user.district = districts.id
        AND user.partner = '$partner'"; 


        $res = $this->db->query($q)->result_array();        
        $data['users'] = $res;        
        $data['title'] = 'RTK Partner Admin Users';
        $data['banner_text'] = "RTK Partner Admin: Users Under $partner_name Region";
        $data['content_view'] = "rtk/rtk/partner/partner_users";
        $this->load->view("rtk/template", $data);
    }

    public function partner_commodity_usage() {
        $partner = $this->session->userdata('partner_id');          
        $commodity = $this->session->userdata('commodity_id');          
        if($commodity!=''){
            $commodity_id = $commodity;
            $sql = "SELECT lab_commodities.commodity_name FROM lab_commodities WHERE lab_commodities.id =$commodity_id";
            $q = $this->db->query($sql);
            $res = $q->result_array();
            foreach ($res as $values) {               
                $commodity_name = $values['commodity_name'];
            }
        }else{

            $sql = "SELECT lab_commodities.id,lab_commodities.commodity_name FROM lab_commodities,lab_commodity_categories WHERE lab_commodities.category = lab_commodity_categories.id AND lab_commodity_categories.active = '1' limit 0,1";
            $q = $this->db->query($sql);
            $res = $q->result_array();
            foreach ($res as $values) {
                $commodity_id = $values['id'];
                $commodity_name = $values['commodity_name'];
            }
        }
        //echo "$commodity_id";die();
        $lastday = date('Y-m-d', strtotime("last day of previous month"));
        $countyid = $this->session->userdata('county_id');
        $districts = districts::getDistrict($countyid);
        $county_name = counties::get_county_name($countyid);
        $County = $county_name[0]['county'];

        $reports = array();

        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);


        $monthyear = $year . '-' . $month . '-1';
        $englishdate = date('F, Y', strtotime($monthyear));
        $data['graphdata'] = $this->partner_commodity_percentages($partner, $commodity_id, $month);
        // echo "<pre>"; print_r($data['graphdata']);die;
        //$data['county_summary'] = $this->_requested_vs_allocated($year, $month, $countyid);
        $data['tdata'] = $tdata;
        $data['county'] = $County;
        $data['commodity_name'] = $commodity_name;
        $data['title'] = 'RTK Partner';
        $data['banner_text'] = 'RTK Partner Commodity Usage';
        $data['content_view'] = "rtk/rtk/partner/commodity_usage";
        $this->load->view("rtk/template", $data);

    }
    function partner_reporting_percentages($partner, $year, $month) {    
        $q = "SELECT 
        count(lab_commodity_orders.id) as total,
        extract(YEAR_MONTH FROM lab_commodity_orders.order_date) as current_month,
        facilities.partner,
        facilities.facility_code
        FROM
        lab_commodity_orders,
        facilities
        WHERE
        facilities.facility_code = lab_commodity_orders.facility_code
        AND facilities.partner = '$partner'
        group by extract(YEAR_MONTH FROM lab_commodity_orders.order_date) limit 10,19";

        $query = $this->db->query($q);

        $sql = $this->db->select('count(id) as county_facility')->get_where('facilities', array('partner' =>$partner))->result_array();
        foreach ($sql as $key => $value) {
            $facilities = intval($value['county_facility']);
        }


        $month = array();
        $reported = array();
        $nonreported = array();
        $reported_value = array();
        $nonreported_value = array();
        foreach ($query->result_array() as $val) {
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        //$percentage_reported = $this->district_reporting_percentages($val['district_id'], $year, $month);
            $reports = intval($val['total']);
            $percentage_reported = round((($reports/$facilities)*100),0);
            if ($percentage_reported > 100) {
                $percentage_reported = 100;
            }
            $unreported = $facilities - $reports;
            array_push($reported, $percentage_reported);
            array_push($nonreported_value, $unreported);
            array_push($reported_value, $reports);

            $percentage_non_reported = 100 - $percentage_reported;
            array_push($nonreported, $percentage_non_reported);
        }



        $month_array = json_encode($month);
        $reported = json_encode($reported);
        $reported_value = json_encode($reported_value);
        $nonreported_value = json_encode($nonreported_value);
        $nonreported = json_encode($nonreported);

        $data['month'] = $month_array;
        $data['reported'] = $reported;
        $data['nonreported'] = $nonreported;
        $data['reported_value'] = $reported_value;
        $data['nonreported_value'] = $nonreported_value;
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
        return $data;
    }    
    function partner_commodity_percentages($usertype, $user_id, $commodity, $month) {  
        //$q = 'select extract(YEAR_MONTH from lab_commodity_details.created_at)as current_month, lab_commodity_details.commodity_id, lab_commodity_details.q_requested, lab_commodity_details.beginning_bal,lab_commodity_details.q_received,lab_commodity_details.no_of_tests_done,lab_commodity_details.losses,lab_commodity_details.closing_stock,lab_commodity_details.q_received, facilities.partner from facilities, lab_commodity_details where facilities.partner = 7 group by extract(YEAR_MONTH from lab_commodity_details.created_at) ';
        // echo "$user_id and usertype as $usertype        ";
        $conditions = '';
        if ($usertype =='14' ) {
            $conditions = ' facilities.partner = '.$user_id;
        } elseif ($usertype =='13') {
            $conditions = ' counties.id = '.$user_id;
        }
        $q = "
        select 
        extract(YEAR_MONTH from lab_commodity_details.created_at) as current_month,
        lab_commodity_details.commodity_id,
        sum(lab_commodity_details.q_requested) as q_requested ,
        sum(lab_commodity_details.beginning_bal) as beginning_bal,
        sum(lab_commodity_details.q_used) as q_used,
        sum(lab_commodity_details.no_of_tests_done) as no_of_tests_done ,
        sum(lab_commodity_details.losses) as losses,
        sum(lab_commodity_details.closing_stock) as closing_stock,
        sum(lab_commodity_details.q_received) as q_received,
        facilities.partner
        from
        facilities,
        lab_commodity_details,
        lab_commodities,
        counties,
        districts
        where
        $conditions
        and facilities.district = districts.id
        AND districts.county = counties.id
        and lab_commodity_details.facility_code = facilities.facility_code
        and lab_commodity_details.commodity_id = lab_commodities.id
        AND lab_commodities.id ='$commodity'
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) limit 10,19";

        $query = $this->db->query($q)->result_array();
        // echo "$  q";die;

        // $sql = $this->db->select('count(id) as county_facility')->get_where('facilities', array('partner' =>7))->result_array();
        // foreach ($sql as $key => $value) {
        //    $facilities = intval($value['county_facility']);
        // }


        $month = array();
        $beginning_bal = array();
        $qty_received = array();;
        $total_tests = array();
        $losses = array();
        $ending_bal = array();
        $qty_requested = array();
        $qty_used = array();
        // $month_array = array();
        // $beginning_bal_array = array();
        // $qty_received_array = array();
        // $total_tests_array = array();
        // $losses_array = array();
        // $ending_bal_array = array();
        // $qty_requested_array = array();


        foreach ($query as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
            array_push($beginning_bal, intval($val['beginning_bal']));
            array_push($qty_received, intval($val['q_received']));
            array_push($total_tests, intval($val['no_of_tests_done']));
            array_push($losses, intval($val['losses']));
            array_push($ending_bal, intval($val['closing_stock']));
            array_push($qty_requested, intval($val['q_requested']));
            array_push($qty_used, intval($val['q_used']));
        //$percentage_reported = $this->district_reporting_percentages($val['district_id'], $year, $month);



        // array_push($month_array, $month);
        // array_push($beginning_bal_array, $beginning_bal);
        // array_push($qty_received_array, $qty_received);
        // array_push($total_tests_array, $total_tests);
        // array_push($losses_array, $losses);
        // array_push($ending_bal_array, $ending_bal);
        // array_push($qty_requested_array, $qty_requested);
        }


        $month_data = json_encode($month);
        $beginning_bal_data = json_encode($beginning_bal);
        $qty_received_data = json_encode($qty_received);
        $total_tests_data = json_encode($total_tests);
        $losses_data = json_encode($losses);
        $ending_bal_data = json_encode($ending_bal);
        $qty_requested_data = json_encode($qty_requested);

        $data['month'] = $month_data;
        $data['beginning_bal'] = $beginning_bal_data;
        $data['qty_received'] = $qty_received_data;
        $data['total_tests'] = $total_tests_data;
        $data['losses'] = $losses_data;
        $data['ending_bal'] = $ending_bal_data;
        $data['qty_requested'] = $qty_requested_data;
        $data['qty_used'] = json_encode($qty_used);
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
        return $data;
    }    

    public function partner_stock_status() {        
        $partner = $this->session->userdata('partner_id');          

        $lastday = date('Y-m-d', strtotime("last day of previous month"));        

        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);


        $monthyear = $year . '-' . $month . '-1';
        $englishdate = date('F, Y', strtotime($monthyear));
        $data['graphdata'] = $this->partner_stock_percentages($partner, $month);       
        $data['tdata'] = $tdata;
        $data['county'] = $County;
        $data['commodity_name'] = $commodity_name;
        $data['title'] = 'RTK Partner';
        $data['banner_text'] = 'RTK Partner Stock Status: Losses';
        $data['content_view'] = "rtk/rtk/partner/partner_stock_status";
        $this->load->view("rtk/template", $data);

    }
    public function partner_stock_status_expiries() {
        $commodity = $this->session->userdata('commodity_id');          
        $partner = $this->session->userdata('partner_id');          

        $lastday = date('Y-m-d', strtotime("last day of previous month"));       

        $reports = array();

        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);


        $monthyear = $year . '-' . $month . '-1';
        $englishdate = date('F, Y', strtotime($monthyear));
        $data['graphdata'] = $this->partner_stock_expiring_percentages($partner);       
        $data['tdata'] = $tdata;
        $data['county'] = $County;
        $data['commodity_name'] = $commodity_name;
        $data['title'] = 'RTK Partner';
        $data['banner_text'] = 'RTK Partner Stock Status: Expiries';
        $data['content_view'] = "rtk/rtk/partner/partner_stock_status_expiries";
        $this->load->view("rtk/template", $data);

    }
    public function partner_stock_level() {
        $partner = $this->session->userdata('partner_id'); 

        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);


        $monthyear = $year . '-' . $month . '-1';
        $englishdate = date('F, Y', strtotime($monthyear));
        $data['graphdata'] = $this->partner_stock_level_percentages($partner); 

        $data['tdata'] = $tdata;        
        $data['title'] = 'RTK Partner';
        $data['banner_text'] = 'RTK Partner Stock Status: Stock Level';
        $data['content_view'] = "rtk/rtk/partner/partner_stock_level";
        $this->load->view("rtk/template", $data);

    }
    function partner_stock_card(){
        $commodity = $this->session->userdata('commodity_id');          
        $partner = $this->session->userdata('partner_id');          

        $lastday = date('Y-m-d', strtotime("last day of previous month"));        
        $reports = array();                

        $month = $this->session->userdata('Month');
        if ($month == '') {
            $month = date('mY', strtotime('-1 month'));
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);      
        $monthyear = $year . '-' . $month . '-1';
        $firstdate = $year . '-' . $month . '-1';
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $lastdate = $year . '-' . $month . '-' . $num_days;
        //echo "The dates are  $firstdate and $lastdate";die();
        $englishdate = date('F, Y', strtotime($monthyear));
        $sql = "select    
        lab_commodity_details.commodity_id,
        sum(lab_commodity_details.q_requested) as qty_requested,
        sum(lab_commodity_details.beginning_bal) as beg_bal,
        sum(lab_commodity_details.q_received) as qty_received,
        sum(lab_commodity_details.no_of_tests_done) as test_done,
        sum(lab_commodity_details.losses) as losses,
        sum(lab_commodity_details.closing_stock) as closing_stock,
        sum(lab_commodity_details.q_used) as qty_used,
        sum(lab_commodity_details.q_expiring) as qty_expiring,
        sum(lab_commodity_details.days_out_of_stock) as days_out_of_stock,
        facilities.partner,
        lab_commodities.commodity_name
        from
        facilities,
        lab_commodity_details,
        lab_commodities
        where
        facilities.partner = '$partner'
        and lab_commodity_details.facility_code = facilities.facility_code
        and lab_commodity_details.commodity_id = lab_commodities.id
        and lab_commodities.category = '1'
        and lab_commodity_details.created_at between '$firstdate' and '$lastdate'
        group by lab_commodities.id";

        $data['result'] = $this->db->query($sql)->result_array();
        $data['active_month'] = $month.$year;
        $data['current_month'] = date('mY', time());       
        $data['tdata'] = $tdata;
        $data['county'] = $County;
        $data['commodity_name'] = $commodity_name;
        $data['title'] = 'RTK Partner';
        $data['banner_text'] = 'RTK Partner Stock Status: Stock Card';
        $data['content_view'] = "rtk/rtk/partner/partner_stock_card";
        $this->load->view("rtk/template", $data);
    }
    function partner_stock_percentages($partner, $month) {    
        //$q = 'select extract(YEAR_MONTH from lab_commodity_details.created_at)as current_month, lab_commodity_details.commodity_id, lab_commodity_details.q_requested, lab_commodity_details.beginning_bal,lab_commodity_details.q_received,lab_commodity_details.no_of_tests_done,lab_commodity_details.losses,lab_commodity_details.closing_stock,lab_commodity_details.q_received, facilities.partner from facilities, lab_commodity_details where facilities.partner = 7 group by extract(YEAR_MONTH from lab_commodity_details.created_at) ';
        $q = "select extract(YEAR_MONTH from lab_commodity_details.created_at) as current_month,
        lab_commodities.commodity_name,
        lab_commodity_details.commodity_id,
        sum(lab_commodity_details.losses) as losses,
        facilities.partner
        from
        facilities,
        lab_commodity_details,
        lab_commodities
        where
        facilities.partner = '$partner'
        and lab_commodity_details.facility_code = facilities.facility_code
        and lab_commodity_details.commodity_id = lab_commodities.id";
        $conditions = 'limit 10,19';
        $q_screen_det   = $q." and commodity_id = 1 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";             
        $query = $this->db->query($q_screen_det)->result_array();

        $q_confirm_uni   = $q." and commodity_id = 2 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query2 = $this->db->query($q_confirm_uni)->result_array();

        $q_screening_khb   = $q." and commodity_id = 4 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query3 = $this->db->query($q_screening_khb)->result_array();

        $q_confrim_first   = $q." and commodity_id = 5 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query4 = $this->db->query($q_confrim_first)->result_array();

        $q_confrim_first   = $q." and commodity_id = 6 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query5 = $this->db->query($q_confrim_first)->result_array();
        //echo("<pre>"); print_r($query);die;

        $month = array();
        $screening_det = array();
        $confirm_uni = array();;
        $screening_khb = array();
        $confrim_first = array();
        $tie_breaker = array();        
        $month_array = array();
        $screening_det_array = array();
        $confirm_uni_array = array();
        $screening_khb_array = array();
        $confrim_first_array = array();
        $tie_breaker_array = array();        


        foreach ($query as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($screening_det, intval($val['losses']));

        }

        foreach ($query2 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($confirm_uni, intval($val['losses']));

        }

        foreach ($query3 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($screening_khb, intval($val['losses']));

        }
        foreach ($query4 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($confrim_first, intval($val['losses']));

        }

        foreach ($query5 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($tie_breaker, intval($val['losses']));

        }  
        $month_data = json_encode($month);
        $screening_det_data = json_encode($screening_det);
        $confirm_uni_data = json_encode($confirm_uni);
        $screening_khb_data = json_encode($screening_khb);
        $confrim_first_data = json_encode($confrim_first);
        $tie_breaker_data = json_encode($tie_breaker);        

        $data['month'] = $month_data;
        $data['screening_det'] = $screening_det_data;
        $data['confirm_uni'] = $confirm_uni_data;
        $data['screening_khb'] = $screening_khb_data;
        $data['confrim_first'] = $confrim_first_data;
        $data['tie_breaker'] = $tie_breaker_data;  
        // echo "<pre>";      
        // print_r($data);die();
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
        return $data;
    } 
    function partner_stock_level_percentages($partner, $month) {    
        //$q = 'select extract(YEAR_MONTH from lab_commodity_details.created_at)as current_month, lab_commodity_details.commodity_id, lab_commodity_details.q_requested, lab_commodity_details.beginning_bal,lab_commodity_details.q_received,lab_commodity_details.no_of_tests_done,lab_commodity_details.losses,lab_commodity_details.closing_stock,lab_commodity_details.q_received, facilities.partner from facilities, lab_commodity_details where facilities.partner = 7 group by extract(YEAR_MONTH from lab_commodity_details.created_at) ';
        $q = "select extract(YEAR_MONTH from lab_commodity_details.created_at) as current_month,
        lab_commodities.commodity_name,
        lab_commodity_details.commodity_id,
        sum(lab_commodity_details.closing_stock) as closing_stock,
        facilities.partner
        from
        facilities,
        lab_commodity_details,
        lab_commodities
        where
        facilities.partner = '$partner'
        and lab_commodity_details.facility_code = facilities.facility_code
        and lab_commodity_details.commodity_id = lab_commodities.id";
        $conditions = 'limit 10,19';
        $q_screen_det   = $q." and commodity_id = 1 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";

        $query = $this->db->query($q_screen_det)->result_array();

        $q_confirm_uni   = $q." and commodity_id = 2 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query2 = $this->db->query($q_confirm_uni)->result_array();

        $q_screening_khb   = $q." and commodity_id = 4 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query3 = $this->db->query($q_screening_khb)->result_array();

        $q_confrim_first   = $q." and commodity_id = 5 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query4 = $this->db->query($q_confrim_first)->result_array();

        $q_confrim_first   = $q." and commodity_id = 6 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query5 = $this->db->query($q_confrim_first)->result_array();
        // echo("<pre>"); print_r($query2);die;

        $month = array();
        $screening_det = array();
        $confirm_uni = array();;
        $screening_khb = array();
        $confrim_first = array();
        $tie_breaker = array();        
        $month_array = array();
        $screening_det_array = array();
        $confirm_uni_array = array();
        $screening_khb_array = array();
        $confrim_first_array = array();
        $tie_breaker_array = array();        


        foreach ($query as $val) {            
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
            array_push($screening_det, intval($val['closing_stock']));

        }

        foreach ($query2 as $val) {            
        // $raw_month =  $val['current_month'];
        // $year = substr($raw_month, 0,4);

        // $month_val = substr($raw_month, 4,2);
        // $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
        // array_push($month, $month_text) ;
            array_push($confirm_uni, intval($val['closing_stock']));

        }

        foreach ($query3 as $val) {            
        // $raw_month =  $val['current_month'];
        // $year = substr($raw_month, 0,4);

        // $month_val = substr($raw_month, 4,2);
        // $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
        // array_push($month, $month_text) ;
            array_push($screening_khb, intval($val['closing_stock']));

        }
        foreach ($query4 as $val) {            
        // $raw_month =  $val['current_month'];
        // $year = substr($raw_month, 0,4);

        // $month_val = substr($raw_month, 4,2);
        // $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
        // array_push($month, $month_text) ;
            array_push($confrim_first, intval($val['closing_stock']));

        }

        foreach ($query5 as $val) {            
        // $raw_month =  $val['current_month'];
        // $year = substr($raw_month, 0,4);

        // $month_val = substr($raw_month, 4,2);
        // $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
        // array_push($month, $month_text) ;
            array_push($tie_breaker, intval($val['closing_stock']));

        } 

        $month_data = json_encode($month);
        $screening_det_data = json_encode($screening_det);
        $confirm_uni_data = json_encode($confirm_uni);
        $screening_khb_data = json_encode($screening_khb);
        $confrim_first_data = json_encode($confrim_first);
        $tie_breaker_data = json_encode($tie_breaker);        

        $data['month'] = $month_data;
        $data['screening_det'] = $screening_det_data;
        $data['confirm_uni'] = $confirm_uni_data;
        $data['screening_khb'] = $screening_khb_data;
        $data['confrim_first'] = $confrim_first_data;
        $data['tie_breaker'] = $tie_breaker_data;        
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
        return $data;
    }    
    function partner_stock_expiring_percentages($partner) {    
        //$q = 'select extract(YEAR_MONTH from lab_commodity_details.created_at)as current_month, lab_commodity_details.commodity_id, lab_commodity_details.q_requested, lab_commodity_details.beginning_bal,lab_commodity_details.q_received,lab_commodity_details.no_of_tests_done,lab_commodity_details.losses,lab_commodity_details.closing_stock,lab_commodity_details.q_received, facilities.partner from facilities, lab_commodity_details where facilities.partner = 7 group by extract(YEAR_MONTH from lab_commodity_details.created_at) ';
        $q = "select extract(YEAR_MONTH from lab_commodity_details.created_at) as current_month,
        lab_commodities.commodity_name,
        lab_commodity_details.commodity_id,
        sum(lab_commodity_details.q_expiring) as q_expiring,
        facilities.partner
        from
        facilities,
        lab_commodity_details,
        lab_commodities
        where
        facilities.partner = '$partner'
        and lab_commodity_details.facility_code = facilities.facility_code
        and lab_commodity_details.commodity_id = lab_commodities.id";
        $conditions = ' limit 10,19';
        $q_screen_det   = $q." and commodity_id = 1 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query = $this->db->query($q_screen_det)->result_array();

        $q_confirm_uni   = $q." and commodity_id = 2 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query2 = $this->db->query($q_confirm_uni)->result_array();

        $q_screening_khb   = $q." and commodity_id = 4 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query3 = $this->db->query($q_screening_khb)->result_array();

        $q_confrim_first   = $q." and commodity_id = 5 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query4 = $this->db->query($q_confrim_first)->result_array();

        $q_confrim_first   = $q." and commodity_id = 6 
        group by extract(YEAR_MONTH from lab_commodity_details.created_at) $conditions";
        $query5 = $this->db->query($q_confrim_first)->result_array();
        // echo("<pre>"); print_r($query);die;

        $month = array();
        $screening_det = array();
        $confirm_uni = array();;
        $screening_khb = array();
        $confrim_first = array();
        $tie_breaker = array();        
        $month_array = array();
        $screening_det_array = array();
        $confirm_uni_array = array();
        $screening_khb_array = array();
        $confrim_first_array = array();
        $tie_breaker_array = array();        


        foreach ($query as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($screening_det, intval($val['q_expiring']));

        }

        foreach ($query2 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($confirm_uni, intval($val['q_expiring']));

        }

        foreach ($query3 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($screening_khb, intval($val['q_expiring']));

        }
        foreach ($query4 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($confrim_first, intval($val['q_expiring']));

        }

        foreach ($query5 as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);

            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
        // if($val['commodity_id' ==1]){
            array_push($tie_breaker, intval($val['q_expiring']));

        }  
        $month_data = json_encode($month);
        $screening_det_data = json_encode($screening_det);
        $confirm_uni_data = json_encode($confirm_uni);
        $screening_khb_data = json_encode($screening_khb);
        $confrim_first_data = json_encode($confrim_first);
        $tie_breaker_data = json_encode($tie_breaker);        

        $data['month'] = $month_data;
        $data['screening_det'] = $screening_det_data;
        $data['confirm_uni'] = $confirm_uni_data;
        $data['screening_khb'] = $screening_khb_data;
        $data['confrim_first'] = $confrim_first_data;
        $data['tie_breaker'] = $tie_breaker_data;        
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
        return $data;
    }    
    function partner_stock_level_percentages_old($partner) {   


        $q = "select 
        extract(YEAR_MONTH from lab_commodity_details.created_at) as current_month,
        lab_commodity_details.commodity_id,
        lab_commodity_details.q_requested,
        lab_commodity_details.beginning_bal,
        lab_commodity_details.q_received,
        lab_commodity_details.no_of_tests_done,
        lab_commodity_details.losses,
        lab_commodity_details.closing_stock,
        lab_commodity_details.q_received,
        facilities.partner
        from
        facilities,
        lab_commodity_details,
        lab_commodities
        where
        facilities.partner = '$partner'
        and lab_commodity_details.facility_code = facilities.facility_code
        and lab_commodity_details.commodity_id = lab_commodities.id
        AND lab_commodities.id in (
        select lab_commodities.id from lab_commodities, lab_commodity_categories where 
        lab_commodities.category = lab_commodity_categories.id and lab_commodity_categories.active='1'
        )
        group by extract(YEAR_MONTH from lab_commodity_details.created_at)";
        $query = $this->db->query($q)->result_array();        


        $month = array();
        $beginning_bal = array();
        $qty_received = array();;
        $total_tests = array();
        $losses = array();
        $ending_bal = array();
        $qty_requested = array();
        $month_array = array();
        $beginning_bal_array = array();
        $qty_received_array = array();
        $total_tests_array = array();
        $losses_array = array();
        $ending_bal_array = array();
        $qty_requested_array = array();


        foreach ($query as $val) {
        //echo intval($val['current_month']);die();
            $raw_month =  $val['current_month'];
            $year = substr($raw_month, 0,4);            
            $month_val = substr($raw_month, 4,2);
            $month_text = date('M',mktime(0,0,0,$month_val,10)).' '.$year;
            array_push($month, $month_text) ;
            array_push($beginning_bal, intval($val['beginning_bal']));
            array_push($qty_received, intval($val['q_received']));
            array_push($total_tests, intval($val['no_of_tests_done']));
            array_push($losses, intval($val['losses']));
            array_push($ending_bal, intval($val['closing_stock']));
            array_push($qty_requested, intval($val['q_requested']));
        //$percentage_reported = $this->district_reporting_percentages($val['district_id'], $year, $month);



        // array_push($month_array, $month);
        // array_push($beginning_bal_array, $beginning_bal);
        // array_push($qty_received_array, $qty_received);
        // array_push($total_tests_array, $total_tests);
        // array_push($losses_array, $losses);
        // array_push($ending_bal_array, $ending_bal);
        // array_push($qty_requested_array, $qty_requested);
        }


        $month_data = json_encode($month);
        $beginning_bal_data = json_encode($beginning_bal);
        $qty_received_data = json_encode($qty_received);
        $total_tests_data = json_encode($total_tests);
        $losses_data = json_encode($losses);
        $ending_bal_data = json_encode($ending_bal);
        $qty_requested_data = json_encode($qty_requested);

        $data['month'] = $month_data;
        $data['beginning_bal'] = $beginning_bal_data;
        $data['qty_received'] = $qty_received_data;
        $data['total_tests'] = $total_tests_data;
        $data['losses'] = $losses_data;
        $data['ending_bal'] = $ending_bal_data;
        $data['qty_requested'] = $qty_requested_data;
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
        return $data;
    }   

    public function partner_county_profile($district) {
        $data = array();
        $partner_id = $this->session->userdata('partner_id');      
        $sql_facilities = "select * from facilities, districts,counties 
        where facilities.district = districts.id and facilities.partner = '$partner_id' 
        and districts.county = counties.id and counties.id = '$district' ";        
        $facilities = $this->db->query($sql_facilities)->result_array();        

        $lastday = date('Y-m-d', strtotime("last day of previous month"));

        $current_month = $this->session->userdata('Month');
        if ($current_month == '') {
            $current_month = date('mY', time());
        }

        $previous_month = date('m', strtotime("last day of previous month"));
        $previous_month_1 = date('mY', strtotime('-2 month'));
        $previous_month_2 = date('mY', strtotime('-3 month'));


        $year_current = substr($current_month, -4);
        $year_previous = date('Y', strtotime("last day of previous month"));
        $year_previous_1 = substr($previous_month_1, -4);
        $year_previous_2 = substr($previous_month_2, -4);

        $current_month = substr_replace($current_month, "", -4);        
        $previous_month_1 = substr_replace($previous_month_1, "", -4);
        $previous_month_2 = substr_replace($previous_month_2, "", -4);

        $monthyear_current = $year_current . '-' . $current_month . '-1';
        $monthyear_previous = $year_previous . '-' . $previous_month . '-1';
        $monthyear_previous_1 = $year_previous_1 . '-' . $previous_month_1 . '-1';
        $monthyear_previous_2 = $year_previous_2 . '-' . $previous_month_2 . '-1';

        $englishdate = date('F, Y', strtotime($monthyear_current));

        $m_c = date("F", strtotime($monthyear_current));

        $m0 = date("F", strtotime($monthyear_previous));
        $m1 = date("F", strtotime($monthyear_previous_1));
        $m2 = date("F", strtotime($monthyear_previous_2));

        $month_text = array($m2, $m1, $m0);

        $district_summary = $this->partner_summary($district, $year_current, $current_month,$partner_id);
        $district_summary_prev = $this->partner_summary($district, $year_previous, $previous_month,$partner_id);
        $district_summary1 = $this->partner_summary($district, $year_previous_1, $previous_month_1,$partner_id);
        $district_summary2 = $this->partner_summary($district, $year_previous_2, $previous_month_2,$partner_id);


        $county_id = districts::get_county_id($district);
        $county_name = counties::get_county_name($district);

        // $cid = $this->db->select('districts.county')->get_where('districts', array('id' =>$district))->result_array();

        //  foreach ($cid as $key => $value) {
        //     $myres = $cid[0]['county'];
        // }
        $sql_counties = "select distinct counties.id,counties.county from facilities, districts,counties 
        where facilities.district = districts.id and facilities.partner = '$partner_id' and facilities.rtk_enabled='1' 
        and districts.county = counties.id";          
        $mycounties = $this->db->query($sql_counties)->result_array();       

        $data['district_balances_current'] = $this->partner_county_totals($year_current, $previous_month, $district);
        $data['district_balances_previous'] = $this->partner_county_totals($year_previous, $previous_month, $district);
        $data['district_balances_previous_1'] = $this->partner_county_totals($year_previous_1, $previous_month_1, $district);
        $data['district_balances_previous_2'] = $this->partner_county_totals($year_previous_2, $previous_month_2, $district);


        $data['district_summary'] = $district_summary;

        $data['counties_list'] = $mycounties;
        $data['facilities']= $facilities;        

        $data['district_name'] = $district_summary['district'];
        $data['county_id'] = $county_name['id'];
        $data['county_name'] = $county_name['county'];     

        $data['title'] = 'RTK Partner County Profile: ' . $district_summary['district'];
        $data['banner_text'] = 'Partner County Profile: ' . $district_summary['district'];
        $data['content_view'] = "rtk/rtk/partner/partner_profile_v";
        $data['months'] = $month_text;

        $this->load->view("rtk/template", $data);
    }
        //Shared Functions

    function _allocation($zone = NULL, $county = NULL, $district = NULL, $facility = NULL, $sincedate = NULL, $enddate = NULL) {
        // function to filter allocation based on multiple parameter
        // zone, county,district, sincedate,
        $conditions = '';
        $conditions = (isset($zone)) ? " AND facilities.Zone = 'Zone $zone'" : '';
        $conditions = (isset($county)) ? $conditions . " AND counties.id = $county" : $conditions . ' ';
        $conditions = (isset($district)) ? $conditions . " AND districts.id = $district" : $conditions . ' ';
        $conditions = (isset($facility)) ? $conditions . " AND facilities.facility_code = $facility" : $conditions . ' ';
        $conditions = (isset($sincedate)) ? $conditions . " AND lab_commodity_details.allocated_date >= $sincedate" : $conditions . ' ';
        $conditions = (isset($enddate)) ? $conditions . " AND lab_commodity_details.allocated_date <= $enddate" : $conditions . ' ';

        $sql = "select facilities.facility_name,facilities.facility_code,facilities.Zone, facilities.contactperson,facilities.cellphone, lab_commodity_details.commodity_id,
        lab_commodity_details.allocated,lab_commodity_details.allocated_date,lab_commodity_orders.order_date,lab_commodities.commodity_name,facility_amc.amc,lab_commodity_details.closing_stock,lab_commodity_details.q_requested
        from facilities, lab_commodity_orders,lab_commodity_details, counties,districts,lab_commodities,lab_commodity_categories,facility_amc
        WHERE facilities.facility_code = lab_commodity_orders.facility_code
        AND lab_commodity_categories.id = 1
        AND lab_commodity_categories.id = lab_commodities.category
        AND counties.id = districts.county
        AND facilities.district = districts.id
        AND facilities.rtk_enabled = 1
        and lab_commodities.id = lab_commodity_details.commodity_id
        and lab_commodities.id = facility_amc.commodity_id
        and facilities.facility_code = facility_amc.facility_code                
        AND lab_commodity_orders.id = lab_commodity_details.order_id
        AND lab_commodity_details.commodity_id between 1 AND 3
        $conditions
        GROUP BY facilities.facility_code, lab_commodity_details.commodity_id";
        $res = $this->db->query($sql);
        $returnable = $res->result_array();
        return $returnable;
#$nonexistent = "AND lab_commodity_orders.order_date BETWEEN '2014-04-01' AND '2014-04-30'";
    }


        //Switch Districts
    public function switch_district($new_dist = null, $switched_as, $month = NULL, $redirect_url = NULL, $newcounty = null, $switched_from = null,$partner=null) {    
        if ($new_dist == 0) {
            $new_dist = null;
        }
        if ($month == 0) {
            $month = null;
        }
        if ($redirect_url == 0) {
            $redirect_url = null;
        }
        if ($newcounty == 0) {
            $newcounty = null;
        }
        if ($redirect_url == NULL) {
            $redirect_url = 'home_controller';
        }           

        if (isset($partner)) {
            $partner_id = $partner;                   
        }
        if (!isset($newcounty)) {
            $newcounty = $this->session->userdata('county_id');
        }

        $session_data = array("session_id" => $this->session->userdata('session_id'),
            "ip_address" => $this->session->userdata('ip_address'),
            "user_agent" => $this->session->userdata('user_agent'),
            "last_activity" => $this->session->userdata('last_activity'),
            "county_id" => $newcounty,
            "phone_no" => $this->session->userdata('phone_no'),
            "user_email" => $this->session->userdata('user_email'),
            "user_db_id" => $this->session->userdata('user_db_id'),
            "full_name" => $this->session->userdata('full_name'),
            "user_id" => $this->session->userdata('user_id'),
            "user_indicator" => $switched_as,
            "names" => $this->session->userdata('names'),
            "inames" => $this->session->userdata('inames'),
            "identity" => $this->session->userdata('identity'),
            "news" => $this->session->userdata('news'),
            "district_id" => $new_dist,
            "drawing_rights" => $this->session->userdata('drawing_rights'),
            "switched_as" => $switched_as,
            "partner_id" => $partner_id,
            "Month" => $month,
            'switched_from' => $switched_from);


        $this->session->set_userdata($session_data);
        redirect($redirect_url);
    }
    public function switch_commodity($month = NULL, $redirect_url = NULL,$commodity) {

        if ($month == 0) {
            $month = null;
        }        

        if ($redirect_url == NULL) {
            $redirect_url = 'home_controller';
        }     


        $url = 'rtk_management/';
        $url.=$redirect_url;

        $session_data = array("session_id" => $this->session->userdata('session_id'),
            "ip_address" => $this->session->userdata('ip_address'),
            "user_agent" => $this->session->userdata('user_agent'),
            "last_activity" => $this->session->userdata('last_activity'),
            "phone_no" => $this->session->userdata('phone_no'),
            "user_email" => $this->session->userdata('user_email'),
            "user_db_id" => $this->session->userdata('user_db_id'),
            "full_name" => $this->session->userdata('full_name'),
            "user_id" => $this->session->userdata('user_id'),
            "names" => $this->session->userdata('names'),
            "inames" => $this->session->userdata('inames'),
            "identity" => $this->session->userdata('identity'),
            "news" => $this->session->userdata('news'),         
            "drawing_rights" => $this->session->userdata('drawing_rights'),         
            "commodity_id" => $commodity,         
            "Month" => $month);


        $this->session->set_userdata($session_data);
        redirect($url);
    }
    public function summary_tab_display($county, $year, $month) {
        // county may be 1 for Nairobi, 5 for busia or 31 for Nakuru
        $htmltable = '';

        $countyname = counties::get_county_name($county);
        $countyname = $countyname[0]['county'];
        $ish = $this->rtk_summary_county($county, $year, $month);            
        $htmltable .= '<tr>
        <td rowspan ="' . $ish['districts'] . '">' . $countyname . '';            
            $total_punctual = 0;
            $county_percentage = 0;

            foreach ($ish['district_summary'] as $vals) {
                $early = $vals['reported'] - $vals['late_reports'];
                $total_punctual += $early;
                $htmltable .= ' 
            </td><td>' . $vals['district'].'</td>
            <td>' . $vals['total_facilities'] . '</td>
            <td>' . $early . '</td>
            <td>' . $vals['late_reports'] . '</td>
            <td>' . $vals['nonreported'] . '</td>
            <td>' . $vals['reported_percentage'] . '%</td></tr>';

        }
        $county_percentage = ($total_punctual + $ish['late_reports']) / $ish['facilities'] * 100;
        $county_percentage = number_format($county_percentage, 0);

        $htmltable .= '<tr style="background: #E9E9E3; border-top: solid 1px #ccc;">
        <td>Totals</td>
        <td>' . $ish['districts'] . ' Sub-Counties</td>
        <td>' . $ish['facilities'] . '</td>
        <td>' . $total_punctual . '</td>
        <td>' . $ish['late_reports'] . '</td>
        <td>' . $ish['nonreported'] . '</td>
        <td>' . $county_percentage . '%</td>

    </tr>';
    echo '
    <table class="data-table">
        <thead><tr>
            <th>County</th>
            <th>Sub-County</th>
            <th>No of facilities</th>
            <th>No reports before 10th</th>
            <th>No of late reports (10th-12th)</th>
            <th>No of non reporting facilities</th>
            <th>Overall reporting rate in % (no of reports submitted/expected no of reports)</th>
        </tr></thead>
        ' . $htmltable . '

    </table>';
}
public function rtk_summary_county($county, $year, $month) {
    date_default_timezone_set('EUROPE/moscow');
    $county_summary = array();
    $county_summary['districts'] = 0;
    $county_summary['punctual_reports'] = 0;
    $county_summary['facilities'] = array();
    $county_summary['reported'] = array();
    $county_summary['reported_percentage'] = array();
    $county_summary['nonreported'] = array();
    $county_summary['nonreported_percentage'] = array();
    $county_summary['late_reports'] = array();
    $county_summary['late_reports_percentage'] = array();
    $county_summary['district_summary'] = array();
/*
* countyname,numberofdistricts,numberoffacilities,reported,nonreported,late
*/

$q = 'SELECT * 
FROM counties, districts
WHERE counties.id = districts.county
AND counties.id = ' . $county . '';
$q_res = $this->db->query($q);
$districts_num = $q_res->num_rows();
$district_count = count($q_res->result_array());

foreach ($q_res->result_array() as $districts) {
    $dist_id = $districts['id'];
    $dist = $districts['district'];

        //$county_summary['district_summary']['district'] = $dist;
        //$county_summary['district_summary']['district_id'] = $dist_id;

    $district_summary = $this->rtk_summary_district($dist_id, $year, $month);           

        // echo "<pre>";
        // print_r($district_summary);


    array_push($county_summary['facilities'], $district_summary['total_facilities']);
    array_push($county_summary['reported'], $district_summary['reported']);
    array_push($county_summary['reported_percentage'], $district_summary['reported_percentage']);
    array_push($county_summary['nonreported'], $district_summary['nonreported']);
    array_push($county_summary['nonreported_percentage'], $district_summary['nonreported_percentage']);
    array_push($county_summary['late_reports'], $district_summary['late_reports']);
    array_push($county_summary['late_reports_percentage'], $district_summary['late_reports_percentage']);
    array_push($county_summary['punctual_reports'], 1);

        //$county_summary['facilities'] = $district_summary['total_facilities'];
        // $county_summary['reported'] += $district_summary['reported'];
        // $county_summary['reported_percentage'] += $district_summary['reported_percentage'];
        // $county_summary['nonreported'] += $district_summary['nonreported'];
        // $county_summary['nonreported_percentage'] += $district_summary['nonreported_percentage'];
        //$county_summary['punctual_reports'] = 1;
        // $county_summary['late_reports'] += $district_summary['late_reports'];

        // $county_summary['late_reports_percentage'] += $district_summary['late_reports_percentage'];
    array_push($county_summary['district_summary'], $district_summary);
        //     echo "<pre>";
        // print_r($county_summary['facilities']);
}

$county_summary['districts'] = $district_count;

$new_reported_percentage = 0;
foreach ($county_summary as $key=> $value) {
    $reported = $value['reported_percentage'];
    $new_reported +=$reported;              
}

$total_facilities = array_sum($county_summary['facilities']);
$total_reported = number_format(array_sum($county_summary['reported']),0);

$total_percentage = number_format(($total_reported / $total_facilities));

$county_summary['reported_percentage'] = $total_percentage;
$county_summary['facilities'] = array_sum($county_summary['facilities']);
$county_summary['reported'] = array_sum($county_summary['reported']);
$county_summary['nonreported'] = array_sum($county_summary['nonreported']);
$county_summary['nonreported_percentage'] = array_sum($county_summary['nonreported_percentage']);
$county_summary['late_reports'] = array_sum($county_summary['late_reports']);
$county_summary['late_reports_percentage'] = array_sum($county_summary['late_reports_percentage']);

        // $county_summary['reported_percentage'] = number_format($county_summary['reported_percentage'], 0);
        // echo "<pre>";
        // print_r($county_summary);

        // die();
        //$county_summary['reported_percentage'] = ($county_summary['reported_percentage'] / $county_summary['districts']);
        //$county_summary['reported_percentage'] = number_format($county_summary['reported_percentage'], 0);

$sortArray = array();
foreach ($county_summary['district_summary'] as $person) {
    foreach ($person as $key => $value) {
        if (!isset($sortArray[$key])) {
            $sortArray[$key] = array();
        }
        $sortArray[$key][] = $value;
    }
}

$orderby = "reported_percentage";

array_multisort($sortArray[$orderby], SORT_DESC, $county_summary['district_summary']);
return $county_summary;
}

public function switch_month($month = NULL, $redirect_url = NULL) {

    if ($month == 0) {
        $month = null;
    }        

    if ($redirect_url == NULL) {
        $redirect_url = 'home_controller';
    }            

    $url = 'rtk_management/';
    $url.=$redirect_url;

    $session_data = array("session_id" => $this->session->userdata('session_id'),
        "ip_address" => $this->session->userdata('ip_address'),
        "user_agent" => $this->session->userdata('user_agent'),
        "last_activity" => $this->session->userdata('last_activity'),
        "phone_no" => $this->session->userdata('phone_no'),
        "user_email" => $this->session->userdata('user_email'),
        "user_db_id" => $this->session->userdata('user_db_id'),
        "full_name" => $this->session->userdata('full_name'),
        "user_id" => $this->session->userdata('user_id'),
        "names" => $this->session->userdata('names'),
        "inames" => $this->session->userdata('inames'),
        "identity" => $this->session->userdata('identity'),
        "news" => $this->session->userdata('news'),         
        "drawing_rights" => $this->session->userdata('drawing_rights'),         
        "Month" => $month);


    $this->session->set_userdata($session_data);
    redirect($url);
}


public function rtk_summary_district($district, $year, $month) {
    $distname = districts::get_district_name($district);
    $districtname = $distname[0]['district'];
    $district_id = $district;
    $returnable = array();
    $nonreported;
    $reported_percentage;
    $late_percentage;


        // Sets the timezone and date variables for last day of previous month and this month
    date_default_timezone_set('EUROPE/moscow');
    $month = $month + 1;
    $prev_month = $month - 1;
    $last_day_current_month = date('Y-m-d', mktime(0, 0, 0, $month, 0, $year));
    $first_day_current_month = date('Y-m-', mktime(0, 0, 0, $month, 0, $year));
    $first_day_current_month .= '01';
    $lastday_thismonth = date('Y-m-d', strtotime("last day of this month"));
    $month -= 1;        
    $day10 = $year . '-' . $month . '-10';
    $day11 = $year . '-' . $month . '-11';
    $day12 = $year . '-' . $month . '-12';
    $late_reporting = 0;
    $text_month = date('F', strtotime($day10));

    $reporting_month = date('F,Y', strtotime('first day of previous month'));

    $q = "SELECT * 
    FROM facilities, districts, counties
    WHERE facilities.district = districts.id
    AND districts.county = counties.id
    AND districts.id = '$district' 
    AND facilities.rtk_enabled =1
    ORDER BY  `facilities`.`facility_name` ASC ";

    $q_res = $this->db->query($q);
    $total_reporting_facilities = $q_res->num_rows();

    $q1 = "SELECT DISTINCT lab_commodity_orders.facility_code, lab_commodity_orders.id,lab_commodity_orders.order_date
    FROM lab_commodity_orders, districts, counties
    WHERE districts.id = lab_commodity_orders.district_id
    AND districts.county = counties.id
    AND districts.id = '$district'
    AND lab_commodity_orders.order_date
    BETWEEN '$first_day_current_month'
    AND '$last_day_current_month'
    group by lab_commodity_orders.facility_code";

    $q_res1 = $this->db->query($q1);
    $new_q_res1 = $q_res1 ->result_array();
    $total_reported_facilities = $q_res1->num_rows();


    foreach ($q_res1->result_array() as $vals) {
        //            echo "<pre>";var_dump($vals);echo "</pre>";
        if ($vals['order_date'] == $day10 || $vals['order_date'] == $day11 || $vals['order_date'] == $day12) {
            $late_reporting += 1;
        //                echo "<pre>";var_dump($vals);echo "</pre>";
        }
    }

    $nonreported = $total_reporting_facilities - $total_reported_facilities;

    if ($total_reporting_facilities == 0) {
        $non_reported_percentage = 0;
    } else {
        $non_reported_percentage = $nonreported / $total_reporting_facilities * 100;
    }

    $non_reported_percentage = number_format($non_reported_percentage, 0);

    if ($total_reporting_facilities == 0) {
        $reported_percentage = 0;
    } else {
        $reported_percentage = $total_reported_facilities / $total_reporting_facilities * 100;
    }

    $reported_percentage = number_format($reported_percentage, 0);

    if ($total_reporting_facilities == 0) {
        $late_percentage = 0;
    } else {
        $late_percentage = $late_reporting / $total_reporting_facilities * 100;
    }


    $late_percentage = number_format($late_percentage, 0);
    if ($total_reported_facilities > $total_reporting_facilities) {
        $reported_percentage = 100;
        $nonreported = 0;
        $total_reported_facilities = $total_reporting_facilities;
    }
    if ($late_reporting > $total_reporting_facilities) {
        $late_reporting = $total_reporting_facilities;
        $late_percentage = $reported_percentage;
    }
    $returnable = array('reporting_month'=>$reporting_month,'Month' => $text_month, 'Year' => $year, 'district' => $districtname, 'district_id' => $district_id, 'total_facilities' => $total_reporting_facilities, 'reported' => $total_reported_facilities, 'reported_percentage' => $reported_percentage, 'nonreported' => $nonreported, 'nonreported_percentage' => $non_reported_percentage, 'late_reports' => $late_reporting, 'late_reports_percentage' => $late_percentage);
    return $returnable;
}

public function partner_summary($county, $year, $month,$partner_id) {                
    $returnable = array();
    $nonreported;
    $reported_percentage;
    $late_percentage;

        // Sets the timezone and date variables for last day of previous month and this month
    date_default_timezone_set('EUROPE/moscow');
    $month = $month + 1;
    $prev_month = $month - 1;
    $last_day_current_month = date('Y-m-d', mktime(0, 0, 0, $month, 0, $year));
    $first_day_current_month = date('Y-m-', mktime(0, 0, 0, $month, 0, $year));
    $first_day_current_month .= '1';
    $lastday_thismonth = date('Y-m-d', strtotime("last day of this month"));
    $month -= 1;
    $day10 = $year . '-' . $month . '-10';
    $day11 = $year . '-' . $month . '-11';
    $day12 = $year . '-' . $month . '-12';
    $late_reporting = 0;
    $text_month = date('F', strtotime($day10));

    $q = "SELECT * 
    FROM facilities, districts, counties
    WHERE facilities.district = districts.id
    AND districts.county = counties.id
    and counties.id = '$county'
    AND facilities.district = districts.id 
    and facilities.partner = '$partner_id'
    AND facilities.rtk_enabled =1
    ORDER BY  `facilities`.`facility_name` ASC ";

    $q_res = $this->db->query($q);
    $total_reporting_facilities = $q_res->num_rows();        
    $q = "SELECT DISTINCT lab_commodity_orders.facility_code, lab_commodity_orders.id,lab_commodity_orders.order_date
    FROM lab_commodity_orders, districts, counties,facilities
    WHERE districts.id = lab_commodity_orders.district_id
    AND districts.county = counties.id
    and counties.id = '$county'
    AND facilities.district = districts.id 
    and facilities.partner = '$partner_id'
    and lab_commodity_orders.facility_code = facilities.facility_code
    AND lab_commodity_orders.order_date
    BETWEEN '$first_day_current_month'
    AND '$last_day_current_month'";       
    $q_res1 = $this->db->query($q);
    $total_reported_facilities = $q_res1->num_rows();    

    foreach ($q_res1->result_array() as $vals) {
        //            echo "<pre>";var_dump($vals);echo "</pre>";
        if ($vals['order_date'] == $day10 || $vals['order_date'] == $day11 || $vals['order_date'] == $day12) {
            $late_reporting += 1;
        //                echo "<pre>";var_dump($vals);echo "</pre>";
        }
    }

    $nonreported = $total_reporting_facilities - $total_reported_facilities;

    if ($total_reporting_facilities == 0) {
        $non_reported_percentage = 0;
    } else {
        $non_reported_percentage = $nonreported / $total_reporting_facilities * 100;
    }

    $non_reported_percentage = number_format($non_reported_percentage, 0);

    if ($total_reporting_facilities == 0) {
        $reported_percentage = 0;
    } else {
        $reported_percentage = $total_reported_facilities / $total_reporting_facilities * 100;
    }

    $reported_percentage = number_format($reported_percentage, 0);

    if ($total_reporting_facilities == 0) {
        $late_percentage = 0;
    } else {
        $late_percentage = $late_reporting / $total_reporting_facilities * 100;
    }


    $late_percentage = number_format($late_percentage, 0);
    if ($total_reported_facilities > $total_reporting_facilities) {
        $reported_percentage = 100;
        $nonreported = 0;
        $total_reported_facilities = $total_reporting_facilities;
    }
    if ($late_reporting > $total_reporting_facilities) {
        $late_reporting = $total_reporting_facilities;
        $late_percentage = $reported_percentage;
    }
    $returnable = array('Month' => $text_month, 'Year' => $year,'total_facilities' => $total_reporting_facilities, 'reported' => $total_reported_facilities, 'reported_percentage' => $reported_percentage, 'nonreported' => $nonreported, 'nonreported_percentage' => $non_reported_percentage, 'late_reports' => $late_reporting, 'late_reports_percentage' => $late_percentage);
    return $returnable;
}

        //Logging Function
public function logData($reference, $object) {
    $timestamp = time();
    $user_id = $this->session->userdata('user_id');
    $sql = "INSERT INTO `rtk_logs`(`id`, `user_id`, `reference`,`reference_object`,`timestamp`) VALUES (NULL,'$user_id','$reference','$object','$timestamp')";
    $this->db->query($sql);
}
        //Update the Average Monthly Consumption
private function update_amc($mfl) {
    $last_update = time();        
    $amc = 0;
    for ($commodity_id = 4; $commodity_id <= 6; $commodity_id++) {
        $amc = $this->_facility_amc($mfl, $commodity_id);
        echo " _ $amc<br/>";
        // $q = "select * from facility_amc where facility_code='$mfl' and commodity_id='$commodity_id' ";
        // $resq = $this->db->query($q)->result_array();
        // $count = count($resq);
        //         // if($count>0){
        //     $sql = "update facility_amc set amc = '$amc', last_update = '$last_update' where facility_code = '$mfl' and commodity_id='$commodity_id'";
        //     $res = $this->db->query($sql); 
        // }else{

        $sql = "INSERT INTO `facility_amc_b`(`facility_code`, `commodity_id`, `amc`,`last_update`) VALUES ('$mfl','$commodity_id','$amc','$last_update')";
        $res = $this->db->query($sql);
        // }

    }
}

        //Facility Amc
public function _facility_amc($mfl_code, $commodity = null) {
    $three_months_ago = date("Y-m-", strtotime("-3 Month "));
    $three_months_ago .='1';
    $end_date = date("Y-m-", strtotime("-1 Month "));
    $end_date .='31';
        // echo "Three months ago = $three_months_ago and End Date =$end_date ";die();
    $q = "SELECT avg(lab_commodity_details.newqused) as avg_used
    FROM  lab_commodity_details,lab_commodity_orders
    WHERE lab_commodity_orders.id =  lab_commodity_details.order_id
    AND lab_commodity_details.facility_code =  $mfl_code
    AND lab_commodity_orders.order_date BETWEEN '$three_months_ago' AND '$end_date'"; 
        // and lab_commodity_details.commodity_id between 4 and 6";

    if (isset($commodity)) {
        $q.=" AND lab_commodity_details.commodity_id = $commodity";
    } else {
        $q.=" AND lab_commodity_details.commodity_id = 4";
    }
    echo "$q";
    $res = $this->db->query($q);
    $result = $res->result_array();
    $result = $result[0]['avg_used'];
    $result = number_format($result, 0);
    return $result;
}

function facility_amc_compute($zone, $a, $b) {
    $sql = "SELECT 
    facilities.facility_code
    FROM
    facilities,districts, counties
    WHERE
    facilities.rtk_enabled = '1'
    and facilities.district = districts.id
    and districts.county = counties.id

    AND counties.zone = '$zone' limit $a, $b";
    $res = $this->db->query($sql);
    $facility = $res->result_array();
    foreach ($facility as $value) {
        $fcode = $value['facility_code'];
        $this->update_amc($fcode);
    }
}

        //             //Update the Average Monthly Consumption
        //    private function update_amc($mfl) {
        //         $last_update = time();        
        //         $amc_3 = 0;
        //         $amc_6 = 0;
        //         for ($commodity_id = 1; $commodity_id <= 7; $commodity_id++) {
        //             $amc = $this->_facility_amc($mfl, $commodity_id);
        //             $amc_3 =$amc['amc_3'];
        //             $amc_6 =$amc['amc_6'];
        //             echo "Facility  $mfl : Commodity $commodity_id,  _ $amc_3 $amc_6<br/>";
        //                     // $q = "select * from facility_amc where facility_code='$mfl' and commodity_id='$commodity_id' ";
        //                     // $resq = $this->db->query($q)->result_array();
        //                     // $count = count($resq);
        //                     //         // if($count>0){
        //                     //     $sql = "update facility_amc set amc = '$amc', last_update = '$last_update' where facility_code = '$mfl' and commodity_id='$commodity_id'";
        //                     //     $res = $this->db->query($sql); 
        //                     // }else{

        //                 $sql = "INSERT INTO `facility_amc_b`(`facility_code`, `commodity_id`, `amc`,`amc_6`,`last_update`) VALUES ('$mfl','$commodity_id','$amc_3','$amc_6','$last_update')";
        //                 $res = $this->db->query($sql);
        //                     // }

        //         }
        //     }

        //             //Facility Amc
        //     public function _facility_amc($mfl_code, $commodity = null) {
        //         $six_months_ago = date("Y-m-", strtotime("-5 Month "));
        //         $three_months_ago = date("Y-m-", strtotime("-2 Month "));
        //         $three_months_ago .='01';
        //         $six_months_ago .='01';
        //         $end_date = date("Y-m-", strtotime("-0 Month "));
        //         $end_date .='31';
        //                 // echo "Three months ago = $six_months_ago and End Date =$end_date ";die();
        //         //         $q = "SELECT 
        //         //     commodity_id,
        //         //     lab_commodity_details.facility_code,
        //         //     COUNT(lab_commodity_details.q_used) AS no_of_count,
        //         //     CASE
        //         //         WHEN COUNT(q_used) = 3 AND SUM(q_used)> 0 THEN (SUM(q_used)) / 3
        //         //         WHEN COUNT(q_used) = 2 AND SUM(q_used)> 0 THEN (SUM(q_used)) / 2
        //         //         WHEN COUNT(q_used) = 1 AND SUM(q_used)> 0 THEN SUM(q_used)
        //         //         ELSE COUNT(q_used) = 0
        //         //     END AS q_used
        //         // FROM
        //         //     lab_commodity_details,
        //         //     lab_commodity_orders
        //         // WHERE
        //         //     lab_commodity_orders.id = lab_commodity_details.order_id
        //         //         AND lab_commodity_details.facility_code = '$mfl_code'
        //         //         AND lab_commodity_orders.order_date BETWEEN '2016-01-1' AND '2016-03-31'
        //         //         AND lab_commodity_details.commodity_id = 1"; 
        //         //                 // and lab_commodity_details.commodity_id between 4 and 6";
        //         //                 // echo "$q";die();
        //         //                 // if (isset($commodity)) {
        //         //                 //     $q.=" AND lab_commodity_details.commodity_id = $commodity";
        //         //                 // } else {
        //         //                 //     $q.=" AND lab_commodity_details.commodity_id = 4";
        //         //                 // }
        //         //         echo "$q";
        //         //         $res = $this->db->query($q);
        //         //         $result = $res->result_array();
        //         //         $result = $result[0]['q_used'];
        //         //        echo $result[0]['no_of_count'];
        //         //         $result = number_format($result, 0);
        //         $where = '';
        //         if (isset($commodity)) {
        //             $where.=" AND lab_commodity_details.commodity_id = '$commodity'";
        //         } else {
        //             $where.=" AND lab_commodity_details.commodity_id = 4";
        //         }
        //         $q ="SELECT 
        //     commodity_id,
        //     avg(q_used) AS total
        // FROM
        //     lab_commodity_details,
        //     lab_commodity_orders
        // WHERE
        //     lab_commodity_orders.id = lab_commodity_details.order_id
        //         AND lab_commodity_details.facility_code = '$mfl_code' $where
        //         AND lab_commodity_orders.order_date BETWEEN '$three_months_ago' AND '$end_date'
        //         AND q_used >= 0    
        //                   ";
        //                 // echo "$q";
        //         $res = $this->db->query($q);
        //         $result = $res->result_array();

        //         $q_6 ="SELECT 
        //     commodity_id,
        //     avg(q_used) AS total
        // FROM
        //     lab_commodity_details,
        //     lab_commodity_orders
        // WHERE
        //     lab_commodity_orders.id = lab_commodity_details.order_id
        //         AND lab_commodity_details.facility_code = '$mfl_code' $where
        //         AND lab_commodity_orders.order_date BETWEEN '$six_months_ago' AND '$end_date'
        //         AND q_used >= 0    
        //                   ";
        //                 // echo "$q";
        //         $result_6 = $this->db->query($q_6)->result_array();
        //                 // $ = $res->result_array();

        //                 // $new_value = null;
        //         $total_3 = intval($result[0]['total']);
        //         $total_6 = intval($result_6[0]['total']);
        //                 // $count = intval($result[0]['count']);
        //                 // switch ($count) {
        //                 //     case 3:
        //                 //         $new_value = $total/$count;
        //                 //         break;
        //                 //     case 2:
        //                 //         $new_value = $total/$count;
        //                 //         break;
        //                 //     case 1:
        //                 //         $new_value = $total/$count;
        //                 //         break;
        //                 //     default:
        //                 //         $new_value = 0;
        //                 //         break;
        //                 // }
        //         $result_3 = number_format($total_3, 0);
        //         $result_6 = number_format($total_6, 0);
        //         return $result[] = array('amc_3' =>$result_3,'amc_6' =>$result_6, );;
        //     }

        // function facility_amc_compute($zone, $a, $b) {
        //         $sql = "SELECT 
        //     facilities.facility_code
        // FROM
        //     facilities,districts, counties
        // WHERE
        //     facilities.rtk_enabled = '1'
        //     and facilities.district = districts.id
        //     and districts.county = counties.id
        //     and counties.id in (39,41)

        //         AND counties.zone = '$zone' limit $a, $b";
        //         $res = $this->db->query($sql);
        //         $facility = $res->result_array();
        //         foreach ($facility as $value) {
        //             $fcode = $value['facility_code'];
        //             $this->update_amc($fcode);
        //         }
        //  }
        //Update the Number of Reports Online
function _update_reports_count($state,$county,$district,$partner=null){ 
    $month = date('mY',time());  
    $q = "select * from  rtk_county_percentage where month='$month' and county_id = '$county'";
    $q1 = "select * from rtk_district_percentage where month='$month' and district_id = '$district'";
    $res_county = $this->db->query($q)->result_array();        
    $res_district = $this->db->query($q1)->result_array();
    if(count($res_county)==0){
        $this->get_district_percentages_month($month);
    }
    if(count($res_district)==0) {
        $this->get_county_percentages_month($month);
    }


    if($state=="add"){
        $sql = "update rtk_county_percentage set reported = (reported + 1) where month='$month' and county_id = '$county'";
        $sql1 = "update rtk_district_percentage set reported = (reported + 1) where month='$month' and district_id = '$district'";
        $sql2 = "update rtk_partner_percentage set reported = (reported + 1) where month='$month' and partner_id = '$partner'";
    }elseif ($state=="remove") {
        $sql = "update rtk_county_percentage set reported = (reported - 1) where month='$month' and county_id = '$county'";
        $sql1 = "update rtk_district_percentage set reported = (reported - 1) where month='$month' and district_id = '$district'";                
        $sql2 = "update rtk_partner_percentage set reported = (reported - 1) where month='$month' and partner_id = '$partner'";

    }

    $q2 = "update rtk_partner_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and partner_id = '$partner'";

    $this->db->query($sql);
    $this->db->query($sql1);
    if($partner ==null){

    }else{
        $this->db->query($sql2); 
        $this->db->query($q2);
    }

    $q = "update rtk_district_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and district_id = '$district'";                
    $q1 = "update rtk_county_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and county_id = '$county'";


    $this->db->query($q);
    $this->db->query($q1);


} 

        //Function for the Amounts Allocated versus those Requested 
function _requested_vs_allocated($year, $month, $county = null) {


    $firstdate = $year . '-' . $month . '-01';
    $firstday = date("Y-m-d", strtotime("$firstdate +1 Month "));

    $month = date("m", strtotime("$firstdate +1 Month "));
    $year = date("Y", strtotime("$firstdate +1 Month "));
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $returnable = array();

    $common_q = "SELECT
    lab_commodities.commodity_name,
    sum(lab_commodity_details.beginning_bal) as sum_opening, 
    sum(lab_commodity_details.q_received) as sum_received, 
    sum(lab_commodity_details.q_used) as sum_used, 
    sum(lab_commodity_details.no_of_tests_done) as sum_tests, 
    sum(lab_commodity_details.positive_adj) as sum_positive, 
    sum(lab_commodity_details.negative_adj) as sum_negative,
    sum(lab_commodity_details.losses) as sum_losses,
    sum(lab_commodity_details.closing_stock) as sum_closing_bal,
    sum(lab_commodity_details.q_requested) as sum_requested, 
    sum(lab_commodity_details.allocated) as sum_allocated,
    sum(lab_commodity_details.allocated) as sum_days,
    sum(lab_commodity_details.q_expiring) as sum_expiring
    FROM 
    lab_commodities,
    lab_commodity_details,
    districts, 
    facilities,
    counties 
    WHERE lab_commodity_details.commodity_id = lab_commodities.id                 
    AND lab_commodity_details.facility_code = facilities.facility_code
    and facilities.district = districts.id 
    AND districts.county = counties.id 
    AND lab_commodity_details.created_at BETWEEN  '$firstday' AND  '$lastdate'
    AND lab_commodities.category  = 1";
    if (isset($county)) {
        $common_q.= ' AND counties.id =' . $county;
    }

    $common_q.= ' group by lab_commodities.id';

        // echo "$common_q";die();

    $res = $this->db->query($common_q);        

    $result = $res->result_array();
        // echo "<pre>";
        // print_r($result);
        // die();
        // array_push($returnable, $result);


        //         $q = $common_q . " AND lab_commodities.id = 1";
        //         $res = $this->db->query($q);
        //         $result = $res->result_array();
        //         array_push($returnable, $result[0]);

        //         $q2 = $common_q . " AND lab_commodities.id = 2";
        //         $res2 = $this->db->query($q2);
        //         $result2 = $res2->result_array();
        //         array_push($returnable, $result2[0]);        

        //         $q4 = $common_q . " AND lab_commodities.id = 4";
        //         $res4 = $this->db->query($q4);
        //         $result4 = $res4->result_array();
        //         array_push($returnable, $result4[0]);

        //         $q5 = $common_q . " AND lab_commodities.id = 5";
        //         $res5 = $this->db->query($q5);
        //         $result5 = $res5->result_array();
        //         array_push($returnable, $result5[0]);

        //         $q6 = $common_q . " AND lab_commodities.id = 6";
        //         $res6 = $this->db->query($q6);
        //         $result6 = $res6->result_array();
        //         array_push($returnable, $result6[0]);
        // $returnable = $res->result_array();
        // echo"<pre>";print_r($returnable);die;
    return $result;
}


function _requested_vs_allocated_partner($year, $month, $partner_id) {


    $firstdate = $year . '-' . $month . '-01';
    $firstday = date("Y-m-d", strtotime("$firstdate +1 Month "));

    $month = date("m", strtotime("$firstdate +1 Month "));
    $year = date("Y", strtotime("$firstdate +1 Month "));
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $returnable = array();
    $common_q = "select    
    lab_commodities.commodity_name,
    sum(lab_commodity_details.beginning_bal) as sum_opening, 
    sum(lab_commodity_details.q_received) as sum_received, 
    sum(lab_commodity_details.q_used) as sum_used, 
    sum(lab_commodity_details.no_of_tests_done) as sum_tests, 
    sum(lab_commodity_details.positive_adj) as sum_positive, 
    sum(lab_commodity_details.negative_adj) as sum_negative,
    sum(lab_commodity_details.losses) as sum_losses,
    sum(lab_commodity_details.closing_stock) as sum_closing_bal,
    sum(lab_commodity_details.q_requested) as sum_requested, 
    sum(lab_commodity_details.allocated) as sum_allocated,
    sum(lab_commodity_details.allocated) as sum_days,
    sum(lab_commodity_details.q_expiring) as sum_expiring,
    facilities.partner,
    lab_commodities.commodity_name
    from
    facilities,
    lab_commodity_details,
    lab_commodities
    where
    facilities.partner = '$partner_id'
    and lab_commodity_details.facility_code = facilities.facility_code
    and lab_commodity_details.commodity_id = lab_commodities.id
    and lab_commodities.category = '1'
    and lab_commodity_details.created_at between '$firstday' AND  '$lastdate'
    group by lab_commodities.id";

        //echo "$common_q";die();
        //        $common_q = "SELECT
        //        lab_commodities.commodity_name,
        //        sum(lab_commodity_details.beginning_bal) as sum_opening, 
        //        sum(lab_commodity_details.q_received) as sum_received, 
        //        sum(lab_commodity_details.q_used) as sum_used, 
        //        sum(lab_commodity_details.no_of_tests_done) as sum_tests, 
        //        sum(lab_commodity_details.positive_adj) as sum_positive, 
        //        sum(lab_commodity_details.negative_adj) as sum_negative,
        //        sum(lab_commodity_details.losses) as sum_losses,
        //        sum(lab_commodity_details.closing_stock) as sum_closing_bal,
        //        sum(lab_commodity_details.q_requested) as sum_requested, 
        //        sum(lab_commodity_details.allocated) as sum_allocated,
        //        sum(lab_commodity_details.allocated) as sum_days,
        //        sum(lab_commodity_details.q_expiring) as sum_expiring
        //        FROM 
        //            lab_commodities,
        //            lab_commodity_details,
        //            districts, 
        //            counties,facilities 
        //        WHERE lab_commodity_details.commodity_id = lab_commodities.id                 
        //        AND lab_commodity_details.facility_code = facilities.facility_code
        //        AND facilities.partner = '$partner_id'        
        //        AND lab_commodity_details.created_at BETWEEN  '$firstday' AND  '$lastdate'";
        //                //AND lab_commodities.id in (select lab_commodities.id from lab_commodities,lab_commodity_categories 
        //                  //  where lab_commodities.category = lab_commodity_categories.id and lab_commodity_categories.active = '1')";

        // $common_q.= ' group by lab_commodities.id';

        // echo "$common_q";die();

    $res = $this->db->query($common_q);        

    $result = $res->result_array();
        // echo "<pre>";
        // print_r($result);
        // die();
        // array_push($returnable, $result);


        //         $q = $common_q . " AND lab_commodities.id = 1";
        //         $res = $this->db->query($q);
        //         $result = $res->result_array();
        //         array_push($returnable, $result[0]);

        //         $q2 = $common_q . " AND lab_commodities.id = 2";
        //         $res2 = $this->db->query($q2);
        //         $result2 = $res2->result_array();
        //         array_push($returnable, $result2[0]);        

        //         $q4 = $common_q . " AND lab_commodities.id = 4";
        //         $res4 = $this->db->query($q4);
        //         $result4 = $res4->result_array();
        //         array_push($returnable, $result4[0]);

        //         $q5 = $common_q . " AND lab_commodities.id = 5";
        //         $res5 = $this->db->query($q5);
        //         $result5 = $res5->result_array();
        //         array_push($returnable, $result5[0]);

        //         $q6 = $common_q . " AND lab_commodities.id = 6";
        //         $res6 = $this->db->query($q6);
        //         $result6 = $res6->result_array();
        //         array_push($returnable, $result6[0]);
        // $returnable = $res->result_array();
        // echo"<pre>";print_r($returnable);die;
    return $result;
}

function county_reporting_percentages($county, $year, $month) {
    $q = 'SELECT counties.id as county_id,counties.county as countyname,districts.district,districts.county,districts.id as district_id
    FROM  `districts`,`counties` 
    WHERE districts.county = counties.id 
    AND  counties.`id` =' . $county . '';
    $districts = array();
    $reported = array();
    $nonreported = array();
    $query = $this->db->query($q);
    foreach ($query->result_array() as $val) {
        array_push($districts, $val['district']);
        $percentage_reported = $this->district_reporting_percentages($val['district_id'], $year, $month);
        $percentage_reported = $percentage_reported + 0;
        if ($percentage_reported > 100) {
            $percentage_reported = 100;
        }
        array_push($reported, $percentage_reported);
        $percentage_non_reported = 100 - $percentage_reported;
        array_push($nonreported, $percentage_non_reported);
    }

    $districts = json_encode($districts);
    $reported = json_encode($reported);
    $nonreported = json_encode($nonreported);

    $data['districts'] = $districts;
    $data['reported'] = $reported;
    $data['nonreported'] = $nonreported;
        //        $this->load->view('rtk/rtk/rca/county_reporting_view', $data);
    return $data;
}

function district_reporting_percentages($district, $year, $month) {


    $firstdate = $year . '-' . $month . '-01';
    $firstday = date("Y-m-d", strtotime("$firstdate +1 Month "));
    $month = date("m", strtotime("$firstdate +1 Month "));
    $year = date("Y", strtotime("$firstdate +1 Month "));
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $lastday = date('Y-m-d', strtotime("last day of previous month"));
    $q = 'SELECT * FROM  `facilities` WHERE  `district` =' . $district . ' AND  `rtk_enabled` =1';
    $q2 = "SELECT DISTINCT lab_commodity_orders.facility_code, facilities.facility_code, facilities.facility_name, lab_commodity_orders.id, lab_commodity_orders.district_id, lab_commodity_orders.order_date
    FROM lab_commodity_orders, districts, counties, facilities
    WHERE districts.id = lab_commodity_orders.district_id
    AND districts.county = counties.id
    AND facilities.facility_code = lab_commodity_orders.facility_code
    AND districts.id = $district
    AND lab_commodity_orders.order_date
    BETWEEN  '$firstday'
    AND  '$lastdate'";
    $query = $this->db->query($q);
    $query2 = $this->db->query($q2);
    $percentage = $query2->num_rows() / $query->num_rows() * 100;
    $percentage = number_format($percentage, $decimals = 0);
    return $percentage;
}

function _districts_in_county($county) {
    $q = 'SELECT id,district FROM  `districts` WHERE  `county` =' . $county;
    $this->db->query($q);
    $res = $this->db->query($q);
    $returnable = $res->result_array();
    return $returnable;
}

function _facilities_in_county($county, $type = null) {

    $q = 'SELECT 
    facilities.id as facil_id,facilities.facility_name,facilities.owner,facilities.facility_code, facilities.rtk_enabled,
    districts.district as districtname, districts.id as district_id,
    counties.county, counties.id 
    from districts,counties,facilities 
    where facilities.district = districts.id
    AND districts.county = counties.id
    AND counties.id =' . $county . '
    ORDER BY  facilities.facility_name ASC ';
    $result = $this->db->query($q)->result_array();
    return $result;
}

function _get_dmlt_districts($dmlt) {
    $sql = 'SELECT DISTINCT districts.district, districts.id
    FROM dmlt_districts, districts
    WHERE districts.id = dmlt_districts.district
    AND dmlt_districts.dmlt =' . $dmlt;
    $res = $this->db->query($sql);
    $returnable = $res->result_array();
    return $returnable;
}
function _users_in_county($county, $type = null) {
    $q = 'SELECT user.fname, user.lname, user.email, user.id AS id, user.telephone, districts.id AS district_id, districts.district 
    FROM user, districts
    WHERE user.district = districts.id
    AND county_id =' . $county;

    if ($type) {
        $q .= ' AND (usertype_id =' . $type . ' OR usertype_id = 5 )';
    }
    $res = $this->db->query($q);
    $returnable = $res->result_array();
    return $returnable;
}
public function show_dmlt_districts($dmlt, $mode = NULL) {
    $districts = $this->_get_dmlt_districts($dmlt);
    if ($mode == '') {

    }
    foreach ($districts as $value) {
        echo '<a href="' . base_url() . 'rtk_management/remove_dmlt_from_district/' . $dmlt . '/' . $value['id'] . '" style="color: #DD6A6A;">' . $value['district'] . '</a>, ';
    }
}

public function dmlt_district_action() {
    $action = mysql_real_escape_string($_POST['action']);
    $dmlt = mysql_real_escape_string($_POST['dmlt_id']);
    $district = mysql_real_escape_string($_POST['dmlt_district']);

    if ($action == 'add') {
        $this->_add_dmlt_to_district($dmlt, $district);
    } elseif ($action == 'remove') {
        $this->_remove_dmlt_from_district($dmlt, $district);
    }
    echo "Sub-County Added Successfully";        
}

function _add_dmlt_to_district($dmlt, $district) {
    $sql = "INSERT INTO `dmlt_districts` (`id`, `dmlt`, `district`) VALUES (NULL, $dmlt, $district);";
    $this->db->query($sql);
    $object_id = $this->db->insert_id();
    $this->logData('1', $object_id);
}

function _remove_dmlt_from_district($dmlt, $district, $redirect_url) {
    $sql = "DELETE FROM `dmlt_districts` WHERE  dmlt=$dmlt AND district = $district";

    $object_id = $dmlt;
    $this->logData('2', $object_id);
    $this->db->query($sql);
}

public function deactivate_facility($facility_code) {
    $this->db->query('UPDATE `facilities` SET  `rtk_enabled` =  0 WHERE  `facility_code` =' . $facility_code . '');
    $q = $this->db->query('SELECT * FROM  `facilities` WHERE  `facility_code` =' . $facility_code . '');
    $facil = $q->result_array();
    $object_id = $facil[0]['id'];
    $this->logData('24', $object_id);
    $sql = "select district from facilities where facility_code = '$facility_code'";
    $res = $this->db->query($sql)->result_array();
    foreach ($res as $key => $value) {
        $district = $value['district'];
    }
    $sql1 = "select county from districts where id = '$district'";
    $res1 = $this->db->query($sql1)->result_array();
    foreach ($res1 as $key => $value) {
        $county = $value['county'];
    }
    $this->_update_facility_count('remove',$county,$district);        
    redirect('rtk_management/county_admin/facilities');
}

function _update_facility_count($state,$county,$district){
    $month = date('mY',time());
    $q = "select * from  rtk_county_percentage where month='$month' and county_id = '$county'";
    $q1 = "select * from rtk_district_percentage where month='$month' and district_id = '$district'";
    $res_county = $this->db->query($q)->result_array();        
    $res_district = $this->db->query($q1)->result_array();
    if(count($res_county)==0){
        $this->get_district_percentages_month($month);
    }
    if(count($res_district)==0) {
        $this->get_county_percentages_month($month);
    }

    if($state=="add"){
        $sql = "update rtk_county_percentage set facilities = (facilities + 1) where month='$month' and county_id = '$county'";
        $sql1 = "update rtk_district_percentage set facilities = (facilities + 1) where month='$month' and district_id = '$district'";
    }elseif ($state=="remove") {
        $sql = "update rtk_county_percentage set facilities = (facilities - 1) where month='$month' and county_id = '$county'";
        $sql1 = "update rtk_district_percentage set facilities = (facilities - 1) where month='$month' and district_id = '$district'";                
    }
    $this->db->query($sql);
    $this->db->query($sql1);
    $q = "update rtk_district_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and district_id = '$district'";                
    $q1 = "update rtk_county_percentage set percentage = round(((reported/facilities)*100),0) where month='$month' and county_id = '$county'";
    $this->db->query($q);
    $this->db->query($q1);
}

public function activate_facility($facility_code) {
    $this->db->query('UPDATE `facilities` SET  `rtk_enabled` = 1 WHERE  `facility_code` =' . $facility_code . '');
    $q = $this->db->query('SELECT * FROM  `facilities` WHERE  `facility_code` =' . $facility_code . '');
    $facil = $q->result_array();
    $object_id = $facil[0]['id'];
    $this->logData('21', $object_id);
    $sql = "select district from facilities where facility_code = '$facility_code'";
    $res = $this->db->query($sql)->result_array();
    foreach ($res as $key => $value) {
        $district = $value['district'];
    }
    $sql1 = "select county from districts where id = '$district'";
    $res1 = $this->db->query($sql1)->result_array();
    foreach ($res1 as $key => $value) {
        $county = $value['county'];
    }
    $this->_update_facility_count('add',$county,$district);        
    redirect('rtk_management/county_admin/facilities');
}


function reporting_rates($County = NULL,$year = NULL, $month = NULL) {
    if ($year == NULL) {
        $year = date('Y', time());
        $month = date('m', time());
    }

    if($County!=NULL){
        $from = ',districts,counties';
        $conditions .="and lab_commodity_orders.district_id= districts.id and districts.county = counties.id and counties.id = $County";
    }
    $firstdate = $year . '-' . $month . '-01';
    $month = date("m", strtotime("$firstdate"));
    $year = date("Y", strtotime("$firstdate"));
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;
    $firstdate = $year . '-' . $month . '-01';

    $sql = "select 
    lab_commodity_orders.order_date as order_date,
    count(distinct lab_commodity_orders.facility_code) as count
    from
    lab_commodity_orders $from
    WHERE
    lab_commodity_orders.order_date between '$firstdate' and '$lastdate' $conditions
    Group BY lab_commodity_orders.order_date";          
    $res = $this->db->query($sql);
    return ($res->result_array());
}

function rtk_logs($user = NULL, $UserType = NULL, $Action = NULL, $SinceDate = NULL, $FromDate = NULL,$limit=null) {
    $conditions = '';
    $conditions = (isset($user)) ? $conditions . " AND user.id = $user" : $conditions . ' ';
    $conditions = (isset($Action)) ? $conditions . " AND rtk_logs_reference.action = $Action" : $conditions . ' ';
    $conditions = (isset($sincedate)) ? $conditions . "AND rtk_logs.timestamp > = $sincedate" : $conditions . ' ';
    $conditions = (isset($enddate)) ? $conditions . "AND rtk_logs.timestamp < = $enddate" : $conditions . ' ';

    $sql = "SELECT *
    FROM rtk_logs,rtk_logs_reference,user
    WHERE rtk_logs.reference = rtk_logs_reference.id
    AND rtk_logs.user_id = user.id
    $conditions   
    ORDER BY `rtk_logs`.`id` DESC  $limit";    
    $res = $this->db->query($sql);
    return ($res->result_array());
}

public function facility_profile($mfl) {
    $data = array();
    $lastday = date('Y-m-d', strtotime("last day of previous month"));
    $County = $this->session->userdata('county_name');    
    $Countyid = $this->session->userdata('county_id');    
    $districts = districts::getDistrict($Countyid);   
    $sql = "select * from facilities where facility_code=$mfl"; 
    $facility = $this->db->query($sql)->result_array();        
    $mfl =  $facility[0]['facility_code'];       
    $data['reports'] = $this->_monthly_facility_reports($mfl);
        // $data['reports']= str_replace('(', '-', $data['reports']);
        // $data['reports'] = str_replace(')', '', $data['reports']);
        // echo "<pre>"; print_r($data['reports']);die();

    $data['facility_county'] = $data['reports'][0]['county'];
    $data['facility_district'] = $data['reports'][0]['district'];
    $data['district_id'] = $data['reports'][0]['district_id'];

    if($data['district_id']==null){
        $new_dist =  $facility[0]['district'];       
        $data['facilities_in_district'] = json_encode($this->_facilities_in_district($new_dist));
    }else{
        $data['facilities_in_district'] = json_encode($this->_facilities_in_district($data['district_id']));
    }    
    $data['facilities_in_district'] = str_replace('"', "'", $data['facilities_in_district']);

    $data['county_id'] = $Countyid;
        //$data['county_id'] = $data['reports'][0]['county_id'];

    $data['districts'] = $districts;
    $data['county'] = $County;
    $data['mfl'] = $mfl;
    $data['countyid'] = $Countyid;
    $data['title'] = $facility[0]['facility_name'] . '-' . $mfl;
    $data['facility_name'] = $facility[0]['facility_name'];
    $data['banner_text'] = 'Facility Profile: ' . $facility[0]['facility_name'] . '-' . $mfl;
    $data['content_view'] = "rtk/rtk/shared/facility_profile_view";

    $this->load->view("rtk/template", $data);
}

private function _monthly_facility_reports($mfl, $monthyear = null) {
    $conditions = '';
    if (isset($monthyear)) {
        $year = substr($monthyear, -4);
        $month = substr_replace($monthyear, "", -4);
        $firstdate = $year . '-' . $month . '-01';
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $lastdate = $year . '-' . $month . '-' . $num_days;
        $conditions=" AND lab_commodity_orders.order_date
        BETWEEN  '$firstdate'
        AND  '$lastdate'";
    }

    $rtk_sql = "select distinct lab_commodity_orders.order_date,lab_commodity_orders.compiled_by,lab_commodity_orders.id,
    facilities.facility_name,districts.district,districts.id as district_id, counties.county,counties.id as county_id
    FROM lab_commodity_orders,facilities,districts,counties
    WHERE lab_commodity_orders.facility_code = facilities.facility_code
    AND facilities.district = districts.id
    AND counties.id = districts.county
    AND facilities.facility_code =$mfl $conditions 
    group by lab_commodity_orders.order_date Order by lab_commodity_orders.order_date desc";

    $cd4_sql = "select distinct cd4_fcdrr.order_date,cd4_fcdrr.compiled_by,cd4_fcdrr.id as order_id,
    facilities.facility_name,facilities.facility_code, districts.district,districts.id as district_id, counties.county,counties.id as county_id
    FROM cd4_fcdrr,facilities,districts,counties
    WHERE cd4_fcdrr.facility_code = facilities.facility_code
    AND facilities.district = districts.id
    AND counties.id = districts.county
    AND facilities.facility_code =$mfl $conditions 
    group by cd4_fcdrr.order_date desc";  
        // echo "$cd4_sql";

    $rtk_result = $this->db->query($rtk_sql)->result_array();
    $cd4_result = $this->db->query($cd4_sql)->result_array();

    $sum_facilities = array();

    $cd4_facility_arr = array();
    $cd4_all_details = array();

    $rtk_facility_arr = array();
    $rtk_all_details = array();

    foreach ($rtk_result as $key => $value) {
        $rtk_facility_arr = $value;
        $rtk_details = $this->fcdrr_values($value['id']);       
        array_push($rtk_facility_arr, $rtk_details);
        array_push($rtk_all_details, $rtk_facility_arr);
    }
    foreach ($cd4_result as $keys => $values) {
        $cd4_facility_arr = $values;
        $cd4_details = $this->cd4_fcdrr_values($values['order_id']);       
        array_push($cd4_facility_arr, $cd4_details);
        array_push($cd4_all_details, $cd4_facility_arr);
    }

    $sum_facilities = array('rtk_facility_arr' => $rtk_all_details, 'cd4_facility_arr'=>$cd4_all_details);
        // echo "<pre>"; print_r($sum_facilities);die();
    return $sum_facilities;
}
public function fcdrr_values($order_id, $commodity = null) {

    $rtk_sql = "SELECT * 
    FROM lab_commodities, lab_commodity_details
    WHERE lab_commodity_details.order_id ='$order_id'
    AND lab_commodity_details.commodity_id = lab_commodities.id 
    AND lab_commodities.category='1'";


    if (isset($commodity)) {
        $rtk_sql = "SELECT * 
        FROM lab_commodities, lab_commodity_details
        WHERE lab_commodity_details.order_id ='$cd4_order_id'
        AND lab_commodity_details.commodity_id = lab_commodities.id
        AND commodity_id='$commodity'";
    }   
    $returnable = $this->db->query($rtk_sql)->result_array();

        // echo "$order_id";
        // echo($rtk_sql);
        // echo "<pre>"; print_r($returnable);die();

    return $returnable;
}
public function cd4_fcdrr_values($order_id, $commodity = null) {
    $cd4_sql = "SELECT * 
    FROM cd4_commodities, cd4_fcdrr_commodities 
    WHERE cd4_fcdrr_commodities.fcdrr_id ='$order_id'   
    AND cd4_fcdrr_commodities.commodity_id = cd4_commodities.id 
    AND cd4_commodities.category<>'0'";    

        // echo "$cd4_sql";
    $returnable = $this->db->query($cd4_sql)->result_array();
    return $returnable;
}
private function _facilities_in_district($district) {
    $sql = 'select facility_code,facility_name from facilities where district=' . $district;
    $res = $this->db->query($sql);


    return $res->result_array();
}

function district_totals($year, $month, $district = NULL,$commodity_id = null) {
    $conditions = '';
    if(isset($commodity_id)){
        $conditions = "and lab_commodities.id = '$commodity_id'";
    }

    $firstdate = $year . '-' . $month . '-01';
        //$firstday = date("Y-m-d", strtotime("$firstdate +1 Month "));
        //echo "$firstday";die();
        // $month = date("m", strtotime("$firstdate +1 Month "));
        // $year = date("Y", strtotime("$firstdate +1 Month "));
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $returnable = array();

    $common_q = "SELECT lab_commodities.commodity_name,
    sum(lab_commodity_details.beginning_bal) as sum_opening, 
    sum(lab_commodity_details.q_received) as sum_received, 
    sum(lab_commodity_details.q_used) as sum_used, 
    sum(lab_commodity_details.no_of_tests_done) as sum_tests, 
    sum(lab_commodity_details.positive_adj) as sum_positive, 
    sum(lab_commodity_details.negative_adj) as sum_negative,
    sum(lab_commodity_details.losses) as sum_losses,
    sum(lab_commodity_details.closing_stock) as sum_closing_bal,
    sum(lab_commodity_details.q_requested) as sum_requested, 
    sum(lab_commodity_details.allocated) as sum_allocated,
    sum(lab_commodity_details.allocated) as sum_days,
    sum(lab_commodity_details.q_expiring) as sum_expiring
    FROM lab_commodities, lab_commodity_details, lab_commodity_orders, facilities, districts, counties 
    WHERE lab_commodity_details.commodity_id = lab_commodities.id 
    AND lab_commodity_orders.id = lab_commodity_details.order_id 
    AND facilities.facility_code = lab_commodity_details.facility_code AND facilities.district = districts.id 
    AND districts.county = counties.id 
    AND lab_commodity_orders.order_date BETWEEN  '$firstdate' AND  '$lastdate'
    AND lab_commodities.id in (select lab_commodities.id from lab_commodities,lab_commodity_categories 
    where lab_commodities.category = lab_commodity_categories.id and lab_commodity_categories.active = '1') ";

    if (isset($district)) {
        $common_q.= ' AND districts.id =' . $district;
    }

    $common_q.= ' group by lab_commodities.id';
        // echo "$common_q";die();
    $res = $this->db->query($common_q)->result_array();  
        // echo "$common_q";     
        // print_r($res);
    return $res;
}
function partner_county_totals($year, $month, $partner = NULL) {

    $firstdate = $year . '-' . $month . '-01';
    $firstday = date("Y-m-d", strtotime("$firstdate +1 Month "));

    $month = date("m", strtotime("$firstdate +1 Month "));
    $year = date("Y", strtotime("$firstdate +1 Month "));
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $returnable = array();

    $common_q = "SELECT lab_commodities.commodity_name,
    sum(lab_commodity_details.beginning_bal) as sum_opening, 
    sum(lab_commodity_details.q_received) as sum_received, 
    sum(lab_commodity_details.q_used) as sum_used, 
    sum(lab_commodity_details.no_of_tests_done) as sum_tests, 
    sum(lab_commodity_details.positive_adj) as sum_positive, 
    sum(lab_commodity_details.negative_adj) as sum_negative,
    sum(lab_commodity_details.losses) as sum_losses,
    sum(lab_commodity_details.closing_stock) as sum_closing_bal,
    sum(lab_commodity_details.q_requested) as sum_requested, 
    sum(lab_commodity_details.allocated) as sum_allocated,
    sum(lab_commodity_details.allocated) as sum_days,
    sum(lab_commodity_details.q_expiring) as sum_expiring
    FROM lab_commodities, lab_commodity_details, lab_commodity_orders, facilities, districts, counties 
    WHERE lab_commodity_details.commodity_id = lab_commodities.id 
    AND lab_commodity_orders.id = lab_commodity_details.order_id 
    AND facilities.facility_code = lab_commodity_details.facility_code AND facilities.district = districts.id 
    AND districts.county = counties.id 
    and facilities.partner = '$partner'
    AND lab_commodity_orders.order_date BETWEEN  '$firstday' AND  '$lastdate'
    AND lab_commodities.id in (select lab_commodities.id from lab_commodities,lab_commodity_categories 
    where lab_commodities.category = lab_commodity_categories.id and lab_commodity_categories.active = '1')";

    if (isset($county)) {
        $common_q.= ' AND districts.county = counties.id and counties.id=' . $county;
    }

    $common_q.= ' group by lab_commodities.id';

    $res = $this->db->query($common_q)->result_array(); 

    return $res;
}

public function rtk_facilities_not_reported($zone = NULL, $county = NULL, $district = NULL, $facility = NULL, $year = NULL, $month = NULL,$partner= NULL) {

    if (!isset($month)) {
        $month_text = date('mY', strtotime('-1 month'));
        $month = date('m', strtotime("-1 month", time()));
    }

    if (!isset($year)) {
        $year = substr($month_text, -4);
    }

    $firstdate = $year . '-' . $month . '-01';
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $lastdate = $year . '-' . $month . '-' . $num_days;

    $conditions = '';
    $conditions = (isset($zone)) ? "AND facilities.Zone = 'Zone $zone'" : '';
    $conditions = (isset($county)) ? $conditions . " AND counties.id = $county" : $conditions . ' ';
    $conditions = (isset($partner)) ? $conditions . " AND facilities.partner = $partner" : $conditions . ' ';
    $conditions = (isset($district)) ? $conditions . " AND districts.id = $district" : $conditions . ' ';
    $conditions = (isset($facility)) ? $conditions . " AND facilities.facility_code = $facility" : $conditions . ' ';

    $sql = "select distinct counties.county, districts.district, facilities.facility_code, facilities.facility_name, lab_commodity_orders.id as order_id, lab_commodity_orders.facility_code
    from lab_commodity_orders, facilities, districts, counties 
    where lab_commodity_orders.order_date between '$firstdate' and '$lastdate'
    $conditions
    and facilities.district=districts.id 
    and districts.county = counties.id
    and facilities.rtk_enabled='1'
    and lab_commodity_orders.facility_code = facilities.facility_code";
        // echo $conditions;
        // echo "$sql";die();

    $sql2 = "select facilities.facility_code
    from facilities, districts, counties 
    where facilities.district=districts.id
    $conditions
    and districts.county = counties.id
    and facilities.rtk_enabled='1'
    ";

    $res = $this->db->query($sql);
    $reported = $res->result_array();
    $res2 = $this->db->query($sql2);
    $all = $res2->result_array();


    $unreported = array();
    $new_all = array();
    $new_reported = array();

    foreach ($all AS $key => $value) {
        $new_all[] = $value['facility_code'];
    }
    foreach ($reported AS $key => $value) {
        $new_reported[] = $value['facility_code'];
    }
    sort($new_all);
    sort($new_reported);

    $returnable = $this->flip_array_diff_key($new_all, $new_reported);

    foreach ($returnable as $value) {
        $sql3 = "select facilities.facility_code,facilities.facility_name, districts.district, counties.county,facilities.zone
        from facilities, districts, counties 
        where facilities.district=districts.id 
        and districts.county = counties.id
        and rtk_enabled='1'
        and facilities.facility_code = '$value'
        $conditions";
        $res3 = $this->db->query($sql3);
        $my_value = $res3->result_array();
        array_push($unreported, $my_value);
    }
    $report_for = $month . "-" . $year;



    foreach ($unreported AS $key => $value) {
        $new_unreported[] = $value[0];
    }
    foreach ($new_unreported as $key => $value) {
        $new_unreported[$key]['report_for'] = $report_for;
    }
    $returnable = array('non_reported'=>$new_unreported, 'reported'=>$reported);

        // echo '<pre>';print_r($reported);    die;

    return $returnable;
}

function _all_counties() {
    $q = 'SELECT id,county FROM  `counties` ';
    $q_res = $this->db->query($q);
    $returnable = $q_res->result_array();
    return $returnable;
}

function _all_partners() {
    $q = 'SELECT ID,name FROM  `partners` ';
    $q_res = $this->db->query($q);
    $returnable = $q_res->result_array();
    return $returnable;
}

function flip_array_diff_key($b, $a) {
    $at = array_flip($a);
    $bt = array_flip($b);
    $d = array_diff_key($bt, $at);
    return array_keys($d);
}


function _get_rtk_users() {      
    $q = 'SELECT access_level.level,access_level.user_indicator,user.status as status,
    user.email, user.id AS user_id,user.fname,user.lname,user.email,user.county_id,
    user.district,counties.county,user.usertype_id,user.usertype_id FROM access_level,
    user,counties 
    WHERE user.county_id = counties.id AND user.usertype_id = access_level.id
    AND user.usertype_id = 13 ORDER BY `user`.`district` ASC';

    $res = $this->db->query($q);
    $arr = $res->result_array();
    $q2 = 'SELECT access_level.level,access_level.user_indicator,user.status as status,
    user.email,user.id AS user_id,user.fname,user.lname,user.email,user.county_id,
    districts.district as district,counties.county,user.usertype_id
    FROM access_level,user,counties,districts 
    WHERE user.district = districts.id
    AND districts.county = counties.id
    AND user.county_id = counties.id
    AND user.usertype_id = access_level.id
    AND user.usertype_id IN (7,5)';
    $res2 = $this->db->query($q2);
    $arr2 = $res2->result_array();

    $q3 = 'SELECT access_level.level,access_level.user_indicator,user.status as status,
    user.email, user.id AS user_id,user.fname,user.lname,user.email,user.partner,
    user.usertype_id,user.usertype_id FROM access_level, user,partners 
    WHERE user.partner = partners.ID AND user.usertype_id = access_level.id
    AND user.usertype_id in (14,15) ORDER BY user.id ASC';

    $arr3 = $this->db->query($q3)->result_array();
    $returnable = array_merge($arr, $arr2,$arr3);  
        // echo "<pre>";
        // print_r($arr3);  die;   
    return $returnable;
}

function _get_rtk_facilities($zone=null){
    $conditions = (isset($zone)) ? " AND facilities.Zone = 'Zone $zone'" : " AND facilities.Zone = 'Zone $zone'";
    $sql = "select facilities . *,districts.district as facil_district, counties.county from   facilities, districts, counties where
    facilities.district = districts.id  and districts.county = counties.id $conditions order by facility_code ";        
    $res = $this->db->query($sql)->result_array();    
    return $res;
}

public function allocation($zone = NULL, $county = NULL, $district = NULL, $facility = NULL, $sincedate = NULL, $enddate = NULL) {
        // function to filter allocation based on multiple parameter
        // zone, county,district, sincedate,
    $conditions = '';
    $conditions = (isset($zone)) ? " AND facilities.Zone = 'Zone $zone'" : '';
    $conditions = (isset($county)) ? $conditions . " AND counties.id = $county" : $conditions . ' ';
    $conditions = (isset($district)) ? $conditions . " AND districts.id = $district" : $conditions . ' ';
    $conditions = (isset($facility)) ? $conditions . " AND facilities.facility_code = $facility" : $conditions . ' ';
    $conditions = (isset($sincedate)) ? $conditions . " AND lab_commodity_details.allocated_date >= $sincedate" : $conditions . ' ';
    $conditions = (isset($enddate)) ? $conditions . " AND lab_commodity_details.allocated_date <= $enddate" : $conditions . ' ';


    $sql = "select facilities.facility_name,facilities.facility_code,facilities.Zone, facilities.contactperson,facilities.cellphone, lab_commodity_details.commodity_id,
    lab_commodity_details.allocated,lab_commodity_details.allocated_date,lab_commodity_orders.order_date,lab_commodities.commodity_name,facility_amc.amc,lab_commodity_details.closing_stock,lab_commodity_details.q_requested
    from facilities, lab_commodity_orders,lab_commodity_details, counties,districts,lab_commodities,lab_commodity_categories,facility_amc
    WHERE facilities.facility_code = lab_commodity_orders.facility_code
    AND lab_commodity_categories.id = 1
    AND lab_commodity_categories.id = lab_commodities.category
    AND counties.id = districts.county
    AND facilities.district = districts.id
    AND facilities.rtk_enabled = 1
    and lab_commodities.id = lab_commodity_details.commodity_id
    and lab_commodities.id = facility_amc.commodity_id
    and facilities.facility_code = facility_amc.facility_code
    AND lab_commodity_orders.id = lab_commodity_details.order_id
    AND lab_commodity_details.commodity_id between 1 AND 3
    $conditions
    GROUP BY facilities.facility_code, lab_commodity_details.commodity_id";
    $res = $this->db->query($sql);
    $returnable = $res->result_array();      
    return $returnable;
#$nonexistent = "AND lab_commodity_orders.order_date BETWEEN '2014-04-01' AND '2014-04-30'";
}


        //        //        //Administration stuff

public function insert_percentage_tables(){
    $month = date('mY', time());           
    $this->get_county_percentages_month($month);
    $this->get_district_percentages_month($month);
}

public function update_percentage_tables($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year;
    }else{
        $month = date('mY',time());        
    }        
    $this->update_county_percentages_month($month);
    $this->update_district_percentages_month($month);
    $this->update_partner_percentages_month($month);
}
function update_county_percentages_month($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year;                    

    }
    $r = "delete from rtk_county_percentage where month='$monthyear'";
    $this->db->query($r);
    $sql = "select id from counties";

    $result = $this->db->query($sql)->result_array();
    foreach ($result as $key => $value) {
        $id = $value['id'];               
        $sql = "select count(facilities.facility_code) as facilities from
        facilities,
        districts,
        counties
        where
        facilities.district = districts.id
        and districts.county = counties.id
        and counties.id = '$id'
        and facilities.rtk_enabled = 1";
        $facilities = $this->db->query($sql)->result_array();            
        foreach ($facilities as $key => $value) {
            $facility_count = $value['facilities'];
        }



        $reports = $this->rtk_summary_county($id,$year,$month);                
        $reported = $reports['reported']; 
        $total_facilities = $reports['facilities']; 
        //$percentage = ($reported/$facility_count)*100;
        $percentage = ($reported/$total_facilities)*100;

        $q = "insert into rtk_county_percentage (county_id, facilities,reported,percentage,month) values ($id,$total_facilities,$reported,$percentage,'$monthyear')";
        $this->db->query($q);
    }
}

function update_partner_percentages_month($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year;                    

    }
    $r = "delete from rtk_partner_percentage where month='$monthyear'";
    $this->db->query($r);
    $sql = "select id from partners";

    $result = $this->db->query($sql)->result_array();

    foreach ($result as $key => $value) {
        $id = $value['id'];               
        $sql = "select count(facilities.facility_code) as facilities from
        facilities
        where        
        facilities.partner = '$id'
        and facilities.rtk_enabled = 1";

        $facilities = $this->db->query($sql)->result_array();            
        foreach ($facilities as $key => $value) {
            $facility_count = $value['facilities'];
        }



        $reports = $this->rtk_summary_partner($id,$year,$month);                
        $reported = $reports['reported']; 
        $total_facilities = $reports['total_facilities']; 
        //$total_facilities = $facility_count; 

        //$percentage = ceil(($reported/$facility_count)*100);
        $percentage = ceil(($reported/$total_facilities)*100);

        $q = "insert into rtk_partner_percentage (partner_id, facilities,reported,percentage,month) values ($id,$total_facilities,$reported,$percentage,'$monthyear')";        
        $this->db->query($q);
    }
}

public function rtk_summary_partner($partner, $year, $month) {                       
    $returnable = array();
    $nonreported;
    $reported_percentage;
    $late_percentage;


        // Sets the timezone and date variables for last day of previous month and this month
    date_default_timezone_set('EUROPE/moscow');
    $month = $month + 1;
    $prev_month = $month - 1;
    $last_day_current_month = date('Y-m-d', mktime(0, 0, 0, $month, 0, $year));
    $first_day_current_month = date('Y-m-', mktime(0, 0, 0, $month, 0, $year));
    $first_day_current_month .= '01';
    $lastday_thismonth = date('Y-m-d', strtotime("last day of this month"));
    $month -= 1;        
    $day10 = $year . '-' . $month . '-10';
    $day11 = $year . '-' . $month . '-11';
    $day12 = $year . '-' . $month . '-12';
    $late_reporting = 0;
    $text_month = date('F', strtotime($day10));

    $reporting_month = date('F,Y', strtotime('first day of previous month'));

    $q = "SELECT * 
    FROM facilities
    WHERE facilities.partner = '$partner'
    AND facilities.rtk_enabled =1
    ORDER BY  `facilities`.`facility_name` ASC ";

    $q_res = $this->db->query($q);
    $total_reporting_facilities = $q_res->num_rows();

    $q1 = "SELECT DISTINCT
    lab_commodity_orders.facility_code,
    lab_commodity_orders.id,
    lab_commodity_orders.order_date
    FROM
    lab_commodity_orders,facilities
    WHERE
    lab_commodity_orders.facility_code = facilities.facility_code
    AND facilities.partner = '$partner'       
    AND lab_commodity_orders.order_date    BETWEEN '$first_day_current_month'  AND '$last_day_current_month'
    and facilities.rtk_enabled= '1'
    group by lab_commodity_orders.facility_code";

    $q_res1 = $this->db->query($q1);
    $new_q_res1 = $q_res1 ->result_array();
    $total_reported_facilities = $q_res1->num_rows();        

    foreach ($q_res1->result_array() as $vals) {
        //            echo "<pre>";var_dump($vals);echo "</pre>";
        if ($vals['order_date'] == $day10 || $vals['order_date'] == $day11 || $vals['order_date'] == $day12) {
            $late_reporting += 1;
        //                echo "<pre>";var_dump($vals);echo "</pre>";
        }
    }

    $nonreported = $total_reporting_facilities - $total_reported_facilities;

    if ($total_reporting_facilities == 0) {
        $non_reported_percentage = 0;
    } else {
        $non_reported_percentage = $nonreported / $total_reporting_facilities * 100;
    }

    $non_reported_percentage = number_format($non_reported_percentage, 0);

    if ($total_reporting_facilities == 0) {
        $reported_percentage = 0;
    } else {
        $reported_percentage = $total_reported_facilities / $total_reporting_facilities * 100;
    }

    $reported_percentage = number_format($reported_percentage, 0);

    if ($total_reporting_facilities == 0) {
        $late_percentage = 0;
    } else {
        $late_percentage = $late_reporting / $total_reporting_facilities * 100;
    }


    $late_percentage = number_format($late_percentage, 0);
    if ($total_reported_facilities > $total_reporting_facilities) {
        $reported_percentage = 100;
        $nonreported = 0;
        $total_reported_facilities = $total_reporting_facilities;
    }
    if ($late_reporting > $total_reporting_facilities) {
        $late_reporting = $total_reporting_facilities;
        $late_percentage = $reported_percentage;
    }
    $returnable = array('reporting_month'=>$reporting_month,'Month' => $text_month, 'Year' => $year, 'district' => $districtname, 'district_id' => $district_id, 'total_facilities' => $total_reporting_facilities, 'reported' => $total_reported_facilities, 'reported_percentage' => $reported_percentage, 'nonreported' => $nonreported, 'nonreported_percentage' => $non_reported_percentage, 'late_reports' => $late_reporting, 'late_reports_percentage' => $late_percentage);
    return $returnable;

}

function get_county_percentages_month($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year;                    

    }

    $sql = "select id from counties";
    $result = $this->db->query($sql)->result_array();
    foreach ($result as $key => $value) {
        $id = $value['id'];               
        $sql = "select count(facilities.facility_code) as facilities     from
        facilities,
        districts,
        counties
        where
        facilities.district = districts.id
        and districts.county = counties.id
        and counties.id = '$id'
        and facilities.rtk_enabled = 1";
        $facilities = $this->db->query($sql)->result_array();            
        foreach ($facilities as $key => $value) {
            $facility_count = $value['facilities'];
        }
        //$percentage = $this->rtk_summary_county($id,$year,$month);        
        //$reported = $percentage['reported']; 
        $reported = 0;
        $q = "insert into rtk_county_percentage (county_id, facilities,reported,month) values ($id,$facility_count,$reported,'$monthyear')";
        $this->db->query($q);
    }
}
function get_district_percentages_month($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year;                    

    }
    $sql = "select id from districts";
    $result = $this->db->query($sql)->result_array();
    foreach ($result as $key => $value) {
        $id = $value['id'];
        $q = "select count(facilities.facility_code) as facilities from
        facilities
        where
        facilities.district = '$id'
        and facilities.rtk_enabled = '1'";               
        $facilities = $this->db->query($q)->result_array();
        foreach ($facilities as $key => $value) {
            $facility_count = $value['facilities'];
        }            
        //$percentage = $this->rtk_summary_district($id, $year, $month);
        $reported = 0;
        //$reported = $percentage['reported']; 
        $q = "insert into rtk_district_percentage (district_id, facilities,reported,month) values ($id,$facility_count,$reported,'$monthyear')";
        $this->db->query($q);

    }             


}
function update_district_percentages_month($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year;                    

    }
    $r = "delete from rtk_district_percentage where month='$monthyear'";
    $this->db->query($r);
    $sql = "select id from districts";
    $result = $this->db->query($sql)->result_array();
        // echo "<pre>";
        // print_r($result);die();
    foreach ($result as $key => $value) {
        $id = $value['id'];
        $q = "select count(facilities.facility_code) as facilities from
        facilities
        where
        facilities.district = '$id'
        and facilities.rtk_enabled = '1'";               
        $facilities = $this->db->query($q)->result_array();
        foreach ($facilities as $key => $value) {
            $facility_count = $value['facilities'];
        }            
        $reports = $this->rtk_summary_district($id, $year, $month);
        //$reported = 0;
        $reported = $reports['reported']; 
        $percentage = ($reported/ $facility_count)*100;

        $q = "insert into rtk_district_percentage (district_id, facilities,reported,percentage,month) values ($id,$facility_count,$reported,$percentage,'$monthyear')";
        $this->db->query($q);

    }             


}

public function kemsa_district_reports($district) {    
    $pdf_htm = '';
    $month = date('mY', strtotime('-0 month',time()));    
    $year = substr($month, -4);
    $month = date('m', strtotime('-0 month', time()));
    $month_title = date('mY', strtotime('-1 month', time()));
    $year_title = substr($month_title, -4);
    $month_title = date('m', strtotime('-1 month', time()));    
    $date = date('F-Y', mktime(0, 0, 0, $month_title, 1, $year_title));   
    $q = 'SELECT * FROM  `districts` WHERE  `id` =' . $district;
    $res = $this->db->query($q);
    $resval = $res->result_array();    
    $reportname = $resval['0']['district'] . ' Sub-County FCDRR-RTK Reports for ' . $date;  
        // echo "Year $year, Month $month, District $district";die();
    $report_result = $this->district_reports($year, $month, $district);    
    $message = "Dear KEMSA, </br> Please find the RTK reports for ".$date." attached below.</br>Regards, </br> RTK System ";

    if($report_result!=''){
        $reports_html = "<h2>" . $reportname . "</h2><hr> ";        
        $reports_html .= $report_result;                   
        $email_address = "lab@kemsa.co.ke,ttunduny@gmail.com";
        // $email_address = "ttunduny@gmail.com";
        $this->sendmail($reports_html,$message, $reportname, $email_address);
    }else{
        echo "No data to Send";
    }    

        //      $email_address = "cecilia.wanjala@kemsa.co.ke,jbatuka@usaid.gov";
        //$email_address = "lab@kemsa.co.ke,shamim.kuppuswamy@kemsa.co.ke,onjathi@clintonhealthaccess.org,jbatuka@usaid.gov,williamnguru@gmail.com,ttunduny@gmail.com,patrick.mwangi@kemsa.co.ke";
        //         // $email_address = "lab@kemsa.co.ke,williamnguru@gmail.com,ttunduny@gmail.com";
        //  $email_address = "ttunduny@gmail.com";
        // $this->sendmail($test,$test,$reportname, $email_address);
}


public function national_reporting_rates() {
    $pdf_htm = '';
    $current_month = date('mY', strtotime('-0 month',time()));    
    $previous_month = date('mY', strtotime('-1 month',time()));    
    $two_months_ago = date('mY', strtotime('-2 month',time()));                        

    $current_month_text = date('F-Y', strtotime('-1 month',time()));    
    $previous_month_text = date('F-Y',strtotime('-2 month',time()));    
    $two_months_ago_text = date('F-Y',strtotime('-3 month',time()));                        

    $q = "SELECT * FROM  `counties` order by county asc";
    $counties = $this->db->query($q)->result_array();
    $current_percentage = array();
    $previous_percentage = array();
    $previous1_percentage = array();
    foreach ($counties as $key => $value) {
        $id = $value['id'];
        $county = $value['county'];
        $sql_c = "SELECT percentage  FROM `rtk_county_percentage` WHERE county_id='$id'  and`month` = '$current_month' limit 0,1";
        $sql_p = "SELECT percentage  FROM `rtk_county_percentage` WHERE county_id='$id' and `month` = '$previous_month' limit 0,1";
        $sql_p1 = "SELECT percentage  FROM `rtk_county_percentage` WHERE county_id='$id' and `month` = '$two_months_ago' limit 0,1";
        $perc_c =  $this->db->query($sql_c)->result_array();
        $perc_p =  $this->db->query($sql_p)->result_array();
        $perc_p1 =  $this->db->query($sql_p1)->result_array();
        $current_p = $perc_c[0]['percentage'];
        $previous_p = $perc_p[0]['percentage'];
        $previous_p1 = $perc_p1[0]['percentage'];        
        array_push($current_percentage, $current_p);
        array_push($previous_percentage, $previous_p);
        array_push($previous1_percentage, $previous_p1);

    }


    $nat_c = "select sum(reported) as reported, sum(facilities) as facilities from rtk_county_percentage where month='$current_month'";
    $nat_dets = $this->db->query($nat_c)->result_array();
    $national_total = ceil(((($nat_dets[0]['reported'])/($nat_dets[0]['facilities']))*100));  

    $nat_p = "select sum(reported) as reported, sum(facilities) as facilities from rtk_county_percentage where month='$previous_month'";
    $nat_dets_p = $this->db->query($nat_p)->result_array();
    $national_total_p = ceil(((($nat_dets_p[0]['reported'])/($nat_dets_p[0]['facilities']))*100));  

    $nat_p1 = "select sum(reported) as reported, sum(facilities) as facilities from rtk_county_percentage where month='$two_months_ago'";
    $nat_dets_p1 = $this->db->query($nat_p1)->result_array();
    $national_total_p1 = ceil(((($nat_dets_p1[0]['reported'])/($nat_dets_p1[0]['facilities']))*100));  

    $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

    <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>        
    <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>National HIV Rapid Test Kit (RTK) Reporting Rates for $two_months_ago_text to $current_month_text</div><hr />    

    <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
        table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
        table.data-table td, table th {padding: 4px;}
        table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
        .col5{background:#D8D8D8;}</style>";
        $table_head = '
        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
            <thead border="0" style="margin: 10px auto;font-weight:900">
                <tr>
                    <th>County</th>                                       
                    <th>'.$two_months_ago_text.'</th>                                       
                    <th>'.$previous_month_text.'</th>                           
                    <th>'.$current_month_text.'</th>               
                </tr>
            </thead>
            <tbody>';      
                $table_body = '';
                for ($i=0; $i <count($counties) ; $i++) {
                    $county = $counties[$i]['county'];        
                    $current = $current_percentage[$i];        
                    $previous = $previous_percentage[$i];        
                    $previous1 = $previous1_percentage[$i];        
                    $table_body .= '<tr><td>' . $county . '</td>';
                    $table_body .= '<td>' . $previous1 . '</td>';
                    $table_body .= '<td>' . $previous . '</td>';            
                    $table_body .= '<td>' . $current . '</td></tr>';
                }
                $email_address = 'jodek@usaid.gov,jbatuka@usaid.gov,omarabdi2@yahoo.com,njebungeibowen@gmail.com,colwande@yahoo.com,hoy4@cdc.gov,
                uys0@cdc.gov,japhgituku@yahoo.co.uk,bedan.wamuti@kemsa.co.ke,bnmuture@gmail.com';

        // $email_address = 'onjathi@clintonhealthaccess.org';
                $message = "Dear National Team,<br/></br/>Please find attached the <b>UPDATED</b> County Percentages for the Period between 
                $two_months_ago_text and $current_month_text <br/></br>Sent From the RTK System";  
                $table_foot = '<tr><td><b>Total National Reporting Percentage</b></td>
                <td><b>'.$national_total_p1.'%</b></td>
                <td><b>'.$national_total_p.'%</b></td>
                <td><b>'.$national_total.'%</b></td></tr></tbody></table>';
                $html_data = $html_title . $table_head . $table_body . $table_foot;
        // echo "$html_data";die();
        // $email_address = 'annchemu@gmail.com';
        // $email_address.= 'ttunduny@gmail.com,annchemu@gmail.com';
                $reportname = 'Reporting Rates '.$current_month_text;
        // $this->sendmail($html_data,$message, , $email_address);
        // $this->create_pdf($html_data,$reportname);
                $this->sendmail($html_data,$message, $reportname, $email_address);              




            }

            public function national_stockcard() {
                $pdf_htm = '';
                if(isset($month)){           
                    $year = substr($month, -4);
                    $month = substr($month, 0,2);            
                    $monthyear = $year . '-' . $month . '-01';         

                }else{
                    $month = $this->session->userdata('Month');
                    if ($month == '') {
                        $month = date('mY', time());
                    }
                    $year = substr($month, -4);
                    $month = substr_replace($month, "", -4);
                    $monthyear = $year . '-' . $month . '-01';
                }
                $englishdate = date('F-Y', strtotime('-1 Month',time()));    
                $stock_status = $this->_national_reports_sum($year, $month);        
        // echo "<pre>"; 
        // print_r($stock_status);die();


                $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

                <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>
                <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold;display: block; font-size: 13px;'>Health Commodities Management Platform</div>
                <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>Rapid Test Kits (RTK) System</div>   
                <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>National Stock Card  for $englishdate</div><hr />    

                <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
                    table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
                    table.data-table td, table th {padding: 4px;}
                    table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
                    .col5{background:#D8D8D8;}</style>";
                    $table_head = '
                    <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                        <thead border="0" style="margin: 10px auto;font-weight:900">
                            <tr>
                                <th>County</th>
                                <th>Commodity</th>
                                <th>Beginning Balance</th>
                                <th>Received Qty</th>
                                <th>Used Qty</th>
                                <th>Tests Done</th>
                                <th>Closing Balance</th>
                                <th>Requested Qty</th>
                                <th>Quantity Expiring <br/>(in the next 6 months)</th>               
                                <th>No. of Facilities with<br/> Days Out of Stock</th>               
                            </tr>
                        </thead>
                        <tbody>';      
                            $table_body = '';
                            $count = count($stock_status);
                            for ($i=0; $i<$count; $i++){
                                foreach ($stock_status[$i] as $key => $value) {
                                    $county = $value['county'];
                                    $commodity_name = $value['commodity_name'];
                                    $sum_opening = $value['sum_opening'];
                                    $sum_received = $value['sum_received'];
                                    $sum_used = $value['sum_used'];
                                    $sum_tests = $value['sum_tests'];
                                    $sum_closing_bal = $value['sum_closing_bal'];
                                    $sum_requested = $value['sum_requested'];
                                    $sum_days = $value['sum_days'];
                                    $sum_expiring = $value['sum_expiring'];
                                    $sum_allocated = $value['sum_allocated'];                   
                                    $table_body .= '<tr><td>' . $county . '</td>';
                                    $table_body .= '<td>' . $commodity_name . '</td>';
                                    $table_body .= '<td>' . $sum_opening . '</td>';            
                                    $table_body .= '<td>' . $sum_received . '</td>';            
                                    $table_body .= '<td>' . $sum_used . '</td>';            
                                    $table_body .= '<td>' . $sum_tests . '</td>';            
                                    $table_body .= '<td>' . $sum_closing_bal . '</td>';            
                                    $table_body .= '<td>' . $sum_requested . '</td>';       
                                    $table_body .= '<td>' . $sum_expiring . '</td>'; 
                                    $table_body .= '<td>' . $sum_days. '</td>'; 
                                }
                            }
                            $email_address = 'jodek@usaid.gov,jbatuka@usaid.gov,omarabdi2@yahoo.com,njebungeibowen@gmail.com,colwande@yahoo.com,hoy4@cdc.gov,
                            uys0@cdc.gov,japhgituku@yahoo.co.uk,bedan.wamuti@kemsa.co.ke,bnmuture@gmail.com';
        // $email_address = 'onjathi@clintonhealthaccess.org';
                            $message = 'Dear National Team,<br/></br/>Please find attached the<b> UPDATED</b> National Stock Status as at end of '.$englishdate.' <br/> Please ignore the previous document<br/></br>Sent From the RTK System'; 
                            $table_foot = '</tbody></table>';
                            $html_data = $html_title . $table_head . $table_body . $table_foot;
        // echo "$html_data";die();
                            $reportname = 'National Stocks for '.$englishdate;
                            $this->create_pdf($html_data,$reportname);
                            $this->sendmail($html_data,$message, $reportname, $email_address);    




                        }

                        public function send_national_reports(){
                            if(isset($month)){           
                                $year = substr($month, -4);
                                $month = substr($month, 0,2);            
                                $monthyear = $year . '-' . $month . '-01';         

                            }else{
                                $month = $this->session->userdata('Month');
                                if ($month == '') {
                                    $month = date('mY', time());
                                }
                                $year = substr($month, -4);
                                $month = substr_replace($month, "", -4);
                                $monthyear = $year . '-' . $month . '-01';
                            }

                            $current_month_text = date('F-Y', strtotime('-1 month',time()));        

                            $englishdate = date('F-Y', strtotime('-1 month',time()));        
                            $message = 'Dear National Team,<br/></br/>Please find attached the National Stock Status as at end of '.$englishdate.' <br/></br>Sent From the RTK System';     
                            $reporting_rates = 'Reporting Rates '.$current_month_text;
                            $reporting_stocks = 'National Stocks for '.$englishdate;
                            $reporting_expiries = 'National Expiries for '.$englishdate;
                            $reporting_stocks = 'National Stocks for January, 2015';
                            $reports = array($reporting_rates,$reporting_stocks,$reporting_expiries);    
                            $email_address = 'ttunduny@gmail.com';      
                            $this->sendmail_multiple($message,$email_address,$englishdate);    

                        }

                        public function county_reporting_rates($county_id) {
                            $pdf_htm = '';
                            $current_month = date('mY', strtotime('-0 month',time()));    
                            $previous_month = date('mY', strtotime('-1 month',time()));    
                            $two_months_ago = date('mY', strtotime('-2 month',time()));                        

                            $current_month_text = date('F-Y', strtotime('-1 month',time()));    
                            $previous_month_text = date('F-Y',strtotime('-2 month',time()));    
                            $two_months_ago_text = date('F-Y',strtotime('-3 month',time()));                        

                            $c = "select * from counties where id='$county_id'";
                            $county_dets = $this->db->query($c)->result_array();
                            $county_name = $county_dets[0]['county'];
                            $q = "SELECT * FROM  `districts` where county='$county_id' order by district asc";
                            $districts = $this->db->query($q)->result_array();
                            $current_percentage = array();
                            $previous_percentage = array();
                            $previous1_percentage = array();
                            foreach ($districts as $key => $value) {
                                $id = $value['id'];
        //$county = $value['county'];
                                $sql_c = "SELECT percentage  FROM `rtk_district_percentage` WHERE district_id='$id'  and`month` = '$current_month' limit 0,1";
                                $sql_p = "SELECT percentage  FROM `rtk_district_percentage` WHERE district_id='$id' and `month` = '$previous_month' limit 0,1";
                                $sql_p1 = "SELECT percentage  FROM `rtk_district_percentage` WHERE district_id='$id' and `month` = '$two_months_ago' limit 0,1";
                                $perc_c =  $this->db->query($sql_c)->result_array();
                                $perc_p =  $this->db->query($sql_p)->result_array();
                                $perc_p1 =  $this->db->query($sql_p1)->result_array();
                                $current_p = $perc_c[0]['percentage'];
                                $previous_p = $perc_p[0]['percentage'];
                                $previous_p1 = $perc_p1[0]['percentage'];        
                                array_push($current_percentage, $current_p);
                                array_push($previous_percentage, $previous_p);
                                array_push($previous1_percentage, $previous_p1);

                            }

                            $sql_u = "SELECT email FROM user where usertype_id = 13 and county_id='$county_id'";
                            $emails_county = $this->db->query($sql_u)->result_array();
                            $email_address ="";
                            foreach ($emails_county as $key => $value) {
                                $one = $value['email'];
                                $email_address.= $one.',';                        
                            } 


                            $nat_c = "select sum(reported) as reported, sum(facilities) as facilities from rtk_district_percentage where month='$current_month' and district_id in (select id from districts where county='$county_id')";    
                            $nat_dets = $this->db->query($nat_c)->result_array();
                            $national_county = ceil(((($nat_dets[0]['reported'])/($nat_dets[0]['facilities']))*100));   

                            $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

                            <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>
                            <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>Sub-County HIV Rapid Test Kit (RTK) Reporting Rates for $county_name County for $two_months_ago_text to $current_month_text</div><hr />        

                            <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
                                table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
                                table.data-table td, table th {padding: 4px;}
                                table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
                                .col5{background:#D8D8D8;}</style>";
                                $table_head = '
                                <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                    <thead border="0" style="margin: 10px auto;font-weight:900">
                                        <tr>
                                            <th>Sub-County</th>                                       
                                            <th>'.$two_months_ago_text.'</th>                                       
                                            <th>'.$previous_month_text.'</th>                           
                                            <th>'.$current_month_text.'</th>               
                                        </tr>
                                    </thead>
                                    <tbody>';      
                                        $table_body = '';
                                        for ($i=0; $i <count($districts) ; $i++) {
                                            $district = $districts[$i]['district'];        
                                            $current = $current_percentage[$i];        
                                            $previous = $previous_percentage[$i];        
                                            $previous1 = $previous1_percentage[$i];        
                                            $table_body .= '<tr><td>' . $district . '</td>';
                                            $table_body .= '<td>' . $previous1 . '</td>';
                                            $table_body .= '<td>' . $previous . '</td>';            
                                            $table_body .= '<td>' . $current . '</td></tr>';
                                        }
                                        $message = "Dear $county_name Team,<br/></br/>Please find attached the Sub-County Percentages for the Period between 
                                        '$two_months_ago_text' and '$current_month_text' <br/></br>Sent From the RTK System"; 
                                        $table_foot = '<tr><td colspan="3"><b>Total County Reporting Percentage: '.$national_county.'%</td></tr></tbody></table>';
                                        $html_data = $html_title . $table_head . $table_body . $table_foot;
                                        echo "$html_data";die();
                                        $email_address = 'ttunduny@gmail.com';
        //$email_address.= 'onjathi@clintonhealthaccess.org,ttunduny@gmail.com,annchemu@gmail.com';
                                        $reportname = 'Percentages for '.$current_month_text;
        //$this->sendmail($html_data,$message, , $email_address);
                                        $this->sendmail($html_data,$message, $reportname, $email_address);              




                                    }
                                    public function county_detailed_summary($county_id) {

                                        $pdf_htm = '';
                                        $current_month = date('mY', strtotime('-0 month',time()));    
                                        $previous_month = date('mY', strtotime('-1 month',time()));    
                                        $two_months_ago = date('mY', strtotime('-2 month',time()));                        

                                        $current_month_text = date('F-Y', strtotime('-1 month',time()));    
                                        $previous_month_text = date('F-Y',strtotime('-2 month',time()));    
                                        $two_months_ago_text = date('F-Y',strtotime('-3 month',time()));                        
                                        $month = date('mY', strtotime('-0 month'));    
                                        $year = substr($month, -4);    
                                        $month = substr_replace($month, "", -4);         

                                        $month1 = date('mY', strtotime('-1 month'));    
                                        $year1 = substr($month1, -4);    
                                        $month1 = substr_replace($month1, "", -4);     

                                        $first_date = $year . '-' . $month . '-01';    
                                        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                                        $last_date = $year . '-' . $month .'-'. $num_days;      

                                        $c = "select * from counties where id='$county_id'";
                                        $county_dets = $this->db->query($c)->result_array();
                                        $county_name = $county_dets[0]['county'];
                                        $q = "SELECT * FROM  `districts` where county='$county_id' order by district asc";
                                        $districts = $this->db->query($q)->result_array();
                                        $current_percentage = array();
                                        $previous_percentage = array();
                                        $previous1_percentage = array();
                                        foreach ($districts as $key => $value) {
                                            $id = $value['id'];
        //$county = $value['county'];
                                            $sql_c = "SELECT percentage  FROM `rtk_district_percentage` WHERE district_id='$id'  and`month` = '$current_month' limit 0,1";
                                            $sql_p = "SELECT percentage  FROM `rtk_district_percentage` WHERE district_id='$id' and `month` = '$previous_month' limit 0,1";
                                            $sql_p1 = "SELECT percentage  FROM `rtk_district_percentage` WHERE district_id='$id' and `month` = '$two_months_ago' limit 0,1";
                                            $perc_c =  $this->db->query($sql_c)->result_array();
                                            $perc_p =  $this->db->query($sql_p)->result_array();
                                            $perc_p1 =  $this->db->query($sql_p1)->result_array();
                                            $current_p = $perc_c[0]['percentage'];
                                            $previous_p = $perc_p[0]['percentage'];
                                            $previous_p1 = $perc_p1[0]['percentage'];        
                                            array_push($current_percentage, $current_p);
                                            array_push($previous_percentage, $previous_p);
                                            array_push($previous1_percentage, $previous_p1);

                                        }

                                        $sql_u = "SELECT distinct email FROM user,rca_county where user.usertype_id='13' and user.county_id='$county_id' or user.id = rca_county.rca and rca_county.county='$county_id'";
                                        $emails_county = $this->db->query($sql_u)->result_array();
                                        $email_address ="";
                                        foreach ($emails_county as $key => $value) {
                                            $one = $value['email'];
                                            $email_address.= $one.',';                        
                                        } 


                                        $message = "Dear $county_name Team,<br/></br/>Please an updated report for the Period between 
                                        '$two_months_ago_text' and '$current_month_text' <br/></br>Sent From the RTK System"; 

                                        $nat_c = "select sum(reported) as reported, sum(facilities) as facilities from rtk_district_percentage where month='$current_month' and district_id in (select id from districts where county='$county_id')";    
                                        $nat_dets = $this->db->query($nat_c)->result_array();
                                        $national_county = ceil(((($nat_dets[0]['reported'])/($nat_dets[0]['facilities']))*100));   

                                        $nat_p = "select sum(reported) as reported, sum(facilities) as facilities from rtk_district_percentage where month='$previous_month' and district_id in (select id from districts where county='$county_id')";    
                                        $nat_dets_p = $this->db->query($nat_p)->result_array();
                                        $national_county_p = ceil(((($nat_dets_p[0]['reported'])/($nat_dets_p[0]['facilities']))*100));   

                                        $nat_p1 = "select sum(reported) as reported, sum(facilities) as facilities from rtk_district_percentage where month='$two_months_ago' and district_id in (select id from districts where county='$county_id')";    
                                        $nat_dets_p1 = $this->db->query($nat_p1)->result_array();
                                        $national_county_p1 = ceil(((($nat_dets_p1[0]['reported'])/($nat_dets_p1[0]['facilities']))*100));  

                                        $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

                                        <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>
                                        <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>Sub-County HIV Rapid Test Kit (RTK) Comprehensive Summary for $current_month_text</div><hr />             
                                        <h4></h4>
                                        <div>
                                            <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
                                                table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
                                                table.data-table td, table th {padding: 4px;}
                                                table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
                                                .col5{background:#D8D8D8;}</style>";
                                                $table_head = '
                                                <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                    <thead border="0" style="margin: 10px auto;font-weight:900">
                                                        <tr>
                                                            <th>Sub-County</th>                                       
                                                            <th>'.$two_months_ago_text.'</th>                                       
                                                            <th>'.$previous_month_text.'</th>                           
                                                            <th>'.$current_month_text.'</th>               
                                                        </tr>
                                                    </thead>
                                                    <tbody>';      
                                                        $table_body = '';
                                                        for ($i=0; $i <count($districts) ; $i++) {
                                                            $district = $districts[$i]['district'];        
                                                            $current = $current_percentage[$i];        
                                                            $previous = $previous_percentage[$i];        
                                                            $previous1 = $previous1_percentage[$i];        
                                                            $table_body .= '<tr><td>' . $district . '</td>';
                                                            $table_body .= '<td>' . $previous1 . '</td>';
                                                            $table_body .= '<td>' . $previous . '</td>';            
                                                            $table_body .= '<td>' . $current . '</td></tr><div>';
                                                        }

                                                        $table_foot = '<tr><td><b>Total County Reporting Percentage (Aggregate)</b></td>
                                                        <td><b>'.$national_county_p1.'%</b></td>
                                                        <td><b>'.$national_county_p.'%</b></td>
                                                        <td><b>'.$national_county.'%</b></td></tr></tbody></table>';
                                                        $section_1 = $html_title . $table_head . $table_body . $table_foot; 
        // $section_1 = $html_title; 

                                                        $county_data_s = $this->_get_county_expiries($first_date,$last_date,$county_id,4);
                                                        $county_data_t = $this->_get_county_expiries($first_date,$last_date,$county_id,5);
                                                        $county_data_c = $this->_get_county_expiries($first_date,$last_date,$county_id,6);

        //getting stock card table

                                                        $stock_card_data = $this->_requested_vs_allocated($year1, $month1, $county_id);

        // echo "<pre>";
        // echo $month;
        // print_r($stock_card_data);
        // die;
                                                        $table_head_stock_card = '';
                                                        $table_head_stock_card .='<h4>Section 2: County Summary - Stock Card (Amount in Tests)</h4>
                                                        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                            <thead border="0" style="margin: 10px auto;font-weight:900">
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
                                                                <tbody>';
                                                                    $table_body_stock_card = '';
                                                                    for ($i=0; $i <count($stock_card_data) ; $i++) { 
                                                                        $table_body_stock_card .= '<tr><td>'. $stock_card_data[$i]['commodity_name']. '</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_opening'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_received'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_used'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_tests'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_positive'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_negative'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_losses'].'</td>';
                                                                        $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_closing_bal'].'</td></tr>';                
                                                                    }

                                                                    $table_body_stock_card .= '</tbody></table></div>';

        //getting closing balance  data
                                                                    $closing_stock_s = $county_data_s['endbal'];              
                                                                    $closing_stock_c = $county_data_t['endbal'];              
                                                                    $closing_stock_t = $county_data_c['endbal'];              

        //ending balalnce Screening table      

                                                                    if(count($closing_stock_s) ==0){
                                                                        $table_head1_cs = '<h4>Section 2: Facilities with Highest Stocks (in Tests)</h4><br/><h5>a) Screening KHB </h5><br/>';
                                                                        $table_body1_cs = 'No facilities have high stocks';
                                                                    }else{
                                                                        $table_head1_cs = '<h4>Facilities with Highest Stocks (in Tests) </h4><br/><h5>a) Screening KHB </h5><br/>
                                                                        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                            <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                <tr><th>Facility Code</th>
                                                                                    <th>Facility Name</th>    
                                                                                    <th>Sub-County</th>            
                                                                                    <th>Amount (Tests)</th>            
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>';  

                                                                                $table_body1_cs = '';
                                                                                for ($i=0; $i <count($closing_stock_s) ; $i++) {
                                                                                    $c_district = $closing_stock_s[$i]['district'];        
                                                                                    $c_fcode = $closing_stock_s[$i]['facility_code'];                    
                                                                                    $c_fname = $closing_stock_s[$i]['facility_name'];                    
                                                                                    $c_end_bal = $closing_stock_s[$i]['closing_stock'];                    
                                                                                    $table_body1_cs .= '<tr><td>' . $c_fcode . '</td>';
                                                                                    $table_body1_cs .= '<td>' . $c_fname . '</td>';            
                                                                                    $table_body1_cs .= '<td>' . $c_district. '</td>';
                                                                                    $table_body1_cs .= '<td>' . $c_end_bal. '</td></tr>';
                                                                                }
                                                                                $table_body1_cs .=  '</table><div>';
                                                                            }

        //ending stock Confirmatory table

                                                                            if(count($closing_stock_c) ==0){
                                                                                $table_head1_cc = '<h5>b) Confirmatory - First Response </h5>';
                                                                                $table_body1_cc = 'No facilities have high stocks';
                                                                            }else{

                                                                                $table_head1_cc = '<h5>b) Confirmatory - First Response </h5>
                                                                                <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                    <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                        <tr><th>Facility Code</th>
                                                                                            <th>Facility Name</th>
                                                                                            <th>Sub-County</th>            
                                                                                            <th>Amount (Tests)</th>            
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>';      
                                                                                        $table_body1_cc = '';
                                                                                        for ($i=0; $i <count($closing_stock_c) ; $i++) {
                                                                                            $c_district = $closing_stock_c[$i]['district'];        
                                                                                            $c_fcode = $closing_stock_c[$i]['facility_code'];                    
                                                                                            $c_end_bal = $closing_stock_c[$i]['closing_stock'];                    
                                                                                            $c_fname = $closing_stock_c[$i]['facility_name'];                    
                                                                                            $table_body1_cc .= '<tr><td>' . $c_fcode . '</td>';
                                                                                            $table_body1_cc .= '<td>' . $c_fname . '</td>';            
                                                                                            $table_body1_cc .= '<td>' . $c_district. '</td>';
                                                                                            $table_body1_cc .= '<td>' . $c_end_bal. '</td></tr>';
                                                                                        }
                                                                                        $table_body1_cc .=  '</table><div>';
                                                                                    }

        //ending balance tie breaker table
                                                                                    if(count($closing_stock_t) ==0){
                                                                                        $table_head1_ct = '<br/><h5>c) Tiebreaker - Unigold</h5><br/>';
                                                                                        $table_body1_ct = 'No facilities have high stocks';
                                                                                    }else{

                                                                                        $table_head1_ct = '<br/><h5>c) Tiebreaker - Unigold</h5><br/>
                                                                                        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                            <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                <tr><th>Facility Code</th>
                                                                                                    <th>Facility Name</th>
                                                                                                    <th>Sub-County</th>            
                                                                                                    <th>Amount (Tests)</th>            
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>';      
                                                                                                $table_body1_ct = '';
                                                                                                for ($i=0; $i <count($closing_stock_t) ; $i++) {
                                                                                                    $c_district = $closing_stock_t[$i]['district'];        
                                                                                                    $c_fcode = $closing_stock_t[$i]['facility_code'];                    
                                                                                                    $c_fname = $closing_stock_t[$i]['facility_name'];                    
                                                                                                    $c_end_bal = $closing_stock_t[$i]['closing_stock'];                    
                                                                                                    $table_body1_ct .= '<tr><td>' . $c_fcode . '</td>';
                                                                                                    $table_body1_ct .= '<td>' . $c_fname . '</td>';            
                                                                                                    $table_body1_ct .= '<td>' . $c_district. '</td>';
                                                                                                    $table_body1_ct .= '<td>' . $c_end_bal. '</td></tr>';
                                                                                                }
                                                                                                $table_body1_ct .=  '</table><div>';
                                                                                            }

        //getting expiries data
                                                                                            $expiries_s = $county_data_s['expiries'];              
                                                                                            $expiries_c = $county_data_t['expiries'];              
                                                                                            $expiries_t = $county_data_c['expiries'];              
                                                                                            $table_foot = '<tr><td><b>Total County Reporting Percentage</b></td>
                                                                                            <td><b>'.$national_county_p1.'%</b></td>
                                                                                            <td><b>'.$national_county_p.'%</b></td>
                                                                                            <td><b>'.$national_county.'%</b></td></tr></tbody></table>';
        // $section_1 = $html_title . $table_head . $table_body . $table_foot; 

        //expiring Screening table      

                                                                                            if(count($expiries_s) ==0){
                                                                                                $table_head1_s = '<h4>Section 4: Facilities with Highest Expiries (in the next 6 months)</h4><br/><h5>a) Screening KHB </h5><br/>';
                                                                                                $table_body1_s = 'No expiries reported';
                                                                                            }else{
                                                                                                $table_head1_s = '<h4>Section 2: Facilities with Highest Expiries (in the next 6 months)</h4><br/><h5>a) Screening KHB </h5><br/>
                                                                                                <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                    <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                        <tr><th>Facility Code</th>
                                                                                                            <th>Facility Name</th>    
                                                                                                            <th>Sub-County</th>            
                                                                                                            <th>Amount (Tests)</th>            
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>';  

                                                                                                        $table_body1_s = '';
                                                                                                        for ($i=0; $i <count($expiries_s) ; $i++) {
                                                                                                            $district = $expiries_s[$i]['district'];        
                                                                                                            $fcode = $expiries_s[$i]['facility_code'];                    
                                                                                                            $fname = $expiries_s[$i]['facility_name'];                    
                                                                                                            $q_expired = $expiries_s[$i]['q_expiring'];                    
                                                                                                            $table_body1_s .= '<tr><td>' . $fcode . '</td>';
                                                                                                            $table_body1_s .= '<td>' . $fname . '</td>';            
                                                                                                            $table_body1_s .= '<td>' . $district. '</td>';
                                                                                                            $table_body1_s .= '<td>' . $q_expired. '</td></tr>';
                                                                                                        }
                                                                                                        $table_body1_s .=  '</table><div>';
                                                                                                    }

        //expiring Confirmatory table

                                                                                                    if(count($expiries_c) ==0){
                                                                                                        $table_head1_c = '<h5>b) Confirmatory - First Response </h5>';
                                                                                                        $table_body1_c = 'No expiries reported';
                                                                                                    }else{

                                                                                                        $table_head1_c = '<h5>b) Confirmatory - First Response </h5>
                                                                                                        <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                            <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                <tr><th>Facility Code</th>
                                                                                                                    <th>Facility Name</th>
                                                                                                                    <th>Sub-County</th>            
                                                                                                                    <th>Amount (Tests)</th>            
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>';      
                                                                                                                $table_body1_c = '';
                                                                                                                for ($i=0; $i <count($expiries_c) ; $i++) {
                                                                                                                    $district = $expiries_c[$i]['district'];        
                                                                                                                    $fcode = $expiries_c[$i]['facility_code'];                    
                                                                                                                    $q_expired = $expiries_c[$i]['q_expiring'];                    
                                                                                                                    $fname = $expiries_c[$i]['facility_name'];                    
                                                                                                                    $table_body1_c .= '<tr><td>' . $fcode . '</td>';
                                                                                                                    $table_body1_c .= '<td>' . $fname . '</td>';            
                                                                                                                    $table_body1_c .= '<td>' . $district. '</td>';
                                                                                                                    $table_body1_c .= '<td>' . $q_expired. '</td></tr>';
                                                                                                                }
                                                                                                                $table_body1_c .=  '</table><div>';
                                                                                                            }

        //expiring tie breaker table
                                                                                                            if(count($expiries_t) ==0){
                                                                                                                $table_head1_t = '<br/><h5>c) Tiebreaker</h5><br/>';
                                                                                                                $table_body1_t = 'No expiries reported';
                                                                                                            }else{

                                                                                                                $table_head1_t = '<br/><h5>c) Tiebreaker </h5><br/>
                                                                                                                <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                    <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                        <tr><th>Facility Code</th>
                                                                                                                            <th>Facility Name</th>
                                                                                                                            <th>Sub-County</th>            
                                                                                                                            <th>Amount (Tests)</th>            
                                                                                                                        </tr>
                                                                                                                    </thead>
                                                                                                                    <tbody>';      
                                                                                                                        $table_body1_t = '';
                                                                                                                        for ($i=0; $i <count($expiries_t) ; $i++) {
                                                                                                                            $district = $expiries_t[$i]['district'];        
                                                                                                                            $fcode = $expiries_t[$i]['facility_code'];                    
                                                                                                                            $fname = $expiries_t[$i]['facility_name'];                    
                                                                                                                            $q_expired = $expiries_t[$i]['q_expiring'];                    
                                                                                                                            $table_body1_t .= '<tr><td>' . $fcode . '</td>';
                                                                                                                            $table_body1_t .= '<td>' . $fname . '</td>';            
                                                                                                                            $table_body1_t .= '<td>' . $district. '</td>';
                                                                                                                            $table_body1_t .= '<td>' . $q_expired. '</td></tr>';
                                                                                                                        }
                                                                                                                        $table_body1_t .=  '</table><div>';
                                                                                                                    }

                                                                                                                    $section_2 = $table_head_stock_card.$table_body_stock_card;
                                                                                                                    $section_3 = $table_head1_cs.$table_body1_cs.$table_head1_cc.$table_body1_cc.$table_head1_ct.$table_body1_ct;
                                                                                                                    $section_4 = $table_head1_s.$table_body1_s.$table_head1_c.$table_body1_c.$table_head1_t.$table_body1_t;
                                                                                                                    $html_data = $section_1.$section_2.$section_3.$section_4;
        // $html_data = $section_1.$section_3;
        //echo "<pre>";
        //print_r($expiries_t);die();
        // echo "$html_data";die();
        //$email_address = 'annchemu@gmail.com';
        // $email_address.= 'onjathi@clintonhealthaccess.org,ttunduny@gmail.com,annchemu@gmail.com';
                                                                                                                    $reportname = 'Percentages for '.$current_month_text;
        //$this->sendmail($html_data,$message, , $email_address);
                                                                                                                    $this->sendmail($html_data,$message, $reportname, $email_address);           




                                                                                                                }


                                                                                                                public function partner_detailed_summary($partner_id) {

                                                                                                                    $pdf_htm = '';
                                                                                                                    $current_month = date('mY', strtotime('-0 month',time()));    
                                                                                                                    $previous_month = date('mY', strtotime('-1 month',time()));   
                                                                                                                    $two_months_ago = date('mY', strtotime('-2 month',time()));                        

                                                                                                                    $current_month_text = date('F-Y', strtotime('-1 month',time()));    
                                                                                                                    $previous_month_text = date('F-Y',strtotime('-2 month',time()));    
                                                                                                                    $two_months_ago_text = date('F-Y',strtotime('-3 month',time()));                        
                                                                                                                    $month = date('mY', strtotime('-0 month'));    
                                                                                                                    $year = substr($month, -4);    
                                                                                                                    $month = substr_replace($month, "", -4);         

                                                                                                                    $month1 = date('mY', strtotime('-1 month'));    
                                                                                                                    $year1 = substr($month1, -4);    
                                                                                                                    $month1 = substr_replace($month1, "", -4);     

                                                                                                                    $first_date = $year . '-' . $month . '-01';    
                                                                                                                    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                                                                                                                    $last_date = $year . '-' . $month .'-'. $num_days;      

                                                                                                                    $p = "select * from partners where ID='$partner_id'";
                                                                                                                    $partner_dets = $this->db->query($p)->result_array();
                                                                                                                    $partner_name = $partner_dets[0]['name'];

                                                                                                                    $sql_u = "SELECT email FROM user where usertype_id = 14 and partner='$partner_id'";
                                                                                                                    $emails_partner = $this->db->query($sql_u)->result_array();
                                                                                                                    $email_address ="";
                                                                                                                    foreach ($emails_partner as $key => $value) {
                                                                                                                        $one = $value['email'];
                                                                                                                        $email_address.= $one.',';                        
                                                                                                                    } 


                                                                                                                    $message = "Dear $partner_name Team,<br/></br/>Please find attached the Partner Percentages for the Period between 
                                                                                                                    '$two_months_ago_text' and '$current_month_text' <br/></br>Sent From the RTK System"; 

        // $nat_c = "select sum(reported) as reported, sum(facilities) as facilities from rtk_partner_percentage where month='$current_month' and partner_id ='$partner_id'";    
        // $nat_dets = $this->db->query($nat_c)->result_array();
        // $national_county = ceil(((($nat_dets[0]['reported'])/($nat_dets[0]['facilities']))*100));   

        // $nat_p = "select sum(reported) as reported, sum(facilities) as facilities from rtk_partner_percentage where month='$previous_month' and partner_id ='$partner_id'";    
        // $nat_dets_p = $this->db->query($nat_p)->result_array();
        // $national_county_p = ceil(((($nat_dets_p[0]['reported'])/($nat_dets_p[0]['facilities']))*100));   

        // $nat_p1 = "select sum(reported) as reported, sum(facilities) as facilities from rtk_partner_percentage where month='$two_months_ago' and partner_id ='$partner_id'";    
        // $nat_dets_p1 = $this->db->query($nat_p1)->result_array();
        // $national_county_p1 = ceil(((($nat_dets_p1[0]['reported'])/($nat_dets_p1[0]['facilities']))*100));  

                                                                                                                    $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>

                                                                                                                    <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>     Ministry of Health</div>
                                                                                                                    <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>Partner HIV Rapid Test Kit (RTK) Comprehensive Summary for $partner_name  for $two_months_ago_text to $current_month_text</div><hr />               
                                                                                                                    <div>
                                                                                                                        <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
                                                                                                                            table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
                                                                                                                            table.data-table td, table th {padding: 4px;}
                                                                                                                            table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
                                                                                                                            .col5{background:#D8D8D8;}</style>";

                                                                                                                            $partner_data_s = $this->_get_partner_expiries($first_date,$last_date,$partner_id,4);
                                                                                                                            $partner_data_t = $this->_get_partner_expiries($first_date,$last_date,$partner_id,5);
                                                                                                                            $partner_data_c = $this->_get_partner_expiries($first_date,$last_date,$partner_id,6);

        //getting stock card table

                                                                                                                            $stock_card_data = $this->_requested_vs_allocated_partner($year1, $month1, $partner_id);

        // echo "<pre>";
        // echo $month;
        // print_r($stock_card_data);
        // die;
                                                                                                                            $table_head_stock_card = '';
                                                                                                                            $table_head_stock_card .='<h4>Section 1: Partner Summary - Stock Card  (Amount in Tests)</h4>
                                                                                                                            <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                <thead border="0" style="margin: 10px auto;font-weight:900">
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
                                                                                                                                    <tbody>';
                                                                                                                                        $table_body_stock_card = '';
                                                                                                                                        for ($i=0; $i <count($stock_card_data) ; $i++) { 
                                                                                                                                            $table_body_stock_card .= '<tr><td>'. $stock_card_data[$i]['commodity_name']. '</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_opening'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_received'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_used'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_tests'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_positive'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_negative'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_losses'].'</td>';
                                                                                                                                            $table_body_stock_card .= '<td>'. $stock_card_data[$i]['sum_closing_bal'].'</td></tr>';                
                                                                                                                                        }

                                                                                                                                        $table_body_stock_card .= '</tbody></table></div>';

        //getting closing balance  data
                                                                                                                                        $closing_stock_s = $partner_data_s['endbal'];              
                                                                                                                                        $closing_stock_c = $partner_data_t['endbal'];              
                                                                                                                                        $closing_stock_t = $partner_data_c['endbal'];              

        //ending balalnce Screening table      

                                                                                                                                        if(count($closing_stock_s) ==0){
                                                                                                                                            $table_head1_cs = '<h4>Section 2: Facilities with Highest Stocks (in Tests)</h4><h5>a) Screening KHB </h5>';
                                                                                                                                            $table_body1_cs = 'No facilities have high stocks';
                                                                                                                                        }else{
                                                                                                                                            $table_head1_cs = '<h4>Section 2: Facilities with Highest Stocks (in Tests) </h4><h5>a) Screening KHB </h5>
                                                                                                                                            <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                                                    <tr><th>Facility Code</th>
                                                                                                                                                        <th>Facility Name</th>    
                                                                                                                                                        <th>Sub-County</th>            
                                                                                                                                                        <th>Amount (Tests)</th>            
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>';  

                                                                                                                                                    $table_body1_cs = '';
                                                                                                                                                    for ($i=0; $i <count($closing_stock_s) ; $i++) {
                                                                                                                                                        $c_district = $closing_stock_s[$i]['district'];        
                                                                                                                                                        $c_fcode = $closing_stock_s[$i]['facility_code'];                    
                                                                                                                                                        $c_fname = $closing_stock_s[$i]['facility_name'];                    
                                                                                                                                                        $c_end_bal = $closing_stock_s[$i]['closing_stock'];                    
                                                                                                                                                        $table_body1_cs .= '<tr><td>' . $c_fcode . '</td>';
                                                                                                                                                        $table_body1_cs .= '<td>' . $c_fname . '</td>';            
                                                                                                                                                        $table_body1_cs .= '<td>' . $c_district. '</td>';
                                                                                                                                                        $table_body1_cs .= '<td>' . $c_end_bal. '</td></tr>';
                                                                                                                                                    }
                                                                                                                                                    $table_body1_cs .=  '</table><div>';
                                                                                                                                                }

        //ending stock Confirmatory table

                                                                                                                                                if(count($closing_stock_c) ==0){
                                                                                                                                                    $table_head1_cc = '<h5>b) Confirmatory - First Response </h5>';
                                                                                                                                                    $table_body1_cc = 'No facilities have high stocks';
                                                                                                                                                }else{

                                                                                                                                                    $table_head1_cc = '<h5>b) Confirmatory - First Response </h5>
                                                                                                                                                    <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                        <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                                                            <tr><th>Facility Code</th>
                                                                                                                                                                <th>Facility Name</th>
                                                                                                                                                                <th>Sub-County</th>            
                                                                                                                                                                <th>Amount (Tests)</th>            
                                                                                                                                                            </tr>
                                                                                                                                                        </thead>
                                                                                                                                                        <tbody>';      
                                                                                                                                                            $table_body1_cc = '';
                                                                                                                                                            for ($i=0; $i <count($closing_stock_c) ; $i++) {
                                                                                                                                                                $c_district = $closing_stock_c[$i]['district'];        
                                                                                                                                                                $c_fcode = $closing_stock_c[$i]['facility_code'];                    
                                                                                                                                                                $c_end_bal = $closing_stock_c[$i]['closing_stock'];                    
                                                                                                                                                                $c_fname = $closing_stock_c[$i]['facility_name'];                    
                                                                                                                                                                $table_body1_cc .= '<tr><td>' . $c_fcode . '</td>';
                                                                                                                                                                $table_body1_cc .= '<td>' . $c_fname . '</td>';            
                                                                                                                                                                $table_body1_cc .= '<td>' . $c_district. '</td>';
                                                                                                                                                                $table_body1_cc .= '<td>' . $c_end_bal. '</td></tr>';
                                                                                                                                                            }
                                                                                                                                                            $table_body1_cc .=  '</table><div>';
                                                                                                                                                        }

        //ending balance tie breaker table
                                                                                                                                                        if(count($closing_stock_t) ==0){
                                                                                                                                                            $table_head1_ct = '<h5>c) Tiebreaker - Unigold</h5>';
                                                                                                                                                            $table_body1_ct = 'No facilities have high stocks';
                                                                                                                                                        }else{

                                                                                                                                                            $table_head1_ct = '<h5>c) Tiebreaker - Unigold</h5>
                                                                                                                                                            <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                                <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                                                                    <tr><th>Facility Code</th>
                                                                                                                                                                        <th>Facility Name</th>
                                                                                                                                                                        <th>Sub-County</th>            
                                                                                                                                                                        <th>Amount (Tests)</th>            
                                                                                                                                                                    </tr>
                                                                                                                                                                </thead>
                                                                                                                                                                <tbody>';      
                                                                                                                                                                    $table_body1_ct = '';
                                                                                                                                                                    for ($i=0; $i <count($closing_stock_t) ; $i++) {
                                                                                                                                                                        $c_district = $closing_stock_t[$i]['district'];        
                                                                                                                                                                        $c_fcode = $closing_stock_t[$i]['facility_code'];                    
                                                                                                                                                                        $c_fname = $closing_stock_t[$i]['facility_name'];                    
                                                                                                                                                                        $c_end_bal = $closing_stock_t[$i]['closing_stock'];                    
                                                                                                                                                                        $table_body1_ct .= '<tr><td>' . $c_fcode . '</td>';
                                                                                                                                                                        $table_body1_ct .= '<td>' . $c_fname . '</td>';            
                                                                                                                                                                        $table_body1_ct .= '<td>' . $c_district. '</td>';
                                                                                                                                                                        $table_body1_ct .= '<td>' . $c_end_bal. '</td></tr>';
                                                                                                                                                                    }
                                                                                                                                                                    $table_body1_ct .=  '</table><div>';
                                                                                                                                                                }

        //getting expiries data
                                                                                                                                                                $expiries_s = $partner_data_s['expiries'];              
                                                                                                                                                                $expiries_c = $partner_data_t['expiries'];              
                                                                                                                                                                $expiries_t = $partner_data_c['expiries'];              
                                                                                                                                                                $table_foot = '<tr><td><b>Total County Reporting Percentage</b></td>
                                                                                                                                                                <td><b>'.$national_county_p1.'%</b></td>
                                                                                                                                                                <td><b>'.$national_county_p.'%</b></td>
                                                                                                                                                                <td><b>'.$national_county.'%</b></td></tr></tbody></table>';
                                                                                                                                                                $section_1 = $html_title;
        //expiring Screening table      

                                                                                                                                                                if(count($expiries_s) ==0){
                                                                                                                                                                    $table_head1_s = '<h4>Section 3: Facilities with Highest Expiries (in the next 6 months)</h4><h5>a) Screening KHB </h5>';
                                                                                                                                                                    $table_body1_s = 'No expiries reported';
                                                                                                                                                                }else{
                                                                                                                                                                    $table_head1_s = '<h4>Section 3: Facilities with Highest Expiries (in the next 6 months)</h4><h5>a) Screening KHB </h5><br/>
                                                                                                                                                                    <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                                        <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                                                                            <tr><th>Facility Code</th>
                                                                                                                                                                                <th>Facility Name</th>    
                                                                                                                                                                                <th>Sub-County</th>            
                                                                                                                                                                                <th>Amount (Tests)</th>            
                                                                                                                                                                            </tr>
                                                                                                                                                                        </thead>
                                                                                                                                                                        <tbody>';  

                                                                                                                                                                            $table_body1_s = '';
                                                                                                                                                                            for ($i=0; $i <count($expiries_s) ; $i++) {
                                                                                                                                                                                $district = $expiries_s[$i]['district'];        
                                                                                                                                                                                $fcode = $expiries_s[$i]['facility_code'];                    
                                                                                                                                                                                $fname = $expiries_s[$i]['facility_name'];                    
                                                                                                                                                                                $q_expired = $expiries_s[$i]['q_expiring'];                    
                                                                                                                                                                                $table_body1_s .= '<tr><td>' . $fcode . '</td>';
                                                                                                                                                                                $table_body1_s .= '<td>' . $fname . '</td>';            
                                                                                                                                                                                $table_body1_s .= '<td>' . $district. '</td>';
                                                                                                                                                                                $table_body1_s .= '<td>' . $q_expired. '</td></tr>';
                                                                                                                                                                            }
                                                                                                                                                                            $table_body1_s .=  '</table><div>';
                                                                                                                                                                        }

        //expiring Confirmatory table

                                                                                                                                                                        if(count($expiries_c) ==0){
                                                                                                                                                                            $table_head1_c = '<h5>b) Confirmatory - First Response </h5>';
                                                                                                                                                                            $table_body1_c = 'No expiries reported';
                                                                                                                                                                        }else{

                                                                                                                                                                            $table_head1_c = '<h5>b) Confirmatory - First Response </h5>
                                                                                                                                                                            <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                                                <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                                                                                    <tr><th>Facility Code</th>
                                                                                                                                                                                        <th>Facility Name</th>
                                                                                                                                                                                        <th>Sub-County</th>            
                                                                                                                                                                                        <th>Amount (Tests)</th>            
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </thead>
                                                                                                                                                                                <tbody>';      
                                                                                                                                                                                    $table_body1_c = '';
                                                                                                                                                                                    for ($i=0; $i <count($expiries_c) ; $i++) {
                                                                                                                                                                                        $district = $expiries_c[$i]['district'];        
                                                                                                                                                                                        $fcode = $expiries_c[$i]['facility_code'];                    
                                                                                                                                                                                        $q_expired = $expiries_c[$i]['q_expiring'];                    
                                                                                                                                                                                        $fname = $expiries_c[$i]['facility_name'];                    
                                                                                                                                                                                        $table_body1_c .= '<tr><td>' . $fcode . '</td>';
                                                                                                                                                                                        $table_body1_c .= '<td>' . $fname . '</td>';            
                                                                                                                                                                                        $table_body1_c .= '<td>' . $district. '</td>';
                                                                                                                                                                                        $table_body1_c .= '<td>' . $q_expired. '</td></tr>';
                                                                                                                                                                                    }
                                                                                                                                                                                    $table_body1_c .=  '</table><div>';
                                                                                                                                                                                }

        //expiring tie breaker table
                                                                                                                                                                                if(count($expiries_t) ==0){
                                                                                                                                                                                    $table_head1_t = '<h5>c) Tiebreaker - Unigold</h5>';
                                                                                                                                                                                    $table_body1_t = 'No expiries reported';
                                                                                                                                                                                }else{

                                                                                                                                                                                    $table_head1_t = '<h5>c) Tiebreaker - Unigold</h5>
                                                                                                                                                                                    <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                                                        <thead border="0" style="margin: 10px auto;font-weight:900">
                                                                                                                                                                                            <tr><th>Facility Code</th>
                                                                                                                                                                                                <th>Facility Name</th>
                                                                                                                                                                                                <th>Sub-County</th>            
                                                                                                                                                                                                <th>Amount (Tests)</th>            
                                                                                                                                                                                            </tr>
                                                                                                                                                                                        </thead>
                                                                                                                                                                                        <tbody>';      
                                                                                                                                                                                            $table_body1_t = '';
                                                                                                                                                                                            for ($i=0; $i <count($expiries_t) ; $i++) {
                                                                                                                                                                                                $district = $expiries_t[$i]['district'];        
                                                                                                                                                                                                $fcode = $expiries_t[$i]['facility_code'];                    
                                                                                                                                                                                                $fname = $expiries_t[$i]['facility_name'];                    
                                                                                                                                                                                                $q_expired = $expiries_t[$i]['q_expiring'];                    
                                                                                                                                                                                                $table_body1_t .= '<tr><td>' . $fcode . '</td>';
                                                                                                                                                                                                $table_body1_t .= '<td>' . $fname . '</td>';            
                                                                                                                                                                                                $table_body1_t .= '<td>' . $district. '</td>';
                                                                                                                                                                                                $table_body1_t .= '<td>' . $q_expired. '</td></tr>';
                                                                                                                                                                                            }
                                                                                                                                                                                            $table_body1_t .=  '</table><div>';
                                                                                                                                                                                        }

                                                                                                                                                                                        $section_2 = $table_head_stock_card.$table_body_stock_card;
                                                                                                                                                                                        $section_3 = $table_head1_cs.$table_body1_cs.$table_head1_cc.$table_body1_cc.$table_head1_ct.$table_body1_ct;
                                                                                                                                                                                        $section_4 = $table_head1_s.$table_body1_s.$table_head1_c.$table_body1_c.$table_head1_t.$table_body1_t;
                                                                                                                                                                                        $html_data = $section_1.$section_2.$section_3.$section_4;
        //  echo "<pre>";
        //print_r($expiries_t);die();
        // echo "$html_data";die();
        // $email_address = 'ttunduny@gmail.com';
        // $email_address.= 'onjathi@clintonhealthaccess.org,ttunduny@gmail.com,annchemu@gmail.com';
                                                                                                                                                                                        $reportname = 'Percentages for '.$current_month_text;
        //$this->sendmail($html_data,$message, , $email_address);
                                                                                                                                                                                        $this->sendmail($html_data,$message, $reportname, $email_address);           




                                                                                                                                                                                    }

                                                                                                                                                                                    function _get_county_expiries($first_date,$last_date,$county,$commodity_id){
                                                                                                                                                                                        $sql = "SELECT distinct facilities.facility_code,facilities.facility_name,districts.district,lab_commodity_details.q_expiring, lab_commodity_details.closing_stock
                                                                                                                                                                                        FROM lab_commodity_details,facilities,districts
                                                                                                                                                                                        WHERE facilities.facility_code = lab_commodity_details.facility_code
                                                                                                                                                                                        and districts.id = lab_commodity_details.district_id
                                                                                                                                                                                        and lab_commodity_details.created_at BETWEEN '$first_date' AND '$last_date'
                                                                                                                                                                                        and lab_commodity_details.commodity_id = '$commodity_id'
                                                                                                                                                                                        and districts.county = '$county'             
                                                                                                                                                                                        having q_expiring>0 order by lab_commodity_details.q_expiring desc,facilities.facility_code asc limit 0,10";    
                                                                                                                                                                                        $query['expiries'] = $this->db->query($sql)->result_array();

                                                                                                                                                                                        $sql2 = "SELECT distinct facilities.facility_code,facilities.facility_name,districts.district,lab_commodity_details.q_expiring, lab_commodity_details.closing_stock
                                                                                                                                                                                        FROM lab_commodity_details,facilities,districts
                                                                                                                                                                                        WHERE facilities.facility_code = lab_commodity_details.facility_code
                                                                                                                                                                                        and districts.id = lab_commodity_details.district_id
                                                                                                                                                                                        and lab_commodity_details.created_at BETWEEN '$first_date' AND '$last_date'
                                                                                                                                                                                        and lab_commodity_details.commodity_id = '$commodity_id'
                                                                                                                                                                                        and districts.county = '$county'             
                                                                                                                                                                                        having closing_stock>0 order by lab_commodity_details.closing_stock desc,facilities.facility_code asc limit 0,10";    
                                                                                                                                                                                        $query['endbal'] = $this->db->query($sql2)->result_array();

                                                                                                                                                                                        return $query;
                                                                                                                                                                                    }

                                                                                                                                                                                    function _get_partner_expiries($first_date,$last_date,$partner_id,$commodity_id){
                                                                                                                                                                                        $sql = "SELECT distinct facilities.facility_code,facilities.facility_name,districts.district,lab_commodity_details.q_expiring, lab_commodity_details.closing_stock
                                                                                                                                                                                        FROM lab_commodity_details,facilities,districts
                                                                                                                                                                                        WHERE facilities.facility_code = lab_commodity_details.facility_code
                                                                                                                                                                                        and districts.id = lab_commodity_details.district_id
                                                                                                                                                                                        and lab_commodity_details.created_at BETWEEN '$first_date' AND '$last_date'
                                                                                                                                                                                        and lab_commodity_details.commodity_id = '$commodity_id'
                                                                                                                                                                                        and facilities.partner = '$partner_id'             
                                                                                                                                                                                        having q_expiring>0 order by lab_commodity_details.q_expiring desc,facilities.facility_code asc limit 0,10"; 

                                                                                                                                                                                        $query['expiries'] = $this->db->query($sql)->result_array();

                                                                                                                                                                                        $sql2 = "SELECT distinct facilities.facility_code,facilities.facility_name,districts.district,lab_commodity_details.q_expiring, lab_commodity_details.closing_stock
                                                                                                                                                                                        FROM lab_commodity_details,facilities,districts
                                                                                                                                                                                        WHERE facilities.facility_code = lab_commodity_details.facility_code
                                                                                                                                                                                        and districts.id = lab_commodity_details.district_id
                                                                                                                                                                                        and lab_commodity_details.created_at BETWEEN '$first_date' AND '$last_date'
                                                                                                                                                                                        and lab_commodity_details.commodity_id = '$commodity_id'
                                                                                                                                                                                        and facilities.partner = '$partner_id'             
                                                                                                                                                                                        having closing_stock>0 order by lab_commodity_details.closing_stock desc,facilities.facility_code asc limit 0,10";    
                                                                                                                                                                                        $query['endbal'] = $this->db->query($sql2)->result_array();

                                                                                                                                                                                        return $query;
                                                                                                                                                                                    }


                                                                                                                                                                                    public function district_reports($year, $month, $district) {
                                                                                                                                                                                        $pdf_htm = '';   
                                                                                                                                                                                        $first_day_current_month = $year . '-' . $month . '-1';
                                                                                                                                                                                        $firstdate = $year . '-' . $month . '-01';
                                                                                                                                                                                        $month = date("m", strtotime("$firstdate"));
                                                                                                                                                                                        $year = date("Y", strtotime("$firstdate"));
                                                                                                                                                                                        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                                                                                                                                                                        $lastdate = $year . '-' . $month . '-' . $num_days;
                                                                                                                                                                                        $firstdate = $year . '-' . $month . '-01';

                                                                                                                                                                                        $thismonth = date('Y-m', time());
                                                                                                                                                                                        $thismonth .="-1";


                                                                                                                                                                                        $q = "SELECT DISTINCT lab_commodity_orders.facility_code, lab_commodity_orders.id,lab_commodity_orders.order_date
                                                                                                                                                                                        FROM lab_commodity_orders, districts, counties
                                                                                                                                                                                        WHERE districts.id = lab_commodity_orders.district_id
                                                                                                                                                                                        AND districts.county = counties.id
                                                                                                                                                                                        AND districts.id = $district
                                                                                                                                                                                        AND lab_commodity_orders.order_date
                                                                                                                                                                                        BETWEEN '$firstdate'
                                                                                                                                                                                        AND NOW()";       
                                                                                                                                                                                        $res = $this->db->query($q);    
                                                                                                                                                                                        foreach ($res->result_array() as $key => $value) {
                                                                                                                                                                                            $id = $value['id'];
                                                                                                                                                                                            $pdf_htm .= $this->generate_lastpdf($id);
                                                                                                                                                                                            $pdf_htm .= '<br /><br /><br /><hr/><br /><br />';
                                                                                                                                                                                        }
                                                                                                                                                                                        return $pdf_htm;
                                                                                                                                                                                    }

                                                                                                                                                                                    function generate_lastpdf($id) {
                                                                                                                                                                                        $query = $this->db->query('SELECT id
                                                                                                                                                                                            FROM  `lab_commodity_orders` 
                                                                                                                                                                                            where `id` = ' . $id . '
                                                                                                                                                                                            LIMIT 0 , 1');
                                                                                                                                                                                        foreach ($query->result_array() as $row) {
                                                                                                                                                                                            $order_no = $row['id'];
                                                                                                                                                                                        }
                                                                                                                                                                                        $query1 = $this->db->query('SELECT * 
                                                                                                                                                                                            FROM lab_commodity_orders, facilities, districts,counties
                                                                                                                                                                                            WHERE lab_commodity_orders.district_id = districts.id
                                                                                                                                                                                            AND counties.id = districts.county
                                                                                                                                                                                            AND facilities.facility_code = lab_commodity_orders.facility_code
                                                                                                                                                                                            AND lab_commodity_orders.id =' . $order_no . '');
                                                                                                                                                                                        $lab_order = $query1->result_array();

                                                                                                                                                                                        date_default_timezone_set("EUROPE/Moscow");
                                                                                                                                                                                        $firstday = date('D dS M Y', strtotime("first day of previous month"));
                                                                                                                                                                                        $lastday = date('D dS M Y', strtotime("last day of previous month"));
                                                                                                                                                                                        $lastmonth = date('F', strtotime("last day of previous month"));
                                                                                                                                                                                        $end_date = date('dS F Y', strtotime($lab_order[0]['end_date']));
                                                                                                                                                                                        $beg_date = date('dS F Y', strtotime($lab_order[0]['beg_date']));

                                                                                                                                                                                        $orderdate = $lab_order[0]['order_date'];
                                                                                                                                                                                        $month = date('F', strtotime("$orderdate -1 Month"));
                                                                                                                                                                                        $html_title = "<div ALIGN=CENTER><img src='" . base_url() . "assets/img/coat_of_arms.png' height='70' width='70'style='vertical-align: top;' > </img></div>
                                                                                                                                                                                        <div style='text-align:center; font-size: 14px;display: block;font-weight: bold;'>RTK FCDRR Report for " . $lab_order[0]['facility_name'] . "  $month  2014</div>
                                                                                                                                                                                        <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold; font-size: 14px;'>
                                                                                                                                                                                            Ministry of Health</div>
                                                                                                                                                                                            <div style='text-align:center; font-family: arial,helvetica,clean,sans-serif;display: block; font-weight: bold;display: block; font-size: 13px;'>Health Commodities Management Platform</div><hr />
                                                                                                                                                                                            <style>table.data-table {border: 1px solid #DDD;font-size: 13px;border-spacing: 0px;}
                                                                                                                                                                                                table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
                                                                                                                                                                                                table.data-table td, table th {padding: 4px;}
                                                                                                                                                                                                table.data-table td {border: none;border-right: 1px solid #DDD;height: 30px;margin: 0px;border-bottom: 1px solid #DDD;}
                                                                                                                                                                                                .col5{background:#D8D8D8;}</style>";
                                                                                                                                                                                                $table_head = '
                                                                                                                                                                                                <table border="0" class="data-table" style="width: 100%; margin: 10px auto;">
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td>Name of Facility:</td>
                                                                                                                                                                                                        <td colspan="2">' . $lab_order[0]['facility_name'] . '</td>
                                                                                                                                                                                                        <td colspan="3">Applicable to HIV Test Kits Only</td>
                                                                                                                                                                                                        <td colspan="4" style="text-align:center">Applicable to Malaria Testing Only</td>                  
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="2" style="text-align:left">MFL Code:</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['facility_code'] . '</td>
                                                                                                                                                                                                        <td colspan="2" style="text-align:center">Type of Service</td>
                                                                                                                                                                                                        <td colspan="1" style="text-align:center">No. of Tests Done</td>
                                                                                                                                                                                                        <td colspan="1">Test</td>
                                                                                                                                                                                                        <td colspan="1">Category</td>
                                                                                                                                                                                                        <td colspan="1">No. of Tests Performed</td>
                                                                                                                                                                                                        <td colspan="1">No. Positive</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="2" style="text-align:left">District:</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['district'] . '</td>
                                                                                                                                                                                                        <td colspan="2">VCT</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['vct'] . '</td>
                                                                                                                                                                                                        <td rowspan="3">RDT</td>
                                                                                                                                                                                                        <td style="text-align:left">Patients&nbsp;<u>under</u> 5&nbsp;years</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['rdt_under_tests'] . '</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['rdt_under_pos'] . '</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="2" style="text-align:left">County:</td>                     
                                                                                                                                                                                                        <td>' . $lab_order[0]['county'] . '</td>
                                                                                                                                                                                                        <td colspan="2">PITC</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['pitc'] . '</td>
                                                                                                                                                                                                        <td style="text-align:left">Patients&nbsp;aged 5-14&nbsp;yrs</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['rdt_btwn_tests'] . '</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['rdt_btwn_pos'] . '</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr><td colspan="2" style="text-align:right">Beginning:</td> 
                                                                                                                                                                                                        <td>' . $beg_date . '</td>
                                                                                                                                                                                                        <td colspan="2">PMTCT</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['pmtct'] . '</td>
                                                                                                                                                                                                        <td style="text-align:left">Patients&nbsp;<u>over</u> 14&nbsp;years</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['rdt_over_tests'] . '</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['rdt_over_pos'] . '</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="2" style="text-align:right">Ending:</td>
                                                                                                                                                                                                        <td>' . $end_date . '</td>
                                                                                                                                                                                                        <td colspan="2">Blood&nbsp;Screening</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['b_screening'] . '</td>
                                                                                                                                                                                                        <td rowspan="3">Microscopy</td>
                                                                                                                                                                                                        <td style="text-align:left">Patients&nbsp;<u>under</u> 5&nbsp;years</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['micro_under_tests'] . '</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['micro_under_pos'] . '</td>                          
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="3"></td>
                                                                                                                                                                                                        <td colspan="2">Other&nbsp;(Please&nbsp;Specify)</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['other'] . '</td> 
                                                                                                                                                                                                        <td style="text-align:left">Patients&nbsp;aged 5-14&nbsp;yrs</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['micro_btwn_tests'] . '</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['micro_btwn_pos'] . '</td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td colspan="3"></td>
                                                                                                                                                                                                        <td colspan="2">Specify&nbsp;Here:</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['specification'] . '</td>   
                                                                                                                                                                                                        <td style="text-align:left">Patients&nbsp;<u>over</u> 14&nbsp;years</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['micro_over_tests'] . '</td>
                                                                                                                                                                                                        <td>' . $lab_order[0]['micro_over_pos'] . '</td>
                                                                                                                                                                                                    </tr></table>';
                                                                                                                                                                                                    $table_head .= '<style>table.data-table {border: 1px solid #DDD;margin: 10px auto;border-spacing: 0px;}
                                                                                                                                                                                                    table.data-table th {border: none;color: #036;text-align: center;background-color: #F5F5F5;border: 1px solid #DDD;border-top: none;max-width: 450px;}
                                                                                                                                                                                                    table.data-table td, table th {padding: 4px;}
                                                                                                                                                                                                    table.data-table td {border: none;border-left: 1px solid #DDD;border-right: 1px solid #DDD;height: 20px;margin: 0px;border-bottom: 1px solid #DDD;}
                                                                                                                                                                                                    .col5{background:#D8D8D8;}</style></table>
                                                                                                                                                                                                    <table class="data-table" width="100%">
                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                <th rowspan="2"><strong>Commodity</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Unit of Issue</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Beginning Balance</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Quantity Received</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Quantity Used</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Tests Done</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Losses</strong></th>
                                                                                                                                                                                                                <th colspan="2"><strong>Adjustments</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Closing Stock</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Qty Expiring <br />in 6 Months</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Days Out of <br />Stock</strong></th>
                                                                                                                                                                                                                <th rowspan="2"><strong>Qty Requested</strong></th>
                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                <th><strong>Positive</strong></th>
                                                                                                                                                                                                                <th><strong>Negative</strong></th>
                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                        <tbody>';
                                                                                                                                                                                                            $detail_list = Lab_Commodity_Details::get_order($order_no);
                                                                                                                                                                                                            $table_body = '';
                                                                                                                                                                                                            foreach ($detail_list as $detail) {
                                                                                                                                                                                                                $table_body .= '<tr><td>' . $detail['commodity_name'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['unit_of_issue'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['beginning_bal'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['q_received'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['q_used'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['no_of_tests_done'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['losses'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['positive_adj'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['negative_adj'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['closing_stock'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['q_expiring'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['days_out_of_stock'] . '</td>';
                                                                                                                                                                                                                $table_body .= '<td>' . $detail['q_requested'] . '</td></tr>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $table_foot = '</tbody></table>';

                                                                                                                                                                                                            $table_foot .= '
                                                                                                                                                                                                            <table border="0" style="width: 100%;border: 1px solid #DDD;">
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <td style="text-align:left">Explaination of Losses and Adjustments</td><td  style="width: 57%;">' . $lab_order[0]['explanation'] . '</td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                <tr style="background: #ECE8FD;">
                                                                                                                                                                                                                    <td>(1) Daily Activity Register for Laboratory Reagents and Consumables (MOH 642):</td><td>' . $lab_order[0]['moh_642'] . '</td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <td  >(2) F-CDRR for Laboratory Commodities (MOH 643):</b></td><td>' . $lab_order[0]['moh_643'] . '</td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                <tr style="background: #ECE8FD;">                   
                                                                                                                                                                                                                    <td style="text-align:left">Compiled by: </td><td>' . $lab_order[0]['compiled_by'] . '</td>
                                                                                                                                                                                                                </tr> 
                                                                                                                                                                                                            </table>
                                                                                                                                                                                                            <pagebreak/>';
                                                                                                                                                                                                            $report_name = "Lab Commodities Order " . $order_no . " Details";
                                                                                                                                                                                                            $title = "Lab Commodities Order " . $order_no . " Details";
                                                                                                                                                                                                            $html_data = $html_title . $table_head . $table_body . $table_foot;

                                                                                                                                                                                                            $filename = "RTK FCDRR Report for " . $lab_order[0]['facility_name'] . "  $lastmonth  2014";
                                                                                                                                                                                                            return $html_data;
                                                                                                                                                                                                        }

                                                                                                                                                                                                        public function create_pdf($output,$reportname) {
                                                                                                                                                                                                            $this->load->helper('file');        
                                                                                                                                                                                                            $this->load->library('mpdf');
                                                                                                                                                                                                            $mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 5, 'L');
                                                                                                                                                                                                            $mpdf->WriteHTML($output);
                                                                                                                                                                                                            $emailAttachment = $mpdf->Output('./pdf/'.$reportname . '.pdf', 'S');
                                                                                                                                                                                                            $attach_file = './pdf/' . $reportname . '.pdf';
                                                                                                                                                                                                            if (!write_file('./pdf/' . $reportname . '.pdf', $mpdf->Output('report_name.pdf', 'S'))) {        
                                                                                                                                                                                                                $this->session->set_flashdata('system_error_message', 'An error occured');
                                                                                                                                                                                                            } 
                                                                                                                                                                                                        }

                                                                                                                                                                                                        public function sendmail_multiple($message,$email_address,$month) {    
                                                                                                                                                                                                            $this->load->helper('file');
                                                                                                                                                                                                            include 'rtk_mailer.php';
                                                                                                                                                                                                            $newmail = new rtk_mailer();    

                                                                                                                                                                                                            $subject = 'RTK Reports for '.$month;
        //$attach_file = './pdf/' . $reportname . '.pdf';
        // $bcc_email = 'tngugi@clintonhealthaccess.org';
                                                                                                                                                                                                            $bcc_email = 'annchemu@gmail.com';
        //$message = $output;
                                                                                                                                                                                                            $response = $newmail->send_email_multiple($email_address, $message, $subject,$bcc_email);
        // $response= $newmail->send_email(substr($email_address,0,-1),$message,$subject,$attach_file,$bcc_email);
        // if ($response) {            
        //     delete_files('./pdf/' . $reportname . '.pdf');
        // }

                                                                                                                                                                                                        }

                                                                                                                                                                                                        public function sendmail($output, $message,$reportname, $email_address) {
                                                                                                                                                                                                            $this->load->helper('file');
                                                                                                                                                                                                            include 'rtk_mailer.php';
                                                                                                                                                                                                            $newmail = new rtk_mailer();    
                                                                                                                                                                                                            $this->load->library('mpdf');
                                                                                                                                                                                                            $mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 5, 'L');
                                                                                                                                                                                                            $mpdf->WriteHTML($output);
                                                                                                                                                                                                            $emailAttachment = $mpdf->Output('./pdf/'.$reportname . '.pdf', 'S');
                                                                                                                                                                                                            $attach_file = './pdf/' . $reportname . '.pdf';
                                                                                                                                                                                                            if (!write_file('./pdf/' . $reportname . '.pdf', $mpdf->Output('report_name.pdf', 'S'))) {        
                                                                                                                                                                                                                echo "Error";die();
                                                                                                                                                                                                                $this->session->set_flashdata('system_error_message', 'An error occured');
                                                                                                                                                                                                            } else {        
                                                                                                                                                                                                                $subject = '' . $reportname;

                                                                                                                                                                                                                $attach_file = './pdf/' . $reportname . '.pdf';        
        // $bcc_email = 'tngugi@clintonhealthaccess.org';
        //$bcc_email = 'annchemu@gmail.com';
        //$message = $output;
                                                                                                                                                                                                                $response = $newmail->send_email($email_address, $message, $subject, $attach_file, $bcc_email);
        // $response= $newmail->send_email(substr($email_address,0,-1),$message,$subject,$attach_file,$bcc_email);
                                                                                                                                                                                                                if ($response) {            
                                                                                                                                                                                                                    delete_files('./pdf/' . $reportname . '.pdf');
                                                                                                                                                                                                                }
                                                                                                                                                                                                            }
                                                                                                                                                                                                        }
                                                                                                                                                                                                        function _national_reports_sum($year, $month) {

                                                                                                                                                                                                            $returnable = array();

                                                                                                                                                                                                            $firstdate = $year . '-' . $month . '-01';
                                                                                                                                                                                                            $firstday = date("Y-m-d", strtotime("$firstdate Month "));

        // $month = date("m", strtotime("$firstdate  Month "));
        // $year = date("Y", strtotime("$firstdate  Month "));
                                                                                                                                                                                                            $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                                                                                                                                                                                            $lastdate = $year . '-' . $month . '-' . $num_days;    

                                                                                                                                                                                                            $sql = "SELECT 
                                                                                                                                                                                                            SUM(CASE
                                                                                                                                                                                                            WHEN lab_commodity_details.days_out_of_stock > 0 THEN 1
                                                                                                                                                                                                            WHEN lab_commodity_details.days_out_of_stock <= 0 THEN 0
                                                                                                                                                                                                            END) AS sum_days,
                                                                                                                                                                                                            counties.county,
                                                                                                                                                                                                            counties.id,
                                                                                                                                                                                                            lab_commodities.commodity_name,
                                                                                                                                                                                                            SUM(lab_commodity_details.beginning_bal) AS sum_opening,
                                                                                                                                                                                                            SUM(lab_commodity_details.q_received) AS sum_received,
                                                                                                                                                                                                            SUM(lab_commodity_details.q_used) AS sum_used,
                                                                                                                                                                                                            SUM(lab_commodity_details.no_of_tests_done) AS sum_tests,
                                                                                                                                                                                                            SUM(lab_commodity_details.positive_adj) AS sum_positive,
                                                                                                                                                                                                            SUM(lab_commodity_details.negative_adj) AS sum_negative,
                                                                                                                                                                                                            SUM(lab_commodity_details.losses) AS sum_losses,
                                                                                                                                                                                                            SUM(lab_commodity_details.closing_stock) AS sum_closing_bal,
                                                                                                                                                                                                            SUM(lab_commodity_details.q_requested) AS sum_requested,
                                                                                                                                                                                                            SUM(lab_commodity_details.q_expiring) AS sum_expiring
                                                                                                                                                                                                            FROM
                                                                                                                                                                                                            lab_commodity_details,
                                                                                                                                                                                                            facilities,
                                                                                                                                                                                                            districts,
                                                                                                                                                                                                            counties,
                                                                                                                                                                                                            lab_commodities
                                                                                                                                                                                                            WHERE
                                                                                                                                                                                                            created_at BETWEEN '$firstdate' AND '$lastdate'
                                                                                                                                                                                                            AND lab_commodity_details.facility_code = facilities.facility_code
                                                                                                                                                                                                            AND facilities.district = districts.id
                                                                                                                                                                                                            AND districts.county = counties.id
                                                                                                                                                                                                            and lab_commodity_details.commodity_id = lab_commodities.id";                   
        //         and lab_commodities.id between 0 and 6
        // group by counties.id,lab_commodities.id";            
        //$returnable = $this->db->query($sql)->result_array();
                                                                                                                                                                                                            $sql4 = $sql . " AND lab_commodities.id = 4 Group By counties.county";
                                                                                                                                                                                                            $res3 = $this->db->query($sql4)->result_array();
                                                                                                                                                                                                            array_push($returnable, $res3);

                                                                                                                                                                                                            $sql5 = $sql . " AND lab_commodities.id = 5 Group By counties.county";
                                                                                                                                                                                                            $res4 = $this->db->query($sql5)->result_array();
                                                                                                                                                                                                            array_push($returnable, $res4);

                                                                                                                                                                                                            $sql6 = $sql . " AND lab_commodities.id = 6 Group By counties.county";
                                                                                                                                                                                                            $res5 = $this->db->query($sql6)->result_array();
                                                                                                                                                                                                            array_push($returnable, $res5);
        // echo $sql4; die;
        // echo "<pre>";print_r($returnable);die;
                                                                                                                                                                                                            return $returnable;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        public function rtk_manager_admin_settings() {

                                                                                                                                                                                                            $sql = "select rtk_settings.*, user.fname,user.lname from rtk_settings, user where rtk_settings.user_id = user.id ";
                                                                                                                                                                                                            $res = $this->db->query($sql);
                                                                                                                                                                                                            $deadline_data = $res->result_array();

                                                                                                                                                                                                            $sql1 = "select * from rtk_alerts_reference ";
                                                                                                                                                                                                            $res1 = $this->db->query($sql1);
                                                                                                                                                                                                            $alerts_to_data = $res1->result_array();


                                                                                                                                                                                                            $sql3 = "select rtk_alerts.*,rtk_alerts_reference.id as ref_id,rtk_alerts_reference.description as description from rtk_alerts,rtk_alerts_reference where rtk_alerts.reference=rtk_alerts_reference.id order by id ASC,status ASC";
                                                                                                                                                                                                            $res3 = $this->db->query($sql3);
                                                                                                                                                                                                            $alerts_data = $res3->result_array();

                                                                                                                                                                                                            $sql4 = "select lab_commodities.*,lab_commodity_categories.category_name, lab_commodity_categories.id as cat_id from lab_commodities,lab_commodity_categories where lab_commodities.category=lab_commodity_categories.id and lab_commodity_categories.active = '1'";
                                                                                                                                                                                                            $res4 = $this->db->query($sql4);
                                                                                                                                                                                                            $commodities_data = $res4->result_array();

                                                                                                                                                                                                            $sql5 = "select * from lab_commodity_categories";
                                                                                                                                                                                                            $res5 = $this->db->query($sql5);
                                                                                                                                                                                                            $commodity_categories = $res5->result_array();


                                                                                                                                                                                                            $data['deadline_data'] = $deadline_data;
                                                                                                                                                                                                            $data['alerts_to_data'] = $alerts_to_data;
                                                                                                                                                                                                            $data['alerts_data'] = $alerts_data;
                                                                                                                                                                                                            $data['commodities_data'] = $commodities_data;
                                                                                                                                                                                                            $data['commodity_categories'] = $commodity_categories;

                                                                                                                                                                                                            $data['title'] = 'RTK Manager Settings';
                                                                                                                                                                                                            $data['banner_text'] = 'RTK Manager Settings';
        //$data['content_view'] = "rtk/admin/admin_home_view";
                                                                                                                                                                                                            $data['content_view'] = "rtk/rtk/admin/settings";
                                                                                                                                                                                                            $users = $this->_get_rtk_users();
                                                                                                                                                                                                            $data['users'] = $users;
                                                                                                                                                                                                            $this->load->view('rtk/template', $data);
                                                                                                                                                                                                        }

                                                                                                                                                                                                        public function rtk_manager_admin_messages() {

/*$users = array('email' =>'All SCMLTs' , 
'email' =>'All CLCs' ,
'email' =>'Sub-Counties with Less than 25% Reported' ,
'email' =>'Sub-Counties with Less than 50% Reported' ,
'email' =>'Sub-Counties with Less than 75% Reported' ,
'email' =>'Sub-Counties with Less than 90% Reported' );             
echo "<pre>";
print_r($users);die();



$data['emails'] = json_encode($emails);
$data['emails'] = str_replace('"', "'", $data['emails']);
        // echo "<pre>";
        //print_r( $data['emails']);*/



/* $sql1 = "select fname from user";
$res1 = $this->db->query($sql1);
$fname = $res1->result_array();
$data['fname'] = json_encode($fname);
$data['fname'] = str_replace('"', "'", $data['fname']); */
        //$data['details'] = $details;
$data['title'] = 'RTK Manager Messages';
$data['banner_text'] = 'RTK Manager';
        //$data['content_view'] = "rtk/rtk/admin/admin_home_view";
$data['content_view'] = "rtk/rtk/admin/messages";
        //$users = $this->_get_rtk_users();
        // $data['users'] = $users;
$this->load->view('rtk/template', $data);
}
public function create_Deadline() {
    $zones = json_decode($_POST['add_zones']);
    $user_id = $this->session->userdata('user_id');
    $deadline = $_POST['deadline'];
    $five_day_alert = $_POST['five_day_alert'];
    $report_day_alert = $_POST['report_day_alert'];
    $overdue_alert = $_POST['overdue_alert'];
    foreach ($zones as $value) {
        $sql = "INSERT INTO `rtk_settings`(`id`, `deadline`, `status`, `5_day_alert`, `report_day_alert`, `overdue_alert`, `zone`, `user_id`) 
        VALUES (NULL,'$deadline','0','$five_day_alert','$report_day_alert','$overdue_alert','$value','$user_id')";
        $this->db->query($sql);
        $object_id = $this->db->insert_id();
        $this->logData('7', $object_id);
    }
    echo "Deadline Added succesfully";
}

public function create_DMLT() {

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $district = $_POST['district'];
    $county = $_POST['county'];
    $time = date('Y-m-d', time());

    $fname = addslashes($fname);
    $lname = addslashes($lname);

    $sql = "INSERT INTO `user` (`id`, `fname`, `lname`, `email`, `username`, `password`, `usertype_id`, `telephone`, `district`, `facility`, `created_at`, `updated_at`, `status`, `county_id`)
    VALUES (NULL, '$fname', '$lname', '$email', '$email', 'b56578e2f9d28c7497f42b32cbaf7d68', '7', '$phone', '$district', NULL, '$time', '$time', '1', '$county');";
    $this->db->query($sql);
    $object_id = $this->db->insert_id();
    $this->logData('1', $object_id);
    echo "User has been created succesfully";
}
public function create_MLT() {

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $facility = $_POST['facility'];
    $district = $_POST['district'];
    $county = $_POST['county'];
    $time = date('Y-m-d', time());

    $fname = addslashes($fname);
    $lname = addslashes($lname);

    $sql = "INSERT INTO `user` (`id`, `fname`, `lname`, `email`, `username`, `password`, `usertype_id`, `telephone`, `district`, `facility`, `created_at`, `updated_at`, `status`, `county_id`)
    VALUES (NULL, '$fname', '$lname', '$email', '$email', 'b56578e2f9d28c7497f42b32cbaf7d68', '5', '$phone', '$district', '$facility', '$time', '$time', '1', '$county');";
    $this->db->query($sql);
    $object_id = $this->db->insert_id();
    $this->logData('1', $object_id);
    echo "User has been created succesfully";
}
public function update_Deadline() {

    $zones = json_decode($_POST['edit_zones']);
    $edit_id = $_POST['id'];
    $user_id = $this->session->userdata('user_id');
    $deadline = $_POST['deadline'];
    $five_day_alert = $_POST['five_day_alert'];
    $report_day_alert = $_POST['report_day_alert'];
    $overdue_alert = $_POST['overdue_alert'];        

    $sql = "update rtk_settings 
    set deadline='$deadline',5_day_alert = '$five_day_alert',report_day_alert='$report_day_alert',overdue_alert='$overdue_alert',user_id='$user_id' where id='$edit_id'";
    $this->db->query($sql);
    $object_id = $edit_id;
    $this->logData('8', $object_id);
    echo "Deadline Updated succesfully";
}

public function create_Alert() {
    $message = $_POST['message'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    $reference = $_POST['reference'];
    $sql1 = "INSERT INTO `rtk_alerts`(`id`,`message`, `type`, `status`,`reference`) VALUES (NULL,'$message','$type','$status','$reference')";

    $this->db->query($sql1);
    $object_id = $this->db->insert_id();
    $this->logData('10', $object_id);
    echo "Alert Created Succesfully";
}

public function update_Alert() {
    $message = $_POST['message'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    $alert_to = $_POST['alert_to'];
    $id = $_POST['c_id'];
    $sql = "UPDATE `rtk_alerts` SET `message`='$message',`type`='$type',`status`='$status',`reference`='$alert_to' WHERE `id`='$id'";
    $this->db->query($sql);
    $object_id = $id;
    $this->logData('11', $object_id);
    echo "Alert Updated Succesfully";
}

public function create_Commodity() {
    $name = $_POST['name'];
    $unit = $_POST['unit'];
    $category = $_POST['category'];
    $sql = "INSERT INTO `lab_commodities`(`id`, `commodity_name`, `category`, `unit_of_issue`) VALUES (NULL,'$name','$category','$unit')";
    $this->db->query($sql);
    $object_id = $this->db->insert_id();
    $this->logData('4', $object_id);
    echo "Commodity Created Succesfully";
}

public function update_Commodity() {
    $name = $_POST['name'];
    $unit = $_POST['unit'];
    $category = $_POST['category'];
    $c_id = $_POST['c_id'];
    $sql = "UPDATE `lab_commodities` SET `commodity_name`='$name',`category`='$category',`unit_of_issue`='$unit' WHERE id='$c_id'";
    $this->db->query($sql);
    $object_id = $c_id;
    $this->logData('5', $object_id);

    echo "Commodity Updated Succesfully";
}

public function get_all_zone_a_facilities($zone){
    $sql = "SELECT DISTINCT
    facilities.*,
    districts.district,
    counties.county    
    FROM
    facilities,
    counties,
    districts,
    lab_commodity_details
    WHERE
    facilities.zone = 'Zone $zone'
    AND facilities.rtk_enabled = 1
    AND districts.id = facilities.district
    and lab_commodity_details.facility_code = facilities.facility_code                                
    and lab_commodity_details.created_at between '2014-09-01' and '2014-11-31'
    AND counties.id = districts.county ";

    $facilities = $this->db->query($sql)->result_array();
        //echo count($facilities);die;
    $amcs = array();
    foreach ($facilities as $key => $value) {
        $fcode = $value['facility_code'];
        $q = "SELECT DISTINCT
        lab_commodities.*, facility_amc.*,lab_commodity_details.closing_stock
        FROM
        lab_commodities,
        facility_amc,
        lab_commodity_details
        WHERE
        lab_commodities.id = facility_amc.commodity_id
        AND facility_amc.facility_code = '$fcode'
        AND lab_commodity_details.commodity_id = lab_commodities.id
        and lab_commodity_details.facility_code = facility_amc.facility_code
        AND lab_commodity_details.commodity_id BETWEEN 0 AND 6
        and lab_commodity_details.created_at between '2014-11-01' and '2014-11-31'";

        $res1 = $this->db->query($q);
        $amc_details = $res1->result_array();
        //echo "<pre>"; print_r($amc_details);die;

        $amcs[$fcode] = $amc_details;
        //$amcs2[$fcode] = $amc_details2;                 

    }
        // echo "<pre>"; print_r($amcs[$fcode]);die;

    $data['title'] = "Zone $zone List";
    $data['banner_text'] = "Facilities in Zone $zone";
    $data['content_view'] = 'rtk/allocation_committee/zone_a';        
    $data['facilities'] = $facilities;
    $data['amcs'] = $amcs;
    $this->load->view('rtk/template', $data);        

}
public function non_reported_facilities(){
    $month =  date("mY", time());

    $one_months_ago = date("Y-m-", strtotime("-1 Month "));
    $two_months_ago = date("Y-m-", strtotime("-2 Month "));
    $three_months_ago = date("Y-m-", strtotime("-3 Month "));
    $four_months_ago = date("Y-m-", strtotime("-4 Month "));

    $four_months_ago .='1';
    $end_date = date("Y-m-", strtotime("-1 Month "));
    $end_date .='31';

        // $firstday = '2014-06-01';
        // $lastday = '2014-09-30';        
        // ini_set('memory_limit', '750M');
    $sql = "select distinct
    facilities.facility_code,
    lab_commodity_orders.order_date,
    facilities.facility_name,
    facilities.zone,
    districts.district,
    counties.county
    from
    facilities,
    counties,
    districts,
    lab_commodity_orders
    where
    lab_commodity_orders.order_date between '2014-06-01' and '2014-09-30'
    and 
    facilities.rtk_enabled = 1
    and districts.id = facilities.district
    and counties.id = districts.county
    and facilities.facility_code not in (select distinct
    facilities.facility_code
    from
    facilities,
    lab_commodity_orders
    where
    lab_commodity_orders.order_date between '2015-05-01' and '2015-08-30'
    and facilities.facility_code = lab_commodity_orders.facility_code
    )
    group by facilities.facility_code,extract(YEAR_MONTH from lab_commodity_orders.order_date) limit 0,5";
        // echo $sql; die;
    $res = $this->db->query($sql);
    echo "<pre>";
    print_r($res->result_array());die();
    $facilities = $res->result_array(); 

    foreach ($facilities as $key => $value) {
        $fcode = $value['facility_code'];
        $q = "select distinct
        facilities.facility_code,
        lab_commodity_orders.order_date,
        facilities.facility_name,
        facilities.zone,
        districts.district,
        counties.county
        from
        facilities,
        counties,
        districts,
        lab_commodity_orders
        where
        lab_commodity_orders.order_date between '2014-06-01' and '2014-09-30'
        and 
        facilities.rtk_enabled = 1
        and 
        facilities.facility_code = '$fcode'
        and districts.id = facilities.district
        and counties.id = districts.county
        and facilities.facility_code not in (select distinct
        facilities.facility_code
        from
        facilities,
        lab_commodity_orders
        where
        lab_commodity_orders.order_date between '2014-06-01' and '2014-09-30'
        and facilities.facility_code = lab_commodity_orders.facility_code
        )
        group by facilities.facility_code,extract(YEAR_MONTH from lab_commodity_orders.order_date) limit 0,5";
        $res1 = $this->db->query($q)->result_array();  
        $orders = array();
        foreach ($res1 as $keys => $values) {
            $order_date = $values['order_date'];
            array_push($orders, $order_date);
        }

        $dates[$fcode] = array(
            'county'=> $value['county'],
            'subcounty'=> $value['district'],
            'zone'=> $value['zone'],
            'fcode'=> $value['facility_code'],
            'name'=> $value['facility_name'],
            'dates'=> $orders
            );
        // echo "<pre>";
        // print_r($dates);
        // die;
    }

    $data['title'] = 'Unreported Facilities ';
    $data['banner_text'] = 'Facilities not Reported between June and September';
    $data['content_view'] = 'rtk/allocation_committee/allocation_non_reported';        
    $data['facilities'] = $facilities;
    $data['orderdate'] = $dates;
    $this->load->view('rtk/template', $data);   
}

public function new_non_reported_facilities($a=null){    
    if(isset($a)){
        $zone = $a;
    }else{
        $zone = 'A';
    }


        // $m4 = date('Y-m',strtotime('-1 month'));      
    $m3 = date('Y-m',strtotime('-2 month'));
    $m2= date('Y-m',strtotime('-3 month'));
    $m1 = date('Y-m',strtotime('-4 month'));
        // $m0 = date('Y-m',strtotime('-5 month'));

        // $month_text4 = date('F',strtotime('-1 month'));  
    $month_text3 = date('F',strtotime('-2 month'));  
    $month_text2 = date('F',strtotime('-3 month'));  
    $month_text1 = date('F',strtotime('-4 month'));
        // $month_text0 = date('F',strtotime('-5 month'));

    $first = $m1.'-01';
    $last = $m3.'-31';

    $months = array($m1,$m2,$m3);
    $month_texts = array($month_text1,$month_text2,$month_text3);
    $count = count($months); 
        // echo $first.'and'.$last;
        // print_r($month_texts);die;

        // $sql = "select facilities.facility_code,facilities.facility_name,districts.district,counties.county
        //         from  facilities,districts,counties  where rtk_enabled = 1 and zone = 'Zone C' and facilities.district = districts.id               and districts.county = counties.id
        //         order by facility_code ASC    LIMIT 0 , 100";       
    $sql = "select distinct  facilities.facility_code,facilities.facility_name,districts.district,counties.county,facilities.zone,
    COUNT(facilities.facility_code) as total  from
    facilities,
    districts,
    counties,
    lab_commodity_orders
    where
    rtk_enabled = 1 and zone = 'Zone $zone'
    and facilities.district = districts.id
    and districts.county = counties.id
    and facilities.facility_code = lab_commodity_orders.facility_code
    and lab_commodity_orders.order_date between '$first' and '$last'
    group by facilities.facility_code
    having total < 4
    order by facility_code ASC";
        //echo "$sql";die();

    $facilities = $this->db->query($sql)->result_array();


    for ($i=0; $i <$count ; $i++) { 

        $month = $months[$i];
        $final_array[$month] = array();
        $firstdate = $months[$i].'-01';
        $lastdate = $months[$i].'-31';

        foreach ($facilities as $key => $value) {
            $fcode = $value['facility_code'];
            $reportings[$fcode] = array();
            $q = "SELECT distinct COUNT(facilities.facility_code) as total,
            SUM(CASE when facilities.facility_code <> '' THEN 1 ELSE 0 END) FROM
            facilities LEFT JOIN  lab_commodity_orders ON facilities.facility_code= lab_commodity_orders.facility_code
            WHERE facilities.facility_code = '$fcode'
            and lab_commodity_orders.order_date between '$firstdate' and '$lastdate'";     

            $reported = $this->db->query($q)->result_array(); 

            foreach ($reported as $keys => $values) {
                $check = $values['total'];
                $state = '';
                if($check==0){
                    $state = 'N';
                }else{
                    $state = 'Y';
                }


                array_push($reportings[$fcode], $state);
            }               


        }
        array_push($final_array[$month], $reportings);
        array_push($final_array[$month]['total'], $total);

    }     


    $data['facilities'] = $facilities;
    $data['months'] = $months;
    $data['month_texts'] = $month_texts;
    $data['final_array'] = $final_array;   
    $data['title'] = 'RTK Allocations: Non Reported Facilities';    
    $data['banner_text'] = 'Non Reported Facilities';
    $data['content_view'] = "rtk/allocation_committee/allocation_non_reported";
    $this->load->view('rtk/template', $data);        

}
function all_facilities(){
    $sql ="select distinct facilities.facility_code,counties.county, districts.district, facilities.facility_name from facilities, counties, districts
    where facilities.district = districts.id and districts.county = counties.id and facilities.rtk_enabled=1 order by counties.county";
    $result = $this->db->query($sql)->result_array();

    $data['title'] = 'Unreported Facilities ';
    $data['banner_text'] = 'Facilities not Reported between June and September';
    $data['content_view'] = 'rtk/allocation_committee/zone_b';        
    $data['result'] = $result;
    $this->load->view('rtk/template', $data); 
}

public function delete_alert() {       
    $id = $_POST['id'];
    $sql = "DELETE FROM `rtk_alerts` WHERE id='$id'";
    $this->db->query($sql);
    $object_id = $edit_id;
    $this->logData('12', $object_id);
    echo "Alert Deleted Succesfully";
}

public function update_labs($zone,$year=null,$month=null){                
    ini_set(-1);
        // if(isset($year)){
        //     $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        //     $firstdate = $year.'-'.$month.'-01';
        //     $lastdate = $year.'-'.$month.'-'.$num_days; 
        // }else{
        //             //$month = date('mY',strtotime('-3 month'));      
        //     $month =  date("mY", time());
        //     $year = substr($month, -4);
        //     $month = substr($month, 0,2);
        //     $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        //     $firstdate = $year.'-'.$month.'-01';
        //     $lastdate = $year.'-'.$month.'-'.$num_days; 
        // }
    if(isset($year)){
        $month = substr($year, 0,2);
        $year = substr($year, -4);                    
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstdate = $year.'-'.$month.'-01';
        $lastdate = $year.'-'.$month.'-'.$num_days; 
    }else{
        //$month = date('mY',strtotime('-3 month'));      
        $month =  date("mY", time());
        $year = substr($month, -4);
        $month = substr($month, 0,2);
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstdate = $year.'-'.$month.'-01';
        $lastdate = $year.'-'.$month.'-'.$num_days; 
    }
        // echo  $firstdate .'and '. $lastdate; die;

    $sql = "select distinct facility_code from facilities where rtk_enabled=1 and zone='Zone $zone' and exists
    (select distinct facility_code from lab_commodity_details where created_at between '$firstdate' and '$lastdate') order by facility_code asc";

    $facilities = $this->db->query($sql)->result_array();                    
    $count = 0; 
    $large_array[$code] = array();
    foreach ($facilities as $key => $value) {
        $code = $value['facility_code'];
        $q = "select order_id,facility_code,q_used,commodity_id,unit_of_issue,created_at from lab_commodity_details 
        where facility_code='$code' and created_at between '$firstdate' and '$lastdate'";                     
        $res = $this->db->query($q)->result_array();                       
        if(count($res)<1){
        // break 1;
        }else{                         
            foreach ($res as $keys => $values) {                        
                $order_id = $values['order_id'];
                $facility_code = $values['facility_code'];
                $unit = $values['unit_of_issue'];                            
                $q_used = $values['q_used'];                            
                $commodity_id = $values['commodity_id'];
                $created_at = $values['created_at'];
                $small_array[$commodity_id] = array('order_id'=>$order_id,
                    'unit'=>$unit,
                    'q_used'=>$q_used,
                    'created_at'=>$created_at,
                    'commodity_id'=>$commodity_id,
                    'facility_code'=>$facility_code);

                if($values['commodity_id']==1){
                    $screening_det_q_used = $values['q_used'];                            
                }
                if ($values['commodity_id']==4){
                    $screening_khb_q_used =$values['q_used'];                            
                }

            }

            if($screening_det_q_used==$screening_khb_q_used){
                $new_val = $screening_khb_q_used;                        
            }else{
                $new_val = $screening_khb_q_used + (2* $screening_det_q_used);                        
            }

            $large_array[$code][$count] = $small_array;                        
            foreach ($large_array[$code] as $count=>$value) {
                foreach ($value as $key => $values) {                            
                    $order_id = $values['order_id'];
                    $facility_code = $values['facility_code'];
                    $unit = $values['unit'];                                
                    $q_used = $values['q_used'];
                    $commodity_id = $values['commodity_id'];
                    $created_at = $values['created_at'];

                    $new_q = "select * from `lab_commodity_details1` where facility_code='$facility_code' and order_id = '$order_id' and commodity_id = '$commodity_id' and created_at between '$firstdate' and '$lastdate'";
                    $new_res = $this->db->query($new_q)->result_array();   
                    if(count($new_res)>1){
                        if($commodity_id==4){
                            $sql1 = "update `lab_commodity_details1` set  `unit_of_issue`='$unit',
                            `q_used`='$new_val' where facility_code='$facility_code' and order_id = '$order_id' and `commodity_id`='$commodity_id'";                                          
                            $this->db->query($sql1); 
                        }else{
                            $sql1 = "update `lab_commodity_details1` set   `unit_of_issue`='$unit',`q_used`='$q_used' where facility_code='$facility_code' and order_id = '$order_id' and `commodity_id`='$commodity_id'";
        // $sql1 = "INSERT INTO `lab_commodity_details1`(`order_id`, `facility_code`, `commodity_id`, `unit_of_issue`, `q_used`, `created_at`) 
        //  VALUES ('$order_id','$facility_code','$commodity_id','$unit','$q_used','$created_at')";
        //die(); 
                            $this->db->query($sql1); 

                        }
                    }else{
                        if($commodity_id==4){
                            $sql1 = "INSERT INTO `lab_commodity_details1`(`order_id`, `facility_code`, `commodity_id`, `unit_of_issue`, `q_used`, `created_at`) 
                            VALUES ('$order_id','$facility_code','$commodity_id','$unit','$new_val','$created_at')";
        // echo "$sql1";die();
                            $this->db->query($sql1); 
                        }else{
                            $sql1 = "INSERT INTO `lab_commodity_details1`(`order_id`, `facility_code`, `commodity_id`, `unit_of_issue`, `q_used`, `created_at`) 
                            VALUES ('$order_id','$facility_code','$commodity_id','$unit','$q_used','$created_at')";
        // echo "$sql1";                              die(); 
                            $this->db->query($sql1); 

                        }

                    }

                }

            }
        }

        $count++;                                                                         

    }

}

public function add_user() {        
    $this->load->model('user');
    $this->user->add_user();
    redirect('rtk_management/rtk_manager_users');
}


public function change_password() {        
    $this->load->model('user');
    $this->user->edit_user_password();
    echo "Password Succesfully Changed";
}

public function reset_password($id) {        
    $this->load->model('user');
        //$user_id = $this->input->post('user_id');        
    $this->user->reset_user_password($id);
    echo "Password Succesfully Changed";
}

public function manage_user($a,$id) {      

    $this->load->model('user');
        //$user_id = $this->input->post('user_id');        
    $this->user->manage_user_state($a,$id);    
}
public function clean_data($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $year . '-' . $month . '-1';         
        $monthyear1 = $year . '-' . $month . '-31';         

    }
    $q = "select distinct facility_code from facilities where rtk_enabled=1 and exists
    (select distinct facility_code from lab_commodity_details) order by facility_code";
    $res = $this->db->query($q)->result_array();

    foreach ($res as $key => $value) {
        $code = $value['facility_code'];
        $sql = "select  distinct lab_commodity_details1.facility_code
        from
        lab_commodity_details1        
        where
        lab_commodity_details1.facility_code = '$code'";
        $res1 = $this->db->query($sql)->result_array();
        foreach ($res as $key => $value) {
            $fcode = $value['facility_code'];
            $r = "update lab_commodity_details1 set created_at='0000-00-00' 
            where lab_commodity_details1.facility_code = $code and lab_commodity_details1.created_at 
            between '$monthyear' and '$monthyear1'";
            $this->db->query($r);
        }
    }


}   


public function new_get_duplicates($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $first_date = $year . '-' . $month . '-01';         
        $last_date = $year . '-' . $month . '-31';         

    } 

    $commodities = array(1,2,3,4,5,6);
    for ($i=0; $i <count($commodities) ; $i++) { 
        $comm_id = $commodities[$i];
        $sql = "select id,facility_code,order_id,count(order_id) as total from lab_commodity_details 
        where commodity_id='$comm_id' and created_at between '$first_date' and '$last_date' group by order_id having total>1";
        $res_order = $this->db->query($sql)->result_array();
        $count = count($res_order);
        //echo "$sql| $count<br/>";
        if($count!=0){
            $orders = array();
            foreach ($res_order as $key => $value) {
                $mfl = $value['facility_code'];
                $order = $value['order_id'];
        //array_push($facils, $mfl);
                array_push($orders, $order);
            }
            for ($a=1; $a <count($orders); $a++) { 
                $id = $orders[$a];
                $sql2 ="DELETE FROM `lab_commodity_details` WHERE order_id='$id' and commodity_id='$comm_id' and created_at between '$first_date' and '$last_date'";  
                echo "$sql2<br/>";              
                $this->db->query($sql2);
            }      
        }
    }


}     

public function get_duplicates($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $first_date = $year . '-' . $month . '-01';         
        $last_date = $year . '-' . $month . '-31';         

    } 
    $sql = "SELECT lab_commodity_details.facility_code,order_id, COUNT(lab_commodity_details.facility_code ) as total
    FROM lab_commodity_details
    WHERE lab_commodity_details.created_at
    BETWEEN '$first_date'
    AND '$last_date'
    GROUP BY lab_commodity_details.facility_code having total>6
    ORDER BY facility_code,order_id,COUNT( lab_commodity_details.facility_code ) DESC";              
        //echo "$sql";die();sour
    $result = $this->db->query($sql)->result_array();

    $facils = array();
    $orders = array();
    foreach ($result as $key => $value) {
        $mfl = $value['facility_code'];
        $order = $value['order_id'];
        array_push($facils, $mfl);
        array_push($orders, $order);
    }   


    for($i=0;$i<count($facils);$i++){    
        $code= $facils[$i];
        $order_id= $orders[$i];

        $sql1 = "select id from lab_commodity_details where facility_code=$code and order_id=$order_id and created_at  BETWEEN '$first_date'
        AND '$last_date' order by id asc";
        $dups = $this->db->query($sql1)->result_array();
        $new_dups = array();                       
        foreach ($dups as $key=>$value) {
            $id = $value['id'];
            array_push($new_dups,$id);                
        }
        for ($a=6; $a <count($new_dups) ; $a++) { 
            $id = $new_dups[$a];
            $sql2 ="DELETE FROM `lab_commodity_details` WHERE id='$id'";  
            echo "$sql2<br/>";              
            $this->db->query($sql2);
        }                  


    }      

}     
public function get_duplicates_orders($month=null){
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $first_date = $year . '-' . $month . '-01';         
        $last_date = $year . '-' . $month . '-31';         

    } 
    $sql = "SELECT lab_commodity_orders.facility_code,id, COUNT(lab_commodity_orders.facility_code ) as total
    FROM lab_commodity_orders
    WHERE lab_commodity_orders.order_date
    BETWEEN '$first_date'
    AND '$last_date'
    GROUP BY lab_commodity_orders.facility_code having total>1
    ORDER BY facility_code,id,COUNT( lab_commodity_orders.facility_code ) DESC";              
        //echo "$sql";die();
    $result = $this->db->query($sql)->result_array();


    $facils = array();
    $orders = array();
    foreach ($result as $key => $value) {
        $mfl = $value['facility_code'];
        $order = $value['id'];
        array_push($facils, $mfl);
        array_push($orders, $order);
    }   

    for($i=0;$i<count($facils);$i++){    
        $code= $facils[$i];
        $order_id= $orders[$i];            
        $sql1 = "select id from lab_commodity_orders where facility_code='$code' and order_date  BETWEEN '$first_date'
        AND '$last_date' order by id asc";
        $dups = $this->db->query($sql1)->result_array();
        $new_dups = array();       

        foreach ($dups as $key=>$value) {
            $id = $value['id'];
            array_push($new_dups,$id);                
        }


        for ($a=1; $a <count($new_dups) ; $a++) { 
            $id = $new_dups[$a];
            $sql2 ="DELETE FROM `lab_commodity_orders` WHERE id='$id';";  
            echo "$sql2<br/>";              
            $this->db->query($sql2);
        }                  

        //die();

    }      


        // for($i=0;$i<count($facils);$i++){    
        //     $code= $facils[$i];
        //     $order_id= $orders[$i];

        //     $sql1 = "select id from lab_commodity_orders where facility_code='$code' and id='$order_id' and order_date  BETWEEN '$first_date'
        //     AND '$last_date' order by id asc";
        //     $dups = $this->db->query($sql1)->result_array();
        //     $new_dups = array();                       
        //     foreach ($dups as $key=>$value) {
        //         $id = $value['id'];
        //         array_push($new_dups,$id);                
        //     }
        //     for ($a=0; $a <count($new_dups) ; $a++) { 
        //         $id = $new_dups[$a];
        //         $sql2 ="DELETE FROM `lab_commodity_orders` WHERE id='$id'";  
        //                 //echo "$sql2<br/>";              
        //         $this->db->query($sql2);
        //     }                  


        // }      

}     

}

?>
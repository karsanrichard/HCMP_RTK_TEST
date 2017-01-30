<?php
/*

*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once ('home_controller.php');

class cd4_Management extends Home_controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        ini_set('memory_limit', '-1');
        ini_set('max_input_vars', 3000);
    }

    public function index() {
        echo "|";
    }

 public function facility_home(){


        echo $facility_id = $this->session->userdata('facility_id');  

        // // print_r($this->session->all_userdata());die;


        // echo $sql = "SELECT * FROM facility WHERE id = '$facility_id'";

        // $res = $this->db->query($sql);

        // print_r($res);die;

        // $mfl = $res[0]["mfl_code"];


        redirect("cd4_management/facility_profile/$facility_id");

 }

 /////////CD4 ADMIN FUNCTIONS


public function cd4_manager_home() {
    $data = array();
    $data['title'] = 'CD4 Manager';
    $data['banner_text'] = 'CD4 Manager';
    $data['content_view'] = "cd4/admin/home_v";
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

    //SCMLT FUNCTUONS

     public function scmlt_home(){
        date_default_timezone_set("EUROPE/Moscow");
        $district = $this->session->userdata('district_id');                
        $facilities = Facilities::get_total_facilities_cd4_in_district($district);       
        $district_name = districts::get_district_name_($district);                    
        $table_body = '';
        $reported = 0;
        $nonreported = 0;
        $date = date('d', time());

        // $msg = $this->session->flashdata('message');
        // if(isset($msg)){
        //     $data['notif_message'] = $msg;
        // }
        // if(isset($popout)){
        //     $data['popout'] = $popout;
        // }
     

        
        foreach ($facilities as $facility_detail) {

           $lastmonth = date('F', strtotime("last day of previous month"));
            if($date>$deadline_date){
               
                $cd4_report_link = "<td><span class='label label-danger'>  Pending for $lastmonth </span> <span><a href='" . site_url('cd4_management/get_cd4_report/' . $facility_detail['facility_code']) . "' class='link report'> Report</a></span></td>";
                // echo $cd4_report_link;die;
            }else{
                $cd4_report_link = "<td><span class='label label-danger'>  Pending for $lastmonth </span> <span><a href='" . site_url('cd4_management/get_cd4_report/' . $facility_detail['facility_code']) . "' class='link '> Report</a></span></td>";
            }

            
            $table_body .="<tr><td><a class='ajax_call_1' id='county_facility' name='" . base_url() . "rtk_management/get_rtk_facility_detail/$facility_detail[facility_code]' href='#'>" . $facility_detail["facility_code"] . "</td>";
            $table_body .="<td>" . $facility_detail['facility_name'] . "</td><td>" . $district_name['district'] . "</td>";
            $table_body .="";

            $lab_count = lab_commodity_orders::get_recent_lab_orders($facility_detail['facility_code']);
            
            $lab_count = cd4_fcdrr::get_recent_cd4_fcdrr($facility_detail['facility_code']);

            if ($lab_count > 0) {
                $reported = $reported + 1;              
                $table_body .="<td><span class='label label-success'>Submitted  for    $lastmonth </span><a href=" . site_url('cd4_management/fcdrrs') . " class='link'> View</a></td>";
            } 
            else {
                $nonreported = $nonreported + 1;
                $table_body .=$cd4_report_link;
            }   

            $table_body .="</tr>";

        // echo  $table_body;die;
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

        $percentage_complete = ceil($reported / $total * 100);
        $percentage_complete = number_format($percentage_complete, 0);
        $total = $reported + $nonreported;

        // $county = $this->session->userdata('county_name');
        // $countyid = $this->session->userdata('county_id');
        // $data['countyid'] = $countyid;
        // $data['county'] = $county;
        $data['percentage_complete'] = $percentage_complete;
        $data['reported'] = $reported;
        $data['nonreported'] = $nonreported;
        $data['remainingdays'] = $remainingdays;
        $data['facilities'] = Facilities::get_total_facilities_rtk_in_district($district);
        $data['district_name'] = $district_name['district'];
        $data['table_body'] = $table_body;
        $data['content_view'] = "cd4/scmlt/dpp_home_with_table";
        $data['title'] = "Home";
        $data['link'] = "home";        
       
        $this->load->view('rtk/template', $data);

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
    // // $this->_update_reports_count('add',$county,$district_id,$partner);
    // $this->session->set_flashdata('message', 'The report has been saved');
    // redirect('cd4_management/facility_home');

  echo   $usertype_id = $this->session->userdata('user_type_id');

    if ($usertype_id ==8 ||$usertype_id == 7) {
    redirect("cd4_management/scmlt_home");
        
    }else  if ($usertype_id ==5){
    // redirect("Home");
    redirect("cd4_management/facility_profile/".$facility_code);

    }
}


    //Edit FCDRR
public function edit_lab_order_details($order_id, $msg = NULL) {
    
    ini_set('memory_limit', '-1');

    $sql ='SELECT *, cd4_fcdrr.id as order_id FROM
                counties,
                facilities,
                districts,
                cd4_fcdrr,
                cd4_fcdrr_commodities,
                cd4_commodities
            WHERE
                cd4_fcdrr.facility_code = facilities.facility_code
                    AND facilities.district = districts.id
                    AND counties.id = districts.county
                    AND cd4_fcdrr_commodities.commodity_id = cd4_commodities.id
                    AND cd4_fcdrr_commodities.fcdrr_id = cd4_fcdrr.id
                    AND cd4_fcdrr.id = ' . $order_id ;
    $result = $this->db->query($sql)->result_array();
// echo "<pre>"; print_r($result); die;

    $data['order_id'] = $order_id;
    $data['all_details'] = $result;      
    $data['title'] = "Lab Commodity Order Details"; 
    $data['content_view'] = "cd4/scmlt/cd4_fcdrr_edit";
    $data['banner_text'] = "CD4 Lab Commodity Order Details";
      
    $this->load->view("rtk/template", $data);
}

    //Update the FCDRR Online
public function update_lab_commodity_orders() {
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



    $user_id = $this->session->userdata('user_id');  
  
    $sql = "UPDATE `cd4_fcdrr` SET `calibur_adults`='$calibur_adult',`calibur_pead`='$calibur_pead',`caliburs`='$caliburs',`count_adults`='$count_adult',`count_pead`='$count_pead',`counts`='$counts',`cyflow_adults`='$partec_adult',`cyflow_pead`='$partec_pead',`cyflows`='$cyflows',`pima_tests`='$pima',`presto_tests`='$presto',`adults_bel_cl`='$adults_bel_cl',`pead_bel_cl`='$pead_bel_cl',
    `explanation`=explanation,`moh_642`='$moh_642',`moh_643`='$moh_643',`compiled_by`='$compiled_by' WHERE id = $order_id";

    $this->db->query($sql);

    $sql2 = "select id from cd4_fcdrr_commodities where fcdrr_id = $order_id";
    $result2 = $this->db->query($sql2)->result_array();
    
    for ($i = 0; $i < $detail_count; $i++) {

        $id = $result2[$i]['id'];   
        $sql3 = "UPDATE `cd4_fcdrr_commodities` SET `beginning_bal`=$b_balance[$i],
        `q_received`='$q_received[$i]',`q_used`=$q_used[$i],`no_of_tests_done`=$tests_done[$i],`losses`=$losses[$i],`positive_adj`=$pos_adj[$i],`negative_adj`=$neg_adj[$i],`closing_stock`=$physical_count[$i],
        `q_expiring`=$q_expiring[$i],`days_out_of_stock`=$days_out_of_stock[$i],`q_requested`=$q_requested[$i] WHERE id= $id ";
        $this->db->query($sql3);
    }

    $usertype_id = $this->session->userdata('user_type_id');

    if ($usertype_id ==8 ||$usertype_id == 7) {
    redirect("cd4_management/scmlt_home");
        
    }else  if ($usertype_id ==5){
    // redirect("Home");
    redirect("cd4_management/facility_profile/".$facility_code);

    }
}

public function cd4_reports_summary($year, $month, $county = null, $district=null) {
   
        $distname = districts::get_district_name($district);
        $districtname = $distname[0]['district'];
        $district_id = $district;
        $returnable = array();
        $nonreported;
        $reported_percentage;
        $late_percentage;
        $conditions = '';

        if (isset($district)) {
           $conditions .= ' and districts.id = '.$district;
        }
        if (isset($county)) {
           $conditions .= ' and counties.id = '.$county;
        }

        // Sets the timezone and date variables for last day of previous month and this month
        date_default_timezone_set('EUROPE/moscow');
        $month = $month + 1;
        $prev_month = $month - 1;
        $last_day_current_month = date('Y-m-d', mktime(0, 0, 0, $month, 0, $year));
        $first_day_current_month = date('Y-m-', mktime(0, 0, 0, $month, 0, $year));
        $first_day_current_month .= '01';
        $lastday_thismonth = date('Y-m-d', strtotime("last day of this month"));
        $month -= 1;        
        $day15 = $year . '-' . $month . '-15';
        // $day11 = $year . '-' . $month . '-11';
        // $day12 = $year . '-' . $month . '-12';
        $late_reporting = 0;
        $text_month = date('F', strtotime($day10));

        $reporting_month = date('F,Y', strtotime('first day of previous month'));

        $q = "SELECT * 
        FROM facilities, districts, counties
        WHERE facilities.district = districts.id
        AND districts.county = counties.id
        $conditions
        AND facilities.cd4_enabled =1
        ORDER BY  `facilities`.`facility_name` ASC ";
// echo "$q";
        $q_res = $this->db->query($q);
        $total_reporting_facilities = $q_res->num_rows();

        $q1 = "SELECT DISTINCT cd4_fcdrr.facility_code, cd4_fcdrr.id,cd4_fcdrr.order_date
        FROM cd4_fcdrr, districts, counties
        WHERE districts.id = cd4_fcdrr.district_id
        AND districts.county = counties.id
        $conditions
        AND cd4_fcdrr.order_date
        BETWEEN '$first_day_current_month'
        AND '$last_day_current_month'
        group by cd4_fcdrr.facility_code";
        
        $q_res1 = $this->db->query($q1);
        $new_q_res1 = $q_res1 ->result_array();
        $total_reported_facilities = $q_res1->num_rows();
        

        foreach ($q_res1->result_array() as $vals) {
            if ($vals['order_date'] >$day15 ) {
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

        // $non_reported_percentage = number_format($non_reported_percentage, 0);

        if ($total_reporting_facilities == 0) {
            $reported_percentage = 0;
        } else {
            $reported_percentage = $total_reported_facilities / $total_reporting_facilities * 100;
        }

        // $reported_percentage = number_format($reported_percentage, 0);

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

        $returnable = array('reporting_month'=>$reporting_month,'Month' => $text_month, 'Year' => $year, 'district' => $districtname, 'district_id' => $district_id,  'total_facilities' => $total_reporting_facilities, 'reported' => $total_reported_facilities, 'reported_percentage' => $reported_percentage, 'nonreported' => $nonreported, 'nonreported_percentage' => $non_reported_percentage, 'late_reports' => $late_reporting, 'late_reports_percentage' => $late_percentage);
        // echo $nonreported;
        // print_r($returnable);
        return $returnable;
    }
public function cd4_facilities_not_reported($month = NULL,$year = NULL, $county = NULL, $district = NULL, $facility = NULL, $zone = NULL, $partner= NULL) {
// echo "string";
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
    $conditions = (isset($zone)) ? "AND counties.zone = '$zone'" : '';
    $conditions = (isset($county)) ? $conditions . " AND counties.id = $county" : $conditions . ' ';
    $conditions = (isset($partner)) ? $conditions . " AND facilities.partner = $partner" : $conditions . ' ';
    $conditions = (isset($district)) ? $conditions . " AND districts.id = $district" : $conditions . ' ';
    $conditions = (isset($facility)) ? $conditions . " AND facilities.facility_code = $facility" : $conditions . ' ';

    $sql = "SELECT 
                distinct cd4_fcdrr.facility_code, facilities.facility_name, districts.district, counties.county, cd4_fcdrr.id as order_id
            FROM
                cd4_fcdrr,
                counties,
                districts,
                facilities
            WHERE
                created_at BETWEEN '$firstdate' AND '$lastdate'
                    AND facilities.district = districts.id
                    AND districts.county = counties.id
                    AND cd4_fcdrr.facility_code = facilities.facility_code
            GROUP BY counties.id";


    $sql2 = "SELECT 
               facilities.facility_code
            FROM
                counties,
                districts,
                facilities
            WHERE
                facilities.district = districts.id
                    AND districts.county = counties.id
                    AND facilities.cd4_enabled = 1
                    $conditions
            GROUP BY counties.id ";
           
    $reported = $this->db->query($sql)->result_array();
    $all = $this->db->query($sql2)->result_array();


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

    $returnable = array_diff($new_all, $new_reported);


    foreach ($returnable as $value) {
        $sql3 = "select facilities.facility_code,facilities.facility_name, districts.district, counties.county,counties.zone
        from facilities, districts, counties 
        where facilities.district=districts.id 
        and districts.county = counties.id
        and cd4_enabled='1'
        and facilities.facility_code = '$value'
        $conditions";
        $my_value = $this->db->query($sql3)->result_array();
        array_push($unreported, $my_value);
    }
        // echo "$sql3";

    $report_for = $month . "-" . $year;

    foreach ($unreported AS $key => $value) {
        $new_unreported[] = $value[0];
    }

    foreach ($new_unreported as $key => $value) {
        $new_unreported[$key]['report_for'] = $report_for;
    }
    $array_all = array();

    // array_push($array_all, $new_unreported);
    // array_push($array_all, $reported);
    $array_all = array('new_unreported' => $new_unreported, 'reported'=>$reported);
// print_r($reported); 
    return $array_all;
}

function update_county_percentages_month($month=null){
   
    if(isset($month)){           
        $year = substr($month, -4);
        $month = substr($month, 0,2);            
        $monthyear = $month.$year; 
    }
   
    $sql = "select id from counties";

    $result = $this->db->query($sql)->result_array();
     foreach ($result as $key => $value) {
        $id = $value['id'];              
    
        $reports = $this->cd4_reports_summary($year,$month,$id);  

        $cd4_reported = $reports['reported']; 
        $cd4_total_facilities = $reports['total_facilities'];        
        $cd4_percentage = ceil(($cd4_reported/$cd4_total_facilities)*100);
        // echo "<pre>"; print_r($reports);

        $q = "insert into cd4_county_percentage (county_id, cd4_facilities,cd4_reported,cd4_percentage,reported_month) values ($id,$cd4_total_facilities,$cd4_reported,$cd4_percentage,'$monthyear')";
        
        $this->db->query($q);
    }

    
}

function update_cd4_facilities(){
    $sql = 'select * from cd4_facility_device';
    $result = $this->db->query($sql)->result_array();

    foreach ($result as $key => $value) {
        if ($value['facility_code'] >0) {
            # code...
    
        $sql2 = 'update facilities set cd4_enabled = 1 where facility_code= '.$value['facility_code'];
        // $sql2 = 'update facilities set cd4_enabled = 0 ';
      echo   $this->db->query($sql2);
  }
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







    ///*** CLC Functions ***///
   

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


public function facility_profile($mfl) {
    require_once('rtk_management.php');
    $rtk = new Rtk_Management();    

    $sql = "select * from facilities, cd4_facility_device where cd4_facility_device.facility_code =  facilities.facility_code and facilities.cd4_enabled = 1 and facilities.facility_code=$mfl"; 
    $facility = $this->db->query($sql)->result_array();   

    $mfl =  $facility[0]['facility_code'];       
if ($mfl == '') {
    echo "Oops!! Your Facility has been Deactivated";
}else{
    $data['reports'] = $this->_monthly_facility_reports($mfl);
    
    // echo "<pre>"; print_r($data['reports']);die();

    $data['facility_county'] = $data['reports'][0]['county'];
    $data['facility_district'] = $data['reports'][0]['district'];
    $data['district_id'] = $facility[0]['district'];

    if($data['district_id']==null){
        $new_dist =  $facility[0]['district'];       
        $data['facilities_in_district'] = json_encode($this->_facilities_in_district($new_dist));
    }else{
        $data['facilities_in_district'] = json_encode($this->_facilities_in_district($data['district_id']));
    }    
    $data['facilities_in_district'] = str_replace('"', "'", $data['facilities_in_district']);
   
    $data['rtk_orders'] = $rtk -> get_lab_orders( $data['district_id'],$mfl);

    // print_r($data['rtk_orders']); die;

    $data['rtk_report_count'] = lab_commodity_orders::get_recent_lab_orders($mfl);

        if ($data['rtk_report_count'] > 0) {
            // $reported = $reported + 1;              
            
            $data['rtk_button_text'] = 'RTK Report Submitted';
        } else {
            // $nonreported = $nonreported + 1;
            $data['rtk_button_text'] = 'Submit New RTK Lab Commodity Report';
            
        }

        $data['cd4_report_count'] = lab_commodity_orders::get_recent_cd4_lab_orders($mfl);

        if ($data['cd4_report_count'] > 0) {
            // $reported = $reported + 1;              
            
            $data['cd4_button_text'] = 'CD4 Report Submitted';
        } else {
            // $nonreported = $nonreported + 1;
            $data['cd4_button_text'] = 'Submit New CD4 Lab Commodity Report';
            
        }
    $data['districts'] = districts::getDistrict($Countyid);
    $data['county'] = $this->session->userdata('county_name');
    $data['mfl'] = $mfl;
   
    $data['title'] = $facility[0]['facility_name'] . '-' . $mfl;
    $data['facility_name'] = $facility[0]['facility_name'];
    $data['banner_text'] = 'Facility Profile: ' . $facility[0]['facility_name'] . '-' . $mfl;
    $data['content_view'] = "cd4/facility_profile_view";

    $this->load->view("rtk/template", $data);
}
}

private function _monthly_facility_reports($mfl, $monthyear = null) {
    $conditions = '';
    if (isset($monthyear)) {
        $year = substr($monthyear, -4);
        $month = substr_replace($monthyear, "", -4);
        $firstdate = $year . '-' . $month . '-01';
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $lastdate = $year . '-' . $month . '-' . $num_days;
        $conditions=" AND cd4_fcdrr.order_date
        BETWEEN  '$firstdate'
        AND  '$lastdate'";
    }

    $sql = "select cd4_fcdrr.order_date,cd4_fcdrr.compiled_by,cd4_fcdrr.id as order_id,
    facilities.facility_name,districts.district,districts.id as district_id, counties.county,counties.id as county_id
    FROM cd4_fcdrr,facilities,districts,counties
    WHERE cd4_fcdrr.facility_code = facilities.facility_code
    AND facilities.district = districts.id
    AND counties.id = districts.county
    AND facilities.facility_code =$mfl $conditions 
    group by cd4_fcdrr.order_date ";        


    $sql .=' Order by cd4_fcdrr.order_date desc';
    // echo "$sql";die();
    $res = $this->db->query($sql);
    $sum_facilities = array();
    $facility_arr = array();

    foreach ($res->result_array() as $key => $value) {
        $facility_arr = $value;
        $details = $this->fcdrr_values($value['id']);       
        array_push($facility_arr, $details);
        array_push($sum_facilities, $facility_arr);
    }
   
        // echo "<pre>"; print_r($sum_facilities);die();
    return $sum_facilities;
}
public function fcdrr_values($order_id, $commodity = null) {
   // $month = date('mY', strtotime("Month"));
    $q = "SELECT * 
    FROM cd4_commodities, cd4_fcdrr_commodities 
    WHERE cd4_fcdrr_commodities.fcdrr_id ='$order_id'   
    AND cd4_fcdrr_commodities.commodity_id = cd4_commodities.id 
    AND cd4_commodities.category<>'0'";

    // echo "$order_id";
    if (isset($commodity)) {
        $q = "SELECT * 
        FROM cd4_commodities, cd4_fcdrr_commodities
        WHERE cd4_fcdrr_commodities.fcdrr_id ='$order_id'
        AND cd4_fcdrr_commodities.commodity_id = cd4_commodities.id
        AND commodity_id='$commodity'";
    }   
    $q_res = $this->db->query($q);
    $returnable = $q_res->result_array();
// echo "$q";
        // echo "<pre>"; print_r($returnable);die();

    return $returnable;
}


private function _facilities_in_district($district) {
    $sql = 'select facility_code,facility_name from facilities where district=' . $district;
    $res = $this->db->query($sql);


    return $res->result_array();
}


public function cd4_reporting_table(){

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

        $pending_facilities = $this->cd4_facilities_not_reported( $countyid, $year,$month);     
        // $pending_facilities = $this->rtk_facilities_not_reported(NULL, $countyid,NULL,NULL, $year,$month); 

        $data['county'] = $County; 
        $data['pending_facility'] = $pending_facilities;
        $data['title'] = 'RTK County Admin';       
        $data['banner_text'] = 'CD4 County Admin Facility Reporting'; 
        $data['content_view'] = "cd4/reporting_table";
        $this->load->view("rtk/template", $data);


}
// public function cd4_facilities_not_reported($county = NULL, $year = NULL, $month = NULL) {

//     $date = "$year-$month-1";

//     $sql =  "SELECT     `f`.*, 
//                         `cf`.`beg_date`,
//                         MONTHNAME(`cf`.`beg_date`) as 'report_for',
//                         `c`.`county` as county_name,
//                         `d`.`district` as district_name,
//                         u.fname,
//                         u.lname,
//                         u.telephone,
//                         u.email

//                 FROM `facilities` `f`  
//                     LEFT JOIN `cd4_fcdrr` `cf` 
//                         ON  `cf`.`facility_code` = `f`.`facility_code`
//                         AND `cf`.`beg_date` = '$date'
//                     LEFT JOIN `user` u 
//                         ON u.facility = `f`.`facility_code`
//                         AND u.usertype_id = 5
//                     LEFT JOIN districts d
//                         on d.id = f.district
//                         LEFT JOIN counties c
//                         ON c.id=  d.county

//                     RIGHT JOIN `cd4_facility_device` `cfd`
//                     ON cfd.facility_code = f.facility_code
//                 WHERE 
//                  c.id = '$county'


//                 GROUP BY f.id
//                 ";
//     // echo $sql;die;

//     $res = $this->db->query($sql)->result_array();

//     // print_r($res);

//     return $res;

// }

public function fcdrrs($msg = NULL) {
    $district = $this->session->userdata('district_id');        
    $district_name = Districts::get_district_name($district)->toArray();        
    $d_name = $district_name[0]['district'];
    $countyid = $this->session->userdata('county_id');

    
        //        $data['fcdrr_order_list'] = Lab_Commodity_Orders::get_district_orders($district);
    ini_set('memory_limit', '-1');

    date_default_timezone_set('EUROPE/moscow');
    $last_month = date('m');
        //            $month_ago=date('Y-'.$last_month.'-d');
    $month_ago = date('Y-m-d', strtotime("last day of previous month"));
    $sql = 'SELECT  
    facilities.facility_code,facilities.facility_name,cd4_fcdrr.id,cd4_fcdrr.order_date,cd4_fcdrr.district_id,cd4_fcdrr.compiled_by,cd4_fcdrr.facility_code
    FROM cd4_fcdrr, facilities
    WHERE cd4_fcdrr.facility_code = facilities.facility_code 
    AND cd4_fcdrr.order_date between ' . $month_ago . ' AND NOW()
    AND facilities.district =' . $district . '
    ORDER BY  cd4_fcdrr.id DESC ';
          
$query = $this->db->query($sql);

$data['lab_order_list'] = $query->result_array();
$data['all_orders'] = Lab_Commodity_Orders::get_district_orders($district);
$myobj = Doctrine::getTable('districts')->find($district);
        //$data['district_incharge']=array($id=>$myobj->district);
$data['countyid'] = $countyid;

$data['title'] = "Orders";
$data['content_view'] = "cd4/scmlt/fcdrr_listing_v";
$data['banner_text'] = $d_name . "Orders";
$data['myClass'] = $this;
$data['d_name'] = $d_name;
$data['msg'] = $msg;

$this->load->view("rtk/template", $data);
}


    //VIew FCDRR Report
public function fcdrr_details($order_id, $msg = NULL) {
    // $delivery = $this->uri->segment(3);
    // $district = $this->session->userdata('district_id');
    

    // $data['lab_categories'] = Cd4_Lab_Commodity_Categories::get_all();
    // $data['detail_list'] = Cd4_Fcdrr_Commodities::get_order($order_id);


    $result = $this->db->query('SELECT *,cd4_lab_commodity_categories.name  AS category_name, counties.county as county_name, districts.district as district_name
        FROM cd4_fcdrr_commodities, counties, facilities, districts, cd4_fcdrr, cd4_lab_commodity_categories, cd4_commodities
        WHERE counties.id = districts.county
        AND facilities.facility_code = cd4_fcdrr.facility_code
        AND cd4_fcdrr_commodities.commodity_id = cd4_commodities.id
        AND cd4_lab_commodity_categories.id = cd4_commodities.category
        AND facilities.district = districts.id
        AND cd4_fcdrr_commodities.fcdrr_id = cd4_fcdrr.id
        AND cd4_fcdrr.id = ' . $order_id . '');
     $data['all_details'] = $result->result_array();
    // print_r($data['all_details']);die;

    $data['title'] = "Lab Commodity Order Details";       
    $data['order_id'] = $order_id;
    $data['content_view'] = "cd4/fcdrr_report";
    $data['banner_text'] = "Lab Commodity Order Details for";
    $this->load->view("rtk/template", $data);
}

public function county_cd4_reports(){
    $county_id = $this->session->userdata('county_id');
    $beg_date = date("Y-m-01");
    $end_date = date("Y-m-t");

    $sql = "SELECT * FROM hcmp_rtk.counties where id = '$county_id'";
    $result = $this->db->query($sql)->result_array();
    $data['county_name'] = $result[0]['county'];
    

    $sql3 = "SELECT 
                facilities.facility_code,
                facilities.facility_name,
                districts.district,
                facilities.cd4_enabled,
                cd4_device.name as device_name
            FROM
                facilities,
                districts,
                counties,
                cd4_facility_device,
                cd4_device
            WHERE
                facilities.district = districts.id
                    AND districts.county = counties.id
                    AND counties.id = '$county_id'
                    AND facilities.facility_code = cd4_facility_device.facility_code
                    and cd4_facility_device.device = cd4_device.id";
    $result3 = $this->db->query($sql3)->result_array();
    $data['cd4_facilities']= $result3;
    // echo "$sql3";
    // echo "<pre>"; print_r($result3); die;

    $category_details = array();
   
    for ($i=1; $i <=8 ; $i++) { 
        $sql2 = "SELECT * FROM hcmp_rtk.cd4_lab_commodity_categories where id = $i";
        $result2 = $this->db->query($sql2)->result_array();
        
        $sql4 = "SELECT 
                    
                    cd4_fcdrr_commodities.created_at,
                    cd4_lab_commodity_categories.id AS category_id,
                    cd4_lab_commodity_categories.name AS category_name,
                    cd4_commodities.commodity_name,
                    cd4_fcdrr_commodities.commodity_id,
                    SUM(cd4_fcdrr_commodities.beginning_bal) as sum_opening,
                    SUM(cd4_fcdrr_commodities.q_received) as sum_received,
                    SUM(cd4_fcdrr_commodities.q_used) as sum_used,
                    SUM(cd4_fcdrr_commodities.no_of_tests_done) as sum_tests,
                    SUM(cd4_fcdrr_commodities.positive_adj) as sum_positive ,
                    SUM(cd4_fcdrr_commodities.negative_adj) as sum_negative,
                    SUM(cd4_fcdrr_commodities.closing_stock) as sum_closing_bal
                FROM
                    districts,
                    counties,
                    facilities,
                    cd4_fcdrr_commodities,
                    cd4_fcdrr,
                    cd4_commodities,
                    cd4_lab_commodity_categories
                WHERE
                    cd4_fcdrr_commodities.created_at BETWEEN '2016-05-01' AND '2016-05-31'
                        AND counties.id = '1'
                        AND counties.id = districts.county
                        AND districts.id = facilities.district
                        AND cd4_fcdrr_commodities.facility_code = facilities.facility_code
                        AND cd4_commodities.id = cd4_fcdrr_commodities.commodity_id
                        AND cd4_lab_commodity_categories.id = cd4_commodities.category
                        AND cd4_lab_commodity_categories.id = '$i'
                GROUP BY cd4_fcdrr_commodities.commodity_id";
        $result4 = $this->db->query($sql4)->result_array();
        
        array_push($category_details, $result4);
        
    }
    $data['category_details'] = $category_details;

    $data['title'] = "CD4 County Reports";       
    $data['order_id'] = $order_id;
    $data['content_view'] = "cd4/county_report";
    $data['banner_text'] = "CD4 County Reports";
    $this->load->view("rtk/template", $data);
}

public function deactivate_facility($facility_code) {
    $this->db->query('UPDATE `facilities` SET  `cd4_enabled` =  0 WHERE  `facility_code` =' . $facility_code . '');
    
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
    // $this->_update_facility_count('remove',$county,$district);        
    redirect('cd4_management/county_cd4_reports');
}


public function activate_facility($facility_code) {
    $this->db->query('UPDATE `facilities` SET  `cd4_enabled` = 1 WHERE  `facility_code` =' . $facility_code . '');
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
    // $this->_update_facility_count('add',$county,$district);        
    redirect('cd4_management/county_cd4_reports');
}
// public function rtk_facilities_not_reported($zone = NULL, $county = NULL, $district = NULL, $facility = NULL, $year = NULL, $month = NULL,$partner= NULL) {

//     if (!isset($month)) {
//         $month_text = date('mY', strtotime('-1 month'));
//         $month = date('m', strtotime("-1 month", time()));
//     }

//     if (!isset($year)) {
//         $year = substr($month_text, -4);
//     }

//     $firstdate = $year . '-' . $month . '-01';
//     $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//     $lastdate = $year . '-' . $month . '-' . $num_days;

//     $conditions = '';
//     $conditions = (isset($zone)) ? "AND facilities.Zone = 'Zone $zone'" : '';
//     $conditions = (isset($county)) ? $conditions . " AND counties.id = $county" : $conditions . ' ';
//     $conditions = (isset($partner)) ? $conditions . " AND facilities.partner = $partner" : $conditions . ' ';
//     $conditions = (isset($district)) ? $conditions . " AND districts.id = $district" : $conditions . ' ';
//     $conditions = (isset($facility)) ? $conditions . " AND facilities.facility_code = $facility" : $conditions . ' ';

//     $sql = "select distinct lab_commodity_orders.facility_code
//     from lab_commodity_orders, facilities, districts, counties 
//     where lab_commodity_orders.order_date between '$firstdate' and '$lastdate'
//     and facilities.district=districts.id 
//     and districts.county = counties.id
//     and facilities.rtk_enabled='1'";

//         //echo "$sql";die();

//     $sql2 = "select facilities.facility_code
//     from facilities, districts, counties 
//     where facilities.district=districts.id
//     $conditions
//     and districts.county = counties.id
//     and facilities.rtk_enabled='1'
//     ";

//     $res = $this->db->query($sql);
//     $reported = $res->result_array();
//     $res2 = $this->db->query($sql2);
//     $all = $res2->result_array();


//     $unreported = array();
//     $new_all = array();
//     $new_reported = array();

//     foreach ($all AS $key => $value) {
//         $new_all[] = $value['facility_code'];
//     }
//     foreach ($reported AS $key => $value) {
//         $new_reported[] = $value['facility_code'];
//     }
//     sort($new_all);
//     sort($new_reported);

//     $returnable = $this->flip_array_diff_key($new_all, $new_reported);

//     foreach ($returnable as $value) {
//         $sql3 = "select facilities.facility_code,facilities.facility_name, districts.district, counties.county,facilities.zone
//         from facilities, districts, counties 
//         where facilities.district=districts.id 
//         and districts.county = counties.id
//         and rtk_enabled='1'
//         and facilities.facility_code = '$value'
//         $conditions";
//         $res3 = $this->db->query($sql3);
//         $my_value = $res3->result_array();
//         array_push($unreported, $my_value);
//     }
//     $report_for = $month . "-" . $year;



//     foreach ($unreported AS $key => $value) {
//         $new_unreported[] = $value[0];
//     }
//     foreach ($new_unreported as $key => $value) {
//         $new_unreported[$key]['report_for'] = $report_for;
//     }


//     return $new_unreported;
// }




    
}

?>
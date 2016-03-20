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

    //SCMLT FUNCTUONS

    

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
    $data = array('facility_code' => $facility_code, 'district_id' => $district_id, 'compiled_by' => $compiled_by, 'order_date' => $order_date, 'vct' => $vct, 'pitc' => $pitc, 'pmtct' => $pmtct, 'b_screening' => $b_screening, 'other' => $other, 'specification' => $specification, 'rdt_under_tests' => $rdt_under_tests, 'rdt_under_pos' => $rdt_under_pos, 'rdt_btwn_tests' => $rdt_btwn_tests, 'rdt_btwn_pos' => $rdt_btwn_pos, 'rdt_over_tests' => $rdt_over_tests, 'rdt_over_pos' => $rdt_over_pos, 'micro_under_tests' => $micro_under_tests, 'micro_under_pos' => $micro_under_pos, 'micro_btwn_tests' => $micro_btwn_tests, 'micro_btwn_pos' => $micro_btwn_pos, 'micro_over_tests' => $micro_over_tests, 'micro_over_pos' => $micro_over_pos, 'beg_date' => $beg_date, 'end_date' => $end_date, 'explanation' => $explanation, 'moh_642' => $moh_642, 'moh_643' => $moh_643, 'report_for' => $lastmonth);
    $u = new Lab_Commodity_Orders();
    $u->fromArray($data);
    $u->save();
    $object_id = $u->get('id');
    $this->logData('13', $object_id);
    // $this->update_amc($facility_code);

    $lastId = Lab_Commodity_Orders::get_new_order($facility_code);
    $new_order_id = $lastId->maxId;
    $count++;

    for ($i = 0; $i < $commodity_count; $i++) {            
        $mydata = array('order_id' => $new_order_id, 'facility_code' => $facility_code, 'district_id' => $district_id, 'commodity_id' => $drug_id[$i], 'unit_of_issue' => $unit_of_issue[$i], 'beginning_bal' => $b_balance[$i], 'q_received' => $q_received[$i], 'q_used' => $q_used[$i], 'no_of_tests_done' => $tests_done[$i], 'losses' => $losses[$i], 'positive_adj' => $pos_adj[$i], 'negative_adj' => $neg_adj[$i], 'closing_stock' => $physical_count[$i], 'q_expiring' => $q_expiring[$i], 'days_out_of_stock' => $days_out_of_stock[$i], 'q_requested' => $q_requested[$i]);
        Lab_Commodity_Details::save_lab_commodities($mydata);           
    }
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
    redirect('rtk_management/scmlt_home');

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
    redirect('rtk_management/scmlt_home');

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
    $data['content_view'] = "cd4/facility_profile_view";

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
        $conditions=" AND cd4_fcdrr.order_date
        BETWEEN  '$firstdate'
        AND  '$lastdate'";
    }

    $sql = "select distinct cd4_fcdrr.order_date,cd4_fcdrr.compiled_by,cd4_fcdrr.id,
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
    AND cd4_commodities.category='1'";

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


    
}

?>
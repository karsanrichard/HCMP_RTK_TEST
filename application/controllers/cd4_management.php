<?php
/*

*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once ('home_controller.php');

class cd4_Management extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        ini_set('memory_limit', '-1');
        ini_set('max_input_vars', 3000);
    }

    public function index() {
        echo "CD4 Management";
    }


    public function amcs(){
          $sql_facilities = "SELECT 
                                cd4_facilities.id as facility_id, cd4_facilities.mfl_code
                            FROM
                                cd4_facilities
                            where
                                cd4_facilities.id in (select 
                                        fcdrr.facility_id
                                    from
                                        fcdrr)";
        $res_facilities = $this->db->query($sql_facilities)->result_array();

        foreach ($res_facilities as $key => $value) {            
            $facility_code = $value['mfl_code'];
            $this->update_amc($facility_code);
        }
    }
    private function update_amc($mfl) {        
        $amc = 0;         
        $current_month = date("mY",strtotime("-0 Month")); 
        $sql_comm_check = "SELECT id as commodity_id FROM hcmp_rtk.cd4_commodities where reporting_status='1'";
        $commodities = $this->db->query($sql_comm_check)->result_array();  
        foreach ($commodities as $key => $value) {
            $commodity_id = $value['commodity_id'];
            $amc = $this->_facility_amc($mfl, $commodity_id);
            echo "$amc<br/>";
            $q = "select * from cd4_facility_amc where facility_code='$mfl' and commodity_id='$commodity_id' and month='$current_month'";
            $resq = $this->db->query($q)->result_array();
            $count = count($resq);
            if($count>0){
                $sql = "update cd4_facility_amc set amc = '$amc' where facility_code = '$mfl' and commodity_id='$commodity_id'";
                $res = $this->db->query($sql); 
            }else{

                $sql = "INSERT INTO `cd4_facility_amc`(`id`, `facility_code`, `commodity_id`, `amc`, `month`) VALUES (null,'$mfl','$commodity_id','$amc','$current_month')";
                $res = $this->db->query($sql);
            }
            
        }
    }

    //Facility Amc
    public function _facility_amc($mfl_code, $commodity = null) {
        $three_months_ago = date("Y-m-", strtotime("-3 Month "));
        $three_months_ago .='1';
        $end_date = date("Y-m-", strtotime("-0 Month "));
        $end_date .='31';
        // echo "Three months ago = $three_months_ago and End Date =$end_date ";die();
        $q = "SELECT avg(cd4_lab_details.q_used) as avg_used
        FROM  cd4_lab_details
        WHERE cd4_lab_details.facility_code = '$mfl_code'
        AND cd4_lab_details.to_date BETWEEN '$three_months_ago' AND '$end_date'";
        
        if (isset($commodity)) {
            $q.=" AND cd4_lab_details.commodity_id = '$commodity'";
        } else {
            $q.=" AND cd4_lab_details.commodity_id = 1";
        }
        // echo "$q";die;
        $res = $this->db->query($q);
        $result = $res->result_array();
        $result = $result[0]['avg_used'];
        $result = number_format($result, 0);
        return $result;
    }

    function facility_amc_compute($zone) {
            $sql = "select facilities.facility_code from facilities where facilities.rtk_enabled = '1' and zone='Zone $zone'";
            $res = $this->db->query($sql);
            $facility = $res->result_array();
            foreach ($facility as $value) {
                $fcode = $value['facility_code'];
                $this->update_amc($fcode);
            }
     }

     public function sync_cd4($year=null,$month=null){
        if(isset($year)||isset($month)){
            $current_month = $month.$year;
        }else{
            $year = date("Y",strtotime("-0 Month"));        
            $month = date("m",strtotime("-0 Month"));        
            $current_month = $month.$year;
        }

        // $url = 'http://cd4.nascop.org/fcdrr?year=' . $year . '&month=' . $month;
        $url = 'http://nascop.org/cd4/reportingfacsummary.php?year=' . $year . '&month=' . $month;
        //$url = 'http://cd4.nascop.org/fcdrr?year=' . $year . '&month=' . $month;
        if (!$this->_check_url_working($url)) {
            echo ("NASCOP link is down");
            die;
        }else{         
            $converted_cd4_data = $this->api_get_facilities($month, $year,$url);            
            //$converted_cd4_data = json_decode($allfacilities);
            echo "<pre>";
            print_r($converted_cd4_data);die();
            $facilities_count = count($converted_cd4_data);
            foreach ($converted_cd4_data as $value) {
                $fcode = $value['facility_code'];                                                
                $commodity_details = $value['commodities'];                      
                foreach ($commodity_details as $key => $values) {
                    $commodity_name = $values['commodity_name'];
                    $commodity_id = $values['commodity_id'];
                    $q_used = $values['qty_used'];
                    $ending_bal = $values['ending_bal'];
                    $sql = "INSERT INTO `cd4_lab_details`(`id`, `facility_code`, `commodity_id`, `commodity_name`, `ending_bal`, `q_used`, `month`) 
                    VALUES (null,'$fcode','$commodity_id','$commodity_name','$ending_bal','$q_used','$current_month')";

                    $this->db->query($sql);                    
                }          
            }

            echo "Done Syncing...";
            
        }        


     }

   public function api_get_facilities($month, $year,$url) {        
        function objectToArray($object) {
            if (!is_object($object) && !is_array($object)) {
                return $object;
            }
            if (is_object($object)) {
                $object = (array) $object;
            }
            return array_map('objectToArray', $object);
        }
        date_default_timezone_set('EUROPE/Moscow');        
        $string_manual = file_get_contents($url);
        $string = json_decode($string_manual);
        $string = objectToArray($string);
        return $string;
    }




        // Function to check whether url is up
    function _check_url_working($url) {
        $result = false;
        if (@file_get_contents($url, 0, NULL, 0, 1)) {
            $result = true;
        }
        return $result;
    }

    public function migrate_data($year=null,$month=null){
        if(isset($year)||isset($month)){
            $current_month = $month.$year;
        }else{
            $year = date("Y",strtotime("-0 Month"));        
            $month = date("m",strtotime("-0 Month"));        
            $current_month = $month.$year;
        }
        $sql_facilities = "SELECT 
                                cd4_facilities.id as facility_id, cd4_facilities.mfl_code
                            FROM
                                cd4_facilities
                            where
                                cd4_facilities.id in (select 
                                        fcdrr.facility_id
                                    from
                                        fcdrr)";
        $res_facilities = $this->db->query($sql_facilities)->result_array();

        foreach ($res_facilities as $key => $value) {
            $facility_id = $value['facility_id'];
            $facility_code = $value['mfl_code'];
            $sql_orders = "select id as order_id,from_date,to_date from fcdrr where facility_id = '$facility_id' and year = '$year' and month = '$month' ";
            $res_orders =$this->db->query($sql_orders)->result_array(); 

            foreach ($res_orders as $keys => $values) {
                
                $order_id = $values['order_id'];
                $from_date = $values['from_date'];
                $to_date = $values['to_date'];

                $sql_details = "SELECT * FROM hcmp_rtk.fcdrr_commodity where fcdrr_id = '$order_id'";
                $res_details = $this->db->query($sql_details)->result_array();                
                $ending_bal = $res_details[0]['end_bal'];
                $qty_used =  $res_details[0]['qty_used'];
                $commodity_id = $res_details[0]['commodity_id'];                

                $sql_insert = "INSERT INTO `cd4_lab_details`(`id`, `facility_code`, `commodity_id`, `ending_bal`, `q_used`, `from_date`, `to_date`, `month`) 
                VALUES (null,'$facility_code','$commodity_id','$ending_bal','$qty_used','$from_date','$to_date','$current_month')";
                $this->db->query($sql_insert);
                
            }

        }

    }



}

?>
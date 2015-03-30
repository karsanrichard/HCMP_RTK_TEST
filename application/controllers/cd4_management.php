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

    private function update_amc($mfl) {        
        $amc = 0;         
        $current_month = date("mY",strtotime("-0 Month"));        
        for ($commodity_id = 1; $commodity_id <= 9; $commodity_id++) {
            $amc = $this->_facility_amc($mfl, $commodity_id);
            echo "$amc<br/>";
            $q = "select * from cd4_facility_amc where facility_code='$mfl' and commodity_id='$commodity_id' and month='$current_month'";
            $resq = $this->db->query($q)->result_array();
            $count = count($resq);
            if($count>0){
                $sql = "update cd4_facility_amc set amc = '$amc' where facility_code = '$mfl' and commodity_id='$commodity_id'";
                $res = $this->db->query($sql); 
            }else{

                $sql = "INSERT INTO `facility_amc`(`facility_code`, `commodity_id`, `amc`,`month`) VALUES ('$mfl','$commodity_id','$amc','$current_month')";
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
        //echo "Three months ago = $three_months_ago and End Date =$end_date ";die();
        $q = "SELECT avg(cd4_lab_details.q_used) as avg_used
        FROM  lab_commodity_details1,lab_commodity_orders
        WHERE lab_commodity_orders.id =  lab_commodity_details1.order_id
        AND lab_commodity_details1.facility_code =  $mfl_code
        AND lab_commodity_orders.order_date BETWEEN '$three_months_ago' AND '$end_date'";
        
        if (isset($commodity)) {
            $q.=" AND lab_commodity_details1.commodity_id = $commodity";
        } else {
            $q.=" AND lab_commodity_details1.commodity_id = 1";
        }
        echo "$q";
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

        $url = 'http://nascop.org/cd4/reportingfacsummary.php?year=' . $year . '&month=' . $month;
        if (!$this->_check_url_working($url)) {
            echo ("NASCOP link is down");
            die;
        }else{            
            $converted_cd4_data = json_decode($url);
            $facilities_count = count($converted_cd4_data);
            foreach ($converted_cd4_data as $value) {
                $fcode = $value['facility_code'];                                                
                $commodity_details = $value['commodities'];                      
                foreach ($commodity_details as $key => $values) {
                    $commodity_name = $values['commodity_name'];
                    $commodity_id = $values['commodity_id'];
                    $q_used = $values['qty_used'];

                    $sql = "INSERT INTO `cd4_lab_details`(`id`, `facility_code`, `commodity_id`, `commodity_name`, `q_used`, `month`) 
                    VALUES (null,'$fcode','$commodity_id','$commodity_name','$q_used','$current_month')";

                    $this->db->query($sql);                    
                }          
            }

            echo "Done Syncing...";
            
        }        


     }



        // Function to check whether url is up
    function _check_url_working($url) {
        $result = false;
        if (@file_get_contents($url, 0, NULL, 0, 1)) {
            $result = true;
        }
        return $result;
    }



}

?>
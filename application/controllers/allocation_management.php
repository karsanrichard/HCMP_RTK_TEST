 <?php
/*

*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once ('home_controller.php');

class Allocation_Management extends CI_controller {
	function __construct() {
        parent::__construct();
        $this->load->library('Excel');
        //$this->load->database();
       // ini_set('memory_limit', '-1');
       // ini_set('max_input_vars', 3000);
    }

    public function index() {
        echo "blee";
    }
    public function county_reports(){
        $county = $this->session->userdata('county_id'); 
        $user_id = $county;
        $user_type=13;
        require('rtk_management.php');
        $rtk = new rtk_management();
 
        $commodity_id = 4;
        $month = $this->session->userdata('Month');

    if ($month == '') {
        $month = date('mY', time());
    }
        $month= '042016';
    $year = substr($month, -4);
    $months_texts = array();
    $percentages = array();

    for ($i=11; $i >=0; $i--) { 
        $months =  date("mY", strtotime( date( 'Y-m-01' )." -$i months"));
        $j = $i+1;            
        $month_text =  date("M Y", strtotime( date( 'Y-m-01' )." -$j months")); 
        array_push($months_texts,$month_text);
        $sql2 = "select sum(reported) as reported, sum(facilities) as total, month from rtk_county_percentage where month ='$months' and county_id = '$county'";

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
    $sql3 = "select *  from rtk_county_percentage where month ='$month' and county_id = '$county' ";
    $result3 = $this->db->query($sql3)->result_array();  
    // echo "$sql3";
    $total_facilities = $result3[0]['facilities'];
    $reported_facilities=$result3[0]['reported'];
    $nonreported_facilities= $total_facilities - $reported_facilities;
    $data['reported_facilities'] = $reported_facilities;
    $data['reported_facilities_percentage'] = $result3[0]['percentage'];
    $data['total_facilities'] = $total_facilities;
    $data['nonreported_facilities'] =$nonreported_facilities;
    $data['nonreported_facilities_percentage'] = ceil(($nonreported_facilities/$total_facilities)*100);
    // echo '<pre>';print_r($result3);die;

    $sql4 = "SELECT 
                facilities.facility_name,
                facilities.facility_code,
                lab_commodity_orders.order_date
            FROM
                facilities,
                districts,
                counties,
                lab_commodity_orders
            WHERE
                lab_commodity_orders.facility_code = facilities.facility_code
                    AND facilities.district = districts.id
                    AND counties.id = districts.county
                    AND counties.id = 2
                    AND lab_commodity_orders.order_date BETWEEN '2016-04-01' AND '2016-04-31'";
    $result4 = $this->db->query($sql4)->result_array();  
    $sql5 = "SELECT 
   
    count(facilities.facility_code) as late_facilities
FROM
    facilities,
    districts,
    counties,
    lab_commodity_orders
WHERE
    lab_commodity_orders.facility_code = facilities.facility_code
        AND facilities.district = districts.id
        AND counties.id = districts.county
        AND counties.id = 2
        AND lab_commodity_orders.order_date BETWEEN '2016-04-16' AND '2016-04-31'";
    $result5 = $this->db->query($sql5)->result_array();  

    $data['reported_facilities_text'] = $result4;
    $data['late_reported_facilities'] = $result5;
    $data['trend_details'] = json_encode($trend_details);        
    $data['months_texts'] = str_replace('"',"'",json_encode($months_texts));        
  $data['percentages'] = str_replace('"',"'",json_encode($percentages));              
    $data['first_month'] = date("M Y", strtotime( date( 'Y-m-01' )." -12 months")); 
    $data['last_month'] = date("M Y", strtotime( date( 'Y-m-01' )." -1 months")); 
 
        $data['graphdata'] = $rtk->partner_commodity_percentages($user_type,$user_id, $commodity_id);
        // print_r($data['graphdata']);die;
        $data['banner_text'] = 'Allocation Reports';
        $data['active_zone'] = "$zone";
        $data['content_view'] = 'rtk/rtk/clc/county_reports';
        $data['title'] = 'Download Allocation Reports ';       
        $this->load->view("rtk/template", $data);
    }

    function get_county_reporting_trend($county_id, $district_id){
      $month = $this->session->userdata('Month');

    if ($month == '') {
        $month = date('mY', time());
    }
    $year = substr($month, -4);
    $months_texts = array();
    $percentages = array();

    for ($i=11; $i >=0; $i--) { 
        $months =  date("mY", strtotime( date( 'Y-m-01' )." -$i months"));
        $j = $i+1;            
        $month_text =  date("M Y", strtotime( date( 'Y-m-01' )." -$j months")); 
        array_push($months_texts,$month_text);
        $sql2 = "select sum(reported) as reported, sum(facilities) as total, month from rtk_county_percentage where month ='$months' and county_id = '$county_id'";
// echo "$sql2";
        $result2 = $this->db->query($sql2)->result_array();            
        // print_r($result2);die;
        foreach ($result2 as $key => $value) {
            $reported = $value['reported'];
            $total = $value['total'];
            $percentage = round(($reported/$total)*100);
            if($percentage>100){
                $percentage = 100;
            }
            array_push($percentages, $percentage);
            $trend_details[$month] = array('reported'=>$reported,'total'=>$total,'percentage'=>$percentage,'months_texts'=>$months_texts);
        }
    }

    $output =  json_encode($trend_details);        

    }
    //get amcs from facility_amc table and facility details then insert into allocation table
    function get_amcs(){
        //get facility's details
        $sql = "SELECT 
                    counties.county,
                    counties.id as county_id,
                    districts.district,
                    districts.id as district_id,
                    facilities.facility_code,
                    facilities.facility_name,
                    facilities.zone                   
                FROM
                    facilities,
                    counties,
                    districts
                WHERE
                    counties.id = districts.county
                        AND districts.id = facilities.district limit 0,100";
       $result = $this->db->query($sql)->result_array();

       //get the amcs of all four commodities for each facility
       foreach ($result as $key => $value) {
           $facility_code = $value['facility_code'];

           $sql2 = "Select amc, commodity_id, latest from facility_amc where facility_code = '$facility_code'";
           $result2 = $this->db->query($sql2)->result_array();
       
            $county_id = $value['county_id'];
            $county = $value['county'];
            $district_id = $value['district_id'];
            $district = $value['district'];
            $zone = $value['zone'];
            $oldfacility_name = $value['facility_name'];
            $facility_name = str_replace("'", "", $oldfacility_name);
            $amc_s3 = $result2[0]['amc'];
            $amc_c3 = $result2[1]['amc'];
            $amc_t3 = $result2[2]['amc'];
            $amc_d3 = 0; 

            $month = $result2[0]['latest'];
            if (empty($month)) {
                $newmonth = '';

            }else{
                $monthnumber = date("m", strtotime($month));
                $newyear = date("Y", strtotime($month));
                $newmonth = $monthnumber.$newyear;
            }
            //insert the details into allocation details          
            $sql3 = "INSERT INTO `allocation_details`
                    (`county_id`, `county`, `district_id`, `district`, `mfl`, `facility_name`, `zone`, `amc_s`, `amc_t`, `amc_c`, `amc_d`,`month`) 
                    VALUES ('$county_id','$county','district_id','$district','$facility_code','$facility_name','$zone',
                    '$amc_s3','$amc_t3','$amc_c3','$amc_d3','$month')";
        
            $this->db->query($sql3);
       }
                   
    }
    public function allocation_reports(){
        
 
        $data['banner_text'] = 'Allocation Reports';
        $data['active_zone'] = "$zone";
        $data['content_view'] = 'rtk/rtk/allocation/allocation_reports';
        $data['title'] = 'Download Allocation Reports ';       
        $this->load->view("rtk/template", $data);
    }

    function get_counties_districts($county){
     
        // $county = $this->session->userdata('county_id');   
        $sql = 'select counties.id as county_id, counties.county from counties';
        $sql2 = 'select districts.id as district_id, districts.district from districts';
        $sql3 = 'select districts.id as district_id, districts.district from districts where county = '.$county;
        $counties = $this->db->query($sql)->result_array();
        $districts = $this->db->query($sql2)->result_array();
        $districts_county = $this->db->query($sql3)->result_array();
       
        // echo('<pre>'); print_r($districts);die;
       $option_district .= '<option value = "">--Select Sub-County--</option>';
       foreach ($districts as $key => $value) {
            $option_district .= '<option value = "' . $value['district_id'] . '">' . $value['district'] . '</option>';
        } 
        $option_district_county .= '<option value = "0">--All Sub-Counties--</option>';
       foreach ($districts_county as $key => $value) {
            $option_district_county .= '<option value = "' . $value['district_id'] . '">' . $value['district'] . '</option>';
        } 

        $option_county .= '<option value = "">--Select County</option>';
        foreach ($counties as $key => $value) {
            $option_county .= '<option value = "' . $value['county_id'] . '">' . $value['county'] . '</option>';
        } 

          $month = $this->session->userdata('Month');

    if ($month == '') {
        $month = date('mY', time());
    }
    $year = substr($month, -4);
    $months_texts = array();
    $percentages = array();

    for ($i=11; $i >=0; $i--) { 
        $months =  date("mY", strtotime( date( 'Y-m-01' )." -$i months"));
        $j = $i+1;            
        $month_text =  date("M Y", strtotime( date( 'Y-m-01' )." -$j months")); 
        array_push($months_texts,$month_text);
        $sql2 = "select sum(reported) as reported, sum(facilities) as total, month from rtk_county_percentage where month ='$months' and county_id = '$county'";
// echo "$sql2";
        $result2 = $this->db->query($sql2)->result_array();            
        // print_r($result2);die;
        foreach ($result2 as $key => $value) {
            $reported = $value['reported'];
            $total = $value['total'];
            $percentage = round(($reported/$total)*100);
            if($percentage>100){
                $percentage = 100;
            }
            array_push($percentages, $percentage);
            $trend_details[$month] = array('reported'=>$reported,'total'=>$total,'percentage'=>$percentage,'months_texts'=>$months_texts);
        }
    }
        // print_r($trend_details);die;
        $output = array('counties_list'=>$option_county,'districts_list'=>$option_district,'district_county_list'=>$option_district_county, 'trend_details'=>$trend_details);  
        echo json_encode($output);
        
    }


    //get allocation details from all facilities in the country
    function get_all_facilities_amcs(){
        //get all the details from allocation_details table in db
    $sql = "SELECT 
                counties.county  as county_name, districts.district as district_name, allocation_details.*
            FROM
                allocation_details,
                facilities,
                counties,
                districts
            WHERE
                district_id = '$district'
                    AND districts.county = counties.id
                    AND facilities.district = districts.id
                    AND facilities.facility_code = allocation_details.facility_code_name limit 0,100";
    $result = $this->db->query($sql)->result_array();
    
    foreach ($result as $key => $id_details) {
        
        $facility_code = $id_details['mfl'];
        // for each selected facility, get the last reported lab details fro all four commodities
        $sql2 = "SELECT 
				    closing_stock, days_out_of_stock, q_requested
				FROM
				    lab_commodity_details AS a
				WHERE
				    facility_code = '$facility_code'
				        AND commodity_id between 4 and 7
				        AND created_at IN (SELECT 
				            MAX(created_at)
				        FROM
				            lab_commodity_details AS b
				        WHERE
				            a.facility_code = b.facility_code)";
        $result2 = $this->db->query($sql2)->result_array();

        $county = $id_details['county_name'];
        $district = $id_details['district_name'];
        $facility_name = $id_details['facility_name'];
        $amc_s3 = $id_details['amc_s3'];
        $amc_c3 = $id_details['amc_c3'];
        $amc_t3 = $id_details['amc_t3'];
        $amc_d3 = 0;

        $ending_bal_s3 = $result2[0]['closing_stock'];
        $q_requested_s3 = $result2[0]['q_requested'];
        $days_out_of_stock_s3 = $result2[0]['days_out_of_stock'];

        $ending_bal_c3 = $result2[1]['closing_stock'];
        $q_requested_c3 = $result2[1]['q_requested'];
        $days_out_of_stock_c3 = $result2[1]['days_out_of_stock'];

        $ending_bal_t3 = $result2[2]['closing_stock'];
        $q_requested_t3 = $result2[2]['q_requested'];
        $days_out_of_stock_t3 = $result2[2]['days_out_of_stock'];

        $ending_bal_d3 = $result2[3]['closing_stock'];
        $q_requested_d3 = $result2[3]['q_requested'];
        $days_out_of_stock_d3 = $result2[3]['days_out_of_stock'];

        //based on the results, put them in an array to beused in the excell file.

        $alocation_details[] = array($county,$district,$facility_code,$facility_name,
                                     $ending_bal_s3,$amc_s3,$days_out_of_stock_s3,$q_requested_s3,
                                     $ending_bal_c3,$amc_c3,$days_out_of_stock_c3,$q_requested_c3,
                                     $ending_bal_t3,$amc_t3,$days_out_of_stock_t3,$q_requested_t3,
                                     $ending_bal_d3,$amc_d3,$days_out_of_stock_d3,$q_requested_d3);
}
        // echo"<pre>";print_r($alocation_details);
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Screening KHB');
        $this->excel->getActiveSheet()->setCellValue('I4', 'Confirmatory - First Response');
        $this->excel->getActiveSheet()->setCellValue('M4', 'Tie Breaker');
        $this->excel->getActiveSheet()->setCellValue('Q4', 'DBS Bundles');

        $this->excel->getActiveSheet()->setCellValue('A5', '');
        $this->excel->getActiveSheet()->setCellValue('B5', '');
        $this->excel->getActiveSheet()->setCellValue('C5', '');
        $this->excel->getActiveSheet()->setCellValue('D5', '');

        $this->excel->getActiveSheet()->setCellValue('E5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('G5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('H5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('I5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('J5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('K5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('L5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('M5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('N5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('O5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('P5', 'Quantity Requested');
        
        $this->excel->getActiveSheet()->setCellValue('Q5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('R5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('S5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('T5', 'Quantity Requested');
        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:K1');
        $this->excel->getActiveSheet()->mergeCells('E4:H4');
        $this->excel->getActiveSheet()->mergeCells('I4:L4');
        $this->excel->getActiveSheet()->mergeCells('M4:P4');
        $this->excel->getActiveSheet()->mergeCells('Q4:T4');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:T4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5:T5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('Q'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
        }
                            
        foreach ($alocation_details as $row){
                $exceldata[] = $row;
        }
             //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
                $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 
                $filename='National Allocation (all facilities).xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');
    
    }
    function get_zonal_facilities_amcs($zone){
            //get all the details from allocation_details for a particular zone in db
        $sql = "SELECT 
                counties.county  as county_name, districts.district as district_name, allocation_details.*
            FROM
                allocation_details,
                facilities,
                counties,
                districts
            WHERE
                district_id = '$district'
                    AND districts.county = counties.id
                    AND facilities.district = districts.id
                    AND facilities.facility_code = allocation_details.facility_code and counties.zone = '$zone'";
        $result = $this->db->query($sql)->result_array();
    
    
    foreach ($result as $key => $id_details) {
        
        $facility_code = $id_details['mfl'];
        // for each selected facility, get the last reported lab details fro all four commodities
        $sql2 = "SELECT 
                    closing_stock, days_out_of_stock, q_requested
                FROM
                    lab_commodity_details AS a
                WHERE
                    facility_code = '$facility_code'
                        AND commodity_id between 4 and 7
                        AND created_at IN (SELECT 
                            MAX(created_at)
                        FROM
                            lab_commodity_details AS b
                        WHERE
                            a.facility_code = b.facility_code)";
        $result2 = $this->db->query($sql2)->result_array();

        $county = $id_details['county'];
        $district = $id_details['district'];
        $facility_name = $id_details['facility_name'];
        $amc_s3 = $id_details['amc_s3'];
        $amc_c3 = $id_details['amc_c3'];
        $amc_t3 = $id_details['amc_t3'];
        $amc_d3 = 0;

        $ending_bal_s3 = $result2[0]['closing_stock'];
        $q_requested_s3 = $result2[0]['q_requested'];
        $days_out_of_stock_s3 = $result2[0]['days_out_of_stock'];

        $ending_bal_c3 = $result2[1]['closing_stock'];
        $q_requested_c3 = $result2[1]['q_requested'];
        $days_out_of_stock_c3 = $result2[1]['days_out_of_stock'];

        $ending_bal_t3 = $result2[2]['closing_stock'];
        $q_requested_t3 = $result2[2]['q_requested'];
        $days_out_of_stock_t3 = $result2[2]['days_out_of_stock'];

        $ending_bal_d3 = $result2[3]['closing_stock'];
        $q_requested_d3 = $result2[3]['q_requested'];
        $days_out_of_stock_d3 = $result2[3]['days_out_of_stock'];

        //based on the results, put them in an array to beused in the excell file.

        $alocation_details[] = array($county,$district,$facility_code,$facility_name,
                                     $ending_bal_s3,$amc_s3,$days_out_of_stock_s3,$q_requested_s3,
                                     $ending_bal_c3,$amc_c3,$days_out_of_stock_c3,$q_requested_c3,
                                     $ending_bal_t3,$amc_t3,$days_out_of_stock_t3,$q_requested_t3,
                                     $ending_bal_d3,$amc_d3,$days_out_of_stock_d3,$q_requested_d3);
}
        // echo"<pre>";print_r($alocation_details);
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Screening KHB');
        $this->excel->getActiveSheet()->setCellValue('I4', 'Confirmatory - First Response');
        $this->excel->getActiveSheet()->setCellValue('M4', 'Tie Breaker');
        $this->excel->getActiveSheet()->setCellValue('Q4', 'DBS Bundles');

        $this->excel->getActiveSheet()->setCellValue('A5', '');
        $this->excel->getActiveSheet()->setCellValue('B5', '');
        $this->excel->getActiveSheet()->setCellValue('C5', '');
        $this->excel->getActiveSheet()->setCellValue('D5', '');

        $this->excel->getActiveSheet()->setCellValue('E5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('G5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('H5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('I5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('J5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('K5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('L5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('M5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('N5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('O5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('P5', 'Quantity Requested');
        
        $this->excel->getActiveSheet()->setCellValue('Q5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('R5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('S5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('T5', 'Quantity Requested');
        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:K1');
        $this->excel->getActiveSheet()->mergeCells('E4:H4');
        $this->excel->getActiveSheet()->mergeCells('I4:L4');
        $this->excel->getActiveSheet()->mergeCells('M4:P4');
        $this->excel->getActiveSheet()->mergeCells('Q4:T4');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:T4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5:T5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('Q'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
        }
                            
        foreach ($alocation_details as $row){
                $exceldata[] = $row;
        }
              //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
                $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 
                $filename='Zonal Allocation (Zone '.$zone.').xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');        

    }
    function get_county_facilities_amcs($county){
        //get all the details from allocation_details for a particular county in db
    $sql = "SELECT * FROM allocation_details where county_id = '$county'";
    $result = $this->db->query($sql)->result_array();
        
    foreach ($result as $key => $id_details) {
        
        $facility_code = $id_details['mfl'];
        // for each selected facility, get the last reported lab details fro all four commodities
        $sql2 = "SELECT 
                    closing_stock, days_out_of_stock, q_requested
                FROM
                    lab_commodity_details AS a
                WHERE
                    facility_code = '$facility_code'
                        AND commodity_id between 4 and 7
                        AND created_at IN (SELECT 
                            MAX(created_at)
                        FROM
                            lab_commodity_details AS b
                        WHERE
                            a.facility_code = b.facility_code)";
        $result2 = $this->db->query($sql2)->result_array();

        $county = $id_details['county'];
        $district = $id_details['district'];
        $facility_name = $id_details['facility_name'];
        $amc_s3 = $id_details['amc_s3'];
        $amc_c3 = $id_details['amc_c3'];
        $amc_t3 = $id_details['amc_t3'];
        $amc_d3 = 0;

        $ending_bal_s3 = $result2[0]['closing_stock'];
        $q_requested_s3 = $result2[0]['q_requested'];
        $days_out_of_stock_s3 = $result2[0]['days_out_of_stock'];

        $ending_bal_c3 = $result2[1]['closing_stock'];
        $q_requested_c3 = $result2[1]['q_requested'];
        $days_out_of_stock_c3 = $result2[1]['days_out_of_stock'];

        $ending_bal_t3 = $result2[2]['closing_stock'];
        $q_requested_t3 = $result2[2]['q_requested'];
        $days_out_of_stock_t3 = $result2[2]['days_out_of_stock'];

        $ending_bal_d3 = $result2[3]['closing_stock'];
        $q_requested_d3 = $result2[3]['q_requested'];
        $days_out_of_stock_d3 = $result2[3]['days_out_of_stock'];

        //based on the results, put them in an array to beused in the excell file.

        $alocation_details[] = array($county,$district,$facility_code,$facility_name,
                                     $ending_bal_s3,$amc_s3,$days_out_of_stock_s3,$q_requested_s3,
                                     $ending_bal_c3,$amc_c3,$days_out_of_stock_c3,$q_requested_c3,
                                     $ending_bal_t3,$amc_t3,$days_out_of_stock_t3,$q_requested_t3,
                                     $ending_bal_d3,$amc_d3,$days_out_of_stock_d3,$q_requested_d3);
}
        // echo"<pre>";print_r($alocation_details);
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Screening KHB');
        $this->excel->getActiveSheet()->setCellValue('I4', 'Confirmatory - First Response');
        $this->excel->getActiveSheet()->setCellValue('M4', 'Tie Breaker');
        $this->excel->getActiveSheet()->setCellValue('Q4', 'DBS Bundles');

        $this->excel->getActiveSheet()->setCellValue('A5', '');
        $this->excel->getActiveSheet()->setCellValue('B5', '');
        $this->excel->getActiveSheet()->setCellValue('C5', '');
        $this->excel->getActiveSheet()->setCellValue('D5', '');

        $this->excel->getActiveSheet()->setCellValue('E5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('G5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('H5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('I5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('J5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('K5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('L5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('M5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('N5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('O5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('P5', 'Quantity Requested');
        
        $this->excel->getActiveSheet()->setCellValue('Q5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('R5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('S5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('T5', 'Quantity Requested');
        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:K1');
        $this->excel->getActiveSheet()->mergeCells('E4:H4');
        $this->excel->getActiveSheet()->mergeCells('I4:L4');
        $this->excel->getActiveSheet()->mergeCells('M4:P4');
        $this->excel->getActiveSheet()->mergeCells('Q4:T4');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:T4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5:T5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('Q'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
                //$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
                            
        foreach ($alocation_details as $row){
                $exceldata[] = $row;
        }
        

                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
                 
                // $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                // $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 
                $filename='County Allocation('.$alocation_details[0][0].' County).xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');        

    }
    function get_subcounty_facilities_amcs($district){
            //get all the details from allocation_details for a particular sub-county in db
    $sql = "SELECT 
                counties.county  as county_name, districts.district as district_name, allocation_details.*
            FROM
                allocation_details,
                facilities,
                counties,
                districts
            WHERE
                district_id = '$district'
                    AND districts.county = counties.id
                    AND facilities.district = districts.id
                    AND facilities.facility_code = allocation_details.facility_code";
    $result = $this->db->query($sql)->result_array();
        // echo"<pre>";print_r($result);die;
    
        
    foreach ($result as $key => $id_details) {
        
        $facility_code = $id_details['facility_code'];
        // for each selected facility, get the last reported lab details for all four commodities
        $sql2 = "SELECT 
                    closing_stock, days_out_of_stock, q_requested
                FROM
                    lab_commodity_details AS a
                WHERE
                    facility_code = '$facility_code'
                        AND commodity_id between 4 and 7
                        AND created_at IN (SELECT 
                            MAX(created_at)
                        FROM
                            lab_commodity_details AS b
                        WHERE
                            a.facility_code = b.facility_code)";
        $result2 = $this->db->query($sql2)->result_array();

        $county = $id_details['county_name'];
        $district = $id_details['district_name'];
        $facility_name = $id_details['facility_name'];
        $amc_s3 = $id_details['allocate_s'];
        $amc_c3 = $id_details['allocate_c'];
        $amc_t3 = $id_details['allocate_t'];
        $amc_d3 = 0;

        $ending_bal_s3 = $result2[0]['closing_stock'];
        $q_requested_s3 = $result2[0]['q_requested'];
        $days_out_of_stock_s3 = $result2[0]['days_out_of_stock'];

        $ending_bal_c3 = $result2[1]['closing_stock'];
        $q_requested_c3 = $result2[1]['q_requested'];
        $days_out_of_stock_c3 = $result2[1]['days_out_of_stock'];

        $ending_bal_t3 = $result2[2]['closing_stock'];
        $q_requested_t3 = $result2[2]['q_requested'];
        $days_out_of_stock_t3 = $result2[2]['days_out_of_stock'];

        $ending_bal_d3 = $result2[3]['closing_stock'];
        $q_requested_d3 = $result2[3]['q_requested'];
        $days_out_of_stock_d3 = $result2[3]['days_out_of_stock'];

        //based on the results, put them in an array to beused in the excell file.

        $alocation_details[] = array($county,$district,$facility_code,$facility_name,
                                     $ending_bal_s3,$amc_s3,$days_out_of_stock_s3,$q_requested_s3,
                                     $ending_bal_c3,$amc_c3,$days_out_of_stock_c3,$q_requested_c3,
                                     $ending_bal_t3,$amc_t3,$days_out_of_stock_t3,$q_requested_t3,
                                     $ending_bal_d3,$amc_d3,$days_out_of_stock_d3,$q_requested_d3);
}
        // echo"<pre>";print_r($alocation_details);die;
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Screening KHB');
        $this->excel->getActiveSheet()->setCellValue('I4', 'Confirmatory - First Response');
        $this->excel->getActiveSheet()->setCellValue('M4', 'Tie Breaker');
        $this->excel->getActiveSheet()->setCellValue('Q4', 'DBS Bundles');

        $this->excel->getActiveSheet()->setCellValue('A5', '');
        $this->excel->getActiveSheet()->setCellValue('B5', '');
        $this->excel->getActiveSheet()->setCellValue('C5', '');
        $this->excel->getActiveSheet()->setCellValue('D5', '');

        $this->excel->getActiveSheet()->setCellValue('E5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('G5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('H5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('I5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('J5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('K5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('L5', 'Quantity Requested');

        $this->excel->getActiveSheet()->setCellValue('M5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('N5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('O5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('P5', 'Quantity Requested');
        
        $this->excel->getActiveSheet()->setCellValue('Q5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('R5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('S5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('T5', 'Quantity Requested');
        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:K1');
        $this->excel->getActiveSheet()->mergeCells('E4:H4');
        $this->excel->getActiveSheet()->mergeCells('I4:L4');
        $this->excel->getActiveSheet()->mergeCells('M4:P4');
        $this->excel->getActiveSheet()->mergeCells('Q4:T4');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:T4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5:T5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('Q'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
                //$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
                            
        foreach ($alocation_details as $row){
                $exceldata[] = $row;
        }
        

                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
                 
                // $this->excel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                // $this->excel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 
                $filename='County Allocation('.$alocation_details[0][1].' Sub-County).XLSX'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');          

    }
    function get_commodity_facilities_amcs($commodity){
        //get the amc for the selected commodity
    	if ($commodity == 4){
    		 $sql = "SELECT county, district, mfl, facility_name, amc_s3 as amc FROM allocation_details limit 0,100";
             $commodity_name = 'Screening-KHB';

    	}else if($commodity == 5){
    		 $sql = "SELECT county, district, mfl, facility_name, amc_c3 as amc FROM allocation_details";
             $commodity_name = 'Confirmatory-First Response';

    	}else if($commodity==6){
    		 $sql = "SELECT county, district, mfl, facility_name, amc_t3 as amc FROM allocation_details";
             $commodity_name = 'Tie Breaker';

    	}else if($commodity==7){
    		 $sql = "SELECT county, district, mfl, facility_name, amc_d3 as amc FROM allocation_details";
             $commodity_name = 'DBS Bundles';

    	}       
        $result = $this->db->query($sql)->result_array();
        
        foreach ($result as $key => $id_details) {
        
        $facility_code = $id_details['mfl'];
        // for each selected facility, get the last reported lab details for selected commodity
        $sql2 = "SELECT 
                    closing_stock, days_out_of_stock, q_requested
                FROM
                    lab_commodity_details AS a
                WHERE
                    facility_code = '$facility_code'
                        AND commodity_id = '$commodity'
                        AND created_at IN (SELECT 
                            MAX(created_at)
                        FROM
                            lab_commodity_details AS b
                        WHERE
                            a.facility_code = b.facility_code)";
        $result2 = $this->db->query($sql2)->result_array();

        $county = $id_details['county'];
        $district = $id_details['district'];
        $facility_name = $id_details['facility_name'];
        $amc = $id_details['amc'];       

        $ending_bal = $result2[0]['closing_stock'];
        $q_requested = $result2[0]['q_requested'];
        $days_out_of_stock= $result2[0]['days_out_of_stock'];

     //based on the results, put them in an array to be used in the excel file.

        $alocation_details[] = array($county,$district,$facility_code,$facility_name,
                                     $ending_bal,$amc,$days_out_of_stock,$q_requested);
        }
        // echo"<pre>";print_r($alocation_details);die;
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', ' '.$commodity_name.'');
        
        $this->excel->getActiveSheet()->setCellValue('A5', '');
        $this->excel->getActiveSheet()->setCellValue('B5', '');
        $this->excel->getActiveSheet()->setCellValue('C5', '');
        $this->excel->getActiveSheet()->setCellValue('D5', '');

        $this->excel->getActiveSheet()->setCellValue('E5', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('G5', 'Days Out of Stock');
        $this->excel->getActiveSheet()->setCellValue('H5', 'Quantity Requested');

        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:K1');
        $this->excel->getActiveSheet()->mergeCells('E4:H4');
       
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5:H5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('H'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
        }
                            
        foreach ($alocation_details as $row){
                $exceldata[] = $row;
        }      
                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
                 
                $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 
                $filename='Commodity Allocation('.$commodity_name .').xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');  
    }

     function get_months_facilities_amcs($month){
        $sql = "SELECT * FROM allocation_details where month = '$month'";
        $result = $this->db->query($sql)->result_array();
       
        //convert the date into text
        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);              
        $firstdate = $year . '-' . $month . '-01';     
        $month_text =  date("F Y", strtotime($firstdate)); 

        foreach ($result as $key => $id_details) {
        
        $facility_code = $id_details['mfl'];
        $county = $id_details['county'];
        $district = $id_details['district'];
        $facility_name = $id_details['facility_name'];
        $amc_s3 = $id_details['amc_s3'];
        $amc_c3 = $id_details['amc_c3'];
        $amc_t3 = $id_details['amc_t3'];
        $amc_d3 = 0;        
        
        //based on the results, put them in an array to be used in the excel file.

        $alocation_details[] = array($county,$district,$facility_code,$facility_name,$amc_s3,$amc_c3,$amc_t3,$amc_d3);
}
        // echo"<pre>";print_r($alocation_details);
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Screening KHB');
        $this->excel->getActiveSheet()->setCellValue('F4', 'Confirmatory - First Response');
        $this->excel->getActiveSheet()->setCellValue('G4', 'Tie Breaker');
        $this->excel->getActiveSheet()->setCellValue('H4', 'DBS Bundles');

        $this->excel->getActiveSheet()->setCellValue('A5', '');
        $this->excel->getActiveSheet()->setCellValue('B5', '');
        $this->excel->getActiveSheet()->setCellValue('C5', '');
        $this->excel->getActiveSheet()->setCellValue('D5', '');
        $this->excel->getActiveSheet()->setCellValue('E5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('F5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('G5', 'AMC');
        $this->excel->getActiveSheet()->setCellValue('H5', 'AMC');

        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:K1');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5:H5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('H'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
        }
                            
        foreach ($alocation_details as $row){
                $exceldata[] = $row;
        }        

                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A6');
                $this->excel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 
                $filename='Monthly Allocation ('.$month_text.').xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
                $objWriter->save('php://output');

    }
    function get_fcdrr_details($type, $month, $county_id, $district_id, $commodity){

        $district_conditions = '';
        if($district_id > 0){
            $district_conditions = ' AND districts.id = '.$district_id;
        }

        $commodity_conditions = '';
        if($commodity >0){
            $commodity_conditions = ' AND lab_commodity_details.commodity_id = '.$commodity;
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);              
        $firstdate = $year . '-' . $month . '-01';     
        $lastdate = $year . '-' . $month . '-31';     
        $month_text =  date("F Y", strtotime($firstdate)); 

        $sql = "SELECT 
                    counties.county,
                    districts.district,
                    facilities.facility_code,
                    facilities.facility_name,                    
                    lab_commodities.commodity_name,
                    lab_commodity_details.*
                FROM
                    lab_commodity_details,
                    lab_commodities,
                    facilities,
                    districts,
                    counties
                WHERE
                    lab_commodity_details.created_at BETWEEN '$firstdate' AND '$lastdate'                    
                        AND lab_commodities.id = lab_commodity_details.commodity_id $commodity_conditions
                        AND facilities.facility_code = lab_commodity_details.facility_code
                        AND facilities.district = districts.id
                        AND districts.county = counties.id
                        AND counties.id = '$county_id' $district_conditions
                ORDER BY counties.county , districts.district , facilities.facility_code , lab_commodity_details.commodity_id";
        $result = $this->db->query($sql)->result_array();
       echo "$sql";die;
       
        //convert the date into text        

        foreach ($result as $key => $details) {
        
        $facility_code = $details['facility_code'];
        $county = $details['county'];
        $district = $details['district'];
        $facility_name = $details['facility_name'];
        $commodity_name = $details['commodity_name'];
        $beginning_bal = $details['beginning_bal'];
        $q_received = $details['q_received'];
        $q_used = $details['q_used'];
        $no_of_tests_done = $details['no_of_tests_done'];
        $positive_adj = $details['positive_adj'];
        $negative_adj = $details['negative_adj'];
        $closing_stock = $details['closing_stock'];
        $days_out_of_stock = $details['days_out_of_stock'];
        $q_expiring = $details['q_expiring'];
       // $no_of_tests_done = 0;        
        
        //based on the results, put them in an array to be used in the excel file.

        $fcdrr_details[] = array($county,$district,$facility_code,$facility_name,$commodity_name,$beginning_bal,$q_received,$q_used,$no_of_tests_done,$positive_adj,$negative_adj, $closing_stock,$days_out_of_stock, $q_expiring);
}
        // echo"<pre>";print_r($alocation_details);
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(' Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Commodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Commodity Name');
        $this->excel->getActiveSheet()->setCellValue('F4', 'Begining Balance');
        $this->excel->getActiveSheet()->setCellValue('G4', 'Quantity Received');
        $this->excel->getActiveSheet()->setCellValue('H4', 'Quantity Used');
        $this->excel->getActiveSheet()->setCellValue('I4', 'Tests Done');
        $this->excel->getActiveSheet()->setCellValue('J4', 'Positive Adjustments');
        $this->excel->getActiveSheet()->setCellValue('K4', 'Negative Adjustments');
        $this->excel->getActiveSheet()->setCellValue('L4', 'Ending Balance');
        $this->excel->getActiveSheet()->setCellValue('M4', 'Days out of Stock');
        $this->excel->getActiveSheet()->setCellValue('N4', 'Quantity Expiring');

        

        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:N1');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:N4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('M'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
        }
                            
        foreach ($fcdrr_details as $row){
                $exceldata[] = $row;
        }        

                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A5');
                 
                $filename= $county.' County FCDRR Details ('.$month_text.').xlsx'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
                //force user to download the Excel file without writing it to server's HD
                ob_end_clean();
                $objWriter->save('php://output');

    }
    function get_one_fcdrr_details($type, $month, $county_id, $district_id, $commodity){

        $district_conditions = '';
        if($district_id > 0){
            $district_conditions = ' AND districts.id = '.$district_id;
        }

        $commodity_conditions = '';
        if($commodity >0){
            $commodity_conditions = ' AND lab_commodity_details.commodity_id = '.$commodity;
        }

        $year = substr($month, -4);
        $month = substr_replace($month, "", -4);              
        $firstdate = $year . '-' . $month . '-01';     
        $lastdate = $year . '-' . $month . '-31';     
        $month_text =  date("F Y", strtotime($firstdate)); 

        if($type = 1){
            $type_text = 'closing_stock';
            $type_title = 'Ending Balance';
        }else if($type = 2){            
            $type_text = 'q_used';
            $type_title = 'Quantity Used';
        }else if($type = 3){            
            $type_text = 'no_of_tests_done';
            $type_title = 'No of Tests done';
        }

        $sql = "SELECT 
                    counties.county,
                    districts.district,
                    facilities.facility_code,
                    facilities.facility_name,                    
                    lab_commodities.commodity_name,
                    lab_commodity_details.$type_text as type_of_text
                FROM
                    lab_commodity_details,
                    lab_commodities,
                    facilities,
                    districts,
                    counties
                WHERE
                    lab_commodity_details.created_at BETWEEN '$firstdate' AND '$lastdate'                    
                        AND lab_commodities.id = lab_commodity_details.commodity_id $commodity_conditions
                        AND facilities.facility_code = lab_commodity_details.facility_code
                        AND facilities.district = districts.id
                        AND districts.county = counties.id
                        AND counties.id = '$county_id' $district_conditions
                ORDER BY counties.county , districts.district , facilities.facility_code , lab_commodity_details.commodity_id";
        $result = $this->db->query($sql)->result_array();
        //convert the date into text        

        foreach ($result as $key => $details) {
        
        $facility_code = $details['facility_code'];
        $county = $details['county'];
        $district = $details['district'];
        $facility_name = $details['facility_name'];
        $commodity_name = $details['commodity_name'];
        $type_value = $details['type_of_text'];
        // $q_received = $details['q_received'];
        // $q_used = $details['q_used'];
        // $no_of_tests_done = $details['no_of_tests_done'];
        // $positive_adj = $details['positive_adj'];
        // $negative_adj = $details['negative_adj'];
        // $closing_stock = $details['closing_stock'];
        // $days_out_of_stock = $details['days_out_of_stock'];
        // $q_expiring = $details['q_expiring'];
       // $no_of_tests_done = 0;        
        
        //based on the results, put them in an array to be used in the excel file.

        $fcdrr_details[] = array($county,$district,$facility_code,$facility_name,$commodity_name,$type_value);
}
        // echo"<pre>";print_r($alocation_details);
    
         $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(' Counties');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', $district.' RTKCommodity Consumption Data (in Tests)');
        $this->excel->getActiveSheet()->setCellValue('A4', 'County');
        $this->excel->getActiveSheet()->setCellValue('B4', 'Sub-County');
        $this->excel->getActiveSheet()->setCellValue('C4', 'MFL Code');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Facility Name');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Commodity Name');
        $this->excel->getActiveSheet()->setCellValue('F4',  $type_title);
        // $this->excel->getActiveSheet()->setCellValue('G4', 'Quantity Received');
        // $this->excel->getActiveSheet()->setCellValue('H4', 'Quantity Used');
        // $this->excel->getActiveSheet()->setCellValue('I4', 'Tests Done');
        // $this->excel->getActiveSheet()->setCellValue('J4', 'Positive Adjustments');
        // $this->excel->getActiveSheet()->setCellValue('K4', 'Negative Adjustments');
        // $this->excel->getActiveSheet()->setCellValue('L4', 'Ending Balance');
        // $this->excel->getActiveSheet()->setCellValue('M4', 'Days out of Stock');
        // $this->excel->getActiveSheet()->setCellValue('N4', 'Quantity Expiring');

        

        //merge cell A1 until C1
        $this->excel->getActiveSheet()->mergeCells('A1:F1');
        //set aligment to center for that merged cell (A1 to C1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

       for($col = ord('A'); $col <= ord('F'); $col++){
                //set column dimension
                $this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(false);
                 //change the font size
                $this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
                 
        }
                            
        foreach ($fcdrr_details as $row){
                $exceldata[] = $row;
        }        

                //Fill data 
                $this->excel->getActiveSheet()->fromArray($exceldata, null, 'A5');
                 
                $filename= $county.' County '.$type_title.' ('.$month_text.').xlsx'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
                //force user to download the Excel file without writing it to server's HD
                ob_end_clean();
                $objWriter->save('php://output');

    }
    function get_combination_facilities_amcs(){
        $sql = "SELECT * FROM allocation_details where district_id = '$district'";
        $result = $this->db->query($sql)->result_array();
        // print_r($result);
        echo json_encode($result);        

    }

}

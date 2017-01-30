<?php

	ini_set('max_execution_time', -1);
	$mysqli = new mysqli("localhost", "root", "", "hcmp_rtk");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
// $sql = "update facilities set pepfar_supported='0'";
// 				$import = mysqli_query($mysqli,$sql) or die('Could Not Execute');die();
	//include("config.php"); //here is where you specify your db connection path
$rec=0;
$handle = fopen ('dr3.csv', 'r'); //this is the csv file wth the mfl codes and partner ids ( ensure this csv is in same folder as the upload script )
$count=0;
while($count<=1000){
		while (($data = fgetcsv($handle, 5000, ',', '"')) !== FALSE)
		{
			// $rec++;
			// if($rec==1)
			$screening = str_replace(',', '', $data[1]);
			$confirm = $screening*0.1;
			// {
				$sql = "update counties set screening_current_amount='$screening',screening_drawing_rights = '$screening', confimatory_drawing_rights='$confirm',confirmatory_current_amount = '$confirm'  where id='$data[0]'";
				
				// echo "Updated $data[0]<br/>";
				echo $confirm.'</br>';

			 	$import = mysqli_query($mysqli,$sql) or die('Could Not Execute');
					
			// 		if ($import)
			// 		{
			// 			echo "Updated Facility '$data[0]'\n";
			// 			$count=$count+1;
			// 		}	   
			// 		else
			// 		{
			// 			echo "Not Updated Facility '$data[0]'\n";

			// 		}	
			// continue;
			// }
			// else
			// {
			// 	//echo $data[0] .'<br/>' ; 
			// 	$sql = "update facilities set district='104'  where facility_code='$data[0]'";
			// 	echo "Not Updated $data[0]<br/>";
			
 // $import = mysqli_query($mysqli,$sql) or die('Could Not Execute');
					
// 					if ($import)
// 					{
// 						echo "Updated Facility '$data[0]'\n";
// 						$count=$count+1;
// 					}	   
// 					else
// 					{
// 						echo "Not Updated Facility '$data[0]'\n";

// 					}				
			
			// } //end else rec
		}// end while}
		$count++;
	}
		
		// if ($import)
		// {
		// echo $count . " Facility Records updated";
		// }
		// else
		// {
		// echo "Failed Updating, Try again ";
		// }
		

?>
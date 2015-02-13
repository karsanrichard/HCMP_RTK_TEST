<?php

	ini_set('max_execution_time', -1);
	$mysqli = new mysqli("localhost", "root", "", "hcmp_rtk");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

	//include("config.php"); //here is where you specify your db connection path
$rec=0;
$handle = fopen ('pepfar_updated.csv', 'r'); //this is the csv file wth the mfl codes and partner ids ( ensure this csv is in same folder as the upload script )
$count=0;
		while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE)
		{
			$rec++;
			if($rec==1)
			{
			continue;
			}
			else
			{
				//echo $data[0] .'<br/>' ; 
			
$import = mysqli_query($mysqli,"update facilities set pepfar_supported='1'  where facility_code='$data[0]'") or die('Could Not Execute');
						   //rename facility, mfl code to actual attribute names on the hcmp database a
					if ($import)
					{
						$count=$count+1;
					}	   
					else
					{

					}				
			
			} //end else rec
		}// end while
		
		if ($import)
		{
		echo $count . " Facility Records updated";
		}
		else
		{
		echo "Failed Updating, Try again ";
		}
		

?>
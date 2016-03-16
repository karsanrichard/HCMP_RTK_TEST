<?php
ob_start();
class rtk_mailer extends MY_Controller {
 

public function send_email($email_address,$message,$subject,$attach_file=NULL,$bcc_email=NULL){

		$fromm='rtk.kenya@gmail.com';
		$messages=$message;  

		$config['protocol']    = 'smtp';
    $config['smtp_host']    = 'ssl://smtp.gmail.com';
    $config['smtp_port']    = '465';
    $config['smtp_timeout'] = '7';
    $config['smtp_user']    = 'rtk.kenya@gmail.com';
    $config['smtp_pass']    = 'savinglives';
    $config['charset']    = 'utf-8';
    $config['newline']    = "\r\n";
    $config['mailtype'] = 'html'; // or html
    $config['validation'] = TRUE; // bool whether to validate email or not  
		$this->load->library('email', $config);

    $this->email->initialize($config);
	
		$this->email->set_newline("\r\n");
		$this->email->from($fromm,'RTK Kenya'); // change it to yours
		$this->email->to($email_address); // change it to yours
		
		
      // $bcc_list = array('ttunduny@gmail.com', 'annchemu@gmail.com');
      $bcc_list = array( 'annchemu@gmail.com','onjathi@clintonhealthaccess.org','sharon.olwande@yahoo.com');
      $this->email->bcc($bcc_list);
      // $this->email->bcc('onjathi@clintonhealthaccess.org');  
    //   $this->email->bcc('ttunduny@gmail.com');  
  		// $this->email->bcc('annchemu@gmail.com');	
    
		if (isset($attach_file)){
		  $this->email->attach($attach_file); 	      
		}
		else{
			
		}
  		
  	$this->email->subject($subject);
 		$this->email->message($messages);
 
    if($this->email->send())
    {      
      return TRUE;
    }
    else
    {      
      return show_error($this->email->print_debugger());
    }



}
	
  public function send_email_multiple($email_address,$message,$subject,$bcc_email=NULL){

    session_start();
    $reports = $_SESSION['national_reports'];


    $attach = array();
    $count = count($reports);    


    $fromm='rtk.kenya@gmail.com';
    $messages=$message;

      $config['protocol']    = 'smtp';
      $config['smtp_host']    = 'ssl://smtp.gmail.com';
      $config['smtp_port']    = '465';
      $config['smtp_timeout'] = '7';
      $config['smtp_user']    = 'rtk.kenya@gmail.com';
      $config['smtp_pass']    = 'savinglives';
      $config['charset']    = 'utf-8';
      $config['newline']    = "\r\n";
      $config['mailtype'] = 'html'; // or html
      $config['validation'] = TRUE; // bool whether to validate email or not  
      $this->load->library('email', $config);

        $this->email->initialize($config);
    
      $this->email->set_newline("\r\n");
      $this->email->from($fromm,'RTK Kenya'); // change it to yours
      $this->email->to($email_address); // change it to yours
      
      if(isset($bcc_email)){
  //  $this->email->bcc("billnguts@gmail.com,".$bcc_email); 
      }else{
    //$this->email->bcc('billnguts@gmail.com'); 
      }     
      if($count==1){
        $name = $reports[0];        
        $attach_file = './pdf/' . $name . '.pdf';    
        echo "<br/>Attaching File $attach_file<br/>";    
        $this->email->attach($attach_file); 
      }else{
        for ($i=0; $i < $count; $i++) { 
          $name = $reports[$i];        
          $attach_file = './pdf/' . $name . '.pdf';
          echo "<br/>Attaching File $attach_file<br/>";    
          $this->email->attach($attach_file);           
          //array_push($attach, $attach_file);
        }
      }
    // if (isset($attach_file)){
    // $this->email->attach($attach_file);   
    // }
    // else{
      
    // }
      
      $this->email->subject($subject);
    $this->email->message($messages);
 
  if($this->email->send())
 {
return TRUE;
 }
 else
{
 return show_error($this->email->print_debugger());
}



}
  
		

} 
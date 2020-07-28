<?php
session_start();

if($_GET['err']!=""){
	ini_set ("display_errors", "1");	error_reporting(E_ALL);
}

	##################      LIVE SERVER     ###########################

	$host = "localhost";
	$user = "FeatherStoneDashboard";
	$pass = "FSD>Login-1";
	$db	 = "featherstone_db";
	$charset = 'utf8mb4';

	##################     / LIVE SERVER     ##########################
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyz@#$%-+<>-_!*ABCDEFGHIJKLMNOP1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function cleanArray($array){
	if(is_array($array)){
		foreach($array as $key=>$value){

			$value = str_replace("script","scrip t",$value); //no easy javascript injection
			$value = str_replace("union","uni on",$value); //no easy common mysql temper
			
			$value = str_replace("'","''",$value); //no single quotes

			$value = htmlentities($value, ENT_QUOTES); //encodes the string nicely
			$value = addslashes($value); //mysql_real_escape_string() //htmlentities
			
			$array[$key] = $value;
		}
	}else{
		return false;
	}

	return $array;
}
function sanSlash($string){
	$string = htmlentities($string, ENT_QUOTES); //encodes the string nicely
	$string = addslashes($string); //mysql_real_escape_string() //htmlentities
	return $string;
}

function doEmail($address,$pword){
	    // Configuration 
    $to = $address;
    $toBCC = "curiousweasel@gmail.com";

    // ################################   Send the email    ##############################
    $my_t=getdate(date("U"));
    $str_date=$my_t[year]."-".$my_t[mon]."-".$my_t[mday]." ".$my_t[hours].":".$my_t[minutes].":".$my_t[seconds];
	$niceDate = date('l jS M',strtotime($str_date));

	# -=-=-=- MIME BOUNDARY

	$mime_boundary = "----featherstonepartners----".md5(time());
	
	# -=-=-=- MAIL HEADERS
	
	$subject = "Featherstone Password Reset";

	$headers = "From: do-not-reply@featherstonepartners.co.uk\n";

	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
	
	# -=-=-=- TEXT EMAIL PART
	
	$contents = "--$mime_boundary\n";
	$contents .= "Content-Type: text/plain; charset=UTF-8\n";
	$contents .= "Content-Transfer-Encoding: 8bit\n\n";

	$contents .= "You have requested a new password." . PHP_EOL . PHP_EOL;
	$contents .= "Password: $pword" . PHP_EOL . PHP_EOL;
	$contents .= "+++ Message Ends +++\n\n";
	
	# -=-=-=- HTML EMAIL PART
	$contents .= "--$mime_boundary\n";
	$contents .= "Content-Type: text/html; charset=UTF-8\n";
	$contents .= "Content-Transfer-Encoding: 8bit\n\n";
	
	$contents .= "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>\n";
	$contents .= "<html>\n";
	$contents .= "<head>\n";
	$contents .= "<title>You have requested a new password</title>\n";
	$contents .= "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n";
	$contents .= "</head>\n";
	$contents .= "<body bgcolor='#fff'>\n";
	$contents .= "<p style='text-align:left;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; color:#333'>A request was made to reset your password on ".$niceDate.".</p>\n";
	$contents .= "<p style='text-align:left;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; color:#333'>Your new password is : ".$pword.".</p>\n";
	$contents .= "<p style='text-align:left;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px; color:#333'>Please log in and change this as soon as you can.</p>\n";
	$contents .= "</body>\n";
	$contents .= "</html>\n";
	
	# -=-=-=- FINAL BOUNDARY
	
	$contents .= "--$mime_boundary--\n\n";
	
	# -=-=-=- SEND MAIL
	
	$mail_sent = @mail( $to, $subject, $contents, $headers );
    return $mail_sent;
}
?>
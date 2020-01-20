<?php
	include_once'database_config.php';

	$data = json_decode(file_get_contents("php://input"),true);

	$password = $data['newpassword'];
	$password = base64_encode($password);	
	$userId = $data['userId'];
	$userType = $data['userType'];

	// $password = '123456';
	// $password = base64_encode($password);	
	// $userId = '1';
	// $userType = 'professor';

	// print_r($password); exit;

	if($userType == 'admin')
	{
		//Admin - tnega_user 
        $sql = "UPDATE tnega_user SET password = '$password' where id = '$userId'";
	}
	else if($userType == 'professor')
	{
		//Professor - tnega_professor
        $sql = "UPDATE tnega_professor SET password = '$password' where id = '$userId'";
	}
	else
	{
		//Student - tnega_professor
        $sql = "UPDATE tnega_student SET password = '$password' where id = '$userId'";
	}

    $result = mysqli_query($con,$sql);

    if($result)
    {
        
        $response["success"] = true;
        $response["message"] = "success";
    }
    else
    { 
        $response["success"] = false;
        $response["message"] = "Try again";
    }
    echo json_encode($response);
?>


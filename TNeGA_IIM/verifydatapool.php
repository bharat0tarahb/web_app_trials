<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];
    
    $student_id = $_GET["student_id"];
    $datapool = $_GET["datapool"];

    if($datapool == 1)
    {
        $sql = "UPDATE tnega_images SET status = 1 where student_id = '$student_id' and datapool = 1";
    }
    else if($datapool == 2)
    {
        $sql = "UPDATE tnega_images SET status = 1 where student_id = '$student_id' and datapool = 2";
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
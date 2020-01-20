<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];
    
    $image_id = $_GET["image_id"];

    $sql = "Delete From tnega_images where id = '$image_id'";

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
<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];
    
    $student_id = $_GET["student_id"];
    $datapool = $_GET["datapool"];

    if($datapool == 1)
    {
        $sql = "SELECT * FROM tnega_images where student_id = '$student_id' and datapool = 1 ORDER BY tnega_images.id ASC";
    }
    else if($datapool == 2)
    {
        $sql = "SELECT * FROM tnega_images where student_id = '$student_id' and datapool = 2 ORDER BY tnega_images.id ASC";
    }
    else
    {
        $sql = "SELECT * FROM tnega_images where student_id = '$student_id' and status = 1 ORDER BY tnega_images.id ASC";
    }


    $result = mysqli_query($con,$sql);
    $noofrows = mysqli_num_rows($result);

    if($noofrows > 0)
    {
        $response["student_images"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        { 
            $response["success"] = true;
            array_push($response["student_images"],$row);
        }
    }
    else
    { 
        $response["success"] = false;
        $response["message"] = "No data";
    }
    echo json_encode($response);

?>
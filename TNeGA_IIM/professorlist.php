<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    $sql = "SELECT * FROM tnega_professor ORDER BY tnega_professor.id ASC";

    $result = mysqli_query($con,$sql);
    $noofrows = mysqli_num_rows($result);

    if($noofrows > 0)
    {
        $response["professor"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        { 
            $response["success"] = true;
            array_push($response["professor"],$row);
        }
    }
    else
    { 
        $response["success"] = false;
        $response["message"] = "Try again";
    }
    echo json_encode($response);

?>
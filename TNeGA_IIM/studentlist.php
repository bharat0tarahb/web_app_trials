<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    
    $class_id = $_GET["classId"];
    $section_id = $_GET["sectionId"];

    $sql = "SELECT * FROM tnega_student where class_id = '$class_id' and section_id = '$section_id' ORDER BY tnega_student.id ASC";

    $result = mysqli_query($con,$sql);
    $noofrows = mysqli_num_rows($result);

    if($noofrows > 0)
    {
        $response["students"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        { 
            $response["success"] = true;
            array_push($response["students"],$row);
        }
    }
    else
    { 
        $response["success"] = false;
        $response["message"] = "Try again";
    }
    echo json_encode($response);

?>
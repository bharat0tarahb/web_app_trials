<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if($request_method == 'POST')
    {
        $subject_name = $data['subject_name'];

        $sql = "INSERT INTO tnega_subject(subject_name) VALUES ('$subject_name')";

        $insert = mysqli_query($con,$sql);

        if($insert)
        {
          $response["success"] = true;
          $response["message"]="subject inserted";
          echo json_encode($response);
        }
        else
        {
            $response["success"] = false;
            $response["message"] = "Try again later";
            echo json_encode($response);
        }

    } else {

        $sql="SELECT * FROM tnega_subject";
        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        $response["subjects"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        { 
            $response["success"] = true;
            array_push($response["subjects"],$row);
        }
        echo json_encode($response);
    }


?>
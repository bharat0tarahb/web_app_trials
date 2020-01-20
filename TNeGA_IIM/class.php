<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if($request_method == 'POST')
    {
        $degree = $data['degree'];
        $category = $data['category'];

        $sql = "INSERT INTO tnega_class(degree,category) VALUES ('$degree','$category')";

        $insert = mysqli_query($con,$sql);

        if($insert)
        {
          $response["success"] = true;
          $response["message"]="class inserted";
          echo json_encode($response);
        }
        else
        {
            $response["success"] = false;
            $response["message"] = "Try again later";
            echo json_encode($response);
        }

    } else {

        $sql="SELECT * FROM tnega_class";
        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        $response["class_list"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        { 
            $response["success"] = true;
            array_push($response["class_list"],$row);
        }
        echo json_encode($response);
    }


?>
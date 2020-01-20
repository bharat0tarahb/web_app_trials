<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if($request_method == 'POST')
    {
        $class_id = $data['classid'];
        $name = $data['name'];

        $sql = "INSERT INTO tnega_section(class_id,name) VALUES ('$class_id','$name')";

        $insert = mysqli_query($con,$sql);

        if($insert)
        {
          $response["success"] = true;
          $response["message"]="Section inserted";
          echo json_encode($response);
        }
        else
        {
            $response["success"] = false;
            $response["message"] = "Try again later";
            echo json_encode($response);
        }

    } else {

        $classId = $_GET["classId"];

        $sql="SELECT tnega_class.id as class_id,tnega_class.degree,tnega_class.category,tnega_section.id as section_id,tnega_section.name as section_name FROM tnega_section inner join tnega_class on tnega_section.class_id = tnega_class.id where tnega_class.id = '$classId' ORDER BY tnega_class.id ASC";

        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        if($noofrows > 0)
        {
            $response["sections"] = array();
            while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
            { 
                $response["success"] = true;
                array_push($response["sections"],$row);
            }
        }
        else
        { 
            $response["success"] = false;
            $response["message"] = "Try again";
        }
        echo json_encode($response);

    }


?>
<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    $option = $_GET["option"];

    if($option == 'class')
    {
        $sql="SELECT * FROM tnega_class group by degree";
        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        $response["class_list"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        { 
            $response["success"] = true;
            array_push($response["class_list"],$row);
        }

    }
    else if($option == 'catgory')
    {
        $degree = $_GET["degree"];

        $sql="SELECT * FROM tnega_class where degree = '$degree'";
        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        $response["categories"] = array();
        while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
        {
            $response["success"] = true;
            array_push($response["categories"],$row);
        }
    }
    else if($option == 'section')
    {
        $degreevalue = $_GET["degreevalue"];
        $categoryvalue = $_GET["categoryvalue"];

        $sql="SELECT id FROM tnega_class where degree = '$degreevalue' and category = '$categoryvalue' ";
        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);

        if($noofrows > 0)
        {
            $sql="SELECT * FROM tnega_section where class_id = ".$row['id']." ";
            $result = mysqli_query($con,$sql);

            $response["sections"] = array();
            while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
            {
                $response["success"] = true;
                array_push($response["sections"],$row);
            }
        }
    }

    echo json_encode($response);

?>
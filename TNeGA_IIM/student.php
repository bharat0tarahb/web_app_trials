<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if($request_method == 'POST')
    {
        $class_id = $data['classid'];
        $section_id = $data['section_id'];
        $name = $data['name'];
        $rollNumber = $data['rollNumber'];
        $dob = $data['dob'];

        $sql = "SELECT userName from tnega_user WHERE userName = '".$rollNumber."' union all SELECT userName from tnega_professor WHERE userName = '".$rollNumber."' union all SELECT userName from tnega_student WHERE userName = '".$rollNumber."' ";

        $result = mysqli_query($con, $sql);
        $row = mysqli_num_rows($result);

        if ($row) 
        {
            // user is already existed
            $response["Error"] = false;
            $response["message"] = "Username already existed";
            echo json_encode($response);
        }
        else
        {
            $userName = $rollNumber;
            $password = base64_encode($rollNumber);

            $sql = "INSERT INTO tnega_student(class_id,section_id,userName,password,user_type,name,rollNumber,dob) VALUES ('$class_id','$section_id','$userName','$password','student','$name','$rollNumber','$dob')";

            // print_r($sql);

            $insert = mysqli_query($con,$sql);

            if($insert)
            {
              $response["success"] = true;
              $response["message"]="Student inserted";
              echo json_encode($response);
            }
            else
            {
                $response["success"] = false;
                $response["message"] = "Try again later";
                echo json_encode($response);
            }
        }

    } else {

        $studentID = $_GET["studentID"];

        $sql = "SELECT tnega_student.*,tnega_class.degree,tnega_class.category,tnega_section.name as section_name FROM tnega_student inner join tnega_class on tnega_student.class_id = tnega_class.id INNER join tnega_section on tnega_student.section_id = tnega_section.id  where tnega_student.id = '$studentID' ORDER BY tnega_student.id ASC LIMIT 1";

        // print_r($sql); exit;

        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        /******************************************************/

        $sql1_percent = "SELECT ( (SELECT COUNT(DISTINCT date) FROM `tnega_attendance` where student_id = '$studentID' ) * 100  / ( SELECT COUNT(DISTINCT date) FROM `tnega_attendance`) ) as presentpercent FROM `tnega_attendance` ";

        $result1 = mysqli_query($con,$sql1_percent);
        $row1 = mysqli_fetch_assoc($result1);

        $response["studentpercentage"] = array();
        array_push($response["studentpercentage"],round($row1['presentpercent']), 100 - round($row1['presentpercent']));

        /******************************************************/

        if($noofrows > 0)
        {
            while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
            { 
                $response["success"] = true;
                $response["id"] = $row['id'];
                $response["user_type"] = $row['user_type'];
                $response["name"] = $row['name'];
                $response["rollNumber"] = $row['rollNumber'];

                $response["dob"] = $row['dob'];
                $response["degree"] = $row['degree'];
                $response["category"] = $row['category'];
                $response["section_name"] = $row['section_name'];

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
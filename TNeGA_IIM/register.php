<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
   
    $userName =$data['userName'];
    $password =$data['password'];
    $user_type =$data['user_type'];

    $sql = "SELECT userName from tnega_user WHERE userName = '".$username."' union all SELECT userName from tnega_professor WHERE userName = '".$username."'' union all SELECT userName from tnega_student WHERE userName = '".$username."' "; 
    $result = mysqli_query($con, $sql);
    $row = mysqli_num_rows($result);

    if ($row)
    {
        // user is already existed
        $response["success"] = false;
        $response["message"] = "Username already existed";
        echo json_encode($response);
    }
    else
    {
        // user is not there
        $password = base64_encode($password);

        $signup_query = "INSERT INTO tnega_user(userName,password,user_type) VALUES ('$userName','$password','$user_type')";

        $signup_query = mysqli_query($con,$signup_query);

        if($signup_query)
        {
          $response["success"] = true;
          $response["message"]="Registration Successfull";
          echo json_encode($response);
        }
        else
        {
            $response["success"] = false;
            $response["message"] = "User already existed";
            echo json_encode($response);
        }
    }
?>
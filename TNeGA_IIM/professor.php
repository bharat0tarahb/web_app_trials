<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if($request_method == 'POST')
    {
        $professor_own_id = $data['professor_own_id'];
        $username = $data['username'];
        $password = $data['password'];
        $name = $data['name'];

        $sql = "SELECT userName from tnega_user WHERE userName = '".$username."' union all SELECT userName from tnega_professor WHERE userName = '".$username."' union all SELECT userName from tnega_student WHERE userName = '".$username."' ";
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
            $password = base64_encode($password);
            
            $sql = "INSERT INTO tnega_professor(professor_own_id,username,password,user_type,name) VALUES ('$professor_own_id','$username','$password','professor','$name')";
            $insert = mysqli_query($con,$sql);

            if($insert)
            {
              $response["success"] = true;
              $response["message"]="Professor inserted";
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

        $proferssor_id = $_GET["professorID"];

        $sql = "SELECT * FROM tnega_professor where id = '$proferssor_id' ORDER BY tnega_professor.id ASC LIMIT 1";
        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        if($noofrows > 0)
        {
            while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
            { 
                $response["success"] = true;
                $response["id"] = $row['id'];
                $response["professor_own_id"] = $row['professor_own_id'];
                $response["username"] = $row['username'];
                $response["password"] = $row['password'];
                $response["user_type"] = $row['user_type'];
                $response["name"] = $row['name'];
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
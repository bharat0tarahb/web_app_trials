<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];
   
    if($request_method == 'POST')
    {
        $date = $data['date'];
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $student_id = $data['student_id'];
        $professor_id = 0;
        $subject_id = 0;

        $sql = "SELECT date from tnega_attendance WHERE date = '".$date."' and class_id = '".$class_id."' and section_id = '".$section_id."' and student_id = '".$student_id."' and professor_id = '".$professor_id."' "; 
        $result = mysqli_query($con, $sql);
        $row = mysqli_num_rows($result);

        if ($row)
        {
            $sql_delete = "DELETE FROM tnega_attendance WHERE date = '".$date."' and class_id = '".$class_id."' and section_id = '".$section_id."' and student_id = '".$student_id."' and professor_id = '".$professor_id."'";
            $delete = mysqli_query($con,$sql_delete);

            if($delete)
            {
              $response["success"] = true;
                $response["message"] = "Absent Marked";
              echo json_encode($response);
            }
            else
            {
                $response["success"] = false;
                $response["message"] = "Try again later";
                echo json_encode($response);
            }
        }
        else
        {
            $sql = "INSERT INTO tnega_attendance(date,class_id,section_id,student_id,professor_id,subject_id) VALUES ('$date','$class_id','$section_id','$student_id','$professor_id','$subject_id')";
            $insert = mysqli_query($con,$sql);
            if($insert)
            {
              $response["success"] = true;
              $response["message"]="Attendance taken";
              echo json_encode($response);
            }
            else
            {
                $response["success"] = false;
                $response["message"] = "Try again later";
                echo json_encode($response);
            }
        }
    }
?> 
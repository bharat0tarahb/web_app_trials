<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    if($request_method == 'POST')
    {

        $date = $data['date'];
        $period = $data['period'];
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $professor_id = $data['professor_id'];
        $subject_id = $data['subject_id'];

        $sql = "SELECT date from tnega_attendance WHERE date = '".$date."' and period = '".$period."' and class_id = '".$class_id."' and section_id = '".$section_id."' and professor_id = '".$professor_id."' "; 
        $result = mysqli_query($con, $sql);
        $row = mysqli_num_rows($result);

        if ($row)
        {
            $sql = "UPDATE tnega_attendance SET conform_status = 1 where date = '".$date."' and period = '".$period."' and class_id = '".$class_id."' and section_id = '".$section_id."' and professor_id = '".$professor_id."'";
            $result = mysqli_query($con,$sql);

            if($result)
            {
                
                $response["success"] = true;
                $response["message"] = "success";
            }
            else
            { 
                $response["success"] = false;
                $response["message"] = "Try again";
            }
        }
        else
        {

            $sql = "INSERT INTO tnega_attendance(date,period,class_id,section_id,professor_id,subject_id,conform_status) VALUES ('$date','$period','$class_id','$section_id','$professor_id','$subject_id',1)";
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
    else
    {
        $attendanceId = $_GET['attendanceId'];
        
        $sql = "SELECT tnega_attendance.*,tnega_class.*,tnega_section.name as year FROM tnega_attendance 
            inner join tnega_class on tnega_attendance.class_id = tnega_class.id 
            inner join tnega_section on tnega_attendance.section_id = tnega_section.id
            where tnega_attendance.id = '$attendanceId' limit 1";

        $result = mysqli_query($con,$sql);
        $noofrows = mysqli_num_rows($result);

        if($noofrows > 0)
        {
            while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
            { 
                $response["success"] = true;
                $response["degree"] = $row['degree'];
                $response["category"] = $row['category'];
                $response["year"] = $row['year'];
                // $response["subject"] = $row['subject'];
                // $response["subjectId"] = $row['subjectId'];
                // $response["professorName"] = $row['professorName'];
            }
        }
        else
        { 
            $response["success"] = false;
            $response["message"] = "No data";
        }

    }
    echo json_encode($response);
?>
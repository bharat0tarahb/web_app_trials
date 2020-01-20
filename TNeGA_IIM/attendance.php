<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];
   
    if($request_method == 'POST')
    {
        //Take Attendace using socket

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $student_rollNumber = $data['roll_number'];
        $image_path = $data['image_path'];

        $sql_stuId = "SELECT * from tnega_student WHERE rollNumber = '".$student_rollNumber."'";
        $result_stuId = mysqli_query($con, $sql_stuId);
        $row_stuId = mysqli_num_rows($result_stuId);

        if ($row_stuId)
        {
            $row_value = mysqli_fetch_assoc($result_stuId);
            $student_id = $row_value['id'];
            $class_id = $row_value['class_id'];
            $section_id = $row_value['section_id'];

            $sql = "SELECT date from tnega_attendance WHERE date = '".$date."' and class_id = '".$class_id."' and section_id = '".$section_id."' and student_id = '".$student_id."' "; 
            $result = mysqli_query($con, $sql);
            $row = mysqli_num_rows($result);

            if ($row)
            {   
                $sql_img = "INSERT INTO tnega_images(image_path,student_id,datapool,status) VALUES ('$image_path','$student_id',2,0)";
                $img_insert = mysqli_query($con,$sql_img);

                $sql_lastseen = "UPDATE tnega_attendance SET updated_date = NOW() WHERE  date = '".$date."' and class_id = '".$class_id."' and section_id = '".$section_id."' and student_id = '".$student_id."' ";
                $updated = mysqli_query($con,$sql_lastseen);

                // print_r($sql_lastseen); exit;

                $response["success"] = true;
                $response["message"] = "Attendance already taken";
                echo json_encode($response);
            }
            else
            {
                $sql = "INSERT INTO tnega_attendance(date,period,class_id,section_id,student_id,professor_id,subject_id) VALUES ('$date',0,'$class_id','$section_id','$student_id',0,0)";
                $insert = mysqli_query($con,$sql);

                if($insert)
                {

                    $sql_img = "INSERT INTO tnega_images(image_path,student_id,datapool,status) VALUES ('$image_path','$student_id',2,0)";
                    $img_insert = mysqli_query($con,$sql_img);

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
            $response["success"] = false;
            $response["message"] = "User id not found";
            echo json_encode($response);
        }

    }
    else
    {
        //View attendance by professor or student
        $date = $_GET["date"];
        $professorId = isset($_GET['professorId']) ? $_GET['professorId'] : '';
        $studentId = isset($_GET['studentId']) ? $_GET['studentId'] : '';

        if($professorId != "")
        {
            $sql = "SELECT tnega_attendance.id as attendanceId,tnega_attendance.*,tnega_class.*,tnega_section.name as year FROM tnega_attendance 
                inner join tnega_class on tnega_attendance.class_id = tnega_class.id 
                inner join tnega_section on tnega_attendance.section_id = tnega_section.id 
                where tnega_attendance.date = '$date' GROUP BY `tnega_attendance`.`class_id` ORDER BY tnega_attendance.class_id ASC";

            // echo "<pre>"; print_r($sql); exit;

            $result = mysqli_query($con,$sql);
            $noofrows = mysqli_num_rows($result);
            
            $response["presentDetails"] = array();
            if($noofrows > 0)
            {

                while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
                { 
                    
                    $sql1_percent = "SELECT ( (select COUNT( DISTINCT `tnega_attendance`.`student_id`) as total_presentise from tnega_attendance WHERE class_id = ".$row['class_id']." and section_id = ".$row['section_id']." and date = '$date') * 100.0  / (select count(*) from tnega_student WHERE class_id = ".$row['class_id']." and section_id = ".$row['section_id'].") ) as classes_percent from tnega_attendance";

                    $result_percent = mysqli_query($con, $sql1_percent);
                    $percentage = mysqli_fetch_assoc($result_percent);

                    array_push($response["presentDetails"], round($percentage['classes_percent']));
                }
            }

            mysqli_data_seek( $result, 0 );
        }
        else
        {
            $sql = "SELECT tnega_attendance.*,tnega_class.*,tnega_section.name as year,tnega_subject.subject_name as subject,tnega_professor.name as professorName FROM tnega_attendance
            inner join tnega_class on tnega_attendance.class_id = tnega_class.id
            inner join tnega_section on tnega_attendance.section_id = tnega_section.id
            left join tnega_subject on tnega_attendance.subject_id = tnega_subject.id
            inner join tnega_professor on tnega_attendance.professor_id = tnega_professor.id
            where tnega_attendance.date = '$date' and student_id = '$studentId' ORDER BY tnega_attendance.id ASC";


            $result = mysqli_query($con,$sql);
            $noofrows = mysqli_num_rows($result);

            /******************** Now calendar view only ***************************/

            $sql_date = "SELECT * FROM `tnega_attendance`  GROUP BY date ORDER BY `tnega_attendance`.`date` ASC";
            $result_date = mysqli_query($con, $sql_date);

            $response["calendarEvents"] = array();
            while ($row_date = mysqli_fetch_array($result_date,MYSQLI_ASSOC))
            { 
                $sql1_month = "SELECT * FROM tnega_attendance where student_id = '$studentId' and date = '".$row_date['date']."' ORDER BY tnega_attendance.id ASC";
                $result1 = mysqli_query($con,$sql1_month);
                $noofrow1 = mysqli_num_rows($result1);

                if($noofrow1 == 0)
                {
                    $array['title'] = 'Absent';
                    $array['start'] =  $row_date['date'];
                    $array['color'] =  '#f44336';
                    array_push($response["calendarEvents"], $array);
                }
                else
                {
                    $array['title'] = 'Present';
                    $array['start'] =  $row_date['date'];
                    $array['color'] =  '#62b866';
                    array_push($response["calendarEvents"], $array);
                }
            }

            /**********************************************************************/
        }

        if($noofrows > 0)
        {
            $response["attendance"] = array();
            while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
            { 
                $response["success"] = true;
                $response["date"] = $date;
                array_push($response["attendance"], $row);
            }
        }
        else
        { 
            $response["success"] = false;
            $response["message"] = "No data";
        }
        echo json_encode($response);
    }
?>

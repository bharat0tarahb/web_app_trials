<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);
    $request_method = $_SERVER["REQUEST_METHOD"];

    $class_id = $_GET["classId"];
    $section_id = $_GET["sectionId"];
    $date = $_GET["date"];
    // $period = $_GET["period"];
    // $detail = $_GET["detail"];

    $sql_section="SELECT tnega_class.id as class_id,tnega_class.degree,tnega_class.category,tnega_section.id as section_id,tnega_section.name as section_name FROM tnega_section inner join tnega_class on tnega_section.class_id = tnega_class.id where tnega_class.id = '$class_id' ORDER BY tnega_class.id ASC";

    // echo "<pre>"; print_r($sql_section); exit;

    $result_section = mysqli_query($con, $sql_section);
    $noofrows_section = mysqli_num_rows($result_section);

    if($noofrows_section > 0)
    {
        $response["sections"] = array();
        while ($row = mysqli_fetch_array($result_section,MYSQLI_ASSOC))
        { 
            $sql_attendance = "SELECT tnega_student.id as studentId, tnega_student.*, tnega_attendance.*, if(tnega_attendance.date is null, 'Absent', 'Present') as status
                FROM tnega_student
                Left JOIN tnega_attendance
                ON tnega_attendance.student_id = tnega_student.id and (tnega_attendance.date = '$date' OR tnega_attendance.date IS NULL)
                WHERE tnega_student.class_id = '$class_id' AND tnega_student.section_id = ".$row['section_id']."
                ORDER BY tnega_student.id";

                // echo "<pre>"; print_r($sql_attendance); exit;

                $result_attendance = mysqli_query($con, $sql_attendance);
                $noofrows = mysqli_num_rows($result_attendance);

                if($noofrows > 0)
                {
                    $attendancevalue["attendance"] = array();
                    while ($attendance_data = mysqli_fetch_array($result_attendance,MYSQLI_ASSOC))
                    {
                        array_push($attendancevalue["attendance"],$attendance_data);
                    }
                } else {
                    $attendancevalue["attendance"] = array();
                }

            $row['attendanceData'] = $attendancevalue["attendance"];
            array_push($response["sections"],$row);
        }
    }


    $sql_conform = "SELECT DISTINCT `tnega_attendance`.`conform_status` from tnega_attendance WHERE class_id = '$class_id' and section_id = '$section_id' and date = '$date' ";

    $result_conform = mysqli_query($con,$sql_conform);
    $row_conform = mysqli_fetch_assoc($result_conform);


    if($row_conform != '')
    {
        $response["conform_status"] = $row_conform['conform_status'];
    } else {
        $response["conform_status"] = 0;
    }

    $sql1_percent = "SELECT ( (select COUNT( DISTINCT `tnega_attendance`.`student_id`) as total_presentise from tnega_attendance WHERE class_id = '$class_id' and date = '$date') * 100.0  / (select count(*) from tnega_student WHERE class_id = '$class_id') ) as classes_percent, (select COUNT( DISTINCT `tnega_attendance`.`student_id`) as total_presentise from tnega_attendance WHERE class_id = '$class_id' and date = '$date') as total_present,((select count(*) from tnega_student WHERE class_id = '$class_id') - (select COUNT( DISTINCT `tnega_attendance`.`student_id`) as total_presentise from tnega_attendance WHERE class_id = '$class_id' and date = '$date')) as total_absent from tnega_attendance";

    // echo "<pre>"; print_r($sql1_percent); exit;

    $result1 = mysqli_query($con,$sql1_percent);
    $row1 = mysqli_fetch_assoc($result1);

    $response["pieChartData"] = array();
    array_push($response["pieChartData"],round($row1['classes_percent']), 100 - round($row1['classes_percent']));

    $response["attendanceCount"] = array();
    array_push($response["attendanceCount"] , round($row1['total_present']), round($row1['total_absent']));

    echo json_encode($response);

?>

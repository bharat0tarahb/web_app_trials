<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);

    $class_id = $_GET["classId"];

    $sql_section="SELECT tnega_class.id as class_id,tnega_class.degree,tnega_class.category,tnega_section.id as section_id,tnega_section.name as section_name FROM tnega_section inner join tnega_class on tnega_section.class_id = tnega_class.id where tnega_class.id = '$class_id' ORDER BY tnega_class.id ASC";

    $result_section = mysqli_query($con, $sql_section);
    $noofrows_section = mysqli_num_rows($result_section);

    if($noofrows_section > 0)
    {
        $response["sections"] = array();
        while ($row = mysqli_fetch_array($result_section,MYSQLI_ASSOC))
        { 
            $section_id = $row['section_id'];

            $sql_date = "SELECT DISTINCT date FROM tnega_attendance";
            $result_date = mysqli_query($con, $sql_date);
            $noofrows_date = mysqli_num_rows($result_date);

            if($noofrows_date > 0)
            {
                while ($row_date = mysqli_fetch_array($result_date,MYSQLI_ASSOC))
                {
                    $excel_data['section_name'] = $row['section_name'];
                    $excel_data['date'] = $row_date['date'];

                    $sql_attendance = "SELECT tnega_student.id as studentId, tnega_student.*, tnega_attendance.*, if(tnega_attendance.date is null, 'Absent', 'Present') as status
                        FROM tnega_student
                        Left JOIN tnega_attendance
                        ON tnega_attendance.student_id = tnega_student.id and (tnega_attendance.date = '".$row_date['date']."' OR tnega_attendance.date IS NULL)
                        WHERE tnega_student.class_id = '$class_id' AND tnega_student.section_id = ".$section_id."
                        ORDER BY tnega_student.id";
                        
                        $result_attendance = mysqli_query($con, $sql_attendance);
                        $noofrows = mysqli_num_rows($result_attendance);

                        if($noofrows > 0)
                        {
                            $attendancevalue["attendance"] = array();
                            while ($attendance_data = mysqli_fetch_array($result_attendance,MYSQLI_ASSOC))
                            {
                                $excel_data['staffid'] = $attendance_data['rollNumber'];
                                $excel_data['name'] = $attendance_data['name'];
                                $excel_data['seen_at'] = $attendance_data['created_date'];
                                $excel_data['last_seen'] = $attendance_data['updated_date'];
                                $excel_data['status'] = $attendance_data['status'];
                            }

                            array_push($response["sections"],$excel_data);
                        }
                }
            }
        }
    }

    echo json_encode($response);
?>
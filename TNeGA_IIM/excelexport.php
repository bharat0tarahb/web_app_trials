<?php 
    include('database_config.php');
    $data = json_decode(file_get_contents("php://input"),true);

    $class_id = $_GET["classId"];
    $selectedmonth = $_GET["selectedmonth"];

    $sql_section="SELECT tnega_class.id as class_id,tnega_class.degree,tnega_class.category,tnega_section.id as section_id,tnega_section.name as section_name FROM tnega_section inner join tnega_class on tnega_section.class_id = tnega_class.id where tnega_class.id = '$class_id' ORDER BY tnega_class.id ASC";

    $result_section = mysqli_query($con, $sql_section);
    $noofrows_section = mysqli_num_rows($result_section);

    if($noofrows_section > 0)
    {
        $response["sections"] = array();
        while ($row = mysqli_fetch_array($result_section,MYSQLI_ASSOC))
        { 
            $section_id = $row['section_id'];

            $sql_date = "SELECT * FROM tnega_student WHERE tnega_student.class_id = '$class_id' AND tnega_student.section_id = ".$section_id." ORDER BY tnega_student.id";

            $result_date = mysqli_query($con, $sql_date);
            $noofrows_date = mysqli_num_rows($result_date);

            if($noofrows_date > 0)
            {
                while ($row_date = mysqli_fetch_array($result_date,MYSQLI_ASSOC))
                {
                    $excel_data['Studuent Name'] = $row_date['userName'];

                    if($selectedmonth == 0)
                    {
                        $sql_attendance = "SELECT DISTINCT tnega_attendance.date, if(tnega_attendance.student_id = ".$row_date['id'].", tnega_attendance.student_id, Null) as studentId,if(tnega_attendance.student_id = ".$row_date['id'].", 'Present', 'Absent') as status FROM tnega_attendance ORDER BY `tnega_attendance`.`date` ASC";

                    }
                    else
                    {
                        $sql_attendance = "SELECT DISTINCT tnega_attendance.date, if(tnega_attendance.student_id = ".$row_date['id'].", tnega_attendance.student_id, Null) as studentId,if(tnega_attendance.student_id = ".$row_date['id'].", 'Present', 'Absent') as status FROM tnega_attendance WHERE MONTH(tnega_attendance.date) = ".$selectedmonth." ORDER BY `tnega_attendance`.`date` ASC";

                    }

                    $result_attendance = mysqli_query($con, $sql_attendance);
                    $noofrows = mysqli_num_rows($result_attendance);

                    if($noofrows > 0)
                    {
                        $attendancevalue["attendance"] = array();
                        $attendancevalue["data"] = array();
                        $i = 0;
                        $j = 0;

                        while ($attendance_data = mysqli_fetch_array($result_attendance, MYSQLI_ASSOC))
                        {

                            if(!in_array($attendance_data["date"], $attendancevalue["data"]))
                            {
                                $i++;                              
                                array_push($attendancevalue["data"], $attendance_data["date"]);
                            }

                            if($attendance_data['status'] == 'Present')
                            {
                                $j++;
                            }

                            $excel_data[$attendance_data["date"]] = $attendance_data['status'];
                        }

                        $excel_data['TotalDays'] = $i;
                        $excel_data['Total Present Days'] = $j;
                        $excel_data['Percentage'] = round( ($j * 100) / $i ) ;

                        array_push($response["sections"],$excel_data);
                    }
                }
            }
        }
    }

    echo json_encode($response);
?>
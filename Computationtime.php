<?php 
function calculateInteTime($courseUserLogData, $inteTimeMin=60) {
      $startTime = 0;
      $middleTime = 0;
      $endTime = $inteTimeMin * 60;
      $totalTime = 0;

      foreach($courseUserLogData as $courseUserOneLogKey => $courseUserOneLogValue) {
	

            if ( $courseUserOneLogValue['time'] > ($startTime + $endTime) ) {
                  $startTime = $courseUserOneLogValue['time'];
                  $middleTime = $startTime;
            } else {
                  $totalTime = $totalTime + ($courseUserOneLogValue['time'] - $middleTime);
                  $middleTime = $courseUserOneLogValue['time'];
            }
      }
      return round($totalTime/60/60, 2); //hours x.xx
}
function integrate_log($scormfull,$user_id,$setscormid="null") {
    global $DB;

		$selector="";
                $Joins=array();
		$course_module=array();
		if($scormfull->threshold)
		{
                    $course_module = $DB->get_records("course_modules", array("instance"=>$scormfull->threshold, "course"=>$scormfull->course));
		}else{
		    $course_module = $DB->get_records("course_modules", array("instance"=>$setscormid, "course"=>$scormfull->course));

		}

    $Joins_cm = array();
    foreach($course_module as $cm)
        $Joins_cm[] = "l.cmid = '$cm->id'";
    $Joins[] = '(' . implode(' OR ', $Joins_cm) . ')';

                $Joins[] = "l.course = $scormfull->course";
                $Joins[] = "l.userid = $user_id";
                $Joins[] = "l.module = 'scorm'";
                $selector = implode(' AND ', $Joins);
		$order = ' l.time ASC ';
                $limitfrom = '';
                $limitnum = 50000;
                $totalcount = '';

                $courseUserActionLogs = get_logs($selector, null, $order, $limitfrom, $limitnum, $totalcount);

                $courseUserLogData = array();
                foreach ($courseUserActionLogs as $courseUserLog) {
                    $courseUserLogData[$courseUserLog->id] = array(//'userid' => $courseUserLog->userid,
                        'time'   => $courseUserLog->time,
                    );
                }
		return $courseUserLogData;


}

function usersdata($courseID='', $limitfrom='', $limitnum='') {
    global $CFG, $DB;

    $users = array();
    $course = $DB->get_record('course', array('id'=>$courseID));
    $sort='u.id  ASC';
    if ($course->id != SITEID) {
        //$courseusers = get_course_users($course->id, $sort, '', 'u.id, u.firstname, u.lastname, u.idnumber',$limitfrom, $limitnum);
        $context = get_context_instance( CONTEXT_COURSE, $courseID);
        $query = 'select u.id as id, firstname, lastname, picture, imagealt, email from ' . $CFG->prefix . 'role_assignments as a, ' . $CFG->prefix . 'user as u where contextid=' . $context->id . ' and roleid=5 and a.userid=u.id;';

        $courseusers = $DB->get_recordset_sql( $query ); 
    }
    if (count($courseusers) < COURSE_MAX_USERS_PER_DROPDOWN) {
        $showusers = 1;
    }
    if ($showusers) {
        if ($courseusers) {
            foreach ($courseusers as $courseuser) {
                $users[$courseuser->id] = fullname($courseuser, has_capability('moodle/site:viewfullnames', $context));
            }
        }
    }
    return $users;
}
function usersdataall($courseID='', $limitfrom='', $limitnum='') {
    global $CFG, $DB;

    $users = array();
    $course = $DB->get_record('course', array('id'=>$courseID));
    $sort='u.id  ASC';
    if ($course->id != SITEID) {
        //$courseusers = get_course_users($course->id, $sort, '', 'u.id, u.firstname, u.lastname, u.idnumber,u.username',$limitfrom, $limitnum);
        $context = get_context_instance( CONTEXT_COURSE, $courseID);
        $query = 'select u.id as id, firstname, lastname, username, picture, imagealt, email from ' . $CFG->prefix . 'role_assignments as a, ' . $CFG->prefix . 'user as u where contextid=' . $context->id . ' and roleid=5 and a.userid=u.id;';

        $courseusers = $DB->get_recordset_sql( $query );
    }
    if (count($courseusers) < COURSE_MAX_USERS_PER_DROPDOWN) {
        $showusers = 1;
    }
    if ($showusers) {
        if ($courseusers) {
            foreach ($courseusers as $courseuser) {
		$users[$courseuser->id][]=$courseuser->username;
		$users[$courseuser->id][] =fullname($courseuser, has_capability('moodle/site:viewfullnames', $context));
            }
        }
    }
    return $users;
}

function get_quiz_grades($quizID) {
    global $DB;

    $courseActionItem = $DB->get_records_select('quiz_grades', 'quiz='.$quizID);
	$tmp_array=array();
	foreach($courseActionItem as $value)
	{
		$tmp_array[$value->userid]=$value;
	}
    return $tmp_array;
}

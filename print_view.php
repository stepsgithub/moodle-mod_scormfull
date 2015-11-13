<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of scormfull
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage scormfull
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace scormfull with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

require_once($CFG->libdir.'/odslib.class.php');

require_once(dirname(__FILE__).'/Computationtime.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // scormfull instance ID - it should be named as the first character of the module

$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('scormfull', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $scormfull  = $DB->get_record('scormfull', array('id' => $cm->instance), '*', MUST_EXIST);
}else if ($n) {
    $scormfull  = $DB->get_record('scormfull', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $scormfull->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('scormfull', $scormfull->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an scormfull ID');
}

    $quiz       = $DB->get_record('quiz', array("id"=>$scormfull->quid), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (!has_capability('mod/scormfull:manage', $context)) {
    redirect("$CFG->wwwroot/mod/scormfull/user.php?id=$id");
}

    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);
    $filename = 'scormfull'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.ods';
    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);
    $worksheet = array();

    $sheettitle = 'scormfull';
    $worksheet = & $workbook->add_worksheet($sheettitle);


	$struen_name  = get_string('struen_name','scormfull');
	$strthreshold_boo = get_string('threshold_boo','scormfull');
	$Thresholdvalue = get_string('Thresholdvalue','scormfull');
	$scrom_name = get_string('scrom_name','scormfull');
	$starnopass = get_string('starnopass','scormfull');
	$connected_exam = get_string('connected_exam','scormfull');
	$strsetvalue = get_string('strsetvalue','scormfull');
	$cssting = get_string('curriculum_standards_setting','scormfull');

	$hours_of_the_event = get_string('hours_of_the_event','scormfull');
	$through_standard_assessment = get_string('through_standard_assessment','scormfull');
	$scorm_quiz_score = get_string('scorm_quiz_score','scormfull');

	$text_test_pass = get_string('text_test_pass','scormfull');
	$test_scores = get_string('test_scores','scormfull');
	$not_been_exam = get_string('not_been_exam','scormfull');
	$scorm_quiz_score_qualifications=get_string('scorm_quiz_score_qualifications','scormfull');
	$username_txt=get_string('username_txt','scormfull');

	    $worksheet->set_column(1, 1, 60);
    	$worksheet->write_string(0, 0, $strsetvalue);

	$worksheet->write(8,0,$username_txt,'');
	$worksheet->write(8,1,$struen_name,'');
	$worksheet->write(8,2,$scorm_quiz_score_qualifications,'');
	$worksheet->write(8,3,$test_scores,'');
	$worksheet->write(8,4,$text_test_pass,'');

	$myxls =& $worksheet;
	if(! $scormfull->threshold)
	{

		 if (! $scormdata = $DB->get_records("scorm", array("course"=>$course->id))) {
            error("The scorm with id $cm->instance corresponding to this scormfull $id is missing");
        }
		 $worksheet->write_string(1, 0, $course->fullname.get_string('Courses_all_scrom_courses','scormfull'));
		 $worksheet->write_string(2, 0,"$connected_exam:");
		 $worksheet->write_string(2, 1,$quiz->name);
		 $worksheet->write_string(3, 0, "$Thresholdvalue:");
  		 $worksheet->write_string(3, 1, $scormfull->thresholdvalue."%");

                $report_data;
                if($scormfull->report_add==0)
                {
                        $report_data=get_string('Not_joined_the_settings','scormfull');
                }else{
                        $report_data="{$scormfull->report_value}".get_string('hours_txt','scormfull');
                }

		$worksheet->write_string(4, 0, "$cssting:");
		$worksheet->write_string(4, 1, $report_data);
		$worksheet->write_string(5, 0, "$scorm_quiz_score:");
		$worksheet->write_string(5, 1, $scormfull->quid_score);
		


		$sql="SELECT count(*) as allcount FROM {$CFG->prefix}scorm_scoes ss ,{$CFG->prefix}scorm sc WHERE ss.scorm=sc.id And sc.course=$course->id AND ss.scormtype = 'sco'";	
		 $all_count=$DB->get_records_sql($sql);

		$user_grades_array=(get_quiz_grades($quiz->id));

		foreach($all_count as $value)
		{
			$all_count=$value->allcount;
		}
			
		$get_user=usersdataall($course->id,($perpage*$page),$perpage);
		$usercount=count(usersdata($course->id));
		$row=9;
		foreach($get_user as $key  =>  $value2)
		{
		$col = 0;
		$sum=0;
		$calculation_results_sum=0;	
		foreach($scormdata as $value)
		{
			$sql="SELECT st.id,st.userid,st.scormid,st.scoid,st.attempt,st.element,st.value,st.timemodified
                        FROM {$CFG->prefix}scorm_scoes_track st
                        WHERE st.scormid =$value->id and (value='completed' or value='suspend')  and st.userid=$key  group by st.scoid";
			$getcount=$DB->get_records_sql($sql);	
			if(!empty($getcount))
			{
				 $sum=$sum+count($getcount);
			}
			$calculation_results=calculateInteTime(integrate_log($scormfull,$key,$value->id));
			$calculation_results_sum=$calculation_results_sum+$calculation_results;


		}
		
			$getvalue=round($sum/$all_count, 2);
		
                $setjudg= get_string('starnopass','scormfull');
                if($getvalue>=((int)$scormfull->thresholdvalue/100))
		{
                       $setjudg= get_string('strpass','scormfull');
			$setjudg=!$scormfull->report_add ? get_string('strpass','scormfull') : ($calculation_results_sum>=$scormfull->report_value ? get_string('strpass','scormfull')   : get_string('starnopass','scormfull'));
			
		}
		
		$myxls->write_string($row, $col,$value2[0]);
                $col++;
		$myxls->write_string($row, $col,$value2[1]);
		$col++;
		$myxls->write_string($row, $col,$setjudg);
		$col++;
                $myxls->write_string($row, $col,(!empty($user_grades_array[$key])) ? $user_grades_array[$key]->grade : $not_been_exam);
		$col++;
                $myxls->write_string($row, $col,(!empty($user_grades_array[$key])) ? (($user_grades_array[$key]->grade) >= ($scormfull->quid_score)) ? get_string('strpass','scormfull') : get_string('starnopass','scormfull')  : "-");

		$row++;

		}

	}else
	{
		if (! $scorm = $DB->get_record("scorm", array("id"=>$scormfull->threshold))) {
            error(get_string('Courses_have_been_changed','scormfull'));
        }
		 $worksheet->write_string(1, 0,get_string('class_name_txt','scormfull').":");
		 $worksheet->write_string(1, 1,$course->fullname);
		 $worksheet->write_string(2, 0,"scrom_name:");
		 $worksheet->write_string(2, 1,$scorm->name);
                 $worksheet->write_string(3, 0,"$connected_exam:");
                 $worksheet->write_string(3, 1,$quiz->name);
                 $worksheet->write_string(4, 0, "$Thresholdvalue:");
                 $worksheet->write_string(4, 1, $scormfull->thresholdvalue."%");
	

                $report_data;
                if($scormfull->report_add==0)
                {
                        $report_data=get_string('Not_joined_the_settings','scormfull');
                }else{
                        $report_data="{$scormfull->report_value}".get_string('hours_txt','scormfull');
                }
                $worksheet->write_string(5, 0, "$cssting:");
                $worksheet->write_string(5, 1, $report_data);
                $worksheet->write_string(6, 0, "$scorm_quiz_score:");
                $worksheet->write_string(6, 1, $scormfull->quid_score);

		 $table2->data[] = array (("scrom_name : ".$scorm->name));


		    $sql="SELECT COUNT(*) as count 
                FROM  {$CFG->prefix}scorm_scoes sss
               	WHERE sss.scorm = $scormfull->threshold
		AND sss.scormtype = 'sco'";
                $sco_count=0;
                $get_countarray=$DB->get_records_sql($sql);
                foreach($get_countarray as $value)
                {
                       $sco_count=$value->count;
                }
		$user_grades_array=(get_quiz_grades($quiz->id));
		$get_user=usersdataall($course->id,($perpage*$page),$perpage);
                $usercount=count(usersdata($course->id));
		$row=9;
		foreach($get_user as $key => $value)
		{
		$col=0;
		$getvalue=0;

		 $sql="SELECT st.id,st.userid,st.scormid,st.scoid,st.attempt,st.element,st.value,st.timemodified
                        FROM {$CFG->prefix}scorm_scoes_track st
                        WHERE st.scormid =$scormfull->threshold and (value='completed' or value='suspend')  and st.userid=$key  group by st.scoid";	
                $getqudata=$DB->get_records_sql($sql);

		$calculation_results=calculateInteTime(integrate_log($scormfull,$key));
	
		
		if(!empty($getqudata))
                {
		 	$getvalue=round(count($getqudata)/$sco_count, 2);
		}
		$setjudg= get_string('starnopass','scormfull');
		if($getvalue>=((int)$scormfull->thresholdvalue/100))
		{
			$setjudg= get_string('strpass','scormfull');
			$setjudg=!$scormfull->report_add ? get_string('strpass','scormfull') : ($calculation_results>=$scormfull->report_value ? get_string('strpass','scormfull')   : get_string('starnopass','scormfull'));

		}
		$myxls->write_string($row, $col,$value[0]);
                $col++;
                $myxls->write_string($row, $col,$value[1]);
                $col++;
                $myxls->write_string($row, $col,$setjudg);
                $col++;
                $myxls->write_string($row, $col,(!empty($user_grades_array[$key])) ? $user_grades_array[$key]->grade : $not_been_exam);
                $col++;
                $myxls->write_string($row, $col,(!empty($user_grades_array[$key])) ? (($user_grades_array[$key]->grade) >= ($scormfull->quid_score)) ? get_string('strpass','scormfull') : get_string('starnopass','scormfull')  : "-");

                $row++;
		}
		}
	$workbook->close();

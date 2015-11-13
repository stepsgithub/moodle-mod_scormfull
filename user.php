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

require_once(dirname(__FILE__).'/Computationtime.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // scormfull instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('scormfull', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $scormfull  = $DB->get_record('scormfull', array('id' => $cm->instance), '*', MUST_EXIST);
    $quiz       = $DB->get_record('quiz', array("id"=>$scormfull->quid), '*', MUST_EXIST);
}else if ($n) {
    $quiz       = $DB->get_record('quiz', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $quiz->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('quiz', $quiz->id, $course->id, false, MUST_EXIST);

        $sql="SELECT * FROM {$CFG->prefix}scormfull WHERE course=$course->id AND quid= $quiz->id";
        $scormfull=$DB->get_records_sql($sql);
        foreach($scormfull as $value)
        {
                $scormfull=$value;
        }
} else {
    error('You must specify a course_module ID or an scormfull ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, "scormfull", "user", "user.php?id=$cm->id", "$scormfull->id");

/// Print the page header

$PAGE->set_url('/mod/scormfull/user.php', array('id' => $cm->id));
$PAGE->set_title(format_string($scormfull->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

/// Finish the page
echo $OUTPUT->heading($scormfull->name);

if ($scormfull->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('scormfull', $scormfull, $cm->id), 'generalbox mod_introbox', 'scormfullintro');
}

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

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($course->format == 'weeks') {
    $table->head  = array ($struen_name, $strthreshold_boo,$Thresholdvalue,$hours_of_the_event,$through_standard_assessment);
    $table->align = array ('center', 'left','left', 'left','left', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($struen_name, $strthreshold_boo,$Thresholdvalue,$hours_of_the_event,$through_standard_assessment);
    $table->align = array ('center', 'left', 'left', 'left','left', 'left');
} else {
    $table->head  = array ($struen_name, $strthreshold_boo,$Thresholdvalue,$hours_of_the_event,$through_standard_assessment);
    $table->align = array ('left', 'left', 'left', 'left','left', 'left');
}

$table2 = new html_table();
$table2->attributes['class'] = 'generaltable mod_index';

$table3 = new html_table();
$table3->attributes['class'] = 'generaltable mod_index';

	$table3->head  = array ($struen_name, $strthreshold_boo,$test_scores,$text_test_pass);
	$table3->align = array ('center', 'left','left', 'left');


	$table2->head  = array ($strsetvalue);
        $table2->align = array ('center', 'left');
	if(! $scormfull->threshold)
	{
		$table2->data[] = array (($course->fullname.get_string('Courses_all_scrom_courses','scormfull')));

		 if (! $scormdata = $DB->get_records("scorm", array("course"=>$course->id))) {
            error("The scorm with id $cm->instance corresponding to this scormfull $id is missing");
        }

		$sql="SELECT count(*) as allcount FROM {$CFG->prefix}scorm_scoes ss ,{$CFG->prefix}scorm sc WHERE ss.scorm=sc.id And sc.course=$course->id AND ss.scormtype = 'sco'";	
		 $all_count=$DB->get_records_sql($sql);

		$user_grades_array=(get_quiz_grades($quiz->id));

		foreach($all_count as $value)
		{
			$all_count=$value->allcount;
		}
		
		
		$sum=0;
		$calculation_results_sum=0;	
		foreach($scormdata as $value)
		{
			$sql="SELECT st.id,st.userid,st.scormid,st.scoid,st.attempt,st.element,st.value,st.timemodified
                        FROM {$CFG->prefix}scorm_scoes_track st
                        WHERE st.scormid =$value->id and (value='completed' or value='suspend')  and st.userid=$USER->id  group by st.scoid";
			$getcount=$DB->get_records_sql($sql);	
			if(!empty($getcount))
			{
				 $sum=$sum+count($getcount);
			}
			$calculation_results=calculateInteTime(integrate_log($scormfull,$USER->id,$value->id));
                        $calculation_results_sum=$calculation_results_sum+$calculation_results;


		}
		
			$getvalue=round($sum/$all_count, 2);

		if($getvalue>=1)
		{
		   $getvalue=1;
		}
		
                $setjudg= get_string('starnopass','scormfull');
		$threshold_judg= get_string('starnopass','scormfull');
                if($getvalue>=((int)$scormfull->thresholdvalue/100))
		{
                        $setjudg= get_string('strpass','scormfull');
			$threshold_judg=!$scormfull->report_add ? get_string('strpass','scormfull') : ($calculation_results_sum>=$scormfull->report_value ? get_string('strpass','scormfull')   : get_string('starnopass','scormfull'));
		}
		
		$table->data[] = array (("<a href=\"$CFG->wwwroot/user/view.php?id={$USER->id}&amp;course=$course->id\">"."{$USER->lastname}{$USER->firstname}</a><br />"),$setjudg,(($getvalue*100)."%"),$calculation_results_sum,!$scormfull->report_add ? "-" : ($calculation_results_sum>=$scormfull->report_value ? get_string('strpass','scormfull'): get_string('starnopass','scormfull')));
		
		$table3->data[] = array (("<a href=\"$CFG->wwwroot/user/view.php?id={$USER->id}&amp;course=$course->id\">"."{$USER->lastname}{$USER->firstname}</a><br />"),$threshold_judg,(!empty($user_grades_array[$USER->id])) ? $user_grades_array[$USER->id]->grade : $not_been_exam,(!empty($user_grades_array[$USER->id])) ? (($user_grades_array[$USER->id]->grade) >= ($scormfull->quid_score)) ? get_string('strpass','scormfull') : get_string('starnopass','scormfull')  : "-");




		$table2->data[] = array (("$connected_exam : ".$quiz->name));
                $table2->data[] = array (("$Thresholdvalue : ".$scormfull->thresholdvalue."%"));
	

	}else
	{
		if (! $scorm = $DB->get_record("scorm", array("id"=>$scormfull->threshold))) {
            error("The scorm with id $cm->instance corresponding to this scormfull $scormfull->threshold is missing");
        }

		 $table2->data[] = array (("$scrom_name : ".$scorm->name));


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


		 $sql="SELECT st.id,st.userid,st.scormid,st.scoid,st.attempt,st.element,st.value,st.timemodified
                        FROM {$CFG->prefix}scorm_scoes_track st
                        WHERE st.scormid =$scormfull->threshold and (value='completed' or value='suspend')  and st.userid=$USER->id  group by st.scoid";
                $getqudata=$DB->get_records_sql($sql);
		$user_grades_array=(get_quiz_grades($quiz->id));
		
		 $calculation_results=calculateInteTime(integrate_log($scormfull,$USER->id));

		if(!empty($getqudata))
                {
		 $getvalue=round(count($getqudata)/$sco_count, 2);
		}

		if($getvalue>=1)
		{
		   $getvalue=1;
		}
		$setjudg= get_string('starnopass','scormfull');
		$threshold_judg= get_string('starnopass','scormfull');
		if($getvalue>=((int)$scormfull->thresholdvalue/100))
		{
			$setjudg= get_string('strpass','scormfull');
			$threshold_judg=!$scormfull->report_add ? get_string('strpass','scormfull') : ($calculation_results>=$scormfull->report_value ? get_string('strpass','scormfull')   : get_string('starnopass','scormfull'));
		}

//			 $table->data[] = array (($USER->lastname.$USER->firstname),$setjudg,(($getvalue*100)."%"));
		$table->data[] = array (("<a href=\"$CFG->wwwroot/user/view.php?id={$USER->id}&amp;course=$course->id\">"."{$USER->lastname}{$USER->firstname}</a><br />"),$setjudg,(($getvalue*100)."%"), $calculation_results,!$scormfull->report_add ? "-" :($calculation_results>=$scormfull->report_value ? get_string('strpass','scormfull'): get_string('starnopass','scormfull')));

		 $table3->data[] = array (("<a href=\"$CFG->wwwroot/user/view.php?id={$USER->id}&amp;course=$course->id\">"."{$USER->lastname}{$USER->firstname}</a><br />"),$threshold_judg,(!empty($user_grades_array[$USER->id])) ? $user_grades_array[$USER->id]->grade : $not_been_exam,(!empty($user_grades_array[$USER->id])) ? (($user_grades_array[$USER->id]->grade) >= ($scormfull->quid_score)) ? get_string('strpass','scormfull') : get_string('starnopass','scormfull')  : "-");


		$table2->data[] = array (("$connected_exam : ".$quiz->name));
		$table2->data[] = array (("$Thresholdvalue : ".$scormfull->thresholdvalue."%"));
	}

	  if(!$scormfull->report_add)
        {
                $report_data=get_string('Not_joined_the_settings','scormfull');
        }else{
                $report_data="{$scormfull->report_value}".get_string('hours_txt','scormfull');
        }
        $table2->data[] = array (("$cssting : ".$report_data));
	$table2->data[] = array (("$scorm_quiz_score : ".$scormfull->quid_score));


        echo html_writer::table($table2);
        echo "<p></p>";
        echo html_writer::table($table);
        echo "<p></p>";
        echo html_writer::table($table3);

// Finish the page
echo $OUTPUT->footer();

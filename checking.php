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

require_once("Computationtime.php");

	function getJudg($coursedata,$userdata,$quiz_id) {
        global $CFG, $DB;
	
		$sql="SELECT * FROM {$CFG->prefix}scormfull WHERE course=$coursedata->id AND quid= $quiz_id";
                 $scormfull=$DB->get_records_sql($sql);
	
		if(empty($scormfull))
                {
                        return;
                }

        foreach($scormfull as $value)
        {
                $scormfull=$value;
        }

	if(! $scormfull->threshold)
        {

                 if (! $scormdata = $DB->get_records("scorm", array("course"=>$coursedata->id))) {
            error("The scorm with id $cm->instance corresponding to this scormfull $id is missing");
        }
		 $sql="SELECT count(*) as allcount FROM {$CFG->prefix}scorm_scoes ss ,{$CFG->prefix}scorm sc WHERE ss.scorm=sc.id And sc.course=$coursedata->id AND ss.scormtype = 'sco'";
                 $all_count=$DB->get_records_sql($sql);
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
                        WHERE st.scormid =$value->id and (value='completed' or value='suspend')  and st.userid=$userdata->id  group by st.scoid";
                        $getcount=$DB->get_records_sql($sql);
                        if(!empty($getcount))
                        {
                                 $sum=$sum+count($getcount);
                        }
	               if($scormfull->report_add)
        	        {
                        $calculation_results=calculateInteTime(integrate_log($scormfull,$userdata->id,$value->id));
			$calculation_results_sum=$calculation_results_sum+$calculation_results;

               		 }

                }
		  $getvalue=round($sum/$all_count, 2);

                if($getvalue<((int)$scormfull->thresholdvalue/100))
		{
			redirect("$CFG->wwwroot/mod/scormfull/user.php?n=$quiz_id");
		}
                if($scormfull->report_add)
                {

			if($calculation_results_sum>=$scormfull->report_value)
			{
			}else{
				redirect("$CFG->wwwroot/mod/scormfull/user.php?n=$quiz_id");
			}



                }






}else{
		if (! $scorm = $DB->get_record("scorm", array("id"=>$scormfull->threshold))) {
            error("The scorm with id $cm->instance corresponding to this scormfull $scormfull->threshold is missing");
        }
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
                        WHERE st.scormid =$scormfull->threshold and (value='completed' or value='suspend')  and st.userid=$userdata->id group by st.scoid";
                $getqudata=$DB->get_records_sql($sql);

		 $getvalue=round(count($getqudata)/$sco_count, 2);
                if($getvalue<((int)$scormfull->thresholdvalue/100))
		{
			redirect("$CFG->wwwroot/mod/scormfull/user.php?n=$quiz_id");
		}
		 if($scormfull->report_add)
                 {
                        $calculation_results=calculateInteTime(integrate_log($scormfull,$userdata->id));
                        
			if($calculation_results>$scormfull->report_value)
			{
			}else
			{
				redirect("$CFG->wwwroot/mod/scormfull/user.php?n=$quiz_id");
			}

                 }

}

	
	}



?>

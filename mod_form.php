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
 * The main scormfull configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage scormfull
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_scormfull_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        global $COURSE, $CFG, $DB;
        $update=optional_param('update', 0, PARAM_INT);

        if (!$scorm = $DB->get_records("scorm", array("course"=>$COURSE->id) )) {
                error(get_string('Please_add_scorm','scormfull'));

        }

         $sql="SELECT count(*) as count
                FROM {$CFG->prefix}scormfull sf";

         $emptyscdata=$DB->get_records_sql($sql);
        $emptycount=0;
        foreach($emptyscdata as $value)
        {
                $emptycount=$value->count;
        }
         $array_qu;
        if(!$emptycount)
        {
                $sql="SELECT qu.id,qu.name
                FROM {$CFG->prefix}quiz qu
                WHERE  qu.course =$COURSE->id";

                $getqudata=$DB->get_records_sql($sql);

                if(empty($getqudata))
                {
                        error(get_string('Please_add_quiz','scormfull'));
                }

        }else{
         $sql="SELECT qu.id,qu.name
                FROM {$CFG->prefix}quiz qu,{$CFG->prefix}scormfull sf
                WHERE  qu.course =$COURSE->id
                AND sf.quid!=qu.id
                group by qu.id";

         $getqudata=$DB->get_records_sql($sql);
        if(empty($getqudata))
        {
                if($update)
                {
                        if (! $scormfull = $DB->get_record("scormfull", array("id"=>$this->_instance) )) {
                        }else{
                        $sql="SELECT qu.id,qu.name
                        FROM {$CFG->prefix}quiz qu,{$CFG->prefix}scormfull sf
                        WHERE  qu.course =$COURSE->id
                        AND sf.quid=$scormfull->quid
                        group by qu.id";
                        $getqudata=$DB->get_records_sql($sql);
                        }

                }else
                {
                        error(get_string('Please_add_quiz','scormfull'));
                }

        }else{

                if($update)
                {

                if (! $scormfull = $DB->get_record("scormfull", array("id"=>$this->_instance) )) {
                        }else{

                         $sql="SELECT qu.id,qu.name
                FROM {$CFG->prefix}quiz qu,{$CFG->prefix}scormfull sf
                WHERE  qu.course =$COURSE->id
                AND qu.id= sf.quid
                group by qu.id";
                  $getItself=$DB->get_records_sql($sql);
                  $getqudata=array_merge($getqudata,$getItself);
                }
                }

        }
        }

        foreach($getqudata as $value)
        {
                $array_qu[$value->id]=$value->name;
        }
        $sql="SELECT id,name
                FROM {$CFG->prefix}scorm sc
                WHERE  sc.course =$COURSE->id";
                $getscodata=$DB->get_records_sql($sql);
        $array_opt[0]=get_string('alss_class', 'scormfull');
        $scorm_counter=0;
        foreach($getscodata as $value)
        {
                if($COURSE->format=="scorm")
                {
                        if(!$scorm_counter)
                        {

                         $array_opt[$value->id]=$value->name.get_string('Scorm_curriculum_of_the_course_home_page', 'scormfull');
                        }else{
                                $array_opt[$value->id]=$value->name;
                        }
                }else
                {
                         $array_opt[$value->id]=$value->name;
                }
                $scorm_counter++;

        }
        $threshold_array;
        for($i=0;$i<=100;$i=$i+5)
        {
                $threshold_array[$i]="{$i}%";
        }
        $boolean_array[0]=get_string('str_false', 'scormfull');
        $boolean_array[1]=get_string('str_true', 'scormfull');

        $mform = $this->_form;

    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

         if (! $scormfull = $DB->get_record("scormfull", array("id"=>$this->_instance) )) {
        }else{
                $mform->addElement('hidden', 'id', $scormfull->id);
        }

    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('scrom_exam', 'scormfull'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor(true, get_string('scrom_exam_info', 'scormfull'));

        $mform->addElement('select', 'quid', get_string("connected_exam", "scormfull"), $array_qu);
        $mform->setDefault('quid',0);

    /// Adding the rest of newmodule settings, spreeading all them into this fieldset
    /// or adding more fieldsets ('header' elements) if needed for better logic
        $mform->addElement('select', 'threshold', get_string("connected_class", "scormfull"), $array_opt);
        $mform->setDefault('threshold',0);

        $mform->addElement('select', 'thresholdvalue', get_string("Thresholdvalue", "scormfull"), $threshold_array);
        $mform->setDefault('thresholdvalue', 0);
        $mform->addElement('select', 'report_add', get_string("add_thresheldvalue", "scormfull"), $boolean_array);
        $mform->setDefault('thresholdvalue', 0);

        $mform->addElement('text', 'report_value', get_string('hours_value','scormfull'),'maxlength="5" size="5"');
        $mform->setDefault('report_value', 0);

        $mform->addElement('text', 'quid_score', get_string('scorm_quiz_score','scormfull'),'maxlength="5" size="5"');
        $mform->setDefault('quid_score', 0);



        $features = array('groups'=>false, 'groupings'=>false, 'groupmembersonly'=>false,
                          'outcomes'=>false, 'gradecat'=>false, 'idnumber'=>false);


        $this->standard_coursemodule_elements($features);
        // add standard buttons, common to all modules
        $this->add_action_buttons();


    }
}

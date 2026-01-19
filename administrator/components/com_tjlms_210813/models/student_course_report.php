<?php
/**
 * @version     1.0.0
 * @package     com_tjlms
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      TechJoomla <extensions@techjoomla.com> - http://www.techjoomla.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport( 'joomla.application.component.modeladmin' );

/**
 * Tjlms model.
 */
class TjlmsModelStudent_course_report extends JModelLegacy
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TJLMS';

	public function __construct($config = array()) {

		parent::__construct($config);
	}

	/**
	 * Get progress of user for the course
	 *
	 */
	function getUserCourseProgress($course_id, $student_id ){
		$db = JFactory::getDBO();
		$progress = new stdClass;
		$query=$db->getQuery(true);
		$query->select('COUNT(lt.id)')
		->from( ' #__tjlms_lesson_track AS lt' )
		->join('LEFT', '#__tjlms_lesson AS l ON l.id = lt.lesson_id ')
		->where('lt.user_id ='.(int)$student_id)
		->where('l.course_id ='.(int)$course_id)
		->where('lt.lesson_status ='. $db->quote('completed').' OR lt.lesson_status ='. $db->quote('passed').' OR lt.lesson_status ='. $db->quote('failed'));
		$db->setQuery($query);
		$progress->complete=$db->loadResult();

		$query=$db->getQuery(true);
		$query->select('COUNT(l.id)')
		->from( ' #__tjlms_lesson_track AS lt' )
		->join('RIGHT', '#__tjlms_lesson AS l ON l.id = lt.lesson_id ')
		->where('lt.user_id ='.(int)$student_id)
		->where('l.course_id ='.(int)$course_id)
		->where('lt.lesson_status ='. $db->quote('not_attempted'));
		$db->setQuery($query);
		$progress->pending=$db->loadResult();

		return $progress;
	}
	/**
	 * Get course details for the student
	 *
	 */
	public function getUserCourseDetails($course_id,$user_id )
	{

		/*$db = JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('lt.score,lt.attempt,l.name,l.id')
		->from( ' #__tjlms_lesson_track AS lt' )
		->join('LEFT', '#__tjlms_lesson AS l ON l.id = lt.lesson_id ')
		->where('lt.user_id ='.(int)$user_id)
		->where('l.course_id ='.(int)$course_id)
		->order('lt.id DESC ')
		->limit(1);
		$db->setQuery($query);
		$course_details=$db->loadObjectList();*/


		$tjlmsdbhelper	=	new tjlmsdbhelper();

		$lessons	= $tjlmsdbhelper->get_records('*','tjlms_lesson', array("course_id"=>$course_id),'','loadObjectList');
$course_details	=array();
		foreach($lessons as $k=>$lesson){
		$course_details[$k]	=	new stdClass;
		$course_details[$k]->name= $lesson->name;
$scorm	= $tjlmsdbhelper->get_records('id','tjlms_scorm', array("lesson_id"=>$lesson->id),'','loadResult');

			$scoes	=	$tjlmsdbhelper->get_records('*','tjlms_scorm_scoes', array("scorm"=>$scorm),'','loadObjectList');
			$course_details[$k]->score = $this->scorm_grade_user_attempt($scoes, $user_id, $attempt=1);
			$course_details[$k]->attempt	= '1';
		}
		return $course_details;


		$course_details = array();
		$course_details[0] = new stdClass;
		$course_details[0]->uname = 'General overview';
		$course_details[0]->score = 44;
		$course_details[0]->attempt = 1;
		$course_details[0]->report_path = 'path';

		$course_details[1] = new stdClass;
		$course_details[1]->uname = 'How to create a extension';
		$course_details[1]->score = 56;
		$course_details[1]->attempt = 2;
		$course_details[1]->report_path = 'path';

		$course_details[2] = new stdClass;
		$course_details[2]->uname = 'How to create a component';
		$course_details[2]->score = 23;
		$course_details[2]->attempt = 3;
		$course_details[2]->report_path = 'path';

		$course_details[3] = new stdClass;
		$course_details[3]->uname = 'How to create a plugin';
		$course_details[3]->score = 67;
		$course_details[3]->attempt = 2;
		$course_details[3]->report_path = 'path';

		$course_details[4] = new stdClass;
		$course_details[4]->uname = 'Charts and Functions';
		$course_details[4]->score = 54;
		$course_details[4]->attempt = 1;
		$course_details[4]->report_path = 'path';

		$course_details[5] = new stdClass;
		$course_details[5]->uname = 'Additional Tools';
		$course_details[5]->score = 23;
		$course_details[5]->attempt = 1;
		$course_details[5]->report_path = 'path';

		$course_details[6] = new stdClass;
		$course_details[6]->uname = 'Quiz1';
		$course_details[6]->score = 53;
		$course_details[6]->attempt = 1;
		$course_details[6]->report_path = 'path';

		return $course_details;
	}
//JUGAD start


function scorm_grade_user_attempt($scoes, $userid, $attempt=1) {

    $attemptscore = new stdClass();
    $attemptscore->scoes = 0;
    $attemptscore->values = 0;
    $attemptscore->max = 0;
    $attemptscore->sum = 0;
    $attemptscore->lastmodify = 0;


    foreach ($scoes as $sco) {
        if ($userdata=$this->scorm_get_tracks($sco->id, $userid, $attempt)) {
            if (($userdata->status == 'completed') || ($userdata->status == 'passed')) {
                $attemptscore->scoes++;
            }
            if (!empty($userdata->score_raw) || (isset($scorm->type) && $scorm->type=='sco' && isset($userdata->score_raw))) {
                $attemptscore->values++;
                $attemptscore->sum += $userdata->score_raw;
                $attemptscore->max = ($userdata->score_raw > $attemptscore->max)?$userdata->score_raw:$attemptscore->max;
                if (isset($userdata->timemodified) && ($userdata->timemodified > $attemptscore->lastmodify)) {
                    $attemptscore->lastmodify = $userdata->timemodified;
                } else {
                    $attemptscore->lastmodify = 0;
                }
            }
        }
    }

     $score = $attemptscore->max;

    /*switch ($scorm->grademethod) {
        case GRADEHIGHEST:
            $score = (float) $attemptscore->max;
        break;
        case GRADEAVERAGE:
            if ($attemptscore->values > 0) {
                $score = $attemptscore->sum/$attemptscore->values;
            } else {
                $score = 0;
            }
        break;
        case GRADESUM:
            $score = $attemptscore->sum;
        break;
        case GRADESCOES:
            $score = $attemptscore->scoes;
        break;
        default:
            $score = $attemptscore->max;   // Remote Learner GRADEHIGHEST is default
    }-*/

    return $score;
}
function scorm_get_tracks($scoid, $userid, $attempt='') {
    // Gets all tracks of specified sco and user.
    global $DB;
	$attempt =1;

   /* if (empty($attempt)) {
        if ($scormid = $DB->get_field('scorm_scoes', 'scorm', array('id'=>$scoid))) {
            $attempt = scorm_get_last_attempt($scormid, $userid);
        } else {
            $attempt = 1;
        }
    }*/
    $this->tjlmsdbhelper	=	new tjlmsdbhelper();
    $tracks =$this->tjlmsdbhelper->get_records('*','tjlms_scorm_scoes_track', array('userid'=>$userid, 'sco_id'=>$scoid,
                                                              'attempt'=>$attempt),'','loadObjectList');

    if ($tracks =$this->tjlmsdbhelper->get_records('*','tjlms_scorm_scoes_track', array('userid'=>$userid, 'sco_id'=>$scoid,
                                                              'attempt'=>$attempt),'','loadObjectList')){
    //if ($tracks = $DB->get_records('scorm_scoes_track', array('userid'=>$userid, 'scoid'=>$scoid,'attempt'=>$attempt), 'element ASC')) {
        $usertrack = $this->scorm_format_interactions($tracks);
        $usertrack->userid = $userid;
        $usertrack->scoid = $scoid;

        return $usertrack;
    } else {
        return false;
    }
}
function scorm_format_interactions($trackdata) {
    $usertrack = new stdClass();

    // Defined in order to unify scorm1.2 and scorm2004.
    $usertrack->score_raw = '';
    $usertrack->status = '';
    $usertrack->total_time = '00:00:00';
    $usertrack->session_time = '00:00:00';
    $usertrack->timemodified = 0;

    foreach ($trackdata as $track) {
        $element = $track->element;
        $usertrack->{$element} = $track->value;
        switch ($element) {
            case 'cmi.core.lesson_status':
            case 'cmi.completion_status':
                if ($track->value == 'not attempted') {
                    $track->value = 'notattempted';
                }
                $usertrack->status = $track->value;
                break;
            case 'cmi.core.score.raw':
            case 'cmi.score.raw':
                $usertrack->score_raw = (float) sprintf('%2.2f', $track->value);
                break;
            case 'cmi.core.session_time':
            case 'cmi.session_time':
                $usertrack->session_time = $track->value;
                break;
            case 'cmi.core.total_time':
            case 'cmi.total_time':
                $usertrack->total_time = $track->value;
                break;
        }
        if (isset($track->timemodified) && ($track->timemodified > $usertrack->timemodified)) {
            $usertrack->timemodified = $track->timemodified;
        }
    }

    return $usertrack;
}


///JUGAD end

}

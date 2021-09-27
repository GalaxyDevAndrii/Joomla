<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
$doc =JFactory::getDocument();
$doc->addStyleSheet("../components/com_guru/css/quiz.css");

$k = 0;
$n = count($this->ads);    
$quiz_id =  intval(JFactory::getApplication()->input->get("id", ""));
$model_quizz = new guruAdminModelguruQuiz();
$quiz_name = $model_quizz->getQuizName($quiz_id);

$quiz_name = '"'.$quiz_name.'"';
$total_students_quiz = $this->getNrStudentsQuiz();
$total_avg = $this->getTotalAvg();

$db = JFactory::getDBO();
$sql = "Select datetype FROM #__guru_config where id=1 ";
$db->setQuery($sql);
$format_date = $db->loadColumn();
$dateformat = $format_date[0];

?>

    <div id="editcell">
        <table class="table">
		 <tr>
			<span style="font-size:16px; padding-left:10px"><?php echo JText::_("GURU_ADMIN_QUIZ_STUD_RES"). " ".$quiz_name ; ?></span>
		 </tr>
       </table>
       <table height="15px"></table>
        	<table class="table" style="width:50% !important">
            	<tr>
                	<td style="font-weight:bold;"><?php echo JText::_("GURU_TIMES_T");?></td>
                    <td style="font-weight:bold;"><?php echo JText::_("GURU_COU_STUDENTS");?></td>
                    <td style="font-weight:bold;"><?php echo JText::_("GURU_AVG_SCORE");?></td>
                </tr>
                <?php 
				$res = $model_quizz->NbOfTimesandStudents($quiz_id);
				
				$scoresByUserId = array();
				$maxNoOfTimes = 0;
				for($i=0; $i < count($res); $i++) {	
					$newElem = new stdClass();
					$newElem->user_id = $res[$i]["user_id"];
					
					$newElem->scores = explode(",", $res[$i]["score_by_user"]);
						if(count($newElem->scores) > $maxNoOfTimes) {
							$maxNoOfTimes = count($newElem->scores);
						}	
						array_push($scoresByUserId, $newElem);
				}		
				
				$newvect = array();	
				for($i = 0; $i < $maxNoOfTimes; $i++) {	
					$newElem = new stdClass();	
					$newElem->noOfTimes = $i + 1;	
					$newElem->noOfStudents = 0;		
					$newElem->sumScores = 0;	
					for($j = 0; $j < count($scoresByUserId); $j++) {
						if(count($scoresByUserId[$j]->scores) >= $i + 1) {
							$newElem->noOfStudents += 1;
							$newElem->sumScores += $scoresByUserId[$j]->scores[$i];
						}
					}	
					$newElem->avgScore = $newElem->sumScores / $newElem->noOfStudents;	
					array_push($newvect, $newElem);	
				}
				for($i = 0; $i < count($newvect); $i++){					
				?>	
                <tr>
                	<td>
						<?php
							
							if($i + 1 == 1){
								echo ($i+1)."st";
							}
							elseif($i + 1 == 2){
								echo ($i+1)."nd";
							}
							elseif($i + 1 == 3){
								echo ($i+1)."rd";
							}
							elseif($i + 1 > 3){
								echo ($i+1)."th";
							}
					 	?>
					</td>
                    <td><?php echo $newvect[$i]->noOfStudents;?></td>
                    <td><?php echo $newvect[$i]->avgScore;?></td>
                </tr>
				<?php
				}
				
				?>
      	 </table>
         <table height="25px"></table>
         <table class="table table-striped">
         	<thead>
                <th><?php echo JText::_("GURU_FIRS_NAME");?></th>
                <th><?php echo JText::_("GURU_LAST_NAME");?></th>
                <th><?php echo JText::_("GURU_EMAIL");?></th>
                <th><?php echo "#";?></th>
                <th><?php echo JText::_("GURU_USERNAME");?></th>
                <th><?php echo JText::_("GURU_QUIZ_DATE_TIME_TAKEN");?></th>
                <th><?php echo JText::_("GURU_QUIZ_SCORE");?></th>
            </thead>
            <tbody>
            	<?php
					$new_id = 0;
					$nr = 1;
					$db = JFactory::getDBO();
					
                    for ($i = 0; $i < $n; $i++){
                        $ad = $this->ads[$i];
						$id = $ad->id;      
                       	$date_taken_quiz = $model_quizz->DataTaken($quiz_id, $id, $ad->tq_id);
						$score_quiz_taken = $model_quizz->getScoreQuizTaken($quiz_id, $id, $ad->tq_id);						
						$nr_string = "";
                ?>
                    <tr class="row<?php echo $k;?>"> 
                        <td><?php  echo $ad->firstname;?></td>		
                        <td><?php echo $ad->lastname;?></td>		
                        <td><?php echo $ad->email;?></td>
                        <?php 
						
						if($id == $new_id){
							$nr = intval($nr) + 1;
						}
						else{
							$nr = 1;
						}

						if($nr==1){
							$nr_string = $nr."st";
						}
						elseif($nr == 2){
							$nr_string = $nr."nd";
						}
						elseif($nr == 3){
							$nr_string = $nr."rd";
						}
						elseif($nr >3){
							$nr_string = $nr."td";
						}

					?>
                        <td><?php echo $nr_string; ?></td>			
                        <td><?php echo $ad->username; ?></td>
                        <td><?php echo date(''.$dateformat.'' , strtotime($date_taken_quiz )); ?></td>
                        <td><?php echo $score_quiz_taken; ?></td>                     
                    </tr>
                <?php 
					
					 
					   $new_id = $id;
					   
                        $k = 1 - $k;
                    }//end for
                ?>
            </tbody>    
         </table>
    </div>
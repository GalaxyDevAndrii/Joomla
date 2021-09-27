function get_quiz_result(){

var how_many_right_answers = 0;
var zuzu = '';
var number_of_questions = document.getElementById("question_number").value;

quiz_result = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td><strong>Question</td><td><strong>Correct answer</td></tr>';

for(i=1; i<=number_of_questions;i++)
	{
		var the_answer = document.getElementById("question_answergived"+i).value;
		var the_right_answer = document.getElementById("question_answerright"+i).value;
		var the_question = document.getElementById("the_question"+i).value;
		
		if(the_answer == the_right_answer)
			{
				how_many_right_answers = how_many_right_answers +1;
				the_answer=the_answer.split("$$$$$").join("'");
				secondcolumn = '<img src="<?php echo JURI::base().DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."wright.png" ;?>">  '+ the_answer;
			}	
		else
			{
				the_answer=the_answer.split("$$$$$").join("'");
				the_right_answer=the_right_answer.split("$$$$$").join("'");
				secondcolumn = '<img src="<?php echo JURI::base().DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."wrong.png" ;?>">  ' + the_right_answer + '  (Your answer: ' + the_answer + ')';	
			}	
		
		the_question=the_question.split("$$$$$").join("'");	
		quiz_result = quiz_result + '<tr><td>'+ the_question +'</td><td>'+ secondcolumn +'</td></tr>';
	}
	
quiz_result = quiz_result + '<tr><td height="10"></td><td></td></tr><tr><td><strong>Score</strong></td><td><font color="#ff6600"><strong>'+ (how_many_right_answers * 100 / number_of_questions) +'%</strong></font></td></tr></table>';	
								
document.getElementById("the_quiz").innerHTML = quiz_result;			

}
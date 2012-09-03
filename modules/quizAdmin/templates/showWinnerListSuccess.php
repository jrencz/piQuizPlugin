<?php
use_stylesheet('admin.dataTable');
use_stylesheet('piQuizPlugin.dataTable');
use_javascript('lib.metadata');
use_javascript('piQuizPlugin.dataTable');
use_javascript('piQuizPlugin.winnersList');

echo get_partial('winnersListTable', array('winners'=>$winners));

echo button_to(__("Return to quiz list"), 'quizAdmin/index');
if ($sf_user->can('pi_quiz_resolve') || $sf_user->can('pi_quiz_superuser')) {
  echo button_to(__("Resend winner list to quiz resolver"), 'quizAdmin/sendWinnerList?id='.$quiz->getId()); 
  echo button_to(__("Send winner list to quiz creator"), 'quizAdmin/sendWinnerList?id='.$quiz->getId().'&to=creator');  
}


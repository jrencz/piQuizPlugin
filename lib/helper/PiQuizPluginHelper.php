<?php
function getQuizState(PiQuiz $quiz)
{
  // match resolved
  if ($quiz->getIsResolved()) {
    return 'resolved';          
  } 
  else 
  { 
    $now   = new DateTime();
    $start = new DateTime($quiz->getDateStart());
    $end   = new DateTime($quiz->getDateEnd());
    // match pending
    if ($end < $now ) return 'pending';
    // match current
    if (($end > $now) && ($start < $now)) return 'current';
    // match upcoming
    if ($start > $now) return 'upcoming';
  }
}
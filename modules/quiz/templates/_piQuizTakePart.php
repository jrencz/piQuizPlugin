<?php
$now   = new DateTime;
$start = new DateTime($quiz->getDateStart());
$end   = new DateTime($quiz->getDateEnd());

if ((($end > $now) && ($start < $now)) || ($end < $now))
{
  use_javascript('lib.metadata');

  echo _media($quiz->getImage())->size('200x200')->set('.left');
  echo _tag('h2', __("Quiz") . ": " . $quiz->getName());

  echo _tag('h3', __("Question") . ":");
  echo markdown($quiz->getQuestion());

  echo _tag('h3', __('Quiz duration'));
    $start = new DateTime($quiz->dateStart);
    $now   = new DateTime();
    $end   = new DateTime($quiz->dateEnd); 
    $interval = $now->diff($end);

  echo _tag('p', $start->format("d.m.Y [H:i]") . " - " . $end->format('d.m.Y [H:i]'));

  if (($end < $now) && ($start > $now))
  {
     echo "<br>" . $interval->format((__("Quiz ends in %a days, %h hours and %i minutes")));
  }
  
  if (get_class($form['prize_id']->getWidget()) == "sfWidgetFormInputHidden") {
    echo _tag('h3', __('Prize'));
    $prize = $quiz->getPrizes()->getIterator()->current();
    echo _tag('p', $prize->getName() . sprintf(" (%s)", $prize->getQuantity()));
  }
  
  if (($end > $now) && ($start < $now))
  {
    echo $form->render(array('action' => url_for('quiz/ajaxRegisterResponse'), ) );      
  }
  else 
  {
    echo __("This quiz is already closed");
  }
}

if ($start > $now) {
  echo __("This quiz is not yet open");
}

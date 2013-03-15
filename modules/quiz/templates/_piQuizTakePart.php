<?php
$now   = new DateTime;
$start = new DateTime($quiz->getDateStart());
$end   = new DateTime($quiz->getDateEnd());

if ((($end > $now) && ($start < $now)) || ($end < $now))
{
  use_javascript('lib.metadata');
  use_javascript("lib.moment");
  use_javascript("moment.pl");
  use_javascript("livestamp.min");

  echo _tag('div.column_left', _media($quiz->getImage())->size('200x200')->set('.left'));
  
  echo _open('div.column_center');
    echo _tag('h2', $quiz->getName());
    echo _tag('h3', __('Quiz duration'));
    
      $start = new DateTime($quiz->dateStart);
      $now   = new DateTime();
      $end   = new DateTime($quiz->dateEnd); 
      $interval = $now->diff($end);

    echo _open('p');
    echo $start->format("d.m.Y [H:i]") . " - " . $end->format('d.m.Y [H:i]');

    if (($end > $now) && ($start < $now))
    {
      echo  "<br>".__('Quiz ends') . " " . _tag('span', array(' data-livestamp' => $end->getTimestamp()),  $end->format("d-m-Y H:i"));
    }
    
    echo _close('p');
  
    if (get_class($form['prize_id']->getWidget()) == "sfWidgetFormInputHidden") {
      $prize = $quiz->getPrizes()->getIterator()->current();
      echo _tag('h3', ($prize->getQuantity() > 1) ? __('Prizes') : __("Prize"));
      echo _tag('p', $prize->getName() . sprintf(" (%s)", $prize->getQuantity()));
    }
  echo _close('div.column_center');
    
  echo _open('div.column_right');
  
    echo _tag('h3', __("Question") . ":");
    echo _markdown($quiz->getQuestion());
  
    if (($end > $now) && ($start < $now))
    {
      echo $form->render(array('action' => url_for('quiz/ajaxRegisterResponse'), ) );      
    }
    else 
    {
      echo __("This quiz is already closed");
    }
  
  echo _close('div.column_right');
  
  echo _open('div.connected');
  echo _tag('h2', __('See also'));
  echo get_partial('entry/listBy', array(
    'entryPager'       => $quiz->getEntries(),
    'withImages'       => false,
    'truncateTitles'   => false   
  ));
   
  echo _close('div.connected');
}

if ($start > $now) {
  echo __("This quiz is not yet open");
}

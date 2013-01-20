<?php       

echo _tag('h2', __("Quizzes"));

echo $quizPager->renderNavigationTop();

echo _open('ul.elements');

foreach ($quizPager as $quiz)
{             
  $end = new DateTime($quiz->getDateEnd());
  $now = new DateTime();
  if($now > $end) 
  {
    $timeLeft = $now->diff($end)->format('ended %a days ago');
    $class = array(".finished");
  }
  else 
  {
    $timeLeft = $now->diff($end)->format('ends in %a days');
    $class = array(".ongoing");
  }
  
  $class[] = ($quiz->getIsResolved()) ? "resolved" : "unresolved";
  
  echo _open('li.element');

    echo _link($quiz)
      ->set(join(".", $class))
      ->text(_media($quiz->getImage())
              ->width('64')
              ->height('64') . $quiz->name . sprintf(" (%s)", $timeLeft)
      );

  echo _close('li');
}

echo _close('ul');

echo $quizPager->renderNavigationBottom();
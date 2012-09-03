<?php // Vars: $quizPager

echo $quizPager->renderNavigationTop();

echo _open('ul.elements');

foreach ($quizPager as $quiz)
{             
  $start = new DateTime($quiz->getDateEnd());
  $now = new DateTime();
  $timeLeft = $now->diff($start)->format('%a days');
  echo _open('li.element');

    echo _link($quiz)->text(_media($quiz->getImage())->width('64')->height('64') . $quiz->name . sprintf(" (%s)", $timeLeft));

  echo _close('li');
}

echo _close('ul');

echo $quizPager->renderNavigationBottom();
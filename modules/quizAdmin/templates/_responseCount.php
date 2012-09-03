<?php
echo dmDb::query('PiQuizResponse qr')
      ->select('qr.id')
      ->where('qr.quiz_id = ?', $pi_quiz->id)
      ->count();
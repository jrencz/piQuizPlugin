<?php
use_stylesheet('admin.dataTable');
use_stylesheet('lib.colorbox');
use_stylesheet('piQuizPlugin.dataTable');
use_javascript('lib.metadata');
use_javascript('lib.colorbox');
use_javascript('piQuizPlugin.dataTable');
use_stylesheet('piQuizPlugin.resolver');
use_javascript('piQuizPlugin.resolver');



// Step 1: choose correct answers
if (isset($responses))
{ 
  echo _open('form action=' . url_for($helper->getRouteArrayForAction('resolve')) . '/id/' . $quiz->getId() . ' method=post');
  echo _open('div.dm_data');
  $table = _table('.data_table')->set(array('json' => array(
    'translation_url' => _link('quizAdmin/tableTranslation')->getHref(),
    'step' => '1', 
  )))->head(
    __('Response date'),  
    __('Confirmation date'),  
    __('IP address'),  
    __('Winner name'),  
    __('Surname'),  
    __('Email'),  
    __('Chosen prize'),  
    __('Answer'),
    __('Is correct')
  );  
 
  foreach ($responses as $response)  
  {
  
    $checked = ($response->getIsOpenAnswerCorrect() || ($response->isOpenAnswerMatching($quiz->correct_answer))) ? " checked=checked" : "";
    
    $table->body(  
      $response->getCreatedAt(),  
      $response->getUpdatedAt(),  
      _link("http://www.infosniper.net/index.php?ip_address=" . $response->getIp())->text($response->getIp())->set(".ip"),  
      $response->getName(),
      $response->getSurname(),
      $response->getEmail(),
      $response->getPrizes()->getName(),  
      $response->getOpenAnswer(),  
      _tag("input type=checkbox name=ids[] class=correct_answer value=" . $response->getId() . $checked)  
    );  
  }
  echo $table;
  echo _tag('input type=hidden name=nextStep value=2');                     
  echo _tag('input type=submit value=' .  __('Save and go to next step'));                     
  echo _close('div.dm_data');
  echo _close('form');
}
// Step 2: choose winners
else if (isset($correctResponses))
{ 
  $prizesCount = array();
  foreach ($prizes as $prize) {
    $prizesCount[$prize->id] = $prize->quantity;
  }
  
  echo _open('form action=' . url_for($helper->getRouteArrayForAction('resolve')) . '/id/' . $quiz->getId() . ' method=post');
  echo _open('div.dm_data');
  $table = _table('.data_table')->set(array('json' => array(
    'translation_url' => _link('dmPage/tableTranslation')->getHref(),
    'step' => '2', 
  )));
  $headers = array(
    __('Response date'),  
    __('Confirmation date'),  
    __('IP address'),  
    __('Winner name'),  
    __('Surname'),
    __('Email'),  
    __('Chosen prize'),  
    __('Answer'),
  );            

  foreach($prizes as $prize) {
    echo _tag('div.winner_counter', array('data-prize-id'=>$prize->id), implode(" ", array(__("Prize") . ":", $prize->name, sprintf(__("Winners left to be chosen: <span>%s</span>"), $prize->quantity).".", _tag('a.pick_random', __("Pick random")))));
    $headers[] = $prize->getName();
  }
  call_user_func_array(array($table, 'head'), $headers);
 
  foreach ($correctResponses as $response)  
  {    
    $row = array( 
      $response->getCreatedAt(),  
      $response->getUpdatedAt(),  
      _link("http://www.infosniper.net/index.php?ip_address=" . $response->getIp())->text($response->getIp())->set(".ip"),  
      $response->getName(),
      $response->getSurname(),
      $response->getEmail(),
      $response->getPrizes()->getName(),
    );
    
    switch ($quiz->getType()) {
      case 'OPEN':
        $row[] = $response->getOpenAnswer();
        break;
      case 'PREDEFINED':
        $row[] = $response->getPredefinedAnswer()->getAnswer();
        break;
      default:
        # code...
        break;
    }
    
    foreach($prizes as $prize) {
      // putting checkboxes in correct columns
      if ($prize->id == $response->getPrizes()->id) {
        $row[] = _tag('span.hide', 1) . _tag("input type=checkbox name=ids[] class='winner' value=" . $response->getId(), array('data-prize-id'=>$prize->id));
      } else {
        $row[] = _tag('span.hide', 0);
      }
      // INFO: span is just to enable sorting by prize columns
    }
    call_user_func_array(array($table, 'body'), $row);
  }
  
  echo _tag('input type=hidden name=nextStep value=3');
  echo $table;                                         
  echo _tag('input type=submit value=' .  __('Save and go to next step'));                     
  echo _close('div.dm_data');
  echo _close('form');
}
// Step 3: prepare email notification for winners 
else if (isset($template))
{ 
  echo _open('form action=' . url_for($helper->getRouteArrayForAction('resolve')) . '/id/' . $quiz->getId() . ' method=post');
  echo _tag('input type=hidden name=nextStep value=4');
  
  foreach($prizes as $prize) {
    $message  = _tag('textarea name=message[]');
    $shipment = _tag('textarea name=shipment[]', $prize->shipment);
    
    $placeholders = array('%message%', '%prize_name%', '%shipment%');
    $values       = array( $message,    $prize->name,   $shipment);
    
    $template_copy = $template;
    $template_copy = str_replace($placeholders, $values, $template_copy);
    echo _tag('input type=hidden name=prize_id[] value='.$prize->id);                     
    echo _tag("div.messagePreview", $template_copy);
  }
  echo _tag('input type=submit value=' .  __('Save and go to next step'));                     
  echo _close('form');

}
?>
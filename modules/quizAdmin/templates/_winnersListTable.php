<?php
$table = _table('.data_table')->set(array('json' => array(
  'translation_url' => _link('quizAdmin/tableTranslation')->getHref() 
)))->head(
  __('Winner name'),  
  __('Surname'),  
  __('Email'),  
  __('Chosen prize')
);  

foreach ($winners as $response)  
{    
  $table->body(  
    $response->getName(),  
    $response->getSurname(),  
    $response->getEmail(),
    $response->getPrizes()->getName()
  );  
}
echo _tag('div.dm_data', $table);
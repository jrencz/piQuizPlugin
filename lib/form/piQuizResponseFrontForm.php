<?php
/**
* 
*/
class PiQuizResponseFrontForm extends PluginPiQuizResponseForm
{
  public function setup()
  {
    parent::setup(); 
    
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'ip'                     => new sfWidgetFormInputText(),
      'quiz_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Quiz'), 'add_empty' => false)),
      'prize_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Prizes'), 'add_empty' => false)),
      'open_answer'            => new sfWidgetFormInputText(),
      'predefined_answer_id'   => new sfWidgetFormDoctrineChoice(array(
                                  'model' => $this->getRelatedModelName('PredefinedAnswer'), 
                                  'query' => Doctrine_Core::getTable('PiQuizPredefinedAnswer')->createQuery('pa')->where('pa.quiz_id = ?', $this->guessId()),  
                                  'add_empty' => false,
                                  'expanded' => true
                                )),
      'is_open_answer_correct' => new sfWidgetFormInputCheckbox(),
      'is_winner'              => new sfWidgetFormInputCheckbox(),
      'name'                   => new sfWidgetFormInputText(),
      'surname'                => new sfWidgetFormInputText(),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
      'email'                  => new sfWidgetFormInputText(),
      'token'                  => new sfWidgetFormInputText(),
      'is_confirmed'           => new sfWidgetFormInputCheckbox(),

    ));
    $this->setWidget('quiz_id', new sfWidgetFormInputHidden());
    
    $query = Doctrine_Core::getTable('PiQuizPrize')->createQuery('p')
      ->where('p.quiz_id = ?', $this->guessId());
    
    if ($query->count() == 1) {
      $this->setWidget('prize_id', new sfWidgetFormInputHidden());
      $this->setDefault('prize_id', $query->fetchRecord()->getId());
    }
    else
    {
      $this->setWidget('prize_id', new sfWidgetFormDoctrineChoice(array(
        'model' => $this->getRelatedModelName('Prizes'), 
        'add_empty' => false,
        'expanded' => true,
        'query' =>  $query,
        'method' => '__representation', 
      )));      
    }
    
    $this->setValidator('email', new sfValidatorUniqueEmail(array(
      'query' => Doctrine_Core::getTable('PiQuizResponse')->createQuery('r')->where('r.id = ?', $this->guessId()),  
    )));
    
    unset(
      $this['ip'],
      $this['is_open_answer_correct'],
      $this['is_winner'],
      $this['created_at'],
      $this['updated_at'],
      $this['token'],
      $this['is_confirmed']
    );
    
    $this->widgetSchema->setLabel('open_answer','Your answer');
    $this->widgetSchema->setLabel('predefined_answer_id','Your answer');
    $this->widgetSchema->setLabel('name','Your name');
    $this->widgetSchema->setLabel('surname', 'Your surname');
    $this->widgetSchema->setHelp('surname', 'Is needed for claiming the prize');
    $this->widgetSchema->setHelp('email', 'Is needed for verification purposes only');
    $this->widgetSchema->setFormFormatterName('dmList'); 
    
    
    switch (PiQuizTable::getInstance()->find($this->guessId())->getType()) {
      case 'OPEN':
        $this->setValidator('open_answer', new sfValidatorString(array('max_length' => 255, 'required' => true)));
        unset($this['predefined_answer_id']);
        break;
      case 'PREDEFINED':
        $this->setValidator('predefined_answer_id', new sfValidatorDoctrineChoice(array(
          'model' => 'PiQuizResponse',
          'query' => Doctrine_Core::getTable('PiQuizPredefinedAnswer')->createQuery('pa')->where('pa.quiz_id = ?', $this->guessId()),  
        )));        
        unset($this['open_answer']);   
        break;
    }
    
  } 
       
  
}

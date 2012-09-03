<?php 
class PiQuizWinnerNotificationForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'message'   => new sfWidgetFormTextarea(),
    ));
    
    $this->setValidators(array(
      'message'   => new sfValidatorString(array('required' => true)),
    ));                                                               
    
    $this->widgetSchema->setNameFormat('newsletter[%s]');
  }
}
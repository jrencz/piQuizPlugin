<?php

class piQuizTakePartForm extends dmWidgetPluginForm
{
  public function configure()
  { 
    $this->widgetSchema['recordId']    = new sfWidgetFormDoctrineChoice(array(
      'model' => 'PiQuiz', 
      'add_empty' => sprintf('(%s) %s', 
        $this->__('contextual'),
        ($record = $this->getServiceContainer()->getParameter('context.page')->getRecord()) ? $record->__toString() : ""
      )));
    $this->validatorSchema['recordId'] = new sfValidatorDoctrineChoice(array(
      'model' => 'PiQuiz', 
      'required' => false, 
    ));
    
    parent::configure();
    
    $this->widgetSchema->setLabel('recordId', $this->getService('i18n')->__("Quiz"));
  }

  public function getJavascripts()
  {
    return array(
      'piQuizPlugin.takePartForm'
    );
  }


}
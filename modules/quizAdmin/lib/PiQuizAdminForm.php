<?php

/**
 * quizAdmin admin form
 *
 * @package    Polibuda.info
 * @subpackage quizAdmin
 * @author     Programiści Polibuda.info
 */
class PiQuizAdminForm extends BasePiQuizForm
{ 
  /**
   * Prizes scheduled for deletion
   * @var array
   */   
  protected $prizesScheduledForDeletion = array();

  /**
   * Predefined answers scheduled for deletion
   * @var array
   */   
  protected $predefinedAnswersScheduledForDeletion = array();
  
  public function configure()
  {
    parent::configure();
    $userCulture = dmContext::getInstance()->getUser()->getCulture();
    $this->setWidget('date_start', new sfWidgetFormI18nDateTime(array('culture'=>$userCulture)));
    $this->setWidget('date_end',   new sfWidgetFormI18nDateTime(array('culture'=>$userCulture)));
    if ($this->isNew()) 
    {
      $this->setValidator('date_start', new sfValidatorDateTime(array('min'=>time())));      
    }                                                                                  
    else
    {
      $this->setValidator('date_start', new sfValidatorDateTime());
    }
    $this->setValidator('date_end',   new sfValidatorDateTime(array('min'=>time())));
    
    $start = new DateTime;
    $end   = new DateTime;
    if (dmConfig::has('pi_quiz_default_start')) {
      $start->modify(dmConfig::get('pi_quiz_default_start'));    
    } else {
      $start->modify('+1 hour');    
    }  
    if (dmConfig::has('pi_quiz_default_end')) {
      $end->modify(dmConfig::get('pi_quiz_default_end'));    
    } else {
      $end->modify('+1 week');
    }
    $this->setDefault('date_start',  $start->getTimestamp());
    $this->setDefault('date_end',      $end->getTimestamp()); 
    
    $newPrizeForm = new PiQuizPrizeForm();

    $newPrizeForm->setWidget('quiz_id', new sfWidgetFormInputHidden());

    if ($this->isNew())
    {
      $newPrizeForm->setDefault('quiz_id', '');
      $newPrizeForm->setValidator('quiz_id', new sfValidatorPass());
    }
    else
    {
      $newPrizeForm->setDefault('quiz_id', $this->object->id);
      $newPrizeForm->validatorSchema['shipment'] = new sfValidatorPass();
    }
    $this->embedForm('prize_form', $newPrizeForm);
    
    $this->embedRelation('Prizes');
    
    $newPredefinedAnswerForm = new PiQuizPredefinedAnswerForm();
    $newPredefinedAnswerForm->setWidget('quiz_id', new sfWidgetFormInputHidden());
    if ($this->isNew()) 
    {
      $newPredefinedAnswerForm->setDefault('quiz_id', '');
      $newPredefinedAnswerForm->setValidator('quiz_id', new sfValidatorPass());
    }                                                                                  
    else
    {
      $newPredefinedAnswerForm->setDefault('quiz_id', $this->object->id);
    }
    
    $newPredefinedAnswerForm->setWidget('pi_quiz_response_list', new sfWidgetFormInputHidden());
    $newPredefinedAnswerForm->setValidator('pi_quiz_response_list', new sfValidatorPass());
    
    
    $newPredefinedAnswerForm->setValidator('answer', new sfValidatorString(array('max_length' => 255, 'required' => false, )));
    $this->embedForm('predefined_answer_form', $newPredefinedAnswerForm);
    
    
      $subForm = new sfForm();

      foreach ($this->getObject()->Answers as $index => $childObject)
      {
        $form = new PiQuizPredefinedAnswerForm($childObject);
        unset($form['pi_quiz_response_list']);
        unset($form['quiz_id']);
        $form
          ->setWidget('delete', new sfWidgetFormInputCheckbox())
          ->setValidator('delete', new sfValidatorBoolean());
        

        $subForm->embedForm($index, $form);
      }

      $this->embedForm('Answers', $subForm);
    
    
   
    
    
        
    dmContext::getInstance()->getResponse()
      ->addJavascript("piQuizPlugin.quizForm")
      ->addStylesheet("piQuizPlugin.quizForm");
    
    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkDatesConsistency')))
    );
    
    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkTypeConsistency')))
    );

  }
  protected function doBind(array $values)
  { 
    
    if (isset($values['Prizes']))
    {
      foreach ($values['Prizes'] as $i => $prizeValues)
      {
        if (isset($prizeValues['delete']) && $prizeValues['id'])
        {
          $this->prizesScheduledForDeletion[$i] = $prizeValues['id'];
        }
      }
    }
    
    if (isset($values['Answers']))
    {
      foreach ($values['Answers'] as $i => $predefinedAnswerValues)
      {
        if (isset($predefinedAnswerValues['delete']) && $predefinedAnswerValues['id'])
        {
          $this->predefinedAnswersScheduledForDeletion[$i] = $predefinedAnswerValues['id'];
        }
      }
    } 

    parent::doBind($values);
  }
  
  /**
   * Updates object with provided values, dealing with evantual relation deletion
   *
   * @see sfFormDoctrine::doUpdateObject()
   */
  protected function doUpdateObject($values)
  { 
    parent::doUpdateObject($values);
    if (count($this->prizesScheduledForDeletion))
    {
      foreach ($this->prizesScheduledForDeletion as $index => $id)
      {
        unset($values['Prizes'][$index]);
        unset($this->object['Prizes'][$index]);
        Doctrine::getTable('PiQuizPrize')->findOneById($id)->delete();
      }
    }
    if (count($this->predefinedAnswersScheduledForDeletion))
    {
      foreach ($this->predefinedAnswersScheduledForDeletion as $index => $id)
      {
        unset($values['Answers'][$index]);
        unset($this->object['Answers'][$index]);
        Doctrine::getTable('PiQuizPredefinedAnswer')->findOneById($id)->delete();
      }
    }

    $this->getObject()->fromArray($values);
    
  } 
  

  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    $this->getObject()->save($con);
    
    /**
     * bad hack. Since here it's impossible to just override embed form value
     * but we already have newly saved record ID
     * let's miss the form saving workflow and create related records.
     */
    $prizeForm = $this->getValue('prize_form');
    if ($prizeForm['name'] != '')
    {
      $newPrize = new PiQuizPrize();
      $newPrize
        ->setName($prizeForm['name'])
        ->setQuizId($this->getObject()->getId())
        ->setQuantity($prizeForm['quantity'])
        ->save();                  
    }
    unset($this->embeddedForms['prize_form']);
    
    $predefinedAnswerForm = $this->getValue('predefined_answer_form');
    
    $newPredefinedAnswer = new PiQuizPredefinedAnswer();
    if ($predefinedAnswerForm['answer'] != '')
    {
      $newPredefinedAnswer
        ->setAnswer($predefinedAnswerForm['answer'])
        ->setQuizId($this->getObject()->getId())
        ->setIsCorrect($predefinedAnswerForm['is_correct'])
        ->save();
    }
    unset($this->embeddedForms['predefined_answer_form']);
      
    // embedded forms
    $this->saveEmbeddedForms($con);
  }
  /**
   * Saves embedded form objects.
   *
   * @param mixed $con   An optional connection object
   * @param array $forms An array of forms
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }
    
    $prizeForm = $this->getValue('prize_form');
    if ($prizeForm['name']=='')
    {
      unset($forms['prize_form']);
    }
    $predefinedAnswerForm = $this->getValue('predefined_answer_form');
    if ($predefinedAnswerForm['answer']=='')
    {
      unset($forms['predefined_answer_form']);
    }
    
    foreach ($forms as $form)
    {
      if ($form instanceof sfFormObject)
      {
        if (!in_array($form->getObject()->getId(), $this->prizesScheduledForDeletion))
        {
          $form->saveEmbeddedForms($con);
          $form->getObject()->save($con);
        }
        if (!in_array($form->getObject()->getId(), $this->predefinedAnswersScheduledForDeletion))
        {
          $form->saveEmbeddedForms($con);
          $form->getObject()->save($con);
        }
      }
      else
      { 
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }
  }    
  
  public function checkDatesConsistency($validator, $values)
  { 
    $dateConsistencyViolationError = new sfValidatorError($validator, 'Quiz cannot end before it begins');
    if (strtotime($values['date_start']) > strtotime($values['date_end']))
    { 
      throw new sfValidatorErrorSchema($validator, array(
        'date_start' => $dateConsistencyViolationError,
        'date_end' => $dateConsistencyViolationError
      )); 
    }

    return $values;
  }
  
  public function checkTypeConsistency($validator, $values)
  { 
    $typeOpenConsistencyViolationError = new sfValidatorError($validator, 'Correct answer must be given for selected type');
    $typePredefinedConsistencyViolationError = new sfValidatorError($validator, 'Correct answer must be chosen for selected type');
    if ($values['type'] == 'OPEN' && strlen($values['correct_answer'])==0)
    { 
      throw new sfValidatorErrorSchema($validator, array(
        'correct_answer' => $typeOpenConsistencyViolationError,
      )); 
    }

    return $values;
  } 
}
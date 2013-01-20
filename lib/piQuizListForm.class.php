<?php

class piQuizListForm extends dmWidgetPluginForm
{
  public function configure()
  { 
    // Max per page
    $this->widgetSchema['maxPerPage']     = new sfWidgetFormInputText(array(), array(
      'size' => 3
    ));
    $this->validatorSchema['maxPerPage']  = new sfValidatorInteger(array(
      'required' => false,
      'min' => 0,
      'max' => 99999
    ));
    $this->widgetSchema['maxPerPage']->setLabel("Per page");
    
     
    // Only not archived (=ongoing or onresolved) quizzes
    $this->widgetSchema['onlyNotArchived']       = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['onlyNotArchived']    = new sfValidatorBoolean();
    $this->widgetSchema['onlyNotArchived']->setLabel("Only not archived");
    $this->widgetSchema->setHelp('onlyNotArchived', "Display only quizzes that are currently running or that are not yet resolved");

    // Paginators top & bottom
    $this->widgetSchema['navTop']       = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['navTop']    = new sfValidatorBoolean();
    $this->widgetSchema['navTop']->setLabel("Navigation above");

    $this->widgetSchema['navBottom']    = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['navBottom'] = new sfValidatorBoolean();
    $this->widgetSchema['navBottom']->setLabel("Navigation below");

    // Order field selection
    $orderFields = $this->getAvailableOrderFields();
    $this->widgetSchema['orderField']    = new sfWidgetFormSelect(array(
      'choices' => $orderFields
    ));
    $this->validatorSchema['orderField'] = new sfValidatorChoice(array(
      'choices' => array_keys($orderFields)
    ));
    $this->widgetSchema['orderField']->setLabel("Order by");

    // Order type selection
    $orderTypes = $this->getOrderTypes();
    $this->widgetSchema['orderType']    = new sfWidgetFormSelect(array(
      'choices' => $orderTypes
    ));
    $this->validatorSchema['orderType'] = new sfValidatorChoice(array(
      'choices' => array_keys($orderTypes)
    ));
    $this->setDefaults($this->getDefaultsFromLastUpdated(array('maxPerPage', 'navTop', 'navBottom', 'view', 'orderField', 'orderType')));
    $this->widgetSchema['orderType']->setLabel("Order type");
     
  }
  
  protected function getAvailableOrderFields()
  {
    $fields = array();
    
    $fields['date_start'] = "Date start";
    $fields['date_end'] = "Date end";


    return $fields;
  }

  protected function getOrderTypes()
  {
    return array(
      'asc'  => $this->__('Ascendant'),
      'desc' => $this->__('Descendant'),
      'rand' => $this->__('Random')
    );
  }



}
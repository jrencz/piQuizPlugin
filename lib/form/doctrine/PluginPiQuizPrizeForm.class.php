<?php

/**
 * PluginPiQuizPrize form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 */
abstract class PluginPiQuizPrizeForm extends BasePiQuizPrizeForm
{
  public function setup()
  {
    parent::setup();
    if ($this->object->exists())
    {
      $this->widgetSchema['delete'] = new sfWidgetFormInputCheckbox();
      $this->validatorSchema['delete'] = new sfValidatorPass();
    }  
    $this->widgetSchema['quantity'] = new sfWidgetFormInputQuantity();
    
  }
}
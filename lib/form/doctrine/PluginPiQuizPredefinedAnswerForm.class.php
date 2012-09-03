<?php

/**
 * PluginPiQuizPredefinedAnswer form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 */
abstract class PluginPiQuizPredefinedAnswerForm extends BasePiQuizPredefinedAnswerForm
{
  public function setup()
  {
    parent::setup();
    if ($this->object->exists())
    {
      $this->widgetSchema['delete'] = new sfWidgetFormInputCheckbox();
      $this->validatorSchema['delete'] = new sfValidatorPass();
    }
  }
}
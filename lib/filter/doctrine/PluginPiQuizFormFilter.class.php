<?php

/**
 * PluginPiQuiz form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginPiQuizFormFilter extends BasePiQuizFormFilter
{
  public function configure()
  { 
    $this->manageFieldIsPending();
    $this->manageFieldIsUpcoming();
    $this->manageFieldIsCurrent();
  }

  protected function manageFieldPending()
  {
    $this->widgetSchema['is_pending'] = new sfWidgetFormChoice(array(
      'choices' => array(
        '' => 'yes or no',
        1 => 'yes',
        0 => 'no'  
    )));
    $this->validatorSchema['is_pending'] = new sfValidatorPass();
  }

  protected function manageFieldUpcoming()
  {
    $this->widgetSchema['is_upcoming'] = new sfWidgetFormChoice(array(
      'choices' => array(
        '' => 'yes or no',
        1 => 'yes',
        0 => 'no'  
    )));
    $this->validatorSchema['is_upcoming'] = new sfValidatorPass();
  }

  protected function manageFieldCurrent()
  {
    $this->widgetSchema['is_current'] = new sfWidgetFormChoice(array(
      'choices' => array(
        '' => 'yes or no',
        1 => 'yes',
        0 => 'no'  
    )));
    $this->validatorSchema['is_current'] = new sfValidatorPass();
  }
  
  public function getFields()
  {
    $fields = parent::getFields();
    $fields['is_pending'] = 'is_pending';
    $fields['is_upcoming'] = 'is_upcoming';
    $fields['is_current'] = 'is_current';
    return $fields;
  }
  
  public function addIsPendingColumnQuery($query, $field, $value)
  {
    Doctrine::getTable('PiQuiz')->applyPendingFilter($query, $value);
  }
  
  public function addIsUpcomingColumnQuery($query, $field, $value)
  {
    Doctrine::getTable('PiQuiz')->applyUpcomingFilter($query, $value);
  }
  
  public function addIsCurrentColumnQuery($query, $field, $value)
  {
    Doctrine::getTable('PiQuiz')->applyCurrentFilter($query, $value);
  }      
}

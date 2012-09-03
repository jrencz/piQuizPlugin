<?php

/**
 * sfValidatorUniqueEmail validates emails and checks if it's unique in given circumstances
 *
 * @package    piQuizPlugin
 * @subpackage validator
 * @author     Jarek Rencz <jrencz@polibuda.info>
 */
class sfValidatorUniqueEmail extends sfValidatorEmail
{
  /**
   * @see sfValidatorEmail
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addRequiredOption('query');
  }
  
  /**
   * @see sfValidatorEmail
   */
  protected function doClean($value)
  {
    $clean = parent::doClean($value);

    $query = $this->getOption('query');
    
    $query->andWhere($query->getRootAlias() . ".email = ?", $value);
    
    if ($query->count() > 0)
      throw new sfValidatorError($this, 'not unique', array('value' => $value));

    return $clean;
  }
} 


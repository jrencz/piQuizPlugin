<?php

/**
 * PluginPiQuizResponse form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 */
abstract class PluginPiQuizResponseForm extends BasePiQuizResponseForm
{
  public function setup()
  {
    parent::setup();
    $this->setValidator('email', new sfValidatorEmail());
    $this->setValidator('prize_id', new sfValidatorDoctrineChoice(array(
      'model' => 'PiQuizResponse',
      'query' => Doctrine_Core::getTable('PiQuizPrize')->createQuery('p')->where('p.quiz_id = ?', $this->guessId()),  
    )));
  }
  
  public function guessId()
  { 
    $page = dmContext::getInstance()->getPage();
    $request = dmContext::getInstance()->getRequest();
    if ($page instanceof DmPage) {
      return $page->getRecord()->getId();
    } else if ($request->hasParameter('quiz_id')){
      return $request->getParameter('quiz_id');
    }
    
  }
}
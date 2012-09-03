<?php

class piQuizTakePartView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;
  
  public function configure()
  {
    parent::configure();
  }
  
  public function getJavascripts()
  {
    return array(
      'piQuizPlugin.takePartView'
    );
  }
   
  public function getStylesheets()
  {
    return array(
      'piQuizPlugin.takePartView'
    );
  }
  protected function doRender()
  { 
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $viewVars = $this->getViewVars();
        
    if (is_null($viewVars['recordId'])) 
    {
      $quiz = dmContext::getInstance()->getPage()->getRecord();
    }
    else
    {
      $quiz = PiQuizTable::getInstance()->find($viewVars['recordId']);
    }                               
    $response = new PiQuizResponse();
    $response->setQuizId($quiz->getId());
    $form = new PiQuizResponseFrontForm($response);
    
    $html = $this->getHelper()->renderPartial('quiz', 'piQuizTakePart', array('viewVars' => $viewVars, 'quiz' => $quiz, 'form' => $form, ));  

    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
    
  }
}
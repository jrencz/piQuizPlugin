<?php

class piQuizListView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;
  
  public function configure()
  {
    parent::configure();
  }
  
  /**
   * adds variables needed by ajax pager
   * 
   * @see quizComponents
   */
  public function getViewVars()
  {
    $viewVars = parent::getViewVars(); 
    
    $viewVars['page'] = $this->getService('request')->getParameter('page', 1);
    $viewVars['dm_widget'] = $this->widget;
    
    return $viewVars;
    
  }
  
  protected function doRender()
  { 
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
        
    $html = $this->getHelper()->renderComponent('quiz', 'list', $this->getViewVars());
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
    
  }
}
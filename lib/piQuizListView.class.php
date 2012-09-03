<?php

class piQuizListView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;
  
  public function configure()
  {
    parent::configure();
    
    $this->addJavascript(array(
      'piQuizPlugin.listView'
    ));
    
    $this->addStylesheet(array(
      'piQuizPlugin.listView'
    ));

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
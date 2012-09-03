<?php
/**
 * Quiz components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 * 
 * 
 * 
 */
class quizComponents extends myFrontModuleComponents
{

  public function executeList()
  {
    $query = $this->getListQuery();
    
    $this->quizPager = $this->getPager($query);
    
    
  }


}

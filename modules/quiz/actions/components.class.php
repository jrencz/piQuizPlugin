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
    
    if ($this->onlyNotArchived)
    {
      $query->andWhere('
        (' . $query->getRootAlias() . '.date_end > NOW()) 
        OR 
        (' . $query->getRootAlias() . '.is_resolved = ?)', 0
      );
    }
 
    $this->quizPager = $this->getPager($query);
    $this->quizPager->setOption('ajax', true);

  }
}

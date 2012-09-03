<?php

/**
 * PluginPiQuizTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginPiQuizTable extends myDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object PluginPiQuizTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PluginPiQuiz');
    }
    
    /**
     * Applies is_pending attribute to a given query
     *
     * @param Doctrine_Query $query
     * @param Integer $value - is pending?
     */
    static public function applyPendingFilter($query, $value)
    {
      $rootAlias = $query->getRootAlias();
      switch ($value)
      {
        case '0':
          $query
            ->where('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_end) > ?', 0)
            ->orWhere('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_start) < ?', 0)
   	        ->orWhere($rootAlias.'.is_resolved = ?', 1);
          break;
        case '1':
          $query
            ->where('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_end) < ?', 0)
     	      ->andWhere($rootAlias.'.is_resolved = ?', 0);
          break;
      }
      return $query;
    }
    /**
     * Applies is_upcoming attribute to a given query
     *
     * @param Doctrine_Query $query
     * @param Integer $value - is pending?
     */
    static public function applyUpcomingFilter($query, $value)
    {
      $rootAlias = $query->getRootAlias();
      switch ($value)
      {
        case '0':
          $query
            ->where('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_start) < ?', 0);
          break;
        case '1':
          $query
            ->where('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_start) > ?', 0);
          break;       
      }
      return $query;
    }
    /**
     * Applies is_current attribute to a given query
     *
     * @param Doctrine_Query $query
     * @param Integer $value - is current?
     */
    static public function applyCurrentFilter($query, $value)
    {
      $rootAlias = $query->getRootAlias();
      switch ($value)
      {
        case '0':
          $query
            ->where('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_start) > ?', 0)
            ->orWhere('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_end) < ?', 0);
          break;
        case '1':
          $query
            ->where('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_start) < ?', 0)
            ->andWhere('TIMESTAMPDIFF(SECOND, NOW(), '.$rootAlias.'.date_end) > ?', 0);
          break;    
      }
      return $query;
    }     
}
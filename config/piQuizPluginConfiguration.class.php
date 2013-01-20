<?php

/**
 * piQuizPlugin configuration.
 * 
 * @package     piQuizPlugin
 * @subpackage  config
 * @author      Programiści Polibuda.info
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class piQuizPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  { 
    $this->dispatcher->connect('dm.context.loaded', array($this,'listenToDmContextLoaded'));
  }
  public function listenToDmContextLoaded(sfEvent $e)
  {
    if($this->configuration instanceof dmAdminApplicationConfiguration)
    {
      $this->createPermissions();
    }
  }
  
  protected function createPermissions()
  { 
    foreach(array(
      'pi_quiz_creator'   => 'Can create new quizzes. Has control over prizes and predefined answers but only in quiz interface. Cannot resolve quizzes', 
      'pi_quiz_resolver'  => 'Can resolve quizzes',
      'pi_quiz_superuser' => 'Has full controll over quizzes, prizes, predefined answers and responses',  
    ) as $permission_name => $permission_description) {
      $result = Doctrine_Query::create()
                      ->select('p.id')
                      ->from('DmPermission p')
                      ->where('p.name = ?', $permission_name)
                      ->count();

      if($result < 1)
      {
        $permission = new DmPermission();
        $permission->name = $permission_name;
        $permission->description = $permission_description;
        $permission->save();
      }
    }
  }
}

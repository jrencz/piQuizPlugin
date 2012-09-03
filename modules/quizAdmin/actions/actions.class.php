<?php

require_once dirname(__FILE__).'/../lib/quizAdminGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/quizAdminGeneratorHelper.class.php';

/**
 * quizAdmin actions.
 *
 * @package    Polibuda.info
 * @subpackage quizAdmin
 * @author     Programiści Polibuda.info
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class quizAdminActions extends autoQuizAdminActions
{ 
  const RESOLVER = "resolver";
  const CREATOR  = "creator";
  
  /**
   * Executes Edit action. Prevents users with insufficient credentials to edit resolved quizzes
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  public function executeEdit(sfWebRequest $request)
  { 
    $this->fetchQuizById( $request->getParameter('pk') );
    
    $start = new DateTime($this->quiz->getDateStart());
    $now   = new DateTime;
    
    if (($start > $now) || $this->getUser()->can('pi_quiz_superuser')) {
      parent::executeEdit($request);
    } else {
      $this->getUser()->setFlash(
        'notice', 
        join(" ", array(
          $this->getService('i18n')->__("Cannot edit current, pending or resolved quizzes."), 
          $this->getService('i18n')->__("Insufficient permissions.")
        )));
      $this->redirect($this->getModuleName().'/index');
    }
  }
  /**
   * Executes Upcoming Report action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jrencz@polibuda.info>
   */
  public function executeReportUpcoming(sfWebRequest $request)
  { 
    $this->getUser()->setFlash(
      'notice', 
      sprintf(
        $this->getService('i18n')->__("Filter applied: %s"), 
        $this->getService('i18n')->__("Upcoming quizzes")
      ));
    
    $this->setFilters(array('is_upcoming'=> 1));
 
    $this->forward($this->getModuleName(), 'index');

  }
  
  /**
   * Executes Current Report action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jrencz@polibuda.info>
   */
  public function executeReportCurrent(sfWebRequest $request)
  { 
    $this->getUser()->setFlash(
      'notice', 
      sprintf(
        $this->getService('i18n')->__("Filter applied: %s"), 
        $this->getService('i18n')->__("Current quizzes")
      ));
    
    $this->setFilters(array('is_current'=> 1));
 
    $this->forward($this->getModuleName(), 'index');

  }
  
  /**
   * Executes Pending Report action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jrencz@polibuda.info>
   */
  public function executeReportPending(sfWebRequest $request)
  {
    $this->getUser()->setFlash(
      'notice', 
      sprintf(
        $this->getService('i18n')->__("Filter applied: %s"), 
        $this->getService('i18n')->__("Pending quizzes")
      ));
    
    $this->setFilters(array('is_pending'=> 1));
 
    $this->forward($this->getModuleName(), 'index');

  }
  
  /**
   * Executes Resolved Report action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jrencz@polibuda.info>
   */
  public function executeReportResolved(sfWebRequest $request)
  {
    $this->getUser()->setFlash(
      'notice', 
      sprintf(
        $this->getService('i18n')->__("Filter applied: %s"), 
        $this->getService('i18n')->__("Resolved quizzes")
      ));
    
    $this->setFilters(array('is_resolved'=> 1));
 
    $this->forward($this->getModuleName(), 'index');

  }
  
  /**
   * Executes Resolve action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  public function executeResolve(sfWebRequest $request)
  { 
    $this->fetchQuizById( $request->getParameter('id') );
    
    if ( ( ! $request->hasParameter('nextStep')) && $this->quiz->getType() !== 'PREDEFINED' ) 
    {
      $this->fetchResponses($request);
    }
    else if ( $request->hasParameter('nextStep') || $this->quiz->getType() === 'PREDEFINED' )
    {
      switch ( $request->getParameter('nextStep') ) {
        case '2':
          $this->storeCorrectResponses($request);
          $this->fetchCorrectResponses();
          $this->fetchPrizes();
          break;
        case '3':
          $this->storeWinners($request);
          $this->prepareMessagePreview();
          $this->fetchPrizes();
          break;
        case '4':
          $this->notifyWinners($request);
          $this->sendWinnerList($this->quiz);
          $this->quiz->setIsResolved(1)->save();
          $this->forward($this->getModuleName(), 'showWinnerList');
          break;
        default:
          if ($this->quiz->getType() === 'PREDEFINED') {
            $this->fetchCorrectResponses();
            $this->fetchPrizes();  
          }
          break;
      }
    } 
  }
  
  /**
   * Fetches quiz from database
   * 
   * @param integer $id Quiz id
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function fetchQuizById($id)
  {
    $this->quiz = PiQuizTable::getInstance()->find($id);
  }
  
  /**
   * Processes 1st step of Quiz resolving
   * 
   * Gets all responses for given qiuz and
   * 
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function fetchResponses(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $this->forward404Unless($request->hasParameter('id'));
    
    $this->getUser()->setFlash('notice', "Exact matches and previously marked were marked as correct. Your task is to check the match.");

    $this->responses = PiQuizResponseTable::getInstance()
      ->createQuery('r')
      ->where('r.quiz_id = ?', $this->quiz->getId())
      ->fetchRecords();
  }
  
  /**
   * Stores responses marked as correct to database
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function storeCorrectResponses(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $this->forward404Unless($request->hasParameter('ids'));
                             
    $this->batchSetTrue((array) $request->getParameter('ids'), 'is_open_answer_correct');
    
    $this->getUser()->setFlash('notice', "Choose winners. You can select only as many winners as there are prizes");
  }
  
  /**
   * Fetches prizes for quiz from database
   *
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function fetchPrizes()
  {
    if (!($this->prizes instanceOf DoctrineCollection))
    {
      $this->prizes = PiQuizPrizeTable::getInstance()->createQuery('p')
        ->where('p.quiz_id = ?', $this->quiz->getId())->fetchRecords();
    }
  }
  
  /**
   * Fetches responses for quiz marked as correct from database
   *
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function fetchCorrectResponses()
  { 
    $correctResponses = PiQuizResponseTable::getInstance()->createQuery('r')
                                ->where('r.quiz_id = ?', $this->quiz->getId() );
    
    switch ($this->quiz->getType()) {
      case 'OPEN':
          $correctResponses->andWhere('r.is_open_answer_correct = ?', 1);
        break;
      case 'PREDEFINED':
          $correct_answers = PiQuizPredefinedAnswerTable::getInstance()->createQuery('pa')
                              ->select('pa.id')
                              ->where('pa.quiz_id = ?', $this->quiz->getId())
                              ->andWhere('pa.is_correct = ?', 1)
                              ->fetchFlat();
          $correctResponses->andWhereIn( 'r.predefined_answer_id', $correct_answers );          
        break;
      default:
        break;
    }
    $this->correctResponses = $correctResponses->fetchRecords();
  }
  
  /**
   * Stores responses marked as winners to database
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function storeWinners(sfWebRequest $request)
  {
    $this->forward404Unless( $request->isMethod(sfRequest::POST) );
    $this->forward404Unless( $request->hasParameter('ids') );
    
    $this->batchSetTrue((array) $request->getParameter('ids'), 'is_winner');
  }
  
  /**
   * Gets email template from database and replaces non-editable variables for preview
   *
   * @author Jarek Rencz <jaroslaw@rencz.pl>  
   */
  private function prepareMessagePreview()
  {
    $this->getUser()->setFlash('notice', "Write an email for winners. It will be sent after you save it. Note there's no going back after this point.");
            
    $vars = array(
      '%name%',
      '%surname%',
      '%user_email%',
      '%quiz_name%',
    );

    $values = array(
      $this->getService('i18n')->__('Winner name'),
      $this->getService('i18n')->__('Surname'),
      'user@example.com',
      $this->quiz->getName(),
    );
    
    $this->template = str_replace($vars, $values, DmMailTemplateTable::getInstance()->createQuery('mt')->where('mt.name = ?', sfConfig::get('app_piQuizPlugin_winnerNotificationEmailTemplate'))->fetchRecord()->getBody());
  }
  
  /**
   * Sands notification emails to quiz winners
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function notifyWinners(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $this->forward404Unless( $request->hasParameter('prize_id') );
    $this->forward404Unless( $request->hasParameter('message') );
    $this->forward404Unless( $request->hasParameter('shipment') );
    
    $message  = $request->getParameter('message');
    $shipment = $request->getParameter('shipment');
    
    foreach($request->getParameter('prize_id') as $key => $prize_id) {
      $this->winners = $this->getWinners( $this->quiz, $prize_id );
      foreach($this->winners as $winner) 
      {
        try {
          $this->getService('mail')
          ->setTemplate(sfConfig::get('app_piQuizPlugin_winnerNotificationEmailTemplate'))
          ->addValues(array(
            'name'              => $winner->getName(),
            'surname'           => $winner->getSurname(),
            'user_email'        => $winner->getEmail(),
            'quiz_name'         => $this->quiz->getName(),
            'prize_name'        => $winner->getPrizes()->getName(),
            'shipment'          => $shipment[$key],
            'message'           => $message[$key] ,
            'resolver'          => $this->getUser()->getGuardUser()->getEmail(),  
          ))
          ->send();
        } catch (Exception $e) {
          //die($e);
        }
      }  
    }
  }
  /**
   * Executes showWinnerList action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  public function executeShowWinnerList(sfWebRequest $request)
  { 
    $this->forward404Unless($request->hasParameter('id'));
    
    $this->fetchQuizById( $request->getParameter('id') );
    $this->winners = $this->getWinners($this->quiz);
  }
  
  /**
   * Fetches winners from datatbase
   * Can fetch winners for entire quiz or for given prize only
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   * @return DoctrineCollection
   */
  private function getWinners ( PiQuiz $quiz, $prize_id = null )
  { 
    $query = PiQuizResponseTable::getInstance()->createQuery('r')
      ->where('r.quiz_id = ?', $quiz->getId() )
      ->andWhere('r.is_winner = ?', 1)
      ->leftJoin('r.Prizes p ON r.prize_id = p.id')
      ->orderBy('r.prize_id');
    
    if ($quiz->getType() === "OPEN")
      $query->andWhere('r.is_open_answer_correct = ?', 1);
    
    if (!is_null($prize_id))
    {
      $query->andWhere('r.prize_id = ?', $prize_id );
    }
    
    return $query->fetchRecords();
  }
  
  /**
   * Executes sendWinnerList action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  public function executeSendWinnerList(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->fetchQuizById( $id  );
    
    if ( $request->hasParameter('to') ) 
    {
      $this->sendWinnerList($this->quiz, $request->getParameter('to'));      
    }
    else
    {
      $this->sendWinnerList($this->quiz);
    }
    
    if ($referer = strstr($request->getReferer(),"/id", true)) {
      $refererAction = substr(strrchr($referer, "/"), 1);
    } else {
      $refererAction = substr(strrchr($request->getReferer(), "/"), 1);
    } 
                                                           
    $this->redirect($this->getModuleName() . '/' . $refererAction.'?id=' . $id);
  }
  
  /**
   * Sends Winner list for given Quiz to creator, resolver or any other given email address
   *
   * @param PiQuiz $quiz A request object
   * @param $to email address or address constant
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  private function sendWinnerList(PiQuiz $quiz, $to = self::RESOLVER)
  { 
    switch ($to) {
      case self::RESOLVER:
        $guardUser = $this->getUser()->getGuardUser();
        $address = $guardUser->getEmail();
        $name    = $guardUser->getFirstName();
        $surname = $guardUser->getSurname();
        break;
      case self::CREATOR:
        $creator = $quiz->getCreatedBy();
        $address = $creator->getEmail();
        $name    = $creator->getFirstName();
        $surname = $creator->getSurname();
        break;      
      default:
        $address = $to;
        $name    = "";
        $surname = "";
        break;
    }
    
    try {
      $this->getService('mail')
      ->setTemplate(sfConfig::get('app_piQuizPlugin_resolverNotificationTemplate'))
      ->addValues(array(
        'name'              => $name,
        'surname'           => $surname,
        'user_email'        => $address,
        'quiz_name'         => $quiz->getName(),
        'message'           => $this->getPartial('winnersListTable', array('winners' => $this->getWinners($quiz))),  
      ))
      ->send();
      $this->getUser()->setFlash('notice', "Notification sent");
    } catch (Exception $e) {
      die($e);
    }
  }
  
  /**
   * Sets value to true for given quiz ids and stores it to database
   *
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   * @param array @ids array of ids
   * @param $field Field to be set true
   */
  protected function batchSetTrue(array $ids, $field)
  {
    $query = PiQuizResponseTable::getInstance()->createQuery('r')->whereIn('id', $ids);
    
    foreach($query->fetchRecords() as $record)
    {
      $record->set($field, 1);
      $record->save();
    }
  }
  
  /**
   * Executes Close and resolve action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  public function executeCloseAndResolve(sfWebRequest $request)
  {
    $this->forward404Unless( $request->hasParameter('id') );
    
    $this->fetchQuizById( $request->getParameter('id') );
    
    if (strtotime($this->quiz->getDateEnd())>time()) {
      $this->quiz->setDateEnd(date('Y-m-d H:i:s'))->save();
    }
      
    $this->forward($this->getModuleName(), 'resolve');
  }
  
  /**
   * Executes StartNow action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jaroslaw@rencz.pl>
   */
  public function executeStartNow(sfWebRequest $request)
  { 
    $this->forward404Unless( $request->hasParameter('id') );
    
    $this->fetchQuizById( $request->getParameter('id') );

    if (strtotime($this->quiz->getDateStart())>time()) {
      $this->quiz->setDateStart(date('Y-m-d H:i:s'))->save();
      $this->getUser()->setFlash('notice', sprintf($this->getService('i18n')->__("Start date set to %s"), date('Y-m-d H:i:s'))); 
    } else {
      $this->getUser()->setFlash('notice', "Quiz already started");       
    }
    
    $this->forward($this->getModuleName(), 'index');
    
  }
  /**
   * Translates table elements
   * Copied with modified path to get this functionality for users with no 'automatic_metas' credential
   *
   * @see BasedmPageActions
   */
  public function executeTableTranslation()
  { 
    $base = realpath(dirname(__FILE__).'/../../../../..').'/lib/vendor/diem/dmAdminPlugin/modules/dmPage/data/dataTableTranslation/';
    $translationFile = $base . $this->getUser()->getCulture().'.txt';

    if(!file_exists($translationFile))
    {
      $translationFile = $base . 'en.txt';
    }
    
    return $this->renderText(file_get_contents($translationFile));
  }
}

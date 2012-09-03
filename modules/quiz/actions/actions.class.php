<?php
/**
 * Quiz actions
 */
class quizActions extends myFrontModuleActions
{
  /**
   * Executes ajaxRegisterResponse action
   *
   * @param sfRequest $request A request object
   * @author Jarek Rencz <jrencz@polibuda.info>
   */
  public function executeAjaxRegisterResponse(sfWebRequest $request)
  { 
    $this->responseObject = new piResponseObject();
    
    $this->fetchQuizById( $request->getParameter('quiz_id') );
    
    try {
      $quizResponse = $this->createNewPiQuizResponse($request);
      
      $form = $this->createFormAndBind($quizResponse);
            
      if ($form->hasErrors()) {
        $this->responseObject->setStatusError();
        $this->responseObject->setData(array(
          'html'   => $form->render(array(
            'action' => $this->getController()->genUrl('quiz/ajaxRegisterResponse')
          ))
        ));      
      } 
      else
      { 
        $this->doSendConfirmation($quizResponse->saveGet()); 

        $this->responseObject->setStatusSuccess();
        $this->responseObject->setData(array(
          'html'   => $this->getHelper()->tag('div', join(' ', array(
            $this->__('Your response has been added but it\'s not verified yet.'), 
            $this->__('Check your e-mail and click verification link.')
          ))),
        ));
      }
    } catch (Exception $e) {
      die($e);          
    }
    

    $this->getResponse()->setContentType('application/json');
    
    return $this->renderText(json_encode($this->responseObject));

  }
  

  
  
  
  private function createNewPiQuizResponse(sfWebRequest $request)
  {
    $quizResponse = new PiQuizResponse();
    $quizResponse->setIp($this->getRequest()->getHttpHeader ('addr','remote'))
                 ->setName($request->getParameter('name'))
                 ->setSurname($request->getParameter('surname'))
                 ->setEmail($request->getParameter('email'))
                 ->setQuizId($this->quiz->getId())
                 ->setPrizeId($request->getParameter('prize_id'));
                 
    if ( $this->quiz->getType() === "OPEN" ) 
      $quizResponse->setOpenAnswer($request->getParameter('open_answer'));
    
    if ( $this->quiz->getType() === "PREDEFINED" ) 
      $quizResponse->setPredefinedAnswerId($request->getParameter('predefined_answer_id'));    
    
    return $quizResponse;
  }
  
  private function createFormAndBind(PiQuizResponse $quizResponse)
  { 
    $form = new PiQuizResponseFrontForm();
    $form->removeCsrfProtection();
    
    $values = array(
      'name'     => $quizResponse->getName(), 
      'surname'  => $quizResponse->getSurname(), 
      'email'    => $quizResponse->getEmail(), 
      'quiz_id'  => $quizResponse->getQuizId(), 
      'prize_id' => $quizResponse->getPrizeId(), 
    );

    switch ($this->quiz->getType()) {
      case 'OPEN':
        $values['open_answer'] = $quizResponse->getOpenAnswer();
        break;
      case 'PREDEFINED':
        $values['predefined_answer_id'] = $quizResponse->getPredefinedAnswerId();
        break;
    }
    $form->bind($values);
    return $form;
  } 
  
  
  private function doSendConfirmation(PiQuizResponse $response) {
    try {
      
      $this->getService('mail')
      ->setTemplate(sfConfig::get('app_piQuizPlugin_responseVerificationTemplate'))
      ->addValues(array(
        'name'              => $response->getName(),
        'surname'           => $response->getSurname(),
        'user_email'        => $response->getEmail(),
        'quiz_name'         => $response->getQuiz()->getName(),
        'verification_link' => $this->getHelper()->link("@piQuizVerification?quizId=" . $response->getQuiz()->getId() . "&token=" . $response->getToken())->getAbsoluteHref(),
      ))
      ->send();
      return true;
    } catch (Exception $e) {
      return false;
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
  
  private function __($string)
  {
    return $this->context->getI18N()->__($string);
  }

}



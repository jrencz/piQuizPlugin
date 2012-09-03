<td>
  <ul class="sf_admin_td_actions">
    <?php
      if (($sf_user->can("pi_quiz_resolver") || $sf_user->can("pi_quiz_superuser")) && (!$pi_quiz->get('is_resolved') && !(strtotime($pi_quiz->get('date_end'))>time()))) {
        echo _tag('li.sf_admin_action_resolve', link_to(__('Resolve', array(), 'dm'), 'quizAdmin/resolve?id='.$pi_quiz->get('id'), array('class' => 's16 s16_tick',)));
      }
      if (($sf_user->can("pi_quiz_resolver") || $sf_user->can("pi_quiz_superuser")) && (!$pi_quiz->get('is_resolved') && (strtotime($pi_quiz->get('date_start'))<time() && strtotime($pi_quiz->get('date_end'))>time()))) {
        echo _tag('li.sf_admin_action_close_and_resolve', link_to(__('Close now and resolve', array(), 'dm'), 'quizAdmin/closeAndResolve?id='.$pi_quiz->get('id'), array('class' => 's16 s16_status_busy',)));
      }
      if (strtotime($pi_quiz->get('date_start'))>time()) {
        echo _tag('li.sf_admin_action_start_now', link_to(__('Start now', array(), 'dm'), 'quizAdmin/startNow?id='.$pi_quiz->get('id'), array('class' => 's16 s16_status_ok',)));        
      }
      if ($pi_quiz->get('is_resolved')) {
        echo _tag('li.sf_admin_action_show_winner_list', link_to(__('Show winner list', array(), 'dm'), 'quizAdmin/showWinnerList?id='.$pi_quiz->get('id'), array('class' => 'pi_quiz_action pi_quiz_action_showWinnerList',)));
        if (($sf_user->can("pi_quiz_resolver") || $sf_user->can("pi_quiz_superuser"))) {
          echo _tag('li.sf_admin_action_resend_winner_list', link_to(__('Resend winner list', array(), 'dm'), 'quizAdmin/sendWinnerList?id='.$pi_quiz->get('id'), array('class' => 'pi_quiz_action pi_quiz_action_sendWinnerList',)));          
        }
      }
    ?>
  </ul>
</td>

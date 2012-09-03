<?php use_stylesheet('piQuizPlugin.quizList'); ?>
<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()): ?>
    <h2><?php echo __('No result') ?></h2>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th><input class="sf_admin_list_batch_checkbox" type="checkbox" /></th>
          <?php include_partial('quizAdmin/list_th_tabular', array('sort' => $sort)) ?>
          <th class="sf_admin_list_th_actions"><?php echo __('Actions') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th><input class="sf_admin_list_batch_checkbox" type="checkbox" /></th>
          <?php include_partial('quizAdmin/list_th_tabular', array('sort' => $sort)) ?>
          <th class="sf_admin_list_th_actions"><?php echo __('Actions') ?></th>
        </tr>
      </tfoot>
      <tbody class='{toggle_url: "<?php echo £link('@'.$helper->getUrlForAction('toggleBoolean'))->getHref() ?>"}'>
        <?php foreach ($pager->getResults() as $i => $pi_quiz): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
          <tr class="sf_admin_row <?php echo $odd ?> {pk: <?php echo $pi_quiz->getPrimaryKey() ?>}">
            <td>
              <input type="checkbox" name="ids[]" value="<?php echo $pi_quiz->getPrimaryKey() ?>" class="sf_admin_batch_checkbox" />
            </td>
            <?php include_partial('quizAdmin/list_td_tabular', array('pi_quiz' => $pi_quiz)) ?>
            <?php include_partial('quizAdmin/list_td_actions', array('pi_quiz' => $pi_quiz)) ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
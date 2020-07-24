<?php

namespace Drupal\openideal_idea\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\votingapi_widgets\Form\BaseRatingForm;
use Drupal\votingapi\VoteResultFunctionManager;

/**
 * Form controller for OpenidealBaseRatingForm class.
 *
 * Extend the votingapi_widgets module base form
 * and add the ability to cancel/delete the vote,
 * in like widget.
 *
 * @Todo: remove once the ability to cancel/delete vote will be added.
 * https://www.drupal.org/project/votingapi_widgets/issues/3051783
 * https://www.drupal.org/project/votingapi_widgets/issues/2831545
 */
class OpenidealBaseRatingForm extends BaseRatingForm {

  /**
   * {@inheritDoc}
   */
  public function __construct(VoteResultFunctionManager $votingapi_result, EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL) {
    parent::__construct($votingapi_result, $entity_repository);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Add the ability to delete vote.
    $form = parent::buildForm($form, $form_state);

    $form['delete'] = [
      '#type' => 'submit',
      '#value' => $this->t('delete'),
      '#attributes' => ['class' => ['openideal-votes-like-delete', 'hidden']],
      '#ajax' => [
        'callback' => [$this, 'ajaxSubmitDelete'],
        'event' => 'click',
        'wrapper' => $form['#attributes']['id'],
        'progress' => [
          'type' => NULL,
        ],
      ],
    ];
    $entity = $this->getEntity();
    $form['submit']['#attributes']['class'][] = 'openideal-votes-like-submit';
    $form['#attached']['drupalSettings']['openidealUser']['voted'] = !$entity->isNew();
    $form['#attached']['drupalSettings']['openidealUser']['comment'] = $entity->entity_type->value == 'comment';
    return $form;
  }

  /**
   * Ajax deletion submit handler.
   */
  public function ajaxSubmitDelete(array $form, FormStateInterface $form_state) {
    // Delete the vote and recalcute results via voting plugin manager.
    $entity = $this->getEntity();
    $entity->delete();
    $this->votingapiResult->recalculateResults($entity->getVotedEntityType(), $entity->getVotedEntityId(), $entity->getEntityTypeId());

    // Values taken from parent::ajaxSubmit with appropriate adjustments.
    $settings = $form_state->get('settings');
    $result_function = $this->getResultFunction($form_state);
    $plugin = $form_state->get('plugin');
    $result_value = $this->getResults($result_function, TRUE);

    $form['value']['#attributes']['data-show-own-vote'] = 'true';
    $form['value']['#default_value'] = 0;

    if ($settings['show_own_vote'] == '0') {
      $form['value']['#attributes']['data-show-own-vote'] = 'false';
      $form['value']['#default_value'] = $result_value;
    }

    $form['value']['#attributes']['data-vote-value'] = 0;
    $form['value']['#attributes']['data-result-value'] = $result_value;
    if ($settings['show_results'] == '1') {
      $form['result']['#children']['result'] = $plugin->getVoteSummary($entity);
    }

    $form['#attached']['drupalSettings']['openidealUser']['voted'] = FALSE;

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function ajaxSubmit(array $form, FormStateInterface $form_state) {
    $form = parent::ajaxSubmit($form, $form_state);
    // The validation provided by module not working because value in
    // settings is integer and compared that value with string. Comparison
    // executes with strict comparison - "===".
    if ($form_state->get('settings')['show_results'] == '1') {
      $form['result']['#children']['result'] = $form_state->get('plugin')->getVoteSummary($this->getEntity());
    }

    $form['#attached']['drupalSettings']['openidealUser']['voted'] = TRUE;

    return $form;
  }

}

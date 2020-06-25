<?php

namespace Drupal\openideal_idea\Plugin\votingapi_widget;

use Drupal\votingapi_widgets\Plugin\votingapi_widget\LikeWidget;

/**
 * Openideal Useful Widget.
 *
 * @VotingApiWidget(
 *   id = "openideal_useful",
 *   label = @Translation("Openideal Like"),
 *   values = {
 *    1 = @Translation("Opdenideal Like"),
 *   },
 * )
 */
class OpenidealLikeWidget extends LikeWidget {

  /**
   * {@inheritdoc}
   */
  public function buildForm($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name, $settings) {
    $form = parent::buildForm($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name, $settings);
    $form['#attached']['library'] = ['openideal_idea/useful'];
    return $form;
  }

}

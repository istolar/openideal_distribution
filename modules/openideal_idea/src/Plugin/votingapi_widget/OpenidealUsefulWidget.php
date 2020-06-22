<?php

namespace Drupal\openideal_idea\Plugin\votingapi_widget;

use Drupal\votingapi_widgets\Plugin\votingapi_widget\UsefulWidget;

/**
 * Openideal Useful Widget.
 *
 * @VotingApiWidget(
 *   id = "openideal_useful",
 *   label = @Translation("Openideal Usefull rating"),
 *   values = {
 *    1 = @Translation("Not so poor"),
 *   },
 * )
 */
class OpenidealUsefulWidget extends UsefulWidget {

  /**
   * {@inheritdoc}
   */
  public function buildForm($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name, $style, $show_results, $read_only = FALSE) {
    $form = $this->getForm($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name, $style, $show_results, $read_only);
    $build = [
      'rating' => [
        '#theme' => 'container',
        '#attributes' => [
          'class' => [
            'votingapi-widgets',
            'openideal-idea-useful',
            ($read_only) ? 'read_only' : '',
          ],
        ],
        '#children' => [
          'form' => $form,
        ],
      ],
      '#attached' => [
        'library' => ['openideal_idea/useful'],
      ],
    ];
    return $build;
  }

}

<?php

namespace Drupal\openideal_challenge\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Class ChallengeIdeasTitleWithCount.
 *
 * @Block(
 *   id = "openidel_challenge_challenge_ideas_title",
 *   admin_label = @Translation("Challenge ideas title with ideas count"),
 *   category = @Translation("Openideal"),
 *   context = {
 *      "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class ChallengeIdeasTitleWithCount extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $contexts = $this->getContexts();
    $build = [];
    $build['#theme'] = 'challenge_title_with_count';
    if (isset($contexts['node'])
      && !$contexts['node']->getContextValue()->isNew()) {
      $node = $contexts['node']->getContextValue();

      $build['#count'] = [
        '#lazy_builder' => ['openideal_statistics.lazy_builder:getChallengeIdeas', [$node->id()]],
        '#create_placeholder' => TRUE,
      ];
      $build['#cache']['tags'] = ['node_list:idea'];
    }

    return $build;
  }

}

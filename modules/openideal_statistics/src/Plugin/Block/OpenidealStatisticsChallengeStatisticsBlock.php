<?php

namespace Drupal\openideal_statistics\Plugin\Block;

/**
 * Provides a 'OpenidealStatisticsChallengeStatisticsBlock' block.
 *
 * @Block(
 *  id = "openideal_statistics_challenge_statistics",
 *  admin_label = @Translation("Challenge statistics block"),
 *   context = {
 *      "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class OpenidealStatisticsChallengeStatisticsBlock extends OpenidealStatisticsIdeaStatisticsBlock {

  /**
   * {@inheritdoc}
   */
  public function build($challenge = NULL) {
    $build = parent::build(TRUE);

    unset($build['#content']['overall_score']);
    unset($build['#content']['votes']);
    return $build;
  }

}

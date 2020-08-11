<?php

namespace Drupal\openideal_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'OpenidealUserPoints' block.
 *
 * @Block(
 *  id = "openideal_user_user_points_block",
 *  admin_label = @Translation("User points"),
 *   context = {
 *      "user" = @ContextDefinition(
 *       "entity:user",
 *       label = @Translation("Current user"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class OpenidealUserPoints extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $contexts = $this->getContexts();
    if (isset($contexts['user']) && !$contexts['user']->getContextValue()->isNew()) {
      /** @var \Drupal\user\Entity\User $user */
      $user = $contexts['user']->getContextValue();
      $user_points = $user->field_points;
      $value = $user_points->isEmpty() ? 0 : $user_points->first()->view(['settings' => ['scale' => 0]]);
      $build['container'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('User points: @value', ['@value' => $value]),
      ];
      $build['#cache']['tags'] = $user->getCacheTags();
    }

    return $build;
  }

}

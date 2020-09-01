<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\openideal_challenge\OpenidealContextEntityTrait;

/**
 * Provides a 'Node tags' block.
 *
 * @Block(
 *  id = "openideal_idea_tags_block",
 *  admin_label = @Translation("Node tags"),
 *   context = {
 *      "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class OpenidealIdeaTags extends BlockBase {

  use OpenidealContextEntityTrait;

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    if ($node = $this->getEntity($this->getContexts())) {
      $build = [
        '#theme' => 'item_list',
        '#title' => $this->t('Tags'),
        '#attributes' => ['class' => ['idea-tags']],
      ];
      $items = [];
      foreach ($node->field_idea_tags as $tag) {
        $items[] = $tag->entity->label();
      }
      $build['#items'] = $items;
      $build['#cache']['tags'] = $node->getCacheTags();
    }

    return $build;
  }

}

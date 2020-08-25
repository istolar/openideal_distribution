<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a OpenidealContentContentTypeLabel class.
 *
 * @Block(
 *   id = "openidel_idea_node_bundle",
 *   admin_label = @Translation("Node bundle"),
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
class OpenidealIdeaContentTypeLabel extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $build = [];
    $contexts = $this->getContexts();
    // If displayed in layout builder node isn't presented.
    if (isset($contexts['node']) && ($node = $contexts['node']->getContextValue()) && !$node->isNew()) {
      $build['content_type'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => ['class' => ['node_bundle_label', 'node_bundle_label--' . $node->bundle()]],
        '#value' => $node->bundle(),
      ];
    }

    return $build;
  }

}

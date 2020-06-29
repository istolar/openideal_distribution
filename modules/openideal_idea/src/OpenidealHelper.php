<?php

namespace Drupal\openideal_idea;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Class OpenidealHelper.
 */
class OpenidealHelper {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * OpenidealHelper construct.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get the group from "group" module.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node.
   *
   * @return \Drupal\group\Entity\Group
   *   Group.
   */
  public function getGroupByNode(NodeInterface $node) {
    // Get the group_content - gnode.
    $group_contents = $this->entityTypeManager
      ->getStorage('group_content')
      ->loadByEntity($node);

    // Don't need to check all of group contents,
    // such as they all from one group.
    $group_content = reset($group_contents);

    return $group_content->getGroup();
  }

}

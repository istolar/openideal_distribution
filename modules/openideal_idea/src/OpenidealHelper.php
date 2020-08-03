<?php

namespace Drupal\openideal_idea;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\group\GroupMembershipLoader;
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
  protected $entityTypeManager;

  /**
   * Group member ship loader.
   *
   * @var \Drupal\group\GroupMembershipLoader
   */
  protected $groupMembershipLoader;

  /**
   * OpenidealHelper construct.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\group\GroupMembershipLoader $group_membership_loader
   *   Group membership loader.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, GroupMembershipLoader $group_membership_loader) {
    $this->entityTypeManager = $entity_type_manager;
    $this->groupMembershipLoader = $group_membership_loader;
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

    if (!empty($group_contents)) {
      // Don't need to check all of group contents,
      // such as they all from one group.
      $group_content = reset($group_contents);
      return $group_content->getGroup();
    }

    return FALSE;
  }

  /**
   * Get the group membership.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account to fetch.
   * @param \Drupal\node\NodeInterface $node
   *   Node to check in.
   *
   * @return \Drupal\group\GroupMembership|false
   *   Return group member or false.
   */
  public function getGroupMember(AccountInterface $account, NodeInterface $node) {
    if ($group = $this->getGroupByNode($node)) {
      return $this->groupMembershipLoader->load($group, $account);
    }
    return FALSE;
  }

}

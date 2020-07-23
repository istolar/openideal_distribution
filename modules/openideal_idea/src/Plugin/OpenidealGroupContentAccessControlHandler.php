<?php

namespace Drupal\openideal_idea\Plugin;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\group\Entity\GroupContentInterface;
use Drupal\group\Plugin\GroupContentAccessControlHandler;
use Drupal\group\Plugin\GroupContentAccessControlHandlerInterface;

/**
 * Provides custom access control for GroupMembership entities.
 */
class OpenidealGroupContentAccessControlHandler extends GroupContentAccessControlHandler implements GroupContentAccessControlHandlerInterface {

  /**
   * {@inheritdoc}
   */
  public function relationAccess(GroupContentInterface $group_content, $operation, AccountInterface $account, $return_as_object = FALSE) {
    // Check if the account is the owner and an restrict delete access.
    $is_owner = $group_content->getOwnerId() === $account->id();
    if ($is_owner && $operation == 'delete') {
      return AccessResult::forbidden();
    }

    return parent::relationAccess($group_content, $operation, $account, $return_as_object);
  }

}

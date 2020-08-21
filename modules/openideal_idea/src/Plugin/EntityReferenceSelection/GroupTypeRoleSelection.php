<?php

namespace Drupal\openideal_idea\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\group\Plugin\EntityReferenceSelection\GroupTypeRoleSelection as BaseGroupTypeRoleSelection;

/**
 * Class GroupTypeRoleSelections.
 *
 * Change the logic and not give the ability to remove the author role
 * to the node author.
 */
class GroupTypeRoleSelection extends BaseGroupTypeRoleSelection {

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $target_type = $this->getConfiguration()['target_type'];

    $query = $this->buildEntityQuery($match, $match_operator);
    if ($limit > 0) {
      $query->range(0, $limit);
    }

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    // Check permissions.
    $account = $this->currentUser;
    $group_content = $this->getConfiguration()['entity'];
    $group = $group_content->getGroup();

    // Only filter of is not group creation membership wizard.
    if ($group) {
      foreach ($result as $role_id) {
        if (!$group->hasPermission("manage members with role {$role_id}", $account)) {
          unset($result[$role_id]);
        }
      }
    }

    $options = [];
    $entities = $this->entityTypeManager->getStorage($target_type)->loadMultiple($result);
    foreach ($entities as $entity_id => $entity) {
      $bundle = $entity->bundle();
      $options[$bundle][$entity_id] = Html::escape($this->entityManager->getTranslationFromContext($entity)->label());
    }

    return $options;
  }

}

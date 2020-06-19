<?php

namespace Drupal\openideal_user\Event;

use Drupal\group\Entity\GroupContent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OpenidealUserGroupEvent.
 */
class OpenidealUserGroupEvent extends Event {

  /**
   * Group owner user.
   *
   * @var \Drupal\user\Entity\User
   */
  public $user;

  /**
   * Group entity which contains the user.
   *
   * @var \Drupal\group\Entity\GroupContent
   */
  public $group;

  /**
   * OpenideaLUserMentionEvent construct.
   *
   * @param \Drupal\group\Entity\GroupContent $group
   *   Group content entity.
   */
  public function __construct(GroupContent $group) {
    $this->group = $group;
    $this->user = $group->getEntity();
  }

  /**
   * Get user.
   *
   * @return \Drupal\user\Entity\User
   *   User.
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Get group content entity.
   *
   * @return \Drupal\group\Entity\GroupContent
   *   Group content.
   */
  public function getGroup() {
    return $this->group;
  }

}

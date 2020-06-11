<?php

namespace Drupal\openideal_user\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps a mentions event for event listeners.
 */
class OpenidealUserMentionEvent extends Event {

  /**
   * Mentioned user id.
   *
   * @var string
   */
  protected $userId;

  /**
   * OpenideaLUserMentionEvent construct.
   *
   * @param string $user_id
   *   Mentioned user id.
   */
  public function __construct(string $user_id) {

    $this->userId = $user_id;
  }

  /**
   * Get user id.
   *
   * @return string
   *   User id.
   */
  public function getUserId() {
    return $this->userId;
  }

}

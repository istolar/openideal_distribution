<?php

namespace Drupal\openideal_user\Event;

/**
 * Contains all events thrown by openideal_user module.
 */
final class OpenidealUserEvents {

  /**
   * Name of the event fired when the user is mentioned in comment.
   *
   * This event allows modules to perform an action whenever the someone mention
   * user in comments.
   *
   * @var string
   */
  const OPENIDEAL_USER_MENTION = 'openideal_user.user_mention';

}

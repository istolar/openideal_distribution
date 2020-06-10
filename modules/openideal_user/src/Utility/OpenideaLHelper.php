<?php

namespace Drupal\openideal_user\Utility;

/**
 * Helper object for openideal_user module.
 */
class OpenideaLHelper {

  /**
   * Validate the user's name.
   *
   * @var string $name
   *   The name of user.
   * @return bool
   *   TRUE if the Name is in a valid format, FALSE otherwise.
   */
  public static function isValidName($name) {
    return (bool) preg_match('#^[a-z ,.\'-]+$#i', $name);
  }

}

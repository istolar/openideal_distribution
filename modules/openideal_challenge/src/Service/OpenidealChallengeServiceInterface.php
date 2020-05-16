<?php

namespace Drupal\openideal_challenge\Service;

/**
 * OpenidealChallengeServiceInterface file.
 */
interface OpenidealChallengeServiceInterface {

  /**
   * Processing for opening scheduled nodes.
   */
  public function openChallenges();

  /**
   * Processing for closing scheduled nodes.
   */
  public function closeChallenges();

}

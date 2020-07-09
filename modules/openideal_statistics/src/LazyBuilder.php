<?php

namespace Drupal\openideal_statistics;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * LazyBuilder object.
 */
class LazyBuilder {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * LazyBuilder constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Build element that contains idea count.
   */
  public function getIdeas() {
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $result = $query
      ->condition('type', 'idea')
      ->condition('status', '1')
      ->count()
      ->execute();

    return [
      '#markup' => $result,
    ];
  }

  /**
   * Build element that contains members count.
   */
  public function getMember() {
    $query = $this->entityTypeManager->getStorage('user')->getQuery();
    $result = $query
      ->condition('status', '1')
      ->count()
      ->execute();

    return [
      '#markup' => $result,
    ];
  }

  /**
   * Build element that contains comments count.
   */
  public function getComments() {
    $query = $this->entityTypeManager->getStorage('comment')->getQuery();
    $result = $query
      ->condition('status', '1')
      ->count()
      ->execute();

    return [
      '#markup' => $result,
    ];
  }

  /**
   * Build element that contains votes count.
   */
  public function getVotes() {
    $query = $this->entityTypeManager->getStorage('vote')->getQuery();
    $result = $query
      ->count()
      ->execute();

    return [
      '#markup' => $result,
    ];
  }

}

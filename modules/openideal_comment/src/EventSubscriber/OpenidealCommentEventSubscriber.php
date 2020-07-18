<?php

namespace Drupal\openideal_comment\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OpenidealCommentEventSubscriber.
 */
class OpenidealCommentEventSubscriber implements EventSubscriberInterface {

  use MessengerTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * OpenidealCommentEventSubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onComponentBuild'];
    return $events;
  }

  /**
   * Change the way field created is displayed in layout builder.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   Event.
   */
  public function onComponentBuild(SectionComponentBuildRenderArrayEvent $event) {
    if ($event->getPlugin()->getPLuginId() == 'field_block:comment:comment:created') {
      $build = $event->getBuild();
      $content = $build['content'];
      $comment = $content['#object'];
      // Change the default permalink of comment title.
      $commented_entity = $comment->getCommentedEntity();
      $uri = $commented_entity->toUrl();

      // Set attributes for permalink.
      $attributes = $uri->getOption('attributes') ?: [];
      $attributes += ['class' => ['permalink'], 'rel' => 'bookmark'];
      $uri->setOptions([
        'attributes' => $attributes,
        'fragment' => 'comment-' . $comment->id(),
      ]);

      // Render author.
      $author = $this->entityTypeManager->getViewBuilder('user')->view($comment->getOwner(), 'author');
      $build['content'][0] = $author;

      // Render link.
      $build['content'][] = [
        '#type' => 'link',
        '#title' => $content[0]['#markup'],
        '#url' => $uri,
      ];

      $event->setBuild($build);
    }
  }

}

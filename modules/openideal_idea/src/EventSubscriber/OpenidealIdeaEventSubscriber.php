<?php

namespace Drupal\openideal_idea\EventSubscriber;

use Drupal\content_moderation\Event\ContentModerationEvents;
use Drupal\content_moderation\Event\ContentModerationStateChangedEvent;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\flag\Event\FlagEvents;
use Drupal\flag\Event\FlaggingEvent;
use Drupal\flag\Event\UnflaggingEvent;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OpenidealIdeaEventSubscriber.
 */
class OpenidealIdeaEventSubscriber implements EventSubscriberInterface {

  use MessengerTrait;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[ContentModerationEvents::STATE_CHANGED] = ['onContentStateChange'];
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onComponentBuild'];
    $events[FlagEvents::ENTITY_FLAGGED] = ['onFlag'];
    $events[FlagEvents::ENTITY_UNFLAGGED] = ['onUnflag'];
    return $events;
  }

  /**
   * Notify users about approval process once it's published to review.
   *
   * @param \Drupal\content_moderation\Event\ContentModerationStateChangedEvent $event
   *   The dispatched event.
   */
  public function onContentStateChange(ContentModerationStateChangedEvent $event) {
    if ($event->getOriginalState() === 'draft' && $event->getNewState() === 'draft_approval') {
      $openideal_config = config_pages_config('openideal_configurations');
      $message = $openideal_config->field_idea_approval_message->view('default');
      if (!empty($message)) {
        $this->messenger()->addMessage($message);
      }
    }
  }

  /**
   * Add max-age to overall_score field.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   Event.
   */
  public function onComponentBuild(SectionComponentBuildRenderArrayEvent $event) {
    /** @var \Drupal\layout_builder\Plugin\Block\FieldBlock $kek */
    if ($event->getPlugin()->getPLuginId() == 'field_block:node:idea:overall_score') {
      $build = $event->getBuild();
      $build['content']['#cache']['max-age'] = 3600;
      $event->setBuild($build);
    }
  }

  /**
   * Adds the user to an Idea followers field.
   *
   * @param \Drupal\flag\Event\FlaggingEvent $event
   *   Event.
   */
  public function onFlag(FlaggingEvent $event) {
    $flag = $event->getFlagging();
    if ($this->isIdea($node = $flag->getFlaggable())) {
      $node->field_followers->appendItem($flag->getOwner());
      $node->save();
    }

  }

  /**
   * Remove the user from Idea followers field.
   *
   * @param \Drupal\flag\Event\UnflaggingEvent $event
   *   Event.
   */
  public function onUnflag(UnflaggingEvent $event) {
    $flaggings = $event->getFlaggings();
    foreach ($flaggings as $flag) {
      if ($this->isIdea($node = $flag->getFlaggable())) {
        // Remove the follower through filter callback.
        $node->field_followers->filter(function (EntityReferenceItem $item) use ($flag) {
          return $item->target_id != $flag->getOwnerId();
        });
        $node->save();
      }
    }
  }

  /**
   * Check if entity is node of Idea bundle.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity to check.
   *
   * @return bool
   *   TRUE if entity is Idea bundle FALSE otherwise.
   */
  private function isIdea(EntityInterface $entity) {
    return $entity instanceof NodeInterface && $entity->bundle() == 'idea';
  }

}

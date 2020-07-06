<?php

namespace Drupal\openideal_idea\EventSubscriber;

use Drupal\content_moderation\Event\ContentModerationEvents;
use Drupal\content_moderation\Event\ContentModerationStateChangedEvent;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
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
    if ($event->getPlugin()->getPLuginId() == 'field_block:node:idea:overall_score') {
      $build = $event->getBuild();
      $build['content']['#cache']['max-age'] = 3600;
      $event->setBuild($build);
    }
  }

}

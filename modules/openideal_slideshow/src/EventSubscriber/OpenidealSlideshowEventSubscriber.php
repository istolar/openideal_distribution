<?php

namespace Drupal\openideal_slideshow\EventSubscriber;

use Drupal\Core\Messenger\MessengerTrait;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OpenidealIdeaEventSubscriber.
 */
class OpenidealSlideshowEventSubscriber implements EventSubscriberInterface {

  use MessengerTrait;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Make sure to react on event before layout_builder.
    // @see BlockComponentRenderArray
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onBuildRender', 101];
    return $events;
  }

  /**
   * Adds to the Slideshow block context current node.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   Event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $context = $event->getContexts();
    if ($event->getPlugin()->getPLuginId() == 'openidel_slideshow_block' && isset($context['entity'])) {
      $result = [];
      /** @var \Drupal\openideal_slideshow\Plugin\Block\Slideshow $plugin */
      $plugin = $event->getPlugin();
      // Get the node from context, and prepare field for the slideshow plugin.
      $node = $context['entity']->getContextValue();

      if ($node->bundle() == 'challenge' && !$node->field_main_image->isEmpty()) {
        $result[] = $node->field_main_image->first()->entity;
      }

      /** @var \Drupal\file\Plugin\Field\FieldType\FileFieldItemList  $images */
      $images = $node->field_images;
      foreach ($images as $image) {
        $result[] = $image->entity;
      }

      $plugin->setConfigurationValue('images', $result);
      $plugin->setConfigurationValue('node', $node);
    }
  }

}

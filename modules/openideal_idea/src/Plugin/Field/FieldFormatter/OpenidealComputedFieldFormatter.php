<?php

namespace Drupal\openideal_idea\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the overall score formatter.
 *
 * @FieldFormatter(
 *   id = "openideal_overall_score_formatter",
 *   label = @Translation("Openideal overall score formatter"),
 *   description = @Translation("Overall score formatter."),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class OpenidealComputedFieldFormatter extends StringFormatter {

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $time;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->time = $container->get('datetime.time');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $item->value,
        '#cache' => [
          'tags' => Cache::mergeTags($item->getEntity()->getCacheTags(), ['openidea:idea:node:overall_score']),
        ],
      ];
    }
    return $elements;
  }

}

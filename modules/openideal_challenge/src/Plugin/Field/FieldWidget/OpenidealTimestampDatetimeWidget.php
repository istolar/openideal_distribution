<?php

namespace Drupal\openideal_challenge\Plugin\Field\FieldWidget;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\Plugin\Field\FieldWidget\TimestampDatetimeWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the OpenideaL 'datetime timestamp' widget.
 *
 * @FieldWidget(
 *   id = "openideal_datetime_timestamp",
 *   label = @Translation("Openideal Datetime Timestamp"),
 *   field_types = {
 *     "timestamp"
 *   }
 * )
 */
class OpenidealTimestampDatetimeWidget extends TimestampDatetimeWidget {

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$item) {
      if (isset($item['value']) && $item['value'] instanceof DrupalDateTime) {
        $date = $item['value'];
      }
      elseif (isset($item['value']['object']) && $item['value']['object'] instanceof DrupalDateTime) {
        $date = $item['value']['object'];
      }

      $item['value'] = !empty($date) ? $date->getTimestamp() : NULL;
    }
    return $values;
  }

}

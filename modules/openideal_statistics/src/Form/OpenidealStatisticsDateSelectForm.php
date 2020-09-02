<?php

namespace Drupal\openideal_statistics\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OpenidealStatisticsDateSelectForm.
 */
class OpenidealStatisticsDateSelectForm extends FormBase {

  /**
   * The query params to indicate the date filters.
   */
  const FIXED_RANGE = 'range';
  const DATE_TYPE = 'date_type';
  const FROM = 'from';
  const TO = 'to';

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->getRequest();
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openideal_statistics_date_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $query = $this->requestStack->getCurrentRequest()->query;
    $options = [];
    for ($month = 1; $month <= 12; $month++) {
      $options[$month] = $this->formatPlural($month, '1 month', '@count months');
    }

    $form['date_configuration'][self::DATE_TYPE] = [
      '#type' => 'radios',
      '#options' => [
        'fixed' => $this->t('From'),
        'custom' => $this->t('From'),
      ],
      '#default_value' => $query->get(self::DATE_TYPE) ?? '',
      '#required' => TRUE,
    ];
    $form['date_configuration'][self::FIXED_RANGE] = [
      '#type' => 'select',
      '#title' => $this->t('From'),
      '#options' => $options,
      '#default_value' => $query->get(self::FIXED_RANGE) ?? '',
    ];
    $form['date_configuration']['custom_dates'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['custom_dates_w']],
    ];
    $form['date_configuration']['custom_dates'][self::FROM] = [
      '#type' => 'date',
      '#title' => $this->t('From'),
      '#default_value' => $query->get(self::FROM) ?? '',
    ];
    $form['date_configuration']['custom_dates'][self::TO] = [
      '#type' => 'date',
      '#title' => $this->t('To'),
      '#default_value' => $query->get(self::TO) ?? '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('date_configuration');
    if ($values[self::DATE_TYPE] == 'fixed' && !$values[self::FIXED_RANGE]) {
      $form_state->setErrorByName(self::FIXED_RANGE, $this->t('An option should be selected'));
    }
    if ($values[self::DATE_TYPE] == 'custom' && (!$values['custom_dates'][self::FROM] ||!$values['custom_dates'][self::TO])) {
      $form_state->setErrorByName(self::FIXED_RANGE, $this->t('You must specify both dates'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}

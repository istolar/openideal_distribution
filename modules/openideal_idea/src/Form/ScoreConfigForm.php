<?php

namespace Drupal\openideal_idea\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class ScoreConfigForm.
 */
class ScoreConfigForm extends ConfigFormBase {

  /**
   * Drupal\Core\Extension\ModuleHandlerInterface definition.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    ModuleHandlerInterface $module_handler
  ) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'openideal_idea.scoreconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openideal_idea_score_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('openideal_idea.scoreconfig');

    $form['comments_value'] = [
      '#type' => 'number',
      '#title' => $this->t('Comments score value'),
      '#min' => 0,
      '#step' => 0.1,
      '#default_value' => $config->get('comments_value') ?? 10,
    ];

    $form['votes_value'] = [
      '#type' => 'number',
      '#title' => $this->t('Votes score value'),
      '#min' => 0,
      '#step' => 0.1,
      '#default_value' => $config->get('votes_value') ?? 5,
    ];

    $form['node'] = [
      '#type' => 'number',
      '#title' => $this->t('Node'),
      '#min' => 0.1,
      '#step' => 0.1,
      '#default_value' => $config->get('node') ?? 0.2,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('openideal_idea.scoreconfig')
      ->set('comments_value', $form_state->getValue('comments_value'))
      ->set('votes_value', $form_state->getValue('votes_value'))
      ->set('node_value', $form_state->getValue('node'))
      ->save();
  }

}

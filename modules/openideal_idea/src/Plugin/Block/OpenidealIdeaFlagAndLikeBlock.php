<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\flag\FlagLinkBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'FlagAndLike' block.
 *
 * @Block(
 *  id = "openideal_idea_flag_and_like_block",
 *  admin_label = @Translation("Flag and Like block"),
 *   context = {
 *      "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class OpenidealIdeaFlagAndLikeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Flag link builder service.
   *
   * @var \Drupal\flag\FlagLinkBuilderInterface
   */
  protected $flagLinkBuilder;

  /**
   * Constructs a new MobileFooterBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\flag\FlagLinkBuilderInterface $flag_link_builder
   *   Flag link builder service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    FlagLinkBuilderInterface $flag_link_builder
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->flagLinkBuilder = $flag_link_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('flag.link_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#theme'] = 'openideal_idea_flag_and_like_block';
    $contexts = $this->getContexts();
    if (isset($contexts['node']) && ($node = $contexts['node']->getContextValue()) && !$node->isNew()) {
      $flag_link = $this->flagLinkBuilder->build($node->getEntityTypeId(), $node->id(), 'follow');
      $build['#follow'] = $flag_link;
      $build['#main_class'] = $this->configuration['main_class'];
      $settings = [
        'settings' => [
          'show_summary' => FALSE,
          'show_icon' => TRUE,
          'show_label' => TRUE,
          'show_count' => FALSE,
        ],
      ];

      if (!$this->configuration['hide_like']) {
        $like = $node->field_like[0]->view($settings);
        $build['#like'] = $like;
      }

      $build['#cache']['tags'] = $node->getCacheTags();
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['hide_like'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide like'),
      '#default_value' => $this->configuration['hide_like'],
    ];
    $form['main_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Main class'),
      '#default_value' => $this->configuration['main_class'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['hide_like'] = $form_state->getValue('hide_like');
    $this->configuration['main_class'] = $form_state->getValue('main_class');
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    return [
      'hide_like' => FALSE,
      'main_class' => 'region-sidebar--flag-and-follow',
    ];
  }

}

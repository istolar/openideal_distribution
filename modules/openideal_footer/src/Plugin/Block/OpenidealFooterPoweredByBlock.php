<?php

namespace Drupal\openideal_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Theme\ThemeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a "Powered by" Block.
 *
 * @Block(
 *   id = "openidel_footer_powered_by",
 *   admin_label = @Translation("Powered by"),
 *   category = @Translation("Openideal")
 * )
 */
class OpenidealFooterPoweredByBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The openideal official site.
   */
  const OPENIDEAL_OFFICIAL_SITE = 'https://www.openidealapp.com/';

  /**
   * Theme manger.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              ThemeManager $themeManager,
                              ConfigFactory $config_factory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->themeManager = $themeManager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('theme.manager'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['openideal_official_site'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Official site'),
      '#description' => $this->t('The OpenideaL official site.'),
      '#default_value' => $config['openideal_official_site'] ?? self::OPENIDEAL_OFFICIAL_SITE,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['openideal_official_site'] = $values['openideal_official_site'];
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    $block_config = $this->getConfiguration();
    $config = $this->configFactory->get('openideal_footer.openideal_footer_links_config');
    $path = $this->themeManager->getActiveTheme()->getPath();
    $base_theme_path = base_path() . $path;
    return [
      '#theme' => 'openideal_powered_by',
      '#site_url' => $block_config['openideal_official_site'],
      '#logo' => $base_theme_path . '/misc/icons/logo_openideal.png',
      '#links' => [
        'github' => [
          'path' => $config->get('github'),
          'logo' => $base_theme_path . '/misc/icons/github_logo.png',
        ],
        'twitter' => [
          'path' => $config->get('twitter'),
          'logo' => $base_theme_path . '/misc/icons/twitter_logo.png',
        ],
      ],
    ];
  }

}

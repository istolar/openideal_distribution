<?php

namespace Drupal\openideal_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a 'OpenidealRemindToFillFieldsBlock' block.
 *
 * @Block(
 *  id = "openideal_reminds_to_fill_in_fields_block",
 *  admin_label = @Translation("Reminds to fill in user fields"),
 * )
 */
class OpenidealRemindToFillFieldsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Entity Type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Constructs a new OpenidealRemindToFillFIeldsBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The AccountProxyInterface definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // @Todo: customize block?
    if ($this->userHasEmptyFields()) {
      $this->messenger()->addMessage($this->t('Please fill your <a href="@link">profile</a>',
        ['@link' => Url::fromRoute('openideal_user.register.user.more_about_you')->toString()]
      ));
    }
  }

  /**
   * Check if user has empty fields.
   *
   * @return bool
   *   If user has empty fields return TRUE, otherwise FALSE.
   */
  protected function userHasEmptyFields() {
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
    return empty($user->get('field_age_group')->getString()) || empty($user->get('field_gender')->getString());
  }

}

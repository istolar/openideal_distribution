<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxy;
use Drupal\node\NodeInterface;
use Drupal\openideal_idea\OpenidealHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Idea info' block.
 *
 * @Block(
 *  id = "openideal_idea_info_block",
 *  admin_label = @Translation("Idea info"),
 * )
 */
class OpenidealIdeaUpdateInfo extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Openideal helper.
   *
   * @var \Drupal\openideal_idea\OpenidealHelper
   */
  protected $helper;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CurrentRouteMatch $current_route_match,
    DateFormatter $date_formatter,
    OpenidealHelper $helper,
    AccountProxy $currentUser
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $current_route_match;
    $this->dateFormatter = $date_formatter;
    $this->helper = $helper;
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('date.formatter'),
      $container->get('openideal_idea.helper'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');
    $build = ['#theme' => 'openideal_idea_info_block'];
    if ($node instanceof NodeInterface) {
      $created = $this->dateFormatter->format($node->getCreatedTime(), 'html_date');
      $changed = $this->dateFormatter->format($node->getChangedTime(), 'html_date');

      $build['#content']['created'] = $created;
      $build['#content']['changed'] = $changed;

      $member = $this->helper->getGroupMember($this->currentUser, $node);
      if ($member && $member->hasPermission('update any group_node:idea entity')) {
        $link = Link::createFromRoute('Edit', 'entity.node.edit_form', ['node' => $node->id()])->toString();
        $build['#content']['edit idea'] = $link;
      }

    }

    return $build;
  }

}

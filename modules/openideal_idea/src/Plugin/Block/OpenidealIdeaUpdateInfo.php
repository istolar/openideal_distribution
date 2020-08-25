<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxy;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
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
      // @Todo: style, finish logic.
      if ($node->bundle() == 'challenge') {
        $status = $this->getChallengeStatus($node);
        $build['#content']['challenge_status'] = $status;
      }

      $build['#content']['created'] = $created;
      $build['#content']['changed'] = $changed;

      $member = $this->helper->getGroupMember($this->currentUser, $node);
      if ($member && $member->hasPermission('update any group_node:idea entity')) {
        $link = Link::createFromRoute($this->t('Edit'), 'entity.node.edit_form', ['node' => $node->id()])->toString();
        $build['#content']['edit'] = $link;
      }
      $build['#cache']['tags'] = $node->getCacheTags();
    }

    return $build;
  }

  /**
   * Get Challenge status.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Challenge.
   *
   * @return array|array[]
   *   Randarable array.
   */
  protected function getChallengeStatus(NodeInterface $node) {
    $settings = [
      'label' => 'hidden',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
        'date_format' => 'custom',
        'custom_date_format' => 'd/m/Y h:i',
      ],
    ];
    if ($node->field_is_open->value && $node->field_schedule_close->value) {
      $view = $node->field_schedule_close->view($settings);

      return [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Challenge deadline'),
        ],
        'date' => $view,
      ];
    }
    elseif ($node->field_is_open->value && $node->field_schedule_open->value) {
      $view = $node->field_schedule_close->view($settings);

      return [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Challenge will open on'),
        ],
        'date' => $view,
      ];
    }
    else {
      $value = $node->field_is_open->value ? 'Open' : 'Close';
      return [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Challenge status'),
        ],
        'status' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $this->t('@status', ['@status' => $value]),
        ],
      ];

    }
  }

}

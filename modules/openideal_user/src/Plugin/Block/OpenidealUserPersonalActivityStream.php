<?php

namespace Drupal\openideal_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealUserPersonalActivityStream' block.
 *
 * @Block(
 *  id = "openideal_personal_activity_stream_block",
 *  admin_label = @Translation("Personal activity stream"),
 * )
 */
class OpenidealUserPersonalActivityStream extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Limit of activity entities in list.
   *
   * @Todo: Move to config form?
   */
  const LIMIT = 10;

  /**
   * Block manager.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

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
    Connection $database,
    EntityTypeManager $entityTypeManager,
    AccountProxy $currentUser
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->entityTypeManager = $entityTypeManager;
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
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#theme'] = 'openideal_user_activity_list';
    $entities = $this->getContent();
    if (empty($entities)) {
      return $build;
    }
    $view_builder = $this->entityTypeManager->getViewBuilder('message');
    $items = [];
    /** @var \Drupal\message\Entity\Message $entity */
    foreach ($entities as $entity) {
      $items[] = $view_builder->view($entity);
    }

    $build['#list'] = $items;
    $build['#cache']['tags'][] = 'message_list';
    return $build;
  }

  /**
   * Fetch activity stream message ids.
   *
   * @Todo: Find the way to check permissions when the private flavours module is enabled.
   * Because of "node_grants" were removed from the Group module.
   *
   * @return array|null
   *   Fetched entities.
   */
  protected function fetchContent() {
    $user_id = $this->currentUser->id();

    // Activity in content followed by user.
    $query = $this->database->select('message_field_data', 'mfd');
    $query->join('message__field_node_reference', 'nr', "nr.entity_id = mfd.mid AND nr.deleted = '0'");
    $query->fields('mfd', ['created', 'mid']);

    $query->join('node_field_data', 'nfd', "nr.field_node_reference_target_id = nfd.nid");
    $query->innerJoin('flagging', 'flag', "nfd.nid = flag.entity_id AND flag.flag_id = 'follow' AND flag.uid = :id", [':id' => $user_id]);

    // Activity in "My ideas".
    $second_query = $this->database->select('message_field_data', 'mfd');
    $second_query->join('message__field_node_reference', 'nr', "nr.entity_id = mfd.mid AND nr.deleted = '0'");
    $second_query->fields('mfd', ['created', 'mid']);

    $second_query->join('node_field_data', 'nfd', "nr.field_node_reference_target_id = nfd.nid");
    $second_query->innerJoin('group_content_field_data', 'gnode', "gnode.entity_id = nfd.nid AND gnode.type = 'idea-group_node-idea'");
    $second_query->innerJoin('group_content_field_data', 'gmember', "gmember.gid = gnode.gid AND gmember.type = 'idea-group_membership' AND gmember.entity_id = :id", [':id' => $user_id]);

    // My comments queries.
    //
    // Like in my comment.
    $third_query = $this->database->select('message_field_data', 'mfd');
    $third_query->fields('mfd', ['created', 'mid']);

    $third_query->join('message__field_comment_reference', 'cr', "cr.entity_id = mfd.mid AND cr.deleted = '0' AND cr.bundle = 'created_like_on_comment'");
    $third_query->innerJoin('comment_field_data', 'cfd', "cfd.cid = cr.field_comment_reference_target_id AND mfd.uid <> cfd.uid AND cfd.uid = :id", [':id' => $user_id]);

    // Replies to my comments.
    $forth_query = $this->database->select('message_field_data', 'mfd');
    $forth_query->fields('mfd', ['created', 'mid']);

    $forth_query->join('message__field_comment_reference', 'cr', "cr.entity_id = mfd.mid AND cr.deleted = '0' AND cr.bundle = 'created_reply_on_comment'");

    $forth_query->join('comment_field_data', 'cfd', 'cfd.cid = cr.field_comment_reference_target_id');

    $forth_query->innerJoin('comment_field_data', 'cp', 'cfd.pid = cp.cid AND cp.uid = :id', [':id' => $user_id]);

    $query->union($third_query);
    $query->union($forth_query);
    $result = $this->database->select($query->union($second_query))
      ->fields(NULL, ['created', 'mid'])
      ->orderBy('created', 'DESC')
      ->range(0, self::LIMIT);
    return $result->execute()->fetchAllKeyed(1, 1);
  }

  /**
   * Load messages entities.
   *
   * @return false|array
   *   Return array of entities.
   */
  protected function getContent() {
    $fetched_content = $this->fetchContent();
    if (empty($fetched_content)) {
      return FALSE;
    }

    return $this->entityTypeManager->getStorage('message')->loadMultiple($fetched_content);
  }

}

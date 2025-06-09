<?php

namespace Drupal\rr_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'ExampleBlock' block.
 *
 * @Block(
 *   id = "example_block",
 *   admin_label = @Translation("Example block"),
 * )
 */
class ExampleBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): BlockPluginInterface {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  public function build(): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $articles = $storage->loadByProperties(['type' => 'article', 'status' => 1]);

    $items = [];

    foreach ($articles as $article) {
      if (!$article->get('field_show_in_list')->isEmpty() && $article->get('field_show_in_list')->value) {
        $url = Url::fromRoute('entity.node.canonical', ['node' => $article->id()]);
        $items[] = [
          '#type' => 'html_tag',
          '#tag' => 'li',
          '#value' => '<h3><a href="' . $url->toString() . '">' . $article->label() . '</a></h3>',
          '#allowed_tags' => ['h3', 'a', 'li'],
        ];
      }
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Article List'),
      '#suffix' => $this->t('There are @count articles.', ['@count' => count($articles)]),
      '#cache' => [
        'tags' => ['node_list'],
        'contexts' => ['user.roles', 'url'],
        'max-age' => 3600,
      ],
    ];
  }

}

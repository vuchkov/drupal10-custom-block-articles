<?php

namespace Drupal\rr_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
* Provides a 'ExampleBlock' block.
*
* @Block(
*   id = "example_block",
*   admin_label = @Translation("Example block"),
* )
*/
class ExampleBlock extends BlockBase {

  /**
  * {@inheritdoc}
  */
  public function build() {
    $build = [];

    $articles = \Drupal::entityTypeManager()->getStorage('node')
    ->loadByProperties(['type' => 'article', 'status' => 1]);

    $markup = '<ul>';
    foreach ($articles as $article) {
      var_dump($article->get('field_show_in_list')->getValue());
      $field_show_in_list = $article->get('field_show_in_list')->getValue();
      $link = '<a href="/node/' . $article->id() .'">' . $article->label() . '</a>';
      if ($field_show_in_list = true) {
        $markup .= '<li><h3>' . $link . '</h3></li>';
      }

    }
    $markup .= '</ul>';

    $markup .= 'There are ' . count($articles) . ' articles.';
    //$markup .= 'There are 3 articles.';

    $build['example_block']['#markup'] = $markup;

    return $build;
  }

}

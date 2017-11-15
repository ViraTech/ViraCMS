<?php
/**
 * ViraCMS Site Search Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class SearchController extends VPublicController
{
  const MAX_SEARCH_RESULTS = 100;
  const MAX_SNIPPET_ENTRIES = 1;
  const MAX_QUERIES_TIME = 60;
  const MAX_QUERIES = 10;

  public $layout = 'default';
  public $pageSize = 10;

  /**
   * Search action
   * @param string $q search query
   * @param integer $pg page number
   */
  public function actionIndex($q = '', $pg = 0)
  {
    $query = '';
    $result = array();

    if (!empty($q)) {
      $result = Yii::app()->cache->get('Vira.Search.Result.' . md5($q));

      if ($result === false) {
        $queries = Yii::app()->user->getState('Vira.Search.Query', array('timestamp' => time(), 'queries' => 0));

        if (self::MAX_QUERIES_TIME < time() - $queries['timestamp']) {
          $queries = array('timestamp' => time(), 'queries' => 0);
        }

        if ($queries['queries'] > self::MAX_QUERIES) {
          throw new CHttpException(400, 'Search queries quota exceeded. Try again later.');
        }

        $query = Yii::app()->searchIndex->search(null, null, $q);

        if (!empty($query)) {
          $result = count($query['results']) > self::MAX_SEARCH_RESULTS ? array_slice($query['results'], 0, self::MAX_SEARCH_RESULTS) : $query['results'];
          foreach ($result as &$record) {
            $snippets = array();
            $processed = array();
            foreach ($query['words'] as $i => $word) {
              if (count($snippets) >= self::MAX_SNIPPET_ENTRIES) {
                break;
              }
              if ($snippet = $this->snippet($record['content'], $word, $query['words'])) {
                $processed[$i] = true;
                $snippets[] = $snippet;
              }
            }
            foreach ($query['stemmed'] as $i => $word) {
              if (isset($processed[$i])) {
                continue;
              }
              if (count($snippets) >= self::MAX_SNIPPET_ENTRIES) {
                break;
              }
              if ($snippet = $this->snippet($record['content'], $word, $query['stemmed'])) {
                $snippets[] = $snippet;
              }
            }
            $record['title'] = $this->title($record['title'], array_merge($query['words'], $query['stemmed']));
            $record['snippets'] = $snippets;
          }
        }

        Yii::app()->cache->set(
          'Vira.Search.Result.' . md5($q), $result, Yii::app()->params['defaultCacheDuration'], new VTaggedCacheDependency(
          'Vira.Content', Yii::app()->params['defaultCacheTagDuration']
          )
        );

        $queries['queries'] ++;
        Yii::app()->user->setState('Vira.Search.Query', $queries);
      }
    }

    $title = Yii::t('common', 'Search');
    $this->setTitle($title);
    $this->setPageTitle($title);
    $this->setBreadcrumbs(array($title));
    $this->render('search', array(
      'results' => $result,
      'q' => $q,
      'title' => $title,
      'page' => $pg > 0 ? $pg - 1 : $pg,
      'pageSize' => $this->pageSize,
    ));
  }

  private function hilight($text)
  {
    return CHtml::tag('span', array('class' => 'hilight'), $text);
  }

  private function title($title, $words)
  {
    $replace = array();

    foreach ($words as $word) {
      $regexp = '/(' . preg_quote($word) . ')/ui';
      if (preg_match($regexp, $title)) {
        $title = preg_replace($regexp, $this->hilight('${1}'), $title);
      }
    }

    return $title;
  }

  private function snippet($content, $word, $words = array())
  {
    $snippet = '';

    if (($position = mb_stripos($content, $word, null, Yii::app()->charset)) !== false) {
      $contentLength = mb_strlen($content, Yii::app()->charset);
      $start = $position > 60 ? $position - 60 : 0;
      $length = $position + ($start == 0 ? 120 : 60 - $start);
      if ($length > $contentLength - $start) {
        $length = $contentLength - $start;
      }
      $snippet = $start > 0 ? '&hellip;' : '';
      $snippet .= mb_substr($content, $start, $length, Yii::app()->charset);
      $snippet .= ($start + $length) >= $contentLength ? '' : '&hellip;';
    }

    if ($snippet) {
      $replace = array();

      foreach ($words as $word) {
        $replace[$word] = $this->hilight($word);
      }

      $snippet = strtr($snippet, $replace);
    }

    return trim($snippet);
  }
}

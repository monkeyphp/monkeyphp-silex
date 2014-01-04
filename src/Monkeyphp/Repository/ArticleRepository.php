<?php
/**
 * ArticleRepository.php
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Repository
 * @author     David White
 */
namespace Monkeyphp\Repository;

/**
 * ArticleRepository
 * 
 * @category   Monkeyphp
 * @package    Monkeyphp
 * @subpackage Monkeyphp\Repository
 * @author     David White
 */
class ArticleRepository extends AbstractRepository
{
    
    
    
    
    public function findArticleBySlug($slug)
    {
        if (false === ($id = $this->getMemcached()->get($slug))) {
            return null;
        }
        return $this->findArticleById($id);
    }
    
    public function findArticleById($id)
    {
        $query = array(
            'index' => 'monkeyphp',
            'type'  => 'article',
            'body'  => array(
                'fields' => array(
                    'created', 'modified',
                    'title', 'tags', 
                    'summary', 'body', 
                    'published', 'slug'
                ),
                'query' => array(
                    'ids' => array(
                        'type' => 'article',
                        'values' => array($id)
                    )
                )
            )
        );
        
        $result = $this->getElasticsearchClient()->search($query);
        
        $article = null;
        
        if ($hits = (array_key_exists('hits', $result) && is_array($result['hits'])) ? $result['hits'] : false) {
            if ($hit = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? reset($hits['hits']) : false) {
                
                if ((null !== ($id = (isset($hit['_id'])) ? $hit['_id'] : null)) &&
                    $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                ) {
                    
                    $options = array(
                        'id'        => $id,
                        'created'   => new \DateTime($fields['created']),
                        'modified'  => new \DateTime($fields['modified']),
                        'title'     => $fields['title'],
                        'tags'      => $fields['tags'],
                        'summary'   => $fields['summary'],
                        'body'      => $fields['body'],
                        'published' => $fields['published'], 
                        'slug'      => $fields['slug']
                    );
                    
                    $article = new \Monkeyphp\Entity\Article($options);
                }
            }
        }
        
        return $article;
    }
    
    /**
     * Return an array of Article instances
     * 
     * @return array
     */
    public function fetchArticles()
    {
        $query = array(
            'index' => 'monkeyphp',
            'type'  => 'article',
            'body'  => array(
                'fields' => array(
                    'created','modified','title','tags', 'summary', 'body', 'published', 'slug'
                ),
                'query'  => array(
                    'match_all' => array()
                )
            )
        );
        
        $results = $this->getElasticsearchClient()->search($query);
        
        $articles = array();
        
        if ($hits = (array_key_exists('hits', $results) && is_array($results['hits'])) ? $results['hits'] : false) {
            
            if ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : false) {
                
                foreach ($hits as $hit) {
                    
                    if ((null !== ($id = (isset($hit['_id'])) ? $hit['_id'] : null)) &&
                        $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                    ) {
                        
                        $options = array(
                            'id'        => $id,
                            'created'   => new \DateTime($fields['created']),
                            'modified'  => new \DateTime($fields['modified']),
                            'title'     => $fields['title'],
                            'tags'      => $fields['tags'],
                            'summary'   => $fields['summary'],
                            'body'      => $fields['body'],
                            'published' => $fields['published'], 
                            'slug'      => $fields['slug']
                        );
                        
                        $this->getMemcached()->set($fields['slug'], $id);
                        
                        $article = new \Monkeyphp\Entity\Article($options);
                    
                        $articles[] = $article;
                        
                    }
                }
            }
        }
        
        return $articles;
    }
            
    
}

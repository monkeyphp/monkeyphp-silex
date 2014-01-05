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

use DateTime;
use Monkeyphp\Entity\Article;
use Monkeyphp\Entity\Comment;

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
    /**
     * Save the supplied Comment instance
     * 
     * @param Comment $comment The Comment instance to save
     * 
     * @return Comment
     */
    public function saveComment(Comment $comment)
    {
        $params = array(
            'index'  => 'monkeyphp',
            'type'   => 'comment',
            'parent' => $comment->getArticleId(),
            'body'   => array(
                'created'   => ($comment->getCreated() instanceof DateTime) ? $comment->getCreated()->format('Y/m/d H:i:s') : date('Y/m/d H:i:s'),
                'modified'  => ($comment->getModified() instanceof DateTime) ? $comment->getModified()->format('Y/m/d H:i:s') : date('Y/m/d H:i:s'), 
                'body'      => $comment->getBody(), 
                'ip'        => $comment->getIp(), 
                'email'     => $comment->getEmail(),
                'published' => $comment->getPublished()
            )
        );
        
        $result = $this->getElasticsearchClient()->index($params);
        
        if ((isset($result['ok']) && $result['ok'] === true) && 
            (null !== ($id = (isset($result['_id']) && is_string($result['_id'])) ? $result['_id'] : null))) 
        {
            return $id;
        }
        
        return null;
    }
    
    /**
     * Return an array of Comments that belong to the supplied Article
     * 
     * @param Article|string $id The id or Article
     * 
     * @return array
     */
    public function fetchCommentsByArticle($article)
    {
        if ($article instanceof Article) {
            $article = $article->getId();
        }
        
        $query = array(
            'index'   => 'monkeyphp', 
            'type'    => 'comment', 
            'routing' => $article,
            'body'    => array(
                'fields' => array(
                    'created',
                    'modified',
                    'body',
                    'ip',
                    'email',
                    'published'
                ),
                'query' => array(
                    'match_all' => array()
                )
            )
        );
        
        $results = $this->getElasticsearchClient()->search($query);
        
        $comments = array();
        
        if ($hits = (array_key_exists('hits', $results) && is_array($results['hits'])) ? $results['hits'] : false) {
            if ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : false) {
                
                foreach ($hits as $hit) {
                    
                    if ((null !== ($cid = (isset($hit['_id'])) ? $hit['_id'] : null)) &&
                        $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                    ) {
                        
                        $options = array(
                            'id'       => $article,
                            'created'  => new DateTime($fields['created']),
                            'modified' => new DateTime($fields['modified']),
                            'body'     => $fields['body'],
                            'ip'       => $fields['ip'],
                            'email'    => $fields['email']
                            
                        );
                        
                        $comment = new Comment($options);
                        
                        $comments[] = $comment;
                    }
                }
            }
        }
        return $comments;
    }
    
    /**
     * Return an Article based on the supplied slug parameter
     * 
     * @param string $slug
     * 
     * @return Article
     */
    public function findArticleBySlug($slug)
    {
        if (false === ($id = $this->getMemcached()->get($slug))) {
            return null;
        }
        return $this->findArticleById($id);
    }
    
    /**
     * Return the instance of Article identified by the supplied id 
     * 
     * @param string $id
     * 
     * @return Article
     */
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
                        'created'   => new DateTime($fields['created']),
                        'modified'  => new DateTime($fields['modified']),
                        'title'     => $fields['title'],
                        'tags'      => $fields['tags'],
                        'summary'   => $fields['summary'],
                        'body'      => $fields['body'],
                        'published' => $fields['published'], 
                        'slug'      => $fields['slug']
                    );
                    
                    $article = new Article($options);
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
                            'created'   => new DateTime($fields['created']),
                            'modified'  => new DateTime($fields['modified']),
                            'title'     => $fields['title'],
                            'tags'      => $fields['tags'],
                            'summary'   => $fields['summary'],
                            'body'      => $fields['body'],
                            'published' => $fields['published'], 
                            'slug'      => $fields['slug']
                        );
                        
                        $this->getMemcached()->set($fields['slug'], $id);
                        
                        $article = new Article($options);
                    
                        $articles[] = $article;
                        
                    }
                }
            }
        }
        return $articles;
    }
            
    
}

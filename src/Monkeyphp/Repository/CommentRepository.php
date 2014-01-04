<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Monkeyphp\Repository;

use DateTime;
use Monkeyphp\Entity\Comment;
/**
 * Description of CommentRepository
 *
 * @author David White <david@monkeyphp.com>
 */
class CommentRepository extends AbstractRepository
{
    /**
     * 
     * @param \Monkeyphp\Repository\Comment $comment
     */
    public function saveComment(Comment $comment)
    {
        $params = array(
            'index'  => 'monkeyphp',
            'type'   => 'comment',
            'parent' => $comment->getArticleId(),
            'body'   => array(
                'created'   => '', 
                'modified'  => '', 
                'body'      => $comment->getBody(), 
                'ip'        => $comment->getIp(), 
                'email'     => $comment->getEmail(),
                'published' => $comment->getPublished()
            )
        );
        
        $result = $this->getElasticsearchClient()->index($params);
        
    }
    
    /**
     * 
     * @param string $id
     */
    public function fetchCommentsByArticleId($id)
    {
        $query = array(
            'index'   => 'monkeyphp', 
            'type'    => 'comment', 
            'routing' => $id,
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
                            'id'       => $id,
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
}

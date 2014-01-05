<?php
/**
 * AboutRepository.php
 */
namespace Monkeyphp\Repository;

use DateTime;
use DateTimeZone;
use Monkeyphp\Entity\About;
use Monkeyphp\Repository\AbstractRepository;


/**
 * Description of AboutRepository
 *
 * @author David White <david@monkeyphp.com>
 */
class AboutRepository extends AbstractRepository
{
    /**
     * Return the instance of About
     * 
     * @return About
     */
    public function fetchAbout()
    {
        $query = array(
            'index' => 'monkeyphp',
            'type' => 'about',
            'body' => array(
                'fields' => array(
                    'created',
                    'modified',
                    'body'
                ),
                'from' => 0,
                'size' => 1,
                'query' => array(
                    'match_all' => array()
                )
            )
        );
        
        $results = $this->getElasticsearchClient()->search($query);
        
        $about = null;
        
        if ($hits = (array_key_exists('hits', $results) && is_array($results['hits'])) ? $results['hits'] : false) {

            if (array_key_exists('total', $hits) && $hits['total'] === 1) {
            
                if ($hits = (array_key_exists('hits', $hits) && is_array($hits['hits'])) ? $hits['hits'] : false) {
                    
                    $hit = reset($hits);
                    
                    if ((null !== ($id = (isset($hit['_id'])) ? $hit['_id'] : null)) && 
                        $fields = (array_key_exists('fields', $hit) && is_array($hit['fields'])) ? $hit['fields'] : false
                    ) {
                        
                        $created  = null;
                        $modified = null;

                        if (isset($fields['created']) && 
                            is_array($fields['created']) && 
                            isset($fields['created']['date'])
                        ) {
                            $timezone = (isset($fields['created']['timezone'])) ? $fields['created']['timezone'] : null;
                            $dateTimeZone = (! is_null($timezone) && is_string($timezone)) ? new DateTimeZone($timezone) : null;
                            $created = new DateTime($fields['created']['date'], $dateTimeZone);
                        }

                        if (isset($fields['modified']) && 
                            is_array($fields['modified']) && 
                            isset($fields['modified']['date'])
                        ) {
                            $timezone = (isset($fields['modified']['timezone'])) ? $fields['modified']['timezone'] : null;
                            $dateTimeZone = (! is_null($timezone) && is_string($timezone)) ? new DateTimeZone($timezone) : null;
                            $modified = new DateTime($fields['modified']['date'], $dateTimeZone);
                        }
                        
                        $options = array(
                            'created' => new DateTime($created),
                            'modifed' => new DateTime($modified), 
                            'body' => isset($fields['body']) ? $fields['body'] : null
                        );
                        
                        $about = new About($options);
                    }
                }
            }
        }
        
        return $about;
    }
    
}

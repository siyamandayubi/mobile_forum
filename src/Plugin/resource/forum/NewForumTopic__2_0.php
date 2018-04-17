<?php
namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\ResourceDbQuery;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Exception\UnauthorizedException;

/**
 * Class NewForumTopic
 * @package Drupal\mobile_forum\Plugin\resource\forum
 *
 * @Resource(
 *   name = "newforumtopic:2.0",
 *   resource = "newforumtopic",
 *   label = "NEWFORUMNS",
 *   description = "Expose the new topics count per forum.",
 *   dataProvider = {
 *     "tableName": "forum_index",
 *     "idColumn": "nid",
 *     "tid": "tid",
 *     "nid": "nid",
 *     "node_count": "node_count",
 *   },
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   majorVersion = 2,
 *   minorVersion = 0
 * )
 */

class NewForumTopic__2_0 extends ResourceDbQuery implements ResourceInterface {
    //put your code here
    protected function publicFields() {
        $fields = array();
        
        $fields['tid'] = array('property' => 'tid');
        $fields['nid'] = array('property' => 'nid');
        $fields['node_count'] = array('property' => 'node_count');
        $fields['node_maxid'] = array('property' => 'node_maxid');
        
        return $fields;
    }

    protected function dataProviderClassName(){
        return '\Drupal\mobile_forum\Plugin\resource\forum\NewTopicsDataProvider';
    }
}

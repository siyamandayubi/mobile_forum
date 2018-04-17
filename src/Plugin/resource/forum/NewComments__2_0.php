<?php
namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\ResourceDbQuery;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Exception\UnauthorizedException;

/**
 * Class NewComments
 * @package Drupal\mobile_forum\Plugin\resource\forum
 *
 * @Resource(
 *   name = "newcomments:2.0",
 *   resource = "newcomments",
 *   label = "NEWCOMMENTS",
 *   description = "Expose the new comments numbers per forum.",
 *   dataProvider = {
 *     "tableName": "forum_index",
 *     "idColumn": "nid",
 *     "tid": "tid",
 *     "nid": "nid",
 *     "comment_count": "comment_count",
 *   },
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   majorVersion = 2,
 *   minorVersion = 0
 * )
 */

class NewComments__2_0 extends ResourceDbQuery implements ResourceInterface {
    //put your code here
    protected function publicFields() {
        $fields = array();
        
        $fields['tid'] = array('property' => 'tid');
        $fields['nid'] = array('property' => 'nid');
        $fields['comment_count'] = array('property' => 'comment_count');
        $fields['comment_maxid'] = array('property' => 'comment_maxid');
        
        return $fields;
    }

    protected function dataProviderClassName(){
        return '\Drupal\mobile_forum\Plugin\resource\forum\NewCommentsDataProvider';
    }
}

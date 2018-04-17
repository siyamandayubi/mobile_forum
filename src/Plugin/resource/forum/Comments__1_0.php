<?php

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\ResourceEntity;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Exception\UnauthorizedException;

/**
 * Class Comments
 * @package Drupal\mobile_forum\Plugin\resource\forum
 *
 * @Resource(
 *   name = "Comments:1.0",
 *   resource = "Comments",
 *   label = "Comments",
 *   description = "Export the comments with all authentication providers.",
 *   authenticationTypes = {
 *     "token"
 *   },
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "comment",
 *     "bundles": FALSE,
 *   },
 *   majorVersion = 1,
 *   minorVersion = 0
 * )
 */
class Comments__1_0 extends ResourceEntity implements ResourceInterface {

    /**
     * {@inheritdoc}
     */
    protected function publicFields() {
        $public_fields = parent::publicFields();

        $public_fields['nid'] = array(
            'property' => 'node',
            'sub_property' => 'nid',
        );

        //   $public_fields['cid'] = array(
        //   'property' => 'node',
        // 'sub_property' => 'cid',
        //);
        $public_fields['created'] = array(
            'property' => 'node',
            'sub_property' => 'created',
        );

        $public_fields['changed'] = array(
            'property' => 'node',
            'sub_property' => 'changed',
        );


        $public_fields['status'] = array(
            'property' => 'node',
            'sub_property' => 'status',
        );

        // Add a custom field for test only.
        //if (field_info_field('comment_body')) {
            $public_fields['comment_body'] = array(
                'property' => 'comment_body',
                'sub_property' => 'value'
                //,'bundle' => 'comment_node_forum'
            );
        //}

        $public_fields['cid'] = array(
            'process_callbacks' => array(
                array($this, 'getCid')
            )
        );


        $public_fields['uid'] = array(
            'property' => 'node',
            'sub_property' => 'uid',
            'methods' => array(
                \Drupal\restful\Http\RequestInterface::METHOD_POST,
                \Drupal\restful\Http\RequestInterface::METHOD_PUT,
                \Drupal\restful\Http\RequestInterface::METHOD_PATCH,
            )
        );

        $public_fields['thread'] = array(
            'process_callbacks' => array(
                array($this, 'getThread')
            )
        );
        
        $public_fields['username'] = array(
            'process_callbacks' => array(
                array($this, 'getName')
            )
        );

        $public_fields['uuid'] = array(
            'process_callbacks' => array(
                array($this, 'getUid')
            )
        );

        $public_fields['picture'] = array(
            'process_callbacks' => array(
                array($this, 'getPicture')
            )
        );
        
        return $public_fields;
    }

    public function getPicture($node) {
        if (!isset($node->picture)) {
            return null;
        }
        $file = file_load($node->picture);
        $url = file_create_url($file->uri);
        return array('url' => $url, filename => $file->filename);
    }

    public function create($path) {
        // TODO: Compare this with 1.x logic.
        $object = $this->getRequest()->getParsedBody();
        $account = $this->getAccount();
        
        if (!isset($account) || $account->uid == 0){
            throw new Exception("unauthorized access");
        }
        
        $object["uid"] = $account->uid;
        return $this->getDataProvider()->create($object);
    }
    
    public function update($path){
        $account = $this->getAccount();
        
        if (!isset($account) || $account->uid == 0){
            throw new Exception("unauthorized access");
        }
        
        return parent::update($path);
   }
 
     public function remove($path){
        $account = $this->getAccount();
        
        if (!isset($account) || $account->uid == 0){
            throw new Exception("unauthorized access");
        }
        
        return parent::remove($path);
   }

   public function allComments($node) {
        return $node;
    }

    public function getCid($node) {
        return $node->cid;
    }

    public function getName($node) {
        return $node->name;
    }

    public function getThread($node) {
        return $node->thread;
    }

    public function getUid($node) {
        return $node->uid;
    }
    /**
    * {@inheritdoc}
    */
    protected function dataProviderClassName() {
      return '\Drupal\mobile_forum\Plugin\resource\forum\DataProviderComment';
   }
}

<?php

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\ResourceNode;
use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Exception\UnauthorizedException;

/**
 * Class ForumTopic
 * @package Drupal\mobile_forum\Plugin\resource\forum
 *
 * @Resource(
 *   name = "forumtopic:2.0",
 *   resource = "forumtopic",
 *   label = "FORUMS",
 *   description = "Export the article content type.",
 *   authenticationTypes = {
 *     "token"
 *   },
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "node",
 *     "bundles": {
 *       "forum"
 *     },
 *   },
 *   majorVersion = 2,
 *   minorVersion = 0
 * )
 */
class ForumTopic__2_0 extends ResourceNode implements ResourceInterface {

    /**
     * {@inheritdoc}
     */
    protected function publicFields() {
        $public_fields = parent::publicFields();

        $public_fields['body'] = array(
            'property' => 'body',
            'sub_property' => 'value',
        );

        $public_fields['title'] = array(
            'property' => 'title'
        );

        $public_fields['nid'] = array(
            'property' => 'nid'
        );

        $public_fields['status'] = array(
            'property' => 'status'
        );

        $public_fields['name'] = array(
            'process_callbacks' => array(
                array($this, 'getUsername')
            )
        );

        $public_fields['taxonomy_forums'] = array(
            'property' => 'taxonomy_forums'
        );

        $public_fields['vid'] = array(
            'property' => 'vid'
        );

        $public_fields['created'] = array(
            'property' => 'created'
        );

        $public_fields['changed'] = array(
            'property' => 'changed'
        );

        $public_fields['comment'] = array(
            'property' => 'comment'
        );

        $public_fields['comment_count'] = array(
            'property' => 'comment_count'
        );

        $public_fields['last_comment_timestamp'] = array(
            'process_callbacks' => array(
                array($this, 'getLastCommentTimestamp')
            )
        );

        $public_fields['forum_tid'] = array(
            'process_callbacks' => array(
                array($this, 'getForumId')
            )
        );

        $public_fields['last_comment_uid'] = array(
            'process_callbacks' => array(
                array($this, 'getLastCommentUid')
            )
        );

        $public_fields['type'] = array(
            'property' => 'type'
        );

        $public_fields['uuid'] = array(
            'process_callbacks' => array(
                array($this, 'getUid')
            )
        );

        $public_fields['uid'] = array(
            'property' => 'author',
            'sub_property' => 'uid',
        );
        
        $public_fields['picture'] = array(
            'process_callbacks' => array(
                array($this, 'getPicture')
            )
        );

        return $public_fields;
    }

    public function getUid($node){
        return $node->uid;
    }

    protected function dataProviderClassName() {
        return '\Drupal\mobile_forum\Plugin\resource\forum\DataProviderTopic';
    }

    public function getForumId($node) {
        return $node->forum_tid;
    }

    public function getPicture($node) {
        if (!isset($node->picture)) {
            return null;
        }
        $file = file_load($node->picture);
        $url = file_create_url($file->uri);
        return array('url' => $url, filename => $file->filename);
    }

    public function getUsername($node) {
        return $node->name;
    }

    public function getLastCommentUid($node) {
        return $node->last_comment_uid;
    }

    public function getLastCommentTimestamp($node) {
        return $node->last_comment_timestamp;
    }

    public function create($path) {
        // TODO: Compare this with 1.x logic.
        $object = $this->getRequest()->getParsedBody();
        $account = $this->getAccount();

        if (!isset($account) || $account->uid == 0) {
            throw new Exception("unauthorized access");
        }

        $object["uid"] = $account->uid;
        $object["forum_tid"] = $object["tid"];
        $object["taxonomy_forums"] = $object["tid"];
        $object["status"] = 1;
        unset($object["nid"]);
        unset($object["tid"]);
         unset($object["vid"]);
        unset($object["changed"]);
        unset($object["comment_count"]);
        return $this->getDataProvider()->create($object);
    }

    public function replace($path) {
        $object = $this->getRequest()->getParsedBody();
        $account = $this->getAccount();

        if (!isset($account) || $account->uid == 0) {
            throw new Exception("unauthorized access");
        }

        $object["uid"] = $account->uid;
        unset($object["nid"]);
        unset($object["vid"]);
        unset($object["changed"]);
        unset($object["comment_count"]);
        unset($object["tid"]);
        return $this->getDataProvider()->update($path, $object, FALSE);
    }

    public function remove($path) {
        $account = $this->getAccount();

        if (!isset($account) || $account->uid == 0) {
            $response_headers = restful()
                            ->getResponse()->setStatusCode(401);
            throw new UnauthorizedException('Bad credentials. Anonymous user resolved for a resource that requires authentication.');
        }

        $object["uid"] = $account->uid;
        return parent::remove($path);
    }

}

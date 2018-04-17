<?php

/**
 * @file
 * Contains \Drupal\restful_example\Plugin\resource\comment\DataProviderComment.
 */

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\DataProvider\DataProviderEntity;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderInterface;

class DataProviderComment extends DataProviderEntity implements DataProviderInterface {

    protected function alterFilterQuery(array $filter, \EntityFieldQuery $query) {
        if ($filter['public_field'] == 'uuid') {
            $filter['processed'] = true;
            $query->propertyCondition('uid', $filter['value'][0], $filter['operator'][0]);
            return $filter;
        } else {
            return parent::alterFilterQuery($filter, $query);
        }
    }

    protected static function checkPropertyAccess($resource_field, $op, $interpreter){
        $property = $resource_field->getProperty();
        $entity = $interpreter->getWrapper();
        $account = $interpreter->getAccount();

        if($property == 'subject' || $property == "comment_body"){
            $parentResult = parent::checkPropertyAccess($resource_field, $op, $interpreter);
            if(!$entity->getIdentifier()){
                return user_access("post comments", $account );
            }
            else{
                $userOwnContent = $entity->value()->uid == $account->uid;
                return (user_access("edit own comments", $account) && $userOwnContent) || $parentResult;
            }
        }
        else{
            return parent::checkPropertyAccess($resource_field, $op, $interpreter);
        }
    }


    protected function checkEntityAccess($op, $entity_type, $entity) {
      $account = $this->getAccount();
      if ($op == "create"){
         return user_access("post comments", $account);
      }
      else if ($op == "update"){
          return user_access("edit own comments", $account) && $entity->uid == $account->uid;
      }
      else{
          return parent::checkEntityAccess($op, $entity_type, $entity);
      }
    }

    /**
     * Overrides DataProviderEntity::setPropertyValues().
     *
     * Set nid and node type to a comment.
     *
     * Note that to create a comment with 'post comments' permission, apply a
     * patch on https://www.drupal.org/node/2236229
     */
    protected function setPropertyValues(\EntityDrupalWrapper $wrapper, $object, $replace = FALSE) {
        $comment = $wrapper->value();

        if (isset($object['changed'])) {
            unset($object['changed']);
        }

        if (isset($object['created'])) {
            unset($object['created']);
        }

        if (isset($object['status'])) {
            unset($object['status']);
        }

        if (isset($object['uid'])) {
            unset($object['uid']);
        }
        
        // add mode
        if (empty($comment->nid)) {
            // Comment nid must be set manually, as the nid property setter requires
            // 'administer comments' permission.
            if (!empty($object['nid'])) {
                $comment->nid = $object['nid'];
            }

            // Make sure we have a bundle name.
            $node = node_load($comment->nid);
            $comment->node_type = 'comment_node_' . $node->type;
        }
        // edit mode
        else {
            if (!empty($object['created'])) {
                unset($object['created']);
            }
        }
        unset($object['nid']);
        
        parent::setPropertyValues($wrapper, $object, $replace);
    }

    /**
     * Overrides DataProviderEntity::getQueryForList().
     *
     * Expose only published comments.
     */
    public function getQueryForList() {
        $query = parent::getQueryForList();
        $query->propertyCondition('status', COMMENT_PUBLISHED);
        return $query;
    }

    /**
     * Overrides DataProviderEntity::getQueryCount().
     *
     * Only count published comments.
     */
    public function getQueryCount() {
        $query = parent::getQueryCount();
        $query->propertyCondition('status', COMMENT_PUBLISHED);
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function entityPreSave(\EntityDrupalWrapper $wrapper) {
        $comment = $wrapper->value();
        if (!empty($comment->cid)) {
            // Comment is already saved.
            return;
        }

        $comment->uid = $this->getAccount()->uid;
    }

}

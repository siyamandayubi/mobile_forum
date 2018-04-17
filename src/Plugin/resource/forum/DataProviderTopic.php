<?php

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\DataProvider\DataProviderEntity;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderInterface;
use Drupal\restful\Util\RelationalFilter;

class DataProviderTopic extends DataProviderEntity implements DataProviderInterface {

    protected function parseRequestForListFilter() {
        $filters = parent::parseRequestForListFilter();

        foreach ($filters as $key => $filter) {
            if ($filter['public_field'] == "name" && count($filter['value']) > 0) {
                $user = user_load_by_name($filter['value'][0]);
                $filter['public_field'] = 'uid';
                $filter['value'] = array($user->uid);
                $filters[$key] = $filter;
            }

            if ($filter['public_field'] == "forum_tid" && count($filter['value']) > 0) {
                unset($filters[$key]);
            }

            if ($filter['public_field'] == "nid" && count($filter['operator']) == 1 && $filter['operator'][0] == "IN" && is_array($filter['value']) && count($filter['value']) == 1) {
                $filter['value'] = explode(',', $filter['value'][0]);
                $filters[$key] = $filter;
            }
        }

        return $filters;
    }

    protected static function checkPropertyAccess($resource_field, $op, $interpreter) {
        $property = $resource_field->getProperty();
        $entity = $interpreter->getWrapper();
        $account = $interpreter->getAccount();
        $parentResult = parent::checkPropertyAccess($resource_field, $op, $interpreter);
        if ($property == "status" || $property == "comment" || $property == "author") {
            if (!$entity->getIdentifier()) {
                return user_access("create forum content", $account);
            } else {
                $userOwnContent = $entity->value()->uid == $account->uid;
                return (user_access("edit own forum content", $account) && $userOwnContent) || $parentResult;
            }
        } else {
            return parent::checkPropertyAccess($resource_field, $op, $interpreter);
        }
    }

    protected function queryForListFilter(\EntityFieldQuery $query) {
        $query->addTag('forum_filter');

        return parent::queryForListFilter($query);
    }

}

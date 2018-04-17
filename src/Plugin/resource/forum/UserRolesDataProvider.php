<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Exception\BadRequestException;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderDbQueryInterface;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderDbQuery;

/**
 * Description of UserRolesDataProvider
 *
 * @author serva
 */
class UserRolesDataProvider extends DataProviderDbQuery implements DataProviderDbQueryInterface {

    protected function queryForListFilter(\SelectQuery $query) {
        $uid = $this->getAccount()->uid;
        $filters = $this->parseRequestForListFilter();
        $filters[] = array(
            'value' => array($uid),
            'operator' => array('='),
            'public_field' => "uid",
            'conjunction' => 'AND',
            'resource_id' => $this->pluginId
        );

        foreach ($filters as $filter) {
            /* @var ResourceFieldDbColumnInterface $filter_field */
            if (!$filter_field = $this->fieldDefinitions->get($filter['public_field'])) {
                continue;
            }
            $column_name = $filter_field->getColumnForQuery();
            if (in_array(strtoupper($filter['operator'][0]), array('IN', 'NOT IN', 'BETWEEN'))) {
                $query->condition($column_name, $filter['value'], $filter['operator'][0]);
                continue;
            }
            $condition = db_condition($filter['conjunction']);
            for ($index = 0; $index < count($filter['value']); $index++) {
                $condition->condition($column_name, $filter['value'][$index], $filter['operator'][$index]);
            }
            $query->condition($condition);
        }

        return $query;
    }

    protected function getQuery() {
        $table = $this->getTableName();
        $query = db_select('users_roles', 'u');
        $query->join('role', 'r', 'r.rid = u.rid');
        $query->join('role', 'r', 'r.rid = u.rid');
        $query->join('role_permission', 'p', 'p.rid = r.rid');
        $query->fields('p', array('rid', 'permission'));
        $query->fields('r', array('name'));
        $query->fields('u', array('uid'));
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function count() {
        $user = $this->getQueryForList()->execute();

        if (is_null($user)) {
            return 0;
        }

        return count($user->roles);
    }

}

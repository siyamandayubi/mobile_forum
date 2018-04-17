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
 * Description of NewTopicsDataProvider
 *
 * @author serva
 */
class NewTopicsDataProvider extends DataProviderDbQuery implements DataProviderDbQueryInterface {

    protected function queryForListFilter(\SelectQuery $query) {
        if (!is_null($this->account)) {
            $u_alias = $query->join("node", "n", "f.nid = n.nid");
            $query->condition("{$u_alias}.uid", $this->account->uid, "!=");
        }

        foreach ($this->parseRequestForListFilter() as $filter) {

            if (!$filter_field = $this->fieldDefinitions->get($filter['public_field'])) {
                continue;
            }

            $column_name = $filter['public_field'];
            if ($column_name == 'nid') {
                $query->condition("f.{$column_name}", $filter['value'][0], '>');
            }
            if ($column_name == 'tid') {
                $query->condition("f.tid", $filter['value'][0], '=');
            }
        }

        $query = $query->groupBy('f.tid');
        $query = $query->fields('f', array('tid'));
        $query->addExpression('count(f.nid)', 'node_count');
        $query->addExpression("max(f.nid)", "node_maxid");
        $query->addExpression('0', 'nid');

        return $query;
    }

    protected function getQuery() {
        return db_select("forum_index", "f");
    }

}

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
 * Description of NewCommentsDataProvider
 *
 * @author serva
 */
class NewCommentsDataProvider extends DataProviderDbQuery implements DataProviderDbQueryInterface {

    protected function queryForListFilter(\SelectQuery $query) {
        $n_alias = $query->join("node", "n", "c.nid = n.nid");
        $f_alias = $query->join("forum_index", "f", "f.nid = n.nid");

        if (!is_null($this->account)) {
            $or = db_or();
            $or->condition("{$n_alias}.uid", $this->account["uid"], "!=");

            $subQuery = db_select("comment", "c1")->distinct();
            $subQuery->fields("c1", array("nid"));
            $subQuery->condition("c1.uid", $this->account["uid"], "=");
            if ($column_name == 'tid') {
                $query->condition("f.tid", $filter['value'][0], '=');
            }

            $or->condition("c.nid", $subQuery, "IN");

            $query->condition($or);
        }

        foreach ($this->parseRequestForListFilter() as $filter) {

            if (!$filter_field = $this->fieldDefinitions->get($filter['public_field'])) {
                continue;
            }


            $column_name = $filter['public_field'];
            if ($column_name == 'cid') {
                $query->condition("c.cid", $filter['value'][0], '>');
            }

            $query = $query->groupBy('f.tid');
            $query = $query->groupBy('n.nid');
            $query = $query->fields('f', array('tid'));
            $query = $query->fields('n', array('nid'));
            $query->addExpression('count(c.cid)', 'comment_count');
            $query->addExpression("max(c.cid)", "comment_maxid");
        }

        return $query;
    }

    protected function getQuery() {
        $query = db_select("comment", "c");
        return $query;
    }

}

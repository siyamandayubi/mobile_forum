<?php

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\ResourceDbQuery;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class UserRoles__2_0
 * @package Drupal\mobile_forum\Plugin\resource\comment
 *
 * @Resource(
 *   name = "userroles:2.0",
 *   resource = "userroles",
 *   label = "UserRoless",
 *   description = "Export the user roles.",
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "tableName": "users_roles",
 *     "idColumn": "uid, rid",
 *     },
 *   majorVersion = 2,
 *   minorVersion = 0
 * )
 */
class UserRoles__2_0 extends ResourceDbQuery implements ResourceInterface {

    /**
     * {@inheritdoc}
     */
    protected function publicFields() {
        $public_fields = array();

        $public_fields['all'] = array(
            'process_callbacks' => array(
                array($this, 'getAll')
            )
        );
        $public_fields['uid'] = array('property' => 'uid');
        $public_fields['rid'] = array('property' => 'rid');
        $public_fields['role_name'] = array('property' => 'name');
        $public_fields['permission'] = array('property' => 'permission');
        
        return $public_fields;
    }

    public function getAll($node) {

        return $node;
    }


    protected function dataProviderClassName(){
        return '\Drupal\mobile_forum\Plugin\resource\forum\UserRolesDataProvider';
    }
}
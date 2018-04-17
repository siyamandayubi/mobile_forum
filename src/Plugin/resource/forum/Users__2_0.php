<?php

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Plugin\resource\ResourceEntity;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class Users__2_0
 * @package Drupal\mobile_forum\Plugin\resource\comment
 *
 * @Resource(
 *   name = "users:2.0",
 *   resource = "users",
 *   label = "Users",
 *   description = "Export the users.",
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "user",
 *     "bundles": {
 *       "user"
 *     },
 *   },
 *   majorVersion = 2,
 *   minorVersion = 0
 * )
 */
class Users__2_0 extends ResourceEntity implements ResourceInterface {

    /**
     * {@inheritdoc}
     */
    protected function publicFields() {
        $public_fields = parent::publicFields();

        $public_fields['all'] = array(
            'process_callbacks' => array(
                array($this, 'getAll')
            )
        );
        
        return $public_fields;
    }

    public function getAll($node) {

        return $node;
    }

}

<?php

use Drupal\restful\Authentication\AuthenticationManager;

function forum_page() {
    $forum_term = forum_forum_load(0);

    return drupal_json_output($forum_term);
}

function mobile_api_user_roles() {
    $handler = restful()->getResourceManager()->getPlugin('forumtopic:2.0');
    $authentication_manager = new AuthenticationManager();
    $authentication_manager->addAuthenticationProvider('token');
    $account = $authentication_manager->getAccount($handler->getRequest());
    $returnValue = array();
    if (!isset($account)) {
        $returnValue['error'] = t('User is not logined into the system');
    } else {
        $returnValue['roles'] = $account->roles;
    }
    return drupal_json_output($returnValue);
}

function forum_topics($tid = NULL) {
    $forum_term = taxonomy_term_load($tid);

    $forum_per_page = variable_get('forum_per_page', 25);
    $sortby = variable_get('forum_order', 1);

    if (empty($forum_term->container)) {
        $topics = forum_get_topics($forum_term->tid, $sortby, $forum_per_page);
    } else {
        $topics = '';
    }

    $output = array(
        'forum' => $forum_term,
        'topics' => $topics
    );

    return drupal_json_output($output);
}

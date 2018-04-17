<?php

namespace Drupal\mobile_forum\Plugin\resource\forum;

use Drupal\restful\Exception\BadRequestException;
use Drupal\restful\Exception\InaccessibleRecordException;
use Drupal\restful\Exception\UnprocessableEntityException;
use Drupal\restful\Http\RequestInterface;
use Drupal\restful\Plugin\resource\DataInterpreter\ArrayWrapper;
use Drupal\restful\Plugin\resource\DataProvider\DataProvider;
use Drupal\restful\Plugin\resource\DataProvider\DataProviderInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldCollectionInterface;
//require_once DRUPAL_ROOT.'/modules/forum/forum.module';
/**
 * Class DataProviderVariable
 *
 * @package Drupal\restful_example\Plugin\resource\variables
 */
class DataProviderForum extends DataProvider implements DataProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(RequestInterface $request, ResourceFieldCollectionInterface $field_definitions, $account, $plugin_id, $resource_path = NULL, array $options = array(), $langcode = NULL) {
    parent::__construct($request, $field_definitions, $account, $plugin_id, $resource_path, $options, $langcode);
    if (empty($this->options['urlParams'])) {
      $this->options['urlParams'] = array(
        'filter' => TRUE,
        'sort' => TRUE,
        'fields' => TRUE,
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count($this->getIndexIds());
  }

   /**
   * {@inheritdoc}
   */
  public function view($identifier) {
     $forum_term = forum_forum_load(0);
     return $forum_term;
   }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $identifiers) {
    $return = array();
    foreach ($identifiers as $identifier) {
      try {
        $row = $this->view($identifier);
      }
      catch (InaccessibleRecordException $e) {
        $row = NULL;
      }
      $return[] = $row;
    }

    return array_values(array_filter($return));
  }
  
   public function create($object) {
       
   }
   
     public function update($identifier, $object, $replace = FALSE) {

  }

  /**
   * {@inheritdoc}
   */
  public function remove($identifier) {

  }

  /**
   * {@inheritdoc}
   */
  public function getIndexIds() {
    $output = array();
      $output[] = array('name' => 'forum', 'value' => 'forum');
    return array_map(function ($item) { return $item['name']; }, $output);
  }

  /**
   * Removes plugins from the list based on the request options.
   *
   * @param \Drupal\restful\Plugin\resource\ResourceInterface[] $variables
   *   The array of resource plugins keyed by instance ID.
   *
   * @return \Drupal\restful\Plugin\resource\ResourceInterface[]
   *   The same array minus the filtered plugins.
   *
   * @throws \Drupal\restful\Exception\BadRequestException
   * @throws \Drupal\restful\Exception\ServiceUnavailableException
   */
  protected function applyFilters(array $variables) {
    return $variables;
  }

  /**
   * Sorts plugins on the list based on the request options.
   *
   * @param \Drupal\restful\Plugin\resource\ResourceInterface[] $variables
   *   The array of resource plugins keyed by instance ID.
   *
   * @return \Drupal\restful\Plugin\resource\ResourceInterface[]
   *   The sorted array.
   *
   * @throws \Drupal\restful\Exception\BadRequestException
   * @throws \Drupal\restful\Exception\ServiceUnavailableException
   */
  protected function applySort(array $variables) {
    return $variables;
  }

  /**
   * {@inheritdoc}
   */
  protected function initDataInterpreter($identifier) {
    return new DataInterpreterVariable($this->getAccount(), new ArrayWrapper(array(
      'name' => $identifier,
      'value' => variable_get($identifier),
    )));
  }

  /**
   * Finds the public field name that has the provided property.
   *
   * @param string $property
   *   The property to find.
   *
   * @return string
   *   The name of the public name.
   */
  protected function searchPublicFieldByProperty($property) {
    foreach ($this->fieldDefinitions as $public_name => $resource_field) {
      if ($resource_field->getProperty() == $property) {
        return $public_name;
      }
    }
    return NULL;
  }
}

<?php

namespace Drupal\site_studio_custom_elements\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * API Data element plugin for DX8.
 *
 * @package Drupal\cohesion\Plugin\CustomElement
 *
 * @CustomElement(
 *   id = "api_data_element",
 *   label = @Translation("API Data element")
 * )
 */
class APIData extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      'apiurl' => [
        'htmlClass' => 'ssa-grid-col-12',
        'title' => 'API URL.',
        'type' => 'textfield',
        'placeholder' => 'API URL..',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
      'jsonkey' => [
        'htmlClass' => 'ssa-grid-col-12',
        'title' => 'JSON Key',
        'type' => 'textfield',
        'placeholder' => 'JSON Key...',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($element_settings, $element_markup, $element_class, $element_context = []) {
    // Render the element.
    $element_markup['output'] = '';
    \Drupal::logger('apiurl')->notice($element_settings['apiurl']);
    $client = \Drupal::service('http_client');

    $url = $element_settings['apiurl'];

    try {
      // Make the request using the http_client service.
      $response = $client->get($url);

      // Check if the request was successful (status code 200).
      if ($response->getStatusCode() == 200) {
        $data = $response->getBody()->getContents();
        $output = json_decode($data, TRUE);
        $element_markup['output'] = $output[$element_settings['jsonkey']];
      }
    } catch (\Exception $e) {
      // Handle exceptions.
      \Drupal::logger('custom_module')->error('API request failed: @message', ['@message' => $e->getMessage()]);
    }
    return [
      '#theme' => 'site_studio_custom_elements',
      '#template' => 'api-data-element-template',
      '#elementSettings' => $element_settings,
      '#elementMarkup' => $element_markup,
      '#elementContext' => $element_context,
      '#elementClass' => $element_class,
    ];
  }

}

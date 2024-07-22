<?php

namespace Drupal\altudo_cookie\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class UserInfoForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_info_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['altudo_cookie.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $cookies = $_COOKIE['gcc_newprofile'] ?? null;
    $gcc_newprofile = $cookies ? json_decode($cookies, true) : [];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#default_value' => $gcc_newprofile['first_name'] ?? '',
      '#required' => TRUE,
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => $gcc_newprofile['last_name'] ?? '',
      '#required' => TRUE,
    ];
    $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#default_value' => $gcc_newprofile['country'] ?? '',
      '#required' => TRUE,
    ];
    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => $gcc_newprofile['city'] ?? '',
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
      '#submit' => ['::resetForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $gcc_newprofile = [
      'first_name' => $form_state->getValue('first_name'),
      'last_name' => $form_state->getValue('last_name'),
      'country' => $form_state->getValue('country'),
      'city' => $form_state->getValue('city'),
    ];

    setcookie('gcc_newprofile', json_encode($gcc_newprofile), time() + (86400 * 30), '/'); // Set cookie for 30 days
    \Drupal::messenger()->addMessage($this->t('User information saved in cookies.'));
  }

  /**
   * Custom submit handler for the reset button.
   */
  public function resetForm(array &$form, FormStateInterface $form_state) {
    setcookie('gcc_newprofile', '', time() - 3600, '/'); // Delete the cookie
    \Drupal::messenger()->addMessage($this->t('User information cookie has been reset.'));
  }
}

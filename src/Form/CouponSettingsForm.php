<?php

namespace Drupal\smmg_coupon\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class CouponSettingsForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'smmg_coupon_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'smmg_coupon.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('smmg_coupon.settings');

        $options_path_type = ['included'=> 'Included', 'module' => 'Module', 'theme' => 'Theme'];


        // Fieldset General
        //   - Currency
        //   - Coupon Name Singular
        //   - Coupon Name Plural

        //  Fieldset Email
        //   - Email Address From
        //   - Email Address To
        //   - Email Test
        //
        //
        // Fieldset Twig Templates
        //   - Root of Templates
        //     - Module or Theme
        //     - Name of Module or Theme
        //   - Template Thank You
        //   - Template Email HTML
        //   - Template Email Plain
        //
        // Fieldset Fields for Coupon
        //   - Number
        //   - Amount


        // Fieldset General
        // -------------------------------------------------------------
        $form['general'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('General'),
            '#attributes' => ['class' => ['coupon-settings-general']],
        ];

        // - Currency
        $form['general']['currency'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Currency (USD, EUR, SFr)'),
            '#default_value' => $config->get('currency'),
        );

        //   - Coupon Name Singular
        $form['general']['coupon_name_singular'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Coupon Name Singular'),
            '#default_value' => $config->get('coupon_name_singular'),
        );

        //   - Coupon Name Plural
        $form['general']['coupon_name_plural'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Coupon Name Plural'),
            '#default_value' => $config->get('coupon_name_plural'),
        );

        // Fieldset Email
        // -------------------------------------------------------------
        $form['email'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Email Settings'),
            '#attributes' => ['class' => ['coupon-email-settings']],
        ];

        // - Email From
        $form['email']['email_from'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Email: From (newsletter@example.com)'),
            '#default_value' => $config->get('email_from'),
        );

        // - Email To
        $form['email']['email_to'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Email: to (sale@example.com, info@example.com)'),
            '#default_value' => $config->get('email_to'),
        );

        // - Email Test
        $form['email']['email_test'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Testmode: Don\'t send email to Subscriber'),
            '#default_value' => $config->get('email_test'),
        );

        // Fieldset Twig Templates
        // -------------------------------------------------------------

        $form['templates'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Templates'),
            '#attributes' => ['class' => ['coupon-settings-templates']],
        ];

        //   - Root of Templates
        $form['templates']['root_of_templates'] = array(
            '#markup' => $this->t('Path of Templates'),
        );
        //     - Module or Theme
        $form['templates']['get_path_type'] = array(
            '#type' => 'select',
            '#options' => $options_path_type,
            // '#value' => $default_number,
            '#title' => $this->t('Module or Theme'),
            '#default_value' => $config->get('get_path_type'),
        );

        //     - Name of Module or Theme
        $form['templates']['get_path_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name of Module or Theme'),
            '#default_value' => $config->get('get_path_name'),
        );

        //   - Template Thank You
        $form['templates']['template_thank_you'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Template Thank You'),
            '#default_value' => $config->get('template_thank_you'),
        );

        //   - Template Email HTML
        $form['templates']['template_email_html'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Template Email Html'),
            '#default_value' => $config->get('template_email_html'),
        );

        //   - Template Email Plain
        $form['templates']['template_email_plain'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Template Email Plain'),
            '#default_value' => $config->get('template_email_plain'),
        );


        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Retrieve the configuration
        $this->configFactory->getEditable('smmg_coupon.settings')
            //
            //
            // Fieldset General
            // -------------------------------------------------------------
            // - Currency
            ->set('currency', $form_state->getValue('currency'))
            // - Coupon Name Singular
            ->set('coupon_name_singular', $form_state->getValue('coupon_name_singular'))
            // - Coupon Name Plural
            ->set('coupon_name_plural', $form_state->getValue('coupon_name_plural'))
            //
            //
            // Fieldset Email
            // -------------------------------------------------------------
            // - Email From
            ->set('email_from', $form_state->getValue('email_from'))
            // - Email to
            ->set('email_to', $form_state->getValue('email_to'))
            // - Email Test
            ->set('email_test', $form_state->getValue('email_test'))
            //
            //
            // Fieldset Twig Templates
            // -------------------------------------------------------------
            // - Module or Theme
            ->set('get_path_type', $form_state->getValue('get_path_type'))
            // - Name of Module or Theme
            ->set('get_path_name', $form_state->getValue('get_path_name'))
            // - Template Thank You
            ->set('template_thank_you', $form_state->getValue('template_thank_you'))
            // - Template Email HTML
            ->set('template_email_html', $form_state->getValue('template_email_html'))
            // - Template Email Plain
            ->set('template_email_plain', $form_state->getValue('template_email_plain'))
            //
            // Fieldset Fields for Coupon
            // -------------------------------------------------------------
//
            //   - Number
            //   - Amount

            ->save();

        parent::submitForm($form, $form_state);
    }
}

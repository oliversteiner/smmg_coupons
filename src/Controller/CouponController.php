<?php


namespace Drupal\smmg_coupon\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\small_messages\Utility\Helper;
use Drupal\smmg_coupon\Utility\CouponTrait;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CouponController extends ControllerBase
{

    use CouponTrait;

    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return 'smmg_coupon';
    }

    public static function newOrder(array $data)
    {

        $coupons = [];

        // save coupon units
        for ($i = 0; $i < 10; $i++) {
            $number = $data['coupons'][$i]['number'];
            $amount = $data['coupons'][$i]['amount'];

            if ($number > 0) {
                $result = self::newUnitOrder($number, $amount);
                $coupons[$i] = $result['nid'];
            }
        }


        // Load List for origin
        $vid = 'smmg_origin';
        $origin_list = Helper::getTermsByName($vid);

        // Token
        $token = $data['token'];


        // Fieldset address
        $gender = $data['gender'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $street_and_number = $data['street_and_number'];
        $zip_code = $data['zip_code'];
        $city = $data['city'];
        $email = $data['email'];
        $phone = $data['phone'];


        if ($first_name && $last_name) {
            $title = $first_name . ' ' . $last_name;
        } else {
            $title = $email;
        }

        $output = [
            'status' => FALSE,
            'mode' => 'save',
            'nid' => FALSE,
            'message' => '',
        ];


        $storage = \Drupal::entityTypeManager()->getStorage('node');
        $new_order = $storage->create(
            [
                'type' => 'coupon_order',
                'title' => $title,
                'field_gender' => $gender,
                'field_first_name' => $first_name,
                'field_last_name' => $last_name,
                'field_phone' => $phone,
                'field_street_and_number' => $street_and_number,
                'field_zip_code' => $zip_code,
                'field_city' => $city,
                'field_email' => $email,

                // Origin
                'field_smmg_origin' => $origin_list['coupon'],

                // Token
                'field_smmg_token' => $token,

            ]);


        // coupon
        $new_order->get('field_coupon_unit')->setValue($coupons);


        // Save
        $new_order->save();
        $new_order_nid = $new_order->id();


        // if OK
        if ($new_order_nid) {

            $message = t('Coupon Order successfully saved');
            $output['message'] = $message;
            $output['status'] = TRUE;
            $output['nid'] = $new_order_nid;

            self::sendNotivicationMailNewCoupon($new_order_nid, $token);
        }


        return $output;
    }


    public static function newUnitOrder($number, $amount)
    {
        $output = [
            'status' => FALSE,
            'mode' => 'save',
            'nid' => FALSE,
            'message' => '',
        ];

        $storage = \Drupal::entityTypeManager()->getStorage('node');
        $new_unit_order = $storage->create(
            [
                'type' => 'coupon_unit',
                'field_coupon_number' => $number,
                'field_coupon_amount' => $amount,
            ]);

        // Save
        $new_unit_order->save();
        $new_order_nid = $new_unit_order->id();

        // if OK
        if ($new_order_nid) {

            $message = t('Information successfully saved');
            $output['message'] = $message;
            $output['status'] = TRUE;
            $output['nid'] = $new_order_nid;
        }

        return $output;
    }


    public function sandboxEmail($coupon_order_nid, $token = null, $output_mode = 'html')
    {
        $build = false;

        // Get Content
        $data = self::couponVariables($coupon_order_nid, $token);
        $data['sandbox'] = true;

        // HTML Email
        if ($output_mode == 'html') {

            // Build HTML Content
            $template_path = drupal_get_path('module', 'smmg_coupon') . "/templates/smmg-coupon-email.html.twig";
            $template = file_get_contents($template_path);
            $build_html = [
                'description' => [
                    '#type' => 'inline_template',
                    '#template' => $template,
                    '#context' => $data,
                ],
            ];

            $build = $build_html;
        }


        // Plaintext
        if ($output_mode == 'plain') {

            // Build Plain Text Content
            $template_path = drupal_get_path('module', 'smmg_coupon') . "/templates/smmg-coupon-email-plain.html.twig";
            $template = file_get_contents($template_path);
            $build_plain = [
                'description' => [
                    '#type' => 'inline_template',
                    '#template' => $template,
                    '#context' => $data,
                ],
            ];

            $build = $build_plain;

        }

        return $build;
    }


    public function thankYouPage($coupon_order_nid, $token, $member_nid = null)
    {
        // Make sure you don't trust the URL to be safe! Always check for exploits.
        if ($coupon_order_nid != false && !is_numeric($coupon_order_nid)) {
            // We will just show a standard "access denied" page in this case.
            throw new AccessDeniedHttpException();
        }

        if ($member_nid != false && !is_numeric($member_nid)) {
            throw new AccessDeniedHttpException();
        }

        if ($token == false) {
            throw new AccessDeniedHttpException();
        }


        $template_path = drupal_get_path('module', $this->getModuleName()) . "/templates/smmg-coupon-thanks.html.twig";
        $template = file_get_contents($template_path);
        $build = [
            'description' => [
                '#type' => 'inline_template',
                '#template' => $template,
                '#attached' => ['library' => ['smmg_coupon/smmg_coupon.main']],
                '#context' => $this->couponVariables($coupon_order_nid, $member_nid, $token),
            ],
        ];
        return $build;
    }


    public function sandboxSendEmail($coupon_order_nid, $token = null, $output_mode = 'html')
    {
        $build = false;

        // Get Content
        $data = self::couponVariables($coupon_order_nid, $token);
        $data['sandbox'] = true;

        // HTML Email
        if ($output_mode == 'html') {

            // Build HTML Content
            $template_path = drupal_get_path('module', 'smmg_coupon') . "/templates/smmg-coupon-email.html.twig";
            $template = file_get_contents($template_path);
            $build_html = [
                'description' => [
                    '#type' => 'inline_template',
                    '#template' => $template,
                    '#context' => $data,
                ],
            ];

            $build = $build_html;
        }


        // Plaintext
        if ($output_mode == 'plain') {

            // Build Plain Text Content
            $template_path = drupal_get_path('module', 'smmg_coupon') . "/templates/smmg-coupon-email-plain.html.twig";
            $template = file_get_contents($template_path);
            $build_plain = [
                'description' => [
                    '#type' => 'inline_template',
                    '#template' => $template,
                    '#context' => $data,
                ],
            ];

            $build = $build_plain;

        }

        self::sendNotivicationMailNewCoupon($coupon_order_nid, $token);

        return $build;
    }

}
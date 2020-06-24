<?php
namespace Grav\Plugin\Endpoints;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Uri;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\CurlPost;

require_once 'resource.php';

/**
 * Captcha Verification API
 */
class Captcha extends Resource
{

    /**
     * Post Captcha
     *
     * Implements:
     *
     * - POST /api/captcha
     * - @return response
     */
    public function postItem($config)
    {

        $secret = $config['recaptcha']['secret_key'];

        $data = $this->getPost();
        $uri = $this->grav['uri'];
        $action = $data->action;
        $hostname = $uri->host();
        $ip = Uri::ip();

        $recaptcha = new ReCaptcha($secret);

        if (extension_loaded('curl')) {
            $recaptcha = new ReCaptcha($secret, new CurlPost());
        }

        // Add version 3 specific options

        $token = $data->token;
        $resp = $recaptcha
            ->setExpectedHostname($hostname)
            ->setExpectedAction($action)
            ->setScoreThreshold(0.5)
            ->verify($token, $ip);

        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();

            $this->grav['log']->addWarning('Form reCAPTCHA Errors: [' . $uri->route() . '] ' . json_encode($errors));

            return json_encode($errors);
        }

        return json_encode($resp->isSuccess());

    }
}

<?php

/**
 * Class RecaptchaModel
 *
 * Handles server-side verification of Google reCAPTCHA (v2 checkbox) responses.
 * @see https://developers.google.com/recaptcha/docs/verify
 */
class RecaptchaModel
{
    /**
     * Verifies the "g-recaptcha-response" token (sent by the reCAPTCHA widget on form submit) against
     * Google's siteverify endpoint using the server-side secret key.
     *
     * @param string $recaptcha_response the value of the submitted "g-recaptcha-response" field
     * @return bool true if the user successfully solved the reCAPTCHA, false otherwise
     */
    public static function verify($recaptcha_response)
    {
        if (empty($recaptcha_response)) {
            return false;
        }

        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
            'secret' => Config::get('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        )));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($curl);
        curl_close($curl);

        if ($result === false) {
            return false;
        }

        $result = json_decode($result);

        return isset($result->success) && $result->success === true;
    }
}

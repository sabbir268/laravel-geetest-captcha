<?php

namespace Salahhusa9\GeetestCaptcha;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GeetestCaptcha
{
    private $captcha_id;
    private $captcha_key;
    private $api_server;
    private $validatedData;
    private $timeout;
    private $httpClient;

    public function __construct()
    {
        $this->captcha_id = config('geetest-captcha.captcha_id', env('GEETEST_ID'));
        $this->captcha_key = config('geetest-captcha.captcha_key', env('GEETEST_KEY'));
        $this->api_server = config('geetest-captcha.api_server', 'http://gcaptcha4.geetest.com');
        $this->timeout = config('geetest-captcha.timeout', 5);
        $this->httpClient = new Client(['timeout' => $this->timeout]);
    }

    /**
     * Validate the captcha response
     *
     * @param string|null $value
     * @return bool
     */
    public function validate($value)
    {
        if (empty($value) || empty($this->captcha_id) || empty($this->captcha_key)) {
            return false;
        }

        try {
            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            $decodedValue = json_decode($value, true);

            if (!is_array($decodedValue) || !$this->hasRequiredFields($decodedValue)) {
                return false;
            }

            // Extract verification parameters
            $lot_number = $decodedValue['lot_number'];
            $captcha_output = $decodedValue['captcha_output'];
            $pass_token = $decodedValue['pass_token'];
            $gen_time = $decodedValue['gen_time'];

            // Generate signature
            $sign_token = hash_hmac('sha256', $lot_number, $this->captcha_key);

            // Prepare query parameters
            $query = [
                'lot_number' => $lot_number,
                'captcha_output' => $captcha_output,
                'pass_token' => $pass_token,
                'gen_time' => $gen_time,
                'sign_token' => $sign_token,
            ];

            // Make API request
            $url = sprintf('%s/validate?captcha_id=%s', $this->api_server, $this->captcha_id);
            $response = $this->makeApiRequest($url, $query);

            if (!$response) {
                return false;
            }

            $responseData = json_decode($response, true);
            $this->validatedData = $responseData;

            return isset($responseData['result']) && $responseData['result'] === 'success';

        } catch (\Exception $e) {
            Log::error('GeeTest Captcha validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the decoded value has all required fields
     *
     * @param array $decodedValue
     * @return bool
     */
    private function hasRequiredFields($decodedValue)
    {
        $requiredFields = ['lot_number', 'captcha_output', 'pass_token', 'gen_time'];

        foreach ($requiredFields as $field) {
            if (!isset($decodedValue[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Make API request to GeeTest
     *
     * @param string $url
     * @param array $postData
     * @return string|false
     */
    private function makeApiRequest($url, $postData)
    {
        try {
            $response = $this->httpClient->post($url, [
                'form_params' => $postData,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->getBody()->getContents();
            }

            return false;

        } catch (RequestException $e) {
            Log::error('GeeTest API request failed: ' . $e->getMessage());

            // Return a fallback response for API failures
            return json_encode([
                'result' => 'success',
                'reason' => 'request geetest api fail',
            ]);
        }
    }

    /**
     * Get validated data
     *
     * @return array|null
     */
    public function getValidatedData()
    {
        return $this->validatedData;
    }

    /**
     * Set captcha configuration dynamically
     *
     * @param string $captcha_id
     * @param string $captcha_key
     * @param string|null $api_server
     * @return $this
     */
    public function setConfig($captcha_id, $captcha_key, $api_server = null)
    {
        $this->captcha_id = $captcha_id;
        $this->captcha_key = $captcha_key;

        if ($api_server) {
            $this->api_server = $api_server;
        }

        return $this;
    }
}

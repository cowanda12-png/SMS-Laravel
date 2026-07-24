<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $baseUrl;
    protected $shortcode;
    protected $passkey;
    protected $callbackUrl;
    protected $transactionType;
    protected $environment;

    public function __construct()
    {
        // ⭐ FIX: Use env() directly with fallbacks for better reliability
        $this->consumerKey = env('MPESA_CONSUMER_KEY', config('mpesa.consumer_key', ''));
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET', config('mpesa.consumer_secret', ''));
        $this->passkey = env('MPESA_PASSKEY', config('mpesa.passkey', ''));
        $this->shortcode = env('MPESA_SHORTCODE', config('mpesa.shortcode', '174379'));
        $this->callbackUrl = env('MPESA_CALLBACK_URL', config('mpesa.callback_url', url('/api/mpesa/callback')));
        $this->transactionType = env('MPESA_TRANSACTION_TYPE', config('mpesa.transaction_type', 'CustomerPayBillOnline'));
        $this->environment = env('MPESA_ENV', config('mpesa.env', 'sandbox'));
        
        // ⭐ FIX: Determine base URL based on environment
        $this->baseUrl = $this->environment === 'production' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';

        Log::info('MpesaService initialized:', [
            'environment' => $this->environment,
            'shortcode' => $this->shortcode,
            'callback_url' => $this->callbackUrl,
            'base_url' => $this->baseUrl,
            'is_configured' => $this->isConfigured()
        ]);
    }

    /**
     * Step 1: Get Access Token
     */
    public function getAccessToken()
    {
        try {
            // ⭐ FIX: Check if configured before attempting
            if (!$this->isConfigured()) {
                Log::error('M-Pesa not configured properly');
                return null;
            }

            $url = $this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials';
            
            Log::info('Requesting M-Pesa access token from: ' . $url);

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('M-Pesa Access Token Retrieved Successfully');
                return $data['access_token'] ?? null;
            }

            Log::error('M-Pesa Token Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('M-Pesa Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 2: Send STK Push - FIXED with better error handling
     */
    public function stkPush($phone, $amount, $accountRef = 'STUDENT-PAY', $transactionDesc = 'Student Fee Payment')
    {
        try {
            // ⭐ FIX: Check if configured
            if (!$this->isConfigured()) {
                Log::error('M-Pesa is not configured properly');
                return [
                    'success' => false, 
                    'message' => 'M-Pesa is not configured. Please check your credentials.'
                ];
            }

            // Format phone number
            $phone = $this->formatPhoneNumber($phone);

            // Validate phone number
            if (!$this->validatePhoneNumber($phone)) {
                Log::warning('Invalid phone number format:', ['phone' => $phone]);
                return [
                    'success' => false, 
                    'message' => 'Invalid phone number format. Please use a valid Safaricom number (07XXXXXXXX or 2547XXXXXXXX).'
                ];
            }

            // Get access token
            $token = $this->getAccessToken();
            if (!$token) {
                Log::error('Failed to get access token');
                return ['success' => false, 'message' => 'Failed to get access token. Please check your credentials.'];
            }

            // Generate password
            $timestamp = now()->format('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            // Prepare request
            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => $this->transactionType,
                'Amount' => (int) ceil($amount),
                'PartyA' => $phone,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $accountRef,
                'TransactionDesc' => $transactionDesc,
            ];

            Log::info('STK Push Request:', [
                'phone' => $phone,
                'amount' => $amount,
                'account_ref' => $accountRef,
                'shortcode' => $this->shortcode,
                'environment' => $this->environment,
                'callback_url' => $this->callbackUrl
            ]);

            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

            $data = $response->json();

            Log::info('STK Push Response:', $data);

            // Check for successful response
            if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'checkout_request_id' => $data['CheckoutRequestID'] ?? null,
                    'response_code' => $data['ResponseCode'],
                    'customer_message' => $data['CustomerMessage'] ?? 'STK Push sent successfully',
                    'merchant_request_id' => $data['MerchantRequestID'] ?? null,
                ];
            }

            // ⭐ FIX: Better error message extraction
            $errorMessage = $data['errorMessage'] ?? 
                           $data['ResponseDescription'] ?? 
                           $data['message'] ?? 
                           'STK Push failed';

            // ⭐ FIX: Check for specific error codes
            if (isset($data['ResponseCode'])) {
                $errorCodes = [
                    '1' => 'Invalid amount or phone number',
                    '2' => 'Invalid transaction type',
                    '3' => 'Invalid shortcode',
                    '4' => 'Service unavailable',
                    '5' => 'System error',
                    '6' => 'Transaction declined',
                    '7' => 'Insufficient balance',
                ];
                
                if (isset($errorCodes[$data['ResponseCode']])) {
                    $errorMessage = $errorCodes[$data['ResponseCode']];
                }
            }
            
            return [
                'success' => false,
                'message' => $errorMessage,
                'response_code' => $data['ResponseCode'] ?? null,
                'raw_response' => $data
            ];

        } catch (\Exception $e) {
            Log::error('STK Push Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query the status of a transaction - FIXED
     */
    public function queryStatus($checkoutRequestId)
    {
        try {
            if (empty($checkoutRequestId)) {
                return ['success' => false, 'message' => 'Checkout request ID is required'];
            }

            // ⭐ FIX: Check if configured
            if (!$this->isConfigured()) {
                Log::error('M-Pesa is not configured properly');
                return ['success' => false, 'message' => 'M-Pesa is not configured'];
            }

            $token = $this->getAccessToken();
            if (!$token) {
                Log::error('Failed to get access token for status query');
                return ['success' => false, 'message' => 'Failed to get access token'];
            }

            $timestamp = now()->format('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ];

            Log::info('Query Status Request:', ['checkout_request_id' => $checkoutRequestId]);

            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', $payload);

            $data = $response->json();

            Log::info('Query Status Response:', $data);

            // ⭐ FIX: Check for error responses
            if (isset($data['errorCode'])) {
                return [
                    'success' => false,
                    'message' => $data['errorMessage'] ?? 'Query failed',
                    'error_code' => $data['errorCode']
                ];
            }

            // Check for successful query
            if (isset($data['ResultCode'])) {
                $resultCode = $data['ResultCode'];
                $resultDesc = $data['ResultDesc'] ?? 'No description provided';

                // Extract transaction details if successful
                $amount = null;
                $mpesaReceiptNumber = null;
                $transactionDate = null;
                $phoneNumber = null;

                if ($resultCode === '0' && isset($data['Result']['ResultParameters']['ResultParameter'])) {
                    foreach ($data['Result']['ResultParameters']['ResultParameter'] as $param) {
                        if ($param['Key'] === 'Amount') {
                            $amount = $param['Value'];
                        }
                        if ($param['Key'] === 'MpesaReceiptNumber') {
                            $mpesaReceiptNumber = $param['Value'];
                        }
                        if ($param['Key'] === 'TransactionDate') {
                            $transactionDate = $param['Value'];
                        }
                        if ($param['Key'] === 'PhoneNumber') {
                            $phoneNumber = $param['Value'];
                        }
                    }
                }

                return [
                    'success' => true,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                    'amount' => $amount,
                    'mpesa_receipt_number' => $mpesaReceiptNumber,
                    'transaction_date' => $transactionDate,
                    'phone_number' => $phoneNumber,
                    'data' => $data,
                    'is_successful' => $resultCode === '0',
                    'is_cancelled' => $resultCode === '1032',
                    'is_timeout' => $resultCode === '1037',
                    'is_wrong_pin' => $resultCode === '2001',
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid response from M-Pesa',
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Query Status Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if M-Pesa is configured properly - FIXED
     */
    public function isConfigured()
    {
        $configured = !empty($this->consumerKey) && 
                      !empty($this->consumerSecret) && 
                      !empty($this->passkey) &&
                      !empty($this->shortcode);
        
        if (!$configured) {
            Log::warning('M-Pesa not fully configured:', [
                'consumer_key' => !empty($this->consumerKey) ? 'set' : 'missing',
                'consumer_secret' => !empty($this->consumerSecret) ? 'set' : 'missing',
                'passkey' => !empty($this->passkey) ? 'set' : 'missing',
                'shortcode' => !empty($this->shortcode) ? 'set' : 'missing',
            ]);
        }
        
        return $configured;
    }

    /**
     * Validate phone number format - FIXED with better pattern
     */
    private function validatePhoneNumber($phone)
    {
        // Check if phone is valid Safaricom number
        // Format: 2547XXXXXXXX (12 digits) or 2541XXXXXXXX (12 digits)
        $pattern = '/^254[17][0-9]{8}$/';
        $isValid = preg_match($pattern, $phone) === 1;
        
        if (!$isValid) {
            Log::debug('Phone validation failed:', ['phone' => $phone, 'pattern' => $pattern]);
        }
        
        return $isValid;
    }

    /**
     * Format phone number for M-Pesa - FIXED
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Remove leading 0 if present
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        
        // Remove leading + if present
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }
        
        // If phone is 10 digits and doesn't start with 254, add 254
        if (strlen($phone) === 10 && !str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }
        
        // If phone is 9 digits (starting with 7), add 254
        if (strlen($phone) === 9 && str_starts_with($phone, '7')) {
            $phone = '254' . $phone;
        }
        
        Log::debug('Phone formatted:', ['original' => $phone, 'formatted' => $phone]);
        
        return $phone;
    }

    /**
     * Get human-readable status description - FIXED with more statuses
     */
    public function getStatusDescription($resultCode)
    {
        $statuses = [
            '0' => 'Payment successful',
            '1032' => 'Payment cancelled by user',
            '1037' => 'Payment timed out',
            '2001' => 'Wrong PIN entered',
            '2002' => 'Insufficient balance',
            '2003' => 'Transaction declined',
            '2004' => 'Transaction failed',
            '2005' => 'Invalid transaction',
            '2006' => 'System error',
            '2007' => 'Invalid phone number',
            '2008' => 'Invalid amount',
            '2009' => 'Invalid account reference',
            '2010' => 'Service unavailable',
        ];

        return $statuses[$resultCode] ?? 'Unknown status: ' . $resultCode;
    }

    /**
     * Get M-Pesa environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Check if in sandbox mode
     */
    public function isSandbox()
    {
        return $this->environment === 'sandbox';
    }

    /**
     * Check if in production mode
     */
    public function isProduction()
    {
        return $this->environment === 'production';
    }

    /**
     * Get configuration summary - NEW
     */
    public function getConfigSummary()
    {
        return [
            'environment' => $this->environment,
            'base_url' => $this->baseUrl,
            'shortcode' => $this->shortcode,
            'callback_url' => $this->callbackUrl,
            'transaction_type' => $this->transactionType,
            'is_configured' => $this->isConfigured(),
            'consumer_key_set' => !empty($this->consumerKey),
            'consumer_secret_set' => !empty($this->consumerSecret),
            'passkey_set' => !empty($this->passkey),
        ];
    }

    /**
     * Test the M-Pesa connection - NEW
     */
    public function testConnection()
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'M-Pesa is not configured properly',
                'config' => $this->getConfigSummary()
            ];
        }

        try {
            $token = $this->getAccessToken();
            return [
                'success' => $token !== null,
                'message' => $token ? 'Connection successful' : 'Failed to get access token',
                'token_received' => $token !== null,
                'config' => $this->getConfigSummary()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'config' => $this->getConfigSummary()
            ];
        }
    }
}
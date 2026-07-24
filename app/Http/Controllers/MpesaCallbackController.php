<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    public function callback(Request $request)
    {
        try {
            Log::info('M-Pesa Callback Received:', $request->all());

            $data = $request->all();

            if (!isset($data['Body']['stkCallback'])) {
                Log::error('Invalid callback format');
                return response()->json(['success' => false], 400);
            }

            $callback = $data['Body']['stkCallback'];
            $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
            $resultCode = $callback['ResultCode'] ?? null;
            $resultDesc = $callback['ResultDesc'] ?? '';

            Log::info("Processing: checkout_id={$checkoutRequestId}, result={$resultCode}");

            if (!$checkoutRequestId) {
                Log::error('Missing CheckoutRequestID');
                return response()->json(['success' => false], 400);
            }

            // Find the fee
            $fee = Fee::where('mpesa_checkout_request_id', $checkoutRequestId)->first();

            if (!$fee) {
                Log::warning('Fee not found for checkout_id: ' . $checkoutRequestId);
                
                // Try to find by phone number
                $phone = null;
                if (isset($callback['CallbackMetadata']['Item'])) {
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        if ($item['Name'] === 'PhoneNumber') {
                            $phone = $item['Value'];
                            break;
                        }
                    }
                }
                
                if ($phone) {
                    $fee = Fee::where('mpesa_phone', $phone)->where('status', 'pending')->first();
                    if ($fee) {
                        Log::info("Found fee by phone: {$phone}");
                        $fee->mpesa_checkout_request_id = $checkoutRequestId;
                        $fee->save();
                    }
                }
            }

            if (!$fee) {
                Log::error('Fee not found');
                return response()->json(['success' => false, 'error' => 'Fee not found'], 404);
            }

            if ($resultCode == 0) {
                // Extract receipt number
                $receipt = null;
                $amount = 0;
                if (isset($callback['CallbackMetadata']['Item'])) {
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        if ($item['Name'] === 'MpesaReceiptNumber') {
                            $receipt = $item['Value'];
                        }
                        if ($item['Name'] === 'Amount') {
                            $amount = $item['Value'];
                        }
                    }
                }

                // Update fee
                $fee->status = 'paid';
                $fee->paid_at = now();
                $fee->mpesa_transaction_code = $receipt;
                $fee->mpesa_result_code = $resultCode;
                $fee->mpesa_response = $data;
                $fee->mpesa_result_desc = $resultDesc;
                if ($receipt) {
                    $fee->receipt_no = $receipt;
                }
                $fee->save();

                Log::info('✅ Payment successful! Fee ID: ' . $fee->id . ', Receipt: ' . $receipt);
                
                return response()->json([
                    'success' => true,
                    'fee_id' => $fee->id,
                    'receipt' => $receipt
                ]);

            } else {
                $fee->status = 'failed';
                $fee->mpesa_result_code = $resultCode;
                $fee->mpesa_response = $data;
                $fee->save();

                Log::warning('❌ Payment failed: ' . $resultCode);
                return response()->json(['success' => false, 'error' => 'Payment failed'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Callback Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
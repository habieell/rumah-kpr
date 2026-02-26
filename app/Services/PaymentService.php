<?php

namespace App\Services;

use App\Models\{Installment, MortgageRequest};

class PaymentService
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function createPayment(MortgageRequest $mortgageRequest)
    {
        $sub_total_amount = $mortgageRequest->monthly_amount;
        $insurance = 900000;
        $total_tax_amount = round($sub_total_amount * 0.11);

        // total payment
        $grossAmount = $sub_total_amount + $insurance + $total_tax_amount;

        // Prepare transaction parameters for Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . uniqid(),
                'gross_amount' => round($grossAmount),
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone,
            ],
            'item_details' => [
                [
                    'id' => $mortgageRequest->id,
                    'price' => round($grossAmount),
                    'quantity' => 1,
                    'name' => 'Mortgage Payment for ' . ($mortgageRequest->house->name ?? 'House'),
                ],
            ],
            'custom_field1' => auth()->id(),
            'custom_field2' => $mortgageRequest->id,
        ];

        // Delegate Snap token creation to MidtransService
        return $this->midtransService->createSnapToken($params);
    }

    public function processNotification()
    {
        $notification = $this->midtransService->handleNotification();

        $transactionStatus = $notification['transaction_status'];
        $grossAmount = $notification['gross_amount'];

        // process only settlements / capture
        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            $mortgageRequestId = $notification['custom_field2'];

            $mortgageRequest = MortgageRequest::findOrFail($mortgageRequestId);

            // create installment record
            $this->createInstallment($mortgageRequest, $grossAmount);
        }
    }

    private function createInstallment(MortgageRequest $mortgageRequest, $grossAmount)
    {
        $lastInstallment = $mortgageRequest->installments()
            ->where('is_paid', true)
            ->orderBy('no_of_payment', 'desc')
            ->first();

        $previousRemainingLoan = $lastInstallment
            ? $lastInstallment->remaining_loan_amount
            : $mortgageRequest->loan_interest_total_amount;

        $sub_total_amount = $mortgageRequest->monthly_amount;
        $insurance = 900000;
        $total_tax_amount = round($sub_total_amount * 0.11);

        $remainingLoan = max($previousRemainingLoan - $sub_total_amount, 0);

        return Installment::create([
            'mortgage_request_id' => $mortgageRequest->id,
            'no_of_payment' => $mortgageRequest->installments()->count() + 1,
            'total_tax_amount' => $total_tax_amount,
            'grand_total_amount' => $grossAmount,
            'sub_total_amount' => $sub_total_amount,
            'insurance_amount' => $insurance,
            'is_paid' => true,
            'payment_type' => 'Midtrans',
            'remaining_loan_amount' => $remainingLoan,
        ]);
    }
}

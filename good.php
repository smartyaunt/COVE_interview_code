<?php

/**
 * Please, improve this class and fix all problems.
 *
 * You can change the Tenant class and its methods and properties as you want.
 * You can't change the AccountingService behavior.
 * You can choose PHP 7 or 8.
 * You can consider this class as an Eloquent model, so you are free to use
 * any Laravel methods and helpers.
 *
 * What is important:
 * - design (extensibility, testability)
 * - code cleanliness, following best practices
 * - consistency
 * - naming
 * - formatting
 *
 * Write your perfect code!
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\AccountingService;

class Tenant extends Model
{

    protected $accountingService;

    const STATUS_AWAITING_PAYMENT = 'awaiting-payment';
    const STATUS_PAID = 'paid';

    function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function getInvoices()
    {
        $invoices = $this->getInvoicesByTenantId();

        if (!$invoices) {
            return null;
        }
        return $this->getAwaitingPaymentInvoices($invoices);
    }

    public function getOldInvoices()
    {

        $invoices = $this->getInvoicesByTenantId();

        if (!$invoices) {
            return null;
        }
        return $this->getPaidInvoices();
    }

    protected function getInvoicesByTenantId()
    {
        return $this->accountingService->getAllInvoices(['tenant_id' => $this->id]);
    }

    protected function getAwaitingPaymentInvoices($invoices)
    {
        return array_filter($invoices, function ($invoice) {
            return $invoice['status'] === self::STATUS_AWAITING_PAYMENT;
        });
    }

    protected function getPaidInvoices($invoices)
    {
        return array_filter($invoices, function ($invoice) {
            return $invoice['status'] === self::STATUS_PAID;
        });
    }
}
<?php


namespace App\Services;


use App\Apis\Ecom\EcomApi;

class NotificationService extends BaseService
{
    public function sendNewOrderNotification($order)
    {
        $notifyDistributor = env("NOTIFY_DISTRIBUTOR_FOR_NEW_ORDERS", true);
        $adminEmail = env("ADMIN_EMAIL", "dgsupply@deligram.com");
        $trackingId = $order->tracking_id;
        $orderInvoiceLink = env("KAIJU_ORDER_TRACKING_URL", "https://kaiju.staging.k8s.deligram.com/order/")."{$trackingId}";

        if ($notifyDistributor) {
            $smsToDistributor = "প্রিয় ডিস্ট্রিবিউটর, {$order->customer->shop_name} থেকে অনলাইন অর্ডার এসেছে।
বিস্তারিত দেখুন: {$orderInvoiceLink}";

            // send SMS to distributor
            app(EcomApi::class)->sendSms([
                "number" => $order->distributor->mobile,
                "body" => $smsToDistributor
            ]);
        }

        $mailer = app('mailer');
        $adminEmailMessage = "Dear dgSupply,

A New order from {$order->customer->name}, shop - {$order->customer->shop_name}, ({$order->customer->mobile}) has been placed for distributor {$order->distributor->name_en} ({$order->distributor->mobile}).

Order Details: {$orderInvoiceLink}

Regards
Kaiju Team";

        $mailer->raw($adminEmailMessage, function (\Illuminate\Mail\Message $email) use ($adminEmail,$trackingId) {
            $email->to("{$adminEmail}", $adminEmail);
            $email->subject("New dgSupply order #{$trackingId} received!");
        });
    }
}

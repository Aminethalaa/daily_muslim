<?php

namespace App\Services;

use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

/**
 * Sends web-push notifications to a user's subscribed devices via VAPID.
 * Prunes expired/invalid subscriptions automatically.
 */
class WebPushSender
{
    public function configured(): bool
    {
        return (bool) config('services.webpush.public_key')
            && (bool) config('services.webpush.private_key');
    }

    /**
     * @param  array{title:string,body:string,url?:string}  $payload
     * @return int number of successful sends
     */
    public function send(User $user, array $payload): int
    {
        if (! $this->configured()) {
            return 0;
        }

        $subs = $user->pushSubscriptions()->get();
        if ($subs->isEmpty()) {
            return 0;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('services.webpush.subject'),
                    'publicKey' => config('services.webpush.public_key'),
                    'privateKey' => config('services.webpush.private_key'),
                ],
            ]);
        } catch (Throwable $e) {
            report($e);

            return 0;
        }

        $body = json_encode([
            'title' => $payload['title'],
            'body' => $payload['body'],
            'url' => $payload['url'] ?? '/dashboard',
        ], JSON_UNESCAPED_UNICODE);

        $map = [];
        foreach ($subs as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
            ]);
            $map[$sub->endpoint] = $sub;
            $webPush->queueNotification($subscription, $body);
        }

        $success = 0;
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if ($report->isSuccess()) {
                $success++;
            } elseif ($report->isSubscriptionExpired() && isset($map[$endpoint])) {
                $map[$endpoint]->delete(); // prune dead subscription
            }
        }

        return $success;
    }
}

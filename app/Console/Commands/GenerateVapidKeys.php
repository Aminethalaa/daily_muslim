<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

#[Signature('saout:vapid')]
#[Description('Generate VAPID keys for web push notifications')]
class GenerateVapidKeys extends Command
{
    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->info('VAPID keys generated. Add these to your .env:');
        $this->newLine();
        $this->line('VAPID_PUBLIC_KEY="'.$keys['publicKey'].'"');
        $this->line('VAPID_PRIVATE_KEY="'.$keys['privateKey'].'"');
        $this->newLine();
        $this->warn('Keep the private key secret. Never commit it.');

        return self::SUCCESS;
    }
}

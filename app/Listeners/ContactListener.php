<?php

namespace App\Listeners;

use App\Events\ContactRequestEvent;
use App\Mail\ContactMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactListener implements ShouldQueue
{
    /**
     * Handle the event.
     */

    public function handle(ContactRequestEvent $event): void
    {
        try {
            Mail::to($event->data['email'])->send(new ContactMail($event->data));
        } catch (\Exception $e) {
            Log::error('Email failed: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\ContactMessage;

class FetchEmails extends Command
{
    protected $signature = 'emails:fetch';
    protected $description = 'Fetch emails from cPanel inbox and store them in DB';

    public function handle()
    {
        $client = Client::account('default');
        $client->connect();

        $inbox = $client->getFolder('INBOX');

        foreach ($inbox->messages()->unseen()->get() as $message) {
            ContactMessage::create([
                'name' => $message->getFrom()[0]->personal ?? 'Unknown',
                'email' => $message->getFrom()[0]->mail,
                'subject' => $message->getSubject(),
                'message' => $message->getTextBody(),
            ]);

            $message->setFlag('Seen'); // Mark as read
        }

        $this->info('Emails fetched and saved!');
    }
}

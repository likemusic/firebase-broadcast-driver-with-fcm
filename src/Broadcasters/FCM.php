<?php

namespace ctf0\Firebase\Broadcasters;

use Exception;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FCM extends Broadcaster
{
    use Common;

    protected $db;

    protected $config;

    /**
     * Create a new broadcaster instance.
     *
     * @param mixed $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Broadcasts the notification through the FCM
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $service = (new Factory())->withServiceAccount(base_path($this->config['creds_file']))->createMessaging();

        foreach ($this->formatChannels($channels) as $channel) {
            try {
                $message = CloudMessage::withTarget('topic', $channel)
                    ->withData([
                        'channel' => $channel,
                        'data' => isset($payload['data']) ?: null,
                        'event' => $event,
                        'timestamp' => round(now()->valueOf()),
                    ]);

                if (!empty($payload['title']) && !empty($payload['description'])) {
                    $message->withNotification(Notification::create(
                        $payload['title'],
                        $payload['description'],
                    ));
                }
            } catch (Exception $e) {
                throw new BroadcastException($e);
            }
        }
    }
}

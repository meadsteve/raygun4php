<?php

namespace MeadSteve\Raygun4php;

class RayGun
{
    /**
     * @param string $key - Raygun.io api key
     * @param bool $useAsyncSending
     * @return Client
     */
    public static function getClient($key, $useAsyncSending = true)
    {
        $messageBuilder = new MessageBuilder();

        if ($useAsyncSending) {
            $messageSender = new Senders\ForkCurlSender(
                $key,
                'api.raygun.io',
                '/entries',
                realpath(__DIR__ . '/cacert.crt')
            );
        } else {
            $messageSender = new Senders\BlockingSocketSender(
                $key,
                'api.raygun.io',
                '/entries',
                realpath(__DIR__ . '/cacert.crt')
            );
        }

        return new Client($messageBuilder, $messageSender);
    }
}

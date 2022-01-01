<?php

namespace Stephenchen\LineBot;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Response;

final class LINEBotManager
{
    /**
     * @var CurlHTTPClient
     */
    private CurlHTTPClient $httpClient;

    /**
     * @var LINEBot
     */
    private LINEBot $bot;

    public function __construct()
    {
        $token  = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $secret = env('LINE_BOT_CHANNEL_SECRET');

        $client = new CurlHTTPClient($token);
        $paras  = [
            'channelSecret' => $secret,
        ];

        $this->httpClient = $client;
        $this->bot        = new LINEBot($client, $paras);
    }

    /**
     * cf. https://developers.line.biz/en/reference/messaging-api/#send-multicast-message
     *
     * @param LINEBot\MessageBuilder $builder
     * @param array $users
     * @return LINEBot\Response
     */
    public function sendMulticast(LINEBot\MessageBuilder $builder, array $users)
    {
        return $this->bot->multicast($users, $builder);
    }

    /**
     * cf. https://developers.line.biz/en/reference/messaging-api/#send-broadcast-message
     *
     * @param LINEBot\MessageBuilder $builder
     * @return LINEBot\Response
     */
    public function sendBroadcast(LINEBot\MessageBuilder $builder)
    {
        return $this->bot->broadcast($builder);
    }

    /**
     * cf. https://developers.line.biz/en/reference/messaging-api/#send-narrowcast-message
     *
     * @param LINEBot\MessageBuilder $builder
     * @return Response
     */
    public function sendNarrowcast(LINEBot\MessageBuilder $builder)
    {
        return $this->bot->sendNarrowcast($builder);
    }

    /**
     * @return Response
     */
    public function getNumberOfSentThisMonth(): Response
    {
        return $this->bot->getNumberOfSentThisMonth();
    }
}

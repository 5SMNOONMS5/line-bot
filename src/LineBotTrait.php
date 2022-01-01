<?php

namespace Stephenchen\LineBot;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

trait LineBotTrait
{
    /**
     * @var string
     */
    static string $LINE_LATEST_SEND = "line_latest_send";

    /**
     * @var LINEBotManager
     */
    private LINEBotManager $lineBotManagerClass;

    /**
     * @param string     $message
     * @param array|null $users
     *
     * @throws Exception
     */
    public function sendMulticast(string $message, ?array $users = NULL)
    {
        if (!$this->canSend()) {
            return;
        }

        // Message
        $appName = env('APP_NAME');
        $number  = $this->getNumberOfSentThisMonth();
        $message = "[ $appName ] $message, and total send message count is $number";

        // Send
        $builder = new TextMessageBuilder($message);
        $me      = [env('LINE_BOT_ME_ID')];
        $users   = array_merge($users ?? [], $me);

        $response = $this->getLineBotManagerClass()->sendMulticast($builder, $users);

        // Cache
        Cache::put(self::$LINE_LATEST_SEND, Carbon::now());

        if ($response->getHTTPStatus() != 200) {
            $content = $response->getJSONDecodedBody();
            Log::error("Line API Fail Send, reason： $content");
        }
    }

    /**
     * @param string $message
     *
     * @throws Exception
     */
    public function sendBroadcast(string $message)
    {
        if (!$this->canSend()) {
            return;
        }

        $builder  = new TextMessageBuilder($message);
        $response = $this->getLineBotManagerClass()->sendBroadcast($builder);

        if ($response->getHTTPStatus() != 200) {
            $content = $response->getJSONDecodedBody();
            Log::error("Line API Fail Send, reason： $content");
        }
    }

    /**
     * @throws Exception
     */
    public function canSend(): bool
    {
        if (!env('LINE_BOT_CAN_SEND')) {
            return false;
        }

        $latest   = Cache::get(self::$LINE_LATEST_SEND);
        $distance = Carbon::now()->diffInMinutes($latest);

        $numberOfSentThisMonth = $this->getNumberOfSentThisMonth();

//        dd([
//            'is over line message api send limit' => $numberOfSentThisMonth < 500,
//            'and current limit is'                => $numberOfSentThisMonth,
//            'is distance between two dates'       => ( $distance == 0 ) || ( $distance >= 180 ),
//        ]);

        // 3 小時內都不在發送，避免過量
        return ( ( $distance == 0 ) || ( $distance >= 180 ) ) && ( $numberOfSentThisMonth < 500 );
    }

    /**
     * Return number of usage this month to prevent charge
     * In 2022, my plan of free message is 500 per month
     *
     * @return int
     * @throws Exception
     */
    public function getNumberOfSentThisMonth(): int
    {
        $response = $this->getLineBotManagerClass()->getNumberOfSentThisMonth();
        try {
            $content    = $response->getJSONDecodedBody();
            $totalUsage = $content[ 'totalUsage' ];
            return (int)$totalUsage;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return LINEBotManager
     */
    public function getLineBotManagerClass(): LINEBotManager
    {
        if (!isset($this->lineBotManagerClass)) {
            $this->lineBotManagerClass = new LINEBotManager();
        }

        return $this->lineBotManagerClass;
    }
}

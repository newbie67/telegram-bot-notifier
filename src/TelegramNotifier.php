<?php

namespace newbie67\TelegramNotifier;

use Exception;

/**
 * Class TelegramNotifier
 *
 * @package newbie67\TelegramNotifier
 */
class TelegramNotifier implements TelegramNotifierInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string[]
     */
    private $chatIds;

    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @var string
     */
    private $messageTemplate;

    const HOST = 'https://api.telegram.org/bot';

    /**
     * @param string          $token
     * @param string[]|string $chatOrChannelIds
     * @param string          $dateFormat
     * @param string          $messageTemplate
     */
    public function __construct(
        $token,
        $chatOrChannelIds,
        $dateFormat = 'Y-m-d H:i:s',
        $messageTemplate = '{%date%}' . PHP_EOL . PHP_EOL . '{%message%}'
    ) {
        $this->token   = $token;
        $this->chatIds = is_scalar($chatOrChannelIds) ? [$chatOrChannelIds] : $chatOrChannelIds;
        $this->dateFormat = $dateFormat;
        $this->messageTemplate = $messageTemplate;
    }

    /**
     * @inheritDoc
     */
    public function send(string $message): bool
    {
        $message = $this->compileMessage($message);
        foreach ($this->chatIds as $chatId) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::HOST . $this->token . '/SendMessage');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'text'    => $message,
                'chat_id' => $chatId,
            ]));
            $result = curl_exec($ch);

            $info = curl_getinfo($ch);
            if ($info['http_code'] !== 200) {
                return false;
            }

            $result = json_decode($result, true);
            if ($result['ok'] !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private function compileMessage(string $message): string
    {
        return str_replace(
            ['{%date%}', '{%message%}'],
            [date($this->dateFormat), $message],
            $this->messageTemplate
        );
    }
}

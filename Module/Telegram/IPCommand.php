<?php

namespace Leon\BswBundle\Module\Telegram;

use Leon\BswBundle\Component\Helper;
use Telegram\Bot\Actions;
use Exception;

class IPCommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "ip";

    /**
     * @var string Command Description
     */
    protected $description = "Show ip address.";

    /**
     * @inheritdoc
     * @return mixed
     */
    public function handle()
    {
        $ip = $this->arguments();
        if (empty($ip)) {
            return $this->replyWithMessage(
                ['text' => '*Error*: Please given a ip address', 'parse_mode' => 'Markdown']
            );
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        try {

            $url = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$ip}&resource_id=6006";
            $data = file_get_contents($url);
            $data = iconv("gbk", "utf-8//IGNORE", $data);

            $data = Helper::parseJsonString($data);
            $data = current($data['data'])['location'] ?? 'pull failed';

        } catch (Exception $e) {
            return $this->replyWithMessage(['text' => '*Error*: ' . $e->getMessage(), 'parse_mode' => 'Markdown']);
        }

        return $this->replyWithMessage(['text' => "*Location*：{$data}", 'parse_mode' => 'Markdown']);
    }
}
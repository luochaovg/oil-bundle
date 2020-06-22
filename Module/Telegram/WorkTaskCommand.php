<?php

namespace Leon\BswBundle\Module\Telegram;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Telegram\Bot\Actions;

class WorkTaskCommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "mytask";

    /**
     * @var string Command Description
     */
    protected $description = "Check my task list.";

    /**
     * @inheritdoc
     * @throws
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $telegram = $this->getTelegram();
        $message = $telegram->getWebhookUpdate()->getMessage();

        $pdo = $this->pdo();
        $pdo->insertInto(
            'bsw_token',
            [
                'userId'      => $message->from->id,
                'scene'       => 1,
                'token'       => $token = Helper::generateToken(),
                'expiresTime' => time() + Abs::TIME_MINUTE,
            ]
        );

        if (empty($_ENV['WORK_TASK_URL'])) {
            return $this->replyWithMessage(
                [
                    'text'       => 'Configure the `WORK_TASK_URL` in env file first.',
                    'parse_mode' => 'Markdown',
                ]
            );
        }

        $tips = 'Do not publish the link, valid once and in 3 minutes.';
        return $this->replyWithMessage(
            [
                'text'       => "[Doorway -> <my task>]({$_ENV['WORK_TASK_URL']}?token={$token}) ({$tips})",
                'parse_mode' => 'Markdown',
            ]
        );
    }
}
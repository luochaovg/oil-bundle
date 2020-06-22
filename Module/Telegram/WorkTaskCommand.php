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
        $result = $pdo->insertInto(
            'bsw_token',
            [
                'user_id'      => $message->from->id,
                'scene'        => 1,
                'token'        => $token = Helper::generateToken(),
                'expires_time' => time() + Abs::TIME_MINUTE * 3,
            ]
        )->execute();

        if (empty($result)) {
            return $this->replyWithMessage(
                [
                    'text'       => 'Create token failed.',
                    'parse_mode' => 'Markdown',
                ]
            );
        }

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
                'text'       => "[Doorway]({$_ENV['WORK_TASK_URL']}?token={$token}) -> <my task> `({$tips})`",
                'parse_mode' => 'Markdown',
            ]
        );
    }
}
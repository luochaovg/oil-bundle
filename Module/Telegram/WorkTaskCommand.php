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
        $exists = $pdo->from('bsw_admin_user')
            ->where('team_id > ?', 0)
            ->where('telegram_id = ?', $message->from->id)
            ->fetch();
        if (empty($exists)) {
            return $this->textMessage('`Sorry, permission denied.`');
        }

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
            return $this->textMessage('Create token failed.');
        }

        if (empty($host = $_ENV['WORK_TASK_URL'] ?? null)) {
            return $this->textMessage('Configure the `WORK_TASK_URL` in env file first.');
        }

        $tips = 'Do not publish the link, valid once and in 3 minutes.';

        return $this->textMessage("[Doorway]({$host}?token={$token}) -> <my task> `({$tips})`");
    }
}
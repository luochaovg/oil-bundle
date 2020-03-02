<?php

namespace Leon\BswBundle\Controller\BswMixed;

use App\Kernel;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Api;
use Exception;

/**
 * @property Kernel $kernel
 */
trait Telegram
{
    /**
     * TG机器人.调试
     *
     * @Route("/tg/debug", name="app_tg_debug")
     *
     * @O("bot", type="string", label="Bot name")
     * @O("normal_update", type="object[]", label="Normal updates")
     * @O("web_hook_update", type="object[]", label="Web hook updates")
     * @O("commands", type="array", label="Bot commands")
     *
     * @throws
     */
    public function getTgDebugAction()
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        /**
         * @var Api $telegram
         */
        $telegram = $this->telegram();
        $user = $telegram->getMe();

        $update = [];
        $isWebHook = true;

        try {
            $update = $telegram->getUpdates(['limit' => 5]);
        } catch (Exception $e) {
            $isWebHook = false;
        }

        return $this->okayAjax(
            [
                'bot'             => "{$user->getFirstName()}({$user->getUsername()})",
                'normal_update'   => $isWebHook ? [] : $update,
                'web_hook_update' => $telegram->getWebhookUpdates()->all(),
                'commands'        => $telegram->getCommands(),
            ],
            'Just debug telegram bot.'
        );
    }

    /**
     * TG机器人.设置钩子
     *
     * @Route("/tg/hook", name="app_tg_hook")
     *
     * @O("remove_hook_result", type="array", label="Result of delete web hook")
     * @O("set_hook_result", type="array", label="Result of set web hook")
     * @O("web_hook_url", type="string", label="Web hook url")
     *
     * @throws
     */
    public function getTgSetHookAction()
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        /**
         * @var Api $telegram
         */
        $telegram = $this->telegram();
        $params = ['url' => "{$this->parameter('telegram_hooks_host')}/tg/cmd"];

        $removeResult = $telegram->removeWebhook()->getDecodedBody() ?? [];
        $addResult = $telegram->setWebhook($params)->getDecodedBody() ?? [];

        return $this->okayAjax(
            [
                'remove_hook_result' => $removeResult,
                'set_hook_result'    => $addResult,
                'web_hook_url'       => $params['url'],
            ],
            'Telegram bot web hooked done.'
        );
    }

    /**
     * TG机器人.设置指令
     *
     * @Route("/tg/cmd", name="app_tg_cmd", methods="POST")
     *
     * @O("ok", type="bool", label="Is ok for add commands")
     * @O("total", type="int", label="Total commands")
     *
     * @return Response
     * @throws
     */
    public function postTgCmdAction()
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $commands = [];
        $bundlePath = $this->kernel->getBundle('LeonBswBundle')->getPath();
        $telegramPath = "{$bundlePath}/Module/Telegram";

        Helper::directoryIterator(
            $telegramPath,
            $commands,
            function ($_, $item) {
                $command = str_replace('.php', null, $item);
                $command = "Leon\\BswBundle\\Module\\Telegram\\{$command}";

                return new $command;
            }
        );

        /**
         * @var Api $telegram
         */
        $telegram = $this->telegram();
        $telegram->addCommands($commands);
        $telegram->commandsHandler(true);

        return $this->okayAjax(
            [
                'ok'    => !empty($commands),
                'total' => count($commands),
            ],
            'Telegram bot commands hooked done.'
        );
    }
}
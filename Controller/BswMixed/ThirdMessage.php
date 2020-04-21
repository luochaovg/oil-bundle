<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorNoRecord;
use Predis\Client;
use Symfony\Component\HttpFoundation\Response;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * @property Client $redis
 */
trait ThirdMessage
{
    /**
     * Third message
     *
     * @Route("/third-message", name="app_third_message")
     * @Access()
     *
     * @return Response
     * @throws
     */
    public function thirdMessageAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $message = $this->redis->rpop($this->cnf->third_message_key);
        $message = Helper::parseJsonString($message);

        if (empty($message) || empty($message['content']) || empty($message['classify'])) {
            return $this->failedAjax(new ErrorNoRecord());
        }

        return $this->responseMessageWithAjax(
            $message['code'] ?? 200,
            $message['content'],
            $message['url'] ?? null,
            $message['args'] ?? [],
            $message['classify'],
            $message['type'] ?? Abs::TAG_TYPE_MESSAGE,
            $message['duration'] ?? null
        );
    }
}
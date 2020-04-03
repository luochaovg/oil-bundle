<?php

namespace Leon\BswBundle\Module\Traits;

use SplQueue;

trait Message
{
    /**
     * @var SplQueue
     */
    protected $message;

    /**
     * @var SplQueue
     */
    protected $messageFlag;

    /**
     * Push message
     *
     * @param string $message
     * @param string $flag
     *
     * @return false
     */
    protected function push(string $message, string $flag = null)
    {
        if (!isset($this->message)) {
            $this->message = new SplQueue();
            $this->messageFlag = new SplQueue();
        }

        $this->message->enqueue($message);
        $this->messageFlag->enqueue($flag);

        return false;
    }

    /**
     * Pop message
     *
     * @param bool $needFlag
     *
     * @return mixed|null
     */
    public function pop(bool $needFlag = false)
    {
        if ($this->message->isEmpty()) {
            return null;
        }

        $message = isset($this->message) ? $this->message->dequeue() : null;
        $flag = isset($this->messageFlag) ? $this->messageFlag->dequeue() : null;

        return $needFlag ? [$message, $flag] : $message;
    }
}
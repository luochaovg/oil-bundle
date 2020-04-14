<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Module\Entity\Abs;

class Message
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $classify = Abs::TAG_CLASSIFY_INFO;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $type = Abs::TAG_TYPE_MESSAGE;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var array
     */
    private $args = [];

    /**
     * Message constructor.
     *
     * @param string $message
     * @param string $classify
     * @param string $route
     */
    public function __construct(string $message, ?string $classify = null, ?string $route = null)
    {
        $this->message = $message;
        isset($classify) && $this->classify = $classify;
        isset($route) && $this->route = $route;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param string $route
     *
     * @return $this
     */
    public function setRoute(?string $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(?string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return $this
     */
    public function setDuration(?int $duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassify(): string
    {
        return $this->classify;
    }

    /**
     * @param string $classify
     *
     * @return $this
     */
    public function setClassify(string $classify)
    {
        $this->classify = $classify;

        return $this;
    }

    /**
     * @return bool
     */
    public function isErrorClassify(): bool
    {
        return $this->getClassify() == Abs::TAG_CLASSIFY_ERROR;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }
}
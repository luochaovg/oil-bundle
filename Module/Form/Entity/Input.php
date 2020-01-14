<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Input extends Form
{
    use Size;
    use AllowClear;

    /**
     * @const string
     * @see   https://developer.mozilla.org/zh-CN/docs/Web/HTML/Element/input#Form_%3Cinput%3E_types
     */
    const TYPE_BUTTON   = 'button';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_COLOR    = 'color';
    const TYPE_DATE     = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_LOCAL    = 'datetime-local';
    const TYPE_EMAIL    = 'email';
    const TYPE_FILE     = 'file';
    const TYPE_HIDDEN   = 'hidden';
    const TYPE_IMAGE    = 'image';
    const TYPE_MONTH    = 'month';
    const TYPE_NUMBER   = 'number';
    const TYPE_PASSWORD = 'password';
    const TYPE_RADIO    = 'radio';
    const TYPE_RANGE    = 'range';
    const TYPE_RESET    = 'reset';
    const TYPE_SEARCH   = 'search';
    const TYPE_SUBMIT   = 'submit';
    const TYPE_TEL      = 'tel';
    const TYPE_TEXT     = 'text';
    const TYPE_TIME     = 'time';
    const TYPE_URL      = 'url';
    const TYPE_WEEK     = 'week';

    /**
     * @var string
     */
    protected $type = self::TYPE_TEXT;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setAllowClear(false);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }
}
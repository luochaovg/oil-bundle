<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\Enum;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Select extends Form
{
    use Size;
    use Enum;
    use PreviewRoute;
    use AllowClear;
    use ButtonLabel;

    /**
     * @const string
     */
    const MODE_DEFAULT  = 'default';
    const MODE_MULTIPLE = 'multiple';
    const MODE_TAGS     = 'tags';
    const MODE_BOX      = 'combobox';

    const SEARCH_VALUE = 'value';
    const SEARCH_LABEL = 'children';

    /**
     * @var bool
     */
    protected $labelInValue = false;

    /**
     * @var string
     */
    protected $mode = self::MODE_DEFAULT;

    /**
     * @var string
     */
    protected $notFoundContent = Abs::NIL;

    /**
     * @var bool
     */
    protected $showSearch = true;

    /**
     * @var bool
     */
    protected $showArrow = true;

    /**
     * @var string
     */
    protected $optionFilterProp = self::SEARCH_LABEL;

    /**
     * @var array
     */
    protected $tokenSeparators = [';', '；'];

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setButtonLabel('Popup for select');
    }

    /**
     * @return bool
     */
    public function isLabelInValue(): bool
    {
        return $this->labelInValue;
    }

    /**
     * @param bool $labelInValue
     *
     * @return $this
     */
    public function setLabelInValue(bool $labelInValue = true)
    {
        $this->labelInValue = $labelInValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    public function setMode(string $mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotFoundContent(): string
    {
        return $this->notFoundContent;
    }

    /**
     * @param string $notFoundContent
     *
     * @return $this
     */
    public function setNotFoundContent(string $notFoundContent)
    {
        $this->notFoundContent = $notFoundContent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowSearch(): bool
    {
        return $this->showSearch;
    }

    /**
     * @param bool $showSearch
     *
     * @return $this
     */
    public function setShowSearch(bool $showSearch = true)
    {
        $this->showSearch = $showSearch;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowArrow(): bool
    {
        return $this->showArrow;
    }

    /**
     * @param bool $showArrow
     *
     * @return $this
     */
    public function setShowArrow(bool $showArrow = true)
    {
        $this->showArrow = $showArrow;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptionFilterProp(): string
    {
        return $this->optionFilterProp;
    }

    /**
     * @param string $optionFilterProp
     *
     * @return $this
     */
    public function setOptionFilterProp(string $optionFilterProp)
    {
        $this->optionFilterProp = $optionFilterProp;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenSeparators(): string
    {
        return json_encode($this->tokenSeparators, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $tokenSeparators
     *
     * @return $this
     */
    public function setTokenSeparators(array $tokenSeparators)
    {
        $this->tokenSeparators = $tokenSeparators;

        return $this;
    }
}
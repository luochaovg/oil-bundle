<?php

namespace Leon\BswBundle\Module\Form;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

abstract class Form
{
    /**
     * @const string
     */
    const SIZE_SMALL  = 'small';
    const SIZE_MIDDLE = 'default';
    const SIZE_LARGE  = 'large';

    /**
     * @var string
     */
    const SCENE_COMMON = 'common';
    const SCENE_NORMAL = 'normal';
    const SCENE_IFRAME = 'iframe';

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var bool
     */
    protected $alreadyDisabled = false;

    /**
     * @var array
     */
    protected $style = [];

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $field;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     * @param bool $coverAble
     *
     * @return $this
     */
    public function setDisabled(bool $disabled = true, bool $coverAble = false)
    {
        if ($this->alreadyDisabled) {
            if ($coverAble) {
                $this->disabled = $disabled;
            }
        } else {
            $this->disabled = $disabled;
        }

        $this->alreadyDisabled = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return json_encode($this->style, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return string
     */
    public function getStyleStringify(): string
    {
        return Html::renderTagAttributes($this->style);
    }

    /**
     * @param array $style
     *
     * @return $this
     */
    public function setStyle(array $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return Helper::camelToUnder(Helper::clsName(static::class), '-');
    }

    /**
     * @return string
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     *
     * @return $this
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return string
     */
    public function getRules(): string
    {
        $rules = [];
        foreach ($this->rules as $rule => $message) {
            if ($rule == 'required') {
                array_push($rules, ['required' => true, 'message' => $message]);
            } else {
                array_push($rules, ['type' => $rule, 'message' => $message]);
            }
        }

        return json_encode($rules, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return string
     */
    public function getAttributes(): string
    {
        return Html::renderTagAttributes($this->attributes);
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}
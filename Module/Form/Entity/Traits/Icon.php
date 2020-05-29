<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Icon
{
    /**
     * @var string
     */
    protected $icon;
    
    /**
     * @return string|null
     */
    public function getIconTag(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        $flag = 'a';
        if (strpos($this->icon, ':') !== false) {
            $flag = $this->icon[0];
        }

        return $flag;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }
}
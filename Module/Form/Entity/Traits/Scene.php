<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Scene
{
    /**
     * @var string
     */
    public $scene = self::SCENE_NORMAL;

    /**
     * @return string
     */
    public function getScene(): string
    {
        return $this->scene;
    }

    /**
     * @param string $scene
     *
     * @return $this
     */
    public function setScene(string $scene)
    {
        $this->scene = $scene;

        return $this;
    }
}
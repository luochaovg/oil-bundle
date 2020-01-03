<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait PreviewRoute
{
    /**
     * @var string
     */
    protected $previewRoute;

    /**
     * @var array
     */
    protected $previewArgs = [];

    /**
     * @return string
     */
    public function getPreviewRoute(): ?string
    {
        return $this->previewRoute;
    }

    /**
     * @param string $previewRoute
     *
     * @return $this
     */
    public function setPreviewRoute(string $previewRoute)
    {
        $this->previewRoute = $previewRoute;

        return $this;
    }

    /**
     * @return array
     */
    public function getPreviewArgs(): array
    {
        return $this->previewArgs;
    }

    /**
     * @param array $previewArgs
     *
     * @return $this
     */
    public function setPreviewArgs(array $previewArgs)
    {
        $this->previewArgs = $previewArgs;

        return $this;
    }
}
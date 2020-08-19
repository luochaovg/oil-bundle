<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait TreeData
{
    /**
     * @var array|string
     */
    protected $treeData = [];

    /**
     * @return string
     */
    public function getTreeData(): string
    {
        if (is_string($this->treeData)) {
            return $this->treeData;
        }

        return Helper::jsonStringify(Helper::stringValues($this->treeData));
    }

    /**
     * @param array|string $treeData
     *
     * @return $this
     */
    public function setTreeData($treeData)
    {
        $this->treeData = $treeData;

        return $this;
    }
}
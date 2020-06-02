<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait TreeData
{
    /**
     * @var array
     */
    protected $treeData = [];

    /**
     * @return string
     */
    public function getTreeData(): string
    {
        return Helper::jsonStringify($this->treeData);
    }

    /**
     * @param array $treeData
     *
     * @return $this
     */
    public function setTreeData(array $treeData)
    {
        $this->treeData = $treeData;

        return $this;
    }
}
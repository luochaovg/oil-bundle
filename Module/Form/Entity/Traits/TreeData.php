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
        $handler = function (array $treeData) use (&$handler) {
            foreach ($treeData as $key => $value) {
                if (is_array($value)) {
                    $treeData[$key] = $handler($value);
                } elseif (is_numeric($value)) {
                    $treeData[$key] = strval($value); // to string for ant-d bug
                }
            }

            return $treeData;
        };

        return Helper::jsonStringify($handler($this->treeData));
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
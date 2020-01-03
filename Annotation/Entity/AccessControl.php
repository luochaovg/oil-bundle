<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;

/**
 * @Annotation
 */
class AccessControl extends Annotation
{
    /**
     * @var bool
     */
    public $join = true; // Join to manage?

    /**
     * @var string
     */
    public $same; // Same to another route

    /**
     * @var int|array Free role
     */
    public $freeRole;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $tips;
}
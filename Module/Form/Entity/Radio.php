<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Col;
use Leon\BswBundle\Module\Form\Entity\Traits\DynamicDataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\Options;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForMeta;
use Leon\BswBundle\Module\Form\Form;

class Radio extends Form
{
    use Options;
    use VarNameForMeta;
    use DynamicDataSource;
    use Col;

    /**
     * @const array Demo
     */
    const OPTIONS_DEMO = [
        ['value' => 1001, 'label' => 'IT department', 'disabled' => false],
        ['value' => 1002, 'label' => 'DevOps department', 'disabled' => true],
        ['value' => 1003, 'label' => 'Product department', 'disabled' => false],
    ];

    /**
     * @param array $options
     *
     * @return array
     */
    public function enumHandler(array $options): array
    {
        if (!is_scalar(current($options))) {
            return $options;
        }

        $optionsHandling = [];
        foreach ($options as $value => $label) {
            $optionsHandling[] = [
                'value'    => $value,
                'label'    => $label,
                'disabled' => false,
            ];
        }

        return $optionsHandling;
    }
}
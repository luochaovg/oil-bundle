<?php

namespace Leon\BswBundle\Module\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits as Form;

trait Link
{
    use Form\Label;
    use Form\Icon;
    use Form\Route;
    use Form\RootClick;
    use Form\Click;
    use Form\Args;
    use Form\Script;
    use Form\Url;
    use Form\Confirm;

    /**
     * @return string
     */
    public function getData(): string
    {
        $data = [
            'route'    => $this->getRoute(),
            'location' => $this->getUrl(),
            'function' => $this->getClick(),
        ];

        $args = $this->getArgs();
        if ($confirm = $this->getConfirm()) {
            $args['confirm'] = $confirm;
        }

        return Helper::jsonStringify(array_merge($data, $args));
    }
}
<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\Accept;
use Leon\BswBundle\Module\Form\Entity\Traits\Args;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonType;
use Leon\BswBundle\Module\Form\Entity\Traits\FileItems;
use Leon\BswBundle\Module\Form\Entity\Traits\Flag;
use Leon\BswBundle\Module\Form\Entity\Traits\ListType;
use Leon\BswBundle\Module\Form\Entity\Traits\NeedId;
use Leon\BswBundle\Module\Form\Entity\Traits\NeedTips;
use Leon\BswBundle\Module\Form\Entity\Traits\Route;
use Leon\BswBundle\Module\Form\Entity\Traits\Url;

class Upload extends Number
{
    use Route;
    use Args;
    use ButtonLabel;
    use Accept;
    use ListType;
    use Flag;
    use FileItems;
    use Url;
    use NeedId;
    use NeedTips;
    use ButtonStyle;
    use ButtonType;

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setListType(Abs::LIST_TYPE_TEXT);
        $this->setChange('uploaderChange');
        $this->setButtonType(Abs::THEME_DEFAULT);
        $this->setButtonLabel('Click to select for upload');
    }
}
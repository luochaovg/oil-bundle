<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Accept;
use Leon\BswBundle\Module\Form\Entity\Traits\Args;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonType;
use Leon\BswBundle\Module\Form\Entity\Traits\Change;
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
    use Change;
    use FileItems;
    use Url;
    use NeedId;
    use NeedTips;
    use ButtonStyle;
    use ButtonType;

    /**
     * @const string
     */
    const LIST_TYPE_TEXT     = 'text';
    const LIST_TYPE_IMG      = 'picture';
    const LIST_TYPE_IMG_CARD = 'picture-card';

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setListType(self::LIST_TYPE_TEXT);
        $this->setChange('uploaderChange');
        $this->setButtonType(Button::THEME_DEFAULT);
        $this->setButtonLabel('Click to select for upload');
    }
}
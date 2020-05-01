<?php

namespace Leon\BswBundle\Entity;

use Leon\BswBundle\Entity\FoundationEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Leon\BswBundle\Annotation\Entity as BswAnnotation;
use Leon\BswBundle\Module\Entity\Abs as BswAbs;
use Leon\BswBundle\Module\Entity\Enum as BswEnum;
use Leon\BswBundle\Module\Hook\Entity as BswHook;
use Leon\BswBundle\Module\Form\Entity as BswForm;
use Leon\BswBundle\Module\Filter\Entity as BswFilter;
use Leon\BswBundle\Component\Helper as BswHelper;

/**
 * @ORM\Entity(repositoryClass="Leon\BswBundle\Repository\BswCommandQueueRepository")
 */
class BswCommandQueue extends FoundationEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="`id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=1, align="center", width=90, render=BswAbs::RENDER_CODE)
     * @BswAnnotation\Persistence(sort=1, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=1, type=BswForm\Number::class)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="`command`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=128, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=2, render=BswAbs::HTML_TEXT, enumExtra=true)
     * @BswAnnotation\Persistence(sort=2, type=BswForm\Select::class, enumExtra=true)
     * @BswAnnotation\Filter(sort=2, type=BswForm\Select::class, enumExtra=true)
     */
    protected $command;

    /**
     * @ORM\Column(type="string", name="`condition`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=3, width=360, hook={0:BswHook\JsonStringify::class}, render=BswAbs::RENDER_CODE)
     * @BswAnnotation\Persistence(sort=3, hook={0:BswHook\JsonStringify::class}, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=3)
     */
    protected $condition;

    /**
     * @ORM\Column(type="smallint", name="`resource_need`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=4, align="center", render="{value} %")
     * @BswAnnotation\Persistence(sort=4, type=BswForm\Slider::class, typeArgs={"max":50, "marks": {10:10, 20:20, 30:30, 40:40}})
     * @BswAnnotation\Filter(sort=4, type=BswForm\Number::class)
     */
    protected $resourceNeed = 1;

    /**
     * @ORM\Column(type="float", name="`done_percent`")
     * @Assert\Type(type="numeric", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=5, width=140, align="center", render="{value} %")
     * @BswAnnotation\Persistence(sort=5, show=false)
     * @BswAnnotation\Filter(sort=5)
     */
    protected $donePercent = 0;

    /**
     * @ORM\Column(type="string", name="`telegram_receiver`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=128, groups={"modify"})
     * @BswAnnotation\Preview(sort=6, render=BswAbs::HTML_PRE, width=360)
     * @BswAnnotation\Persistence(sort=6, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=6)
     */
    protected $telegramReceiver = "";

    /**
     * @ORM\Column(type="string", name="`cron_date_format`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=32, groups={"modify"})
     * @BswAnnotation\Preview(sort=7, render=BswAbs::RENDER_CODE, align="center")
     * @BswAnnotation\Persistence(sort=7)
     * @BswAnnotation\Filter(sort=7)
     */
    protected $cronDateFormat = "d-H:i";

    /**
     * @ORM\Column(type="string", name="`cron_date_value`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=32, groups={"modify"})
     * @BswAnnotation\Preview(sort=8, render=BswAbs::RENDER_CODE, align="center")
     * @BswAnnotation\Persistence(sort=8)
     * @BswAnnotation\Filter(sort=8)
     */
    protected $cronDateValue = "01-00:00";

    /**
     * @ORM\Column(type="smallint", name="`cron_reuse`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=9, align="center", enum=true, dress="blue")
     * @BswAnnotation\Persistence(sort=9, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=9, type=BswForm\Select::class, enum=true)
     */
    protected $cronReuse = 0;

    /**
     * @ORM\Column(type="string", name="`remark`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=128, groups={"modify"})
     * @BswAnnotation\Preview(sort=10, render=BswAbs::HTML_PRE, width=360)
     * @BswAnnotation\Persistence(sort=10, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=10)
     */
    protected $remark = "";

    /**
     * @ORM\Column(type="string", name="`add_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=11, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=11, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=11, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $addTime;

    /**
     * @ORM\Column(type="string", name="`update_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=12, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=12, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=12, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class)
     */
    protected $updateTime;

    /**
     * @ORM\Column(type="smallint", name="`state`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=13, align="center", enum=true, dress={0:"default", 1:"processing"}, status=true)
     * @BswAnnotation\Persistence(sort=13, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=13, type=BswForm\Select::class, enum=true)
     */
    protected $state = 1;
}
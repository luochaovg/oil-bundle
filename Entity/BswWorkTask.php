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
 * @ORM\Entity(repositoryClass="Leon\BswBundle\Repository\BswWorkTaskRepository")
 */
class BswWorkTask extends FoundationEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="`id`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=1, align="center", width=90, render=BswAbs::RENDER_CODE)
     * @BswAnnotation\Persistence(sort=1, type=BswForm\Number::class)
     * @BswAnnotation\Filter(sort=1, type=BswForm\Number::class, show=false)
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="`user_id`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=2, align="left", width=200, enumExtra=true)
     * @BswAnnotation\Persistence(sort=2, type=BswForm\Select::class, enumExtra=true)
     * @BswAnnotation\Filter(sort=2, column=3, type=BswForm\Select::class, enumExtra=true)
     */
    protected $userId;

    /**
     * @ORM\Column(type="string", name="`title`")
     * @Assert\Type(type="string", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @Assert\Length(max=64, groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=3, render=BswAbs::HTML_TEXT)
     * @BswAnnotation\Persistence(sort=3)
     * @BswAnnotation\Filter(sort=3, showPriority=10)
     */
    protected $title;

    /**
     * @ORM\Column(type="integer", name="`start_time`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=4, align="center", render=BswAbs::RENDER_CODE, hook={0:BswHook\Timestamp::class}, width=190)
     * @BswAnnotation\Persistence(sort=4, type=BswForm\Datetime::class, hook={0:BswHook\Timestamp::class}, show=false)
     * @BswAnnotation\Filter(sort=4, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class, filterArgs={"timestamp":true})
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $startTime;

    /**
     * @ORM\Column(type="integer", name="`end_time`")
     * @Assert\Type(type="integer", groups={"modify", "newly"})
     * @Assert\NotNull(groups={"modify", "newly"})
     * @BswAnnotation\Preview(sort=5, align="center", render=BswAbs::RENDER_CODE, hook={0:BswHook\Timestamp::class}, width=190)
     * @BswAnnotation\Persistence(sort=5, type=BswForm\Datetime::class, hook={0:BswHook\Timestamp::class}, show=false)
     * @BswAnnotation\Filter(sort=5, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class, filterArgs={"timestamp":true})
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $endTime;

    /**
     * @ORM\Column(type="float", name="`done_percent`")
     * @Assert\Type(type="numeric", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=3.2, width=140, align="center", render=BswAbs::RENDER_ROUND_PERCENT)
     * @BswAnnotation\Persistence(sort=6, type=BswForm\Slider::class)
     * @BswAnnotation\Filter(sort=6, show=false)
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $donePercent = 0;

    /**
     * @ORM\Column(type="smallint", name="`weight`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=3.3, align="center")
     * @BswAnnotation\Persistence(sort=7, type=BswForm\Slider::class, typeArgs={"min":1, "max":100, "tipFormatter":"(value) => `${value}`"})
     * @BswAnnotation\Filter(sort=7, type=BswForm\Number::class, show=false)
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $weight = 1;

    /**
     * @ORM\Column(type="string", name="`remark`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @Assert\Length(max=2048, groups={"modify"})
     * @BswAnnotation\Preview(sort=10.1, render=BswAbs::HTML_PRE, width=360, show=false)
     * @BswAnnotation\Persistence(sort=8, type=BswForm\TextArea::class)
     * @BswAnnotation\Filter(sort=8, show=false)
     */
    protected $remark = "";

    /**
     * @ORM\Column(type="string", name="`add_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=9, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=9, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=9, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class, show=false)
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $addTime;

    /**
     * @ORM\Column(type="string", name="`update_time`")
     * @Assert\Type(type="string", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=10, align="center", render=BswAbs::RENDER_CODE, width=190)
     * @BswAnnotation\Persistence(sort=10, show=false, type=BswForm\Datetime::class)
     * @BswAnnotation\Filter(sort=10, type=BswForm\DatetimeRange::class, column=4, filter=BswFilter\Between::class, show=false)
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $updateTime;

    /**
     * @ORM\Column(type="smallint", name="`state`")
     * @Assert\Type(type="integer", groups={"modify"})
     * @Assert\NotNull(groups={"modify"})
     * @BswAnnotation\Preview(sort=3.1, align="center", enum=true, dress={1:"blue",2:"orange",3:"green",4:"cyan"})
     * @BswAnnotation\Persistence(sort=11, type=BswForm\Select::class, enum=true)
     * @BswAnnotation\Filter(sort=11.01, type=BswForm\Select::class, placeholder="Mode", group="state", style={"width": "50%"}, enum=BswFilter\Senior::MODE_SELECT_NUMBER, column=3)
     * @BswAnnotation\Filter(sort=11.02, type=BswForm\Select::class, enum=true, placeholder="Value", group="state", style={"width": "50%"})
     * @BswAnnotation\Mixed(sort=true)
     */
    protected $state = 1;
}
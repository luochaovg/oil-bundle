
### 概览

> 项目基于 Symfony4.3 (bundle) 开发，如果你对该框架了解的话最好，如果不了解没关系，你只要关注你自己的业务即可。

### 通用的模块

* **Error**

    该模块可用于接口返回、用户提示等操作，每个错误独立封装成一个class。  
    如果你需要自定义的类请集成基类 `Leon\BswBundle\Module\Error\Error`，该类为抽象类，必须实现类中属性/方法。  
    
    其中包含:   
    
    - 错误码  
    
        ```php
        /**
         * @const int
         */
        const CODE = 0;
        ```
    
        > 错误码为整形，不可重复（如果在创建文档时发现重复将无法生成文档并提示错误）  
        
    - 错误短语  
    
        ```php
        /**
         * @var string
         */
        protected $tiny = 'Oops';
        ```
    
        > 错误短语用于直面用户，当然你可以使用i18n的key替代，在提示时将根据当前语言自动翻译。  
    
    - 错误描述  
    
        ```php
        /**
         * @var string
         */
        protected $description;
        ```
    
        > 该描述可与短语一致，但是大多数将错误的详细描述记录到此处，仅用于日志记录，方便日志分析。如果为空则视为与 `tiny` 保持一致。  
        
    目前框架自带的错误:  
    // TODO
        
* **Chart**

    - 折线图  
    `Leon\BswBundle\Module\Chart\Entity\Line::class`  
    
    - 柱状图  
    `Leon\BswBundle\Module\Chart\Entity\Bar::class`  
    
    - 饼状图  
    `Leon\BswBundle\Module\Chart\Entity\Pie::class`  
    
    - 地图  
    `Leon\BswBundle\Module\Chart\Entity\Map::class`  
    
    > 图形绘制模块基于 ECharts，基于官方文档配置过于强大和复杂本框架封装了常用的4中绘图类，你也可用在此基础上封装其他图形类。当然，你需要继承基类 `Leon\BswBundle\Module\Chart\Chart`，在此之前，你需要对 [ECharts](https://echarts.apache.org/zh/option.html#title) 有一定的了解。  
    
    如何使用:  
    
    ```php
    use Leon\BswBundle\Module\Chart\Entity\Line;
 
    $line = new Line('user-add');
    $line->setBackgroundColor('pink');
    // do configure
    
    $options = $line->buildOption();
    dd($options); // 此处得到的结果为 EChart 所需的配置，json_encode 后可直接用于前端使用。
    ```
    
* **Filter**

    > 过滤器仅仅用于Web和Admin类项目，并且在Admin类项目中用的非常频繁；  
    通用特性，如果你需要自定义过滤器，请基础基类 `Leon\BswBundle\Module\Filter\Filter`。
    
    目前框架实现了常用的过滤器:  
    
    | 过滤器 | 作用 | 经典场景 |  
    | ------- | ------- |  ------ |  
    | Leon\BswBundle\Module\Filter\Entity\Accurate::class | 精确过滤器 | 数字类型 |  
    | Leon\BswBundle\Module\Filter\Entity\Between:class | 区间过滤器 | 时间区间 |  
    | Leon\BswBundle\Module\Filter\Entity\Like::class | 相似匹配过滤器 | 字符串搜索 |  
    | Leon\BswBundle\Module\Filter\Entity\Mixed::class | 混合过滤器 | 多字段使用同一个输入框进行过滤 |  
    | Leon\BswBundle\Module\Filter\Entity\Senior::class | 高级过滤器 | 常规的过滤条件都将在这里找到 |  
    
    > 过滤器提供了两种模式产出，分别是 `DQL` 和 `SQL`；就是说你可以使用过滤器生成原生 `SQL` 条件语句部分。  
    `DQL` 为默认模式，配合 `Doctrine` 使用的。
    
* **Form**

    > 表单类用于快速生成满足 `Antd` 框架规范的表单组件。每个组件都有自己的配置和方法请参照api。如果你需要实现自己的表单类（任意形态，比如原生html）时你同样的需要继承基类 `Leon\BswBundle\Module\Form\Form`。
    
    以下列表为框架自带表单组件:  
    
    | 表单组件 | 形态 |
    | ------- | ------- |
    | Leon\BswBundle\Module\Form\Entity\AutoComplete::class | 自动完成，类似各大搜索引擎在输入部分字符后进行提示 |
    | Leon\BswBundle\Module\Form\Entity\Button::class | 按钮 |
    | Leon\BswBundle\Module\Form\Entity\Checkbox::class | 复选框 |
    | Leon\BswBundle\Module\Form\Entity\CkEditor::class | 富文本编辑框 |
    | Leon\BswBundle\Module\Form\Entity\Date::class | 日期选择器 |
    | Leon\BswBundle\Module\Form\Entity\DateRange::class | 日期范围选择器 |
    | Leon\BswBundle\Module\Form\Entity\Datetime::class | 日期选择器(含时间) |
    | Leon\BswBundle\Module\Form\Entity\DatetimeRange::class | 日期范围选择器(含时间) |
    | Leon\BswBundle\Module\Form\Entity\Group::class | 组（一行渲染多个组件时使用） |
    | Leon\BswBundle\Module\Form\Entity\Input::class | 输入框 |
    | Leon\BswBundle\Module\Form\Entity\Mentions::class | 提及框，即艾特功能 |
    | Leon\BswBundle\Module\Form\Entity\Month::class | 月份选择器 |
    | Leon\BswBundle\Module\Form\Entity\Number::class | 数值输入框 |
    | Leon\BswBundle\Module\Form\Entity\Password::class | 密码输入框 |
    | Leon\BswBundle\Module\Form\Entity\Radio::class | 单选框 |
    | Leon\BswBundle\Module\Form\Entity\Score::class | 评分组件 |
    | Leon\BswBundle\Module\Form\Entity\Select::class | 下拉选择器 |
    | Leon\BswBundle\Module\Form\Entity\SelectTree::class | 下拉选择器(树状) |
    | Leon\BswBundle\Module\Form\Entity\Slider::class | 进度条组件 |
    | Leon\BswBundle\Module\Form\Entity\Switcher::class | 开关组件 |
    | Leon\BswBundle\Module\Form\Entity\Text::class | 文本组件 |
    | Leon\BswBundle\Module\Form\Entity\TextArea::class | 多行文本输入框 |
    | Leon\BswBundle\Module\Form\Entity\Time::class | 时间选择器(仅) |
    | Leon\BswBundle\Module\Form\Entity\Upload::class | 文件上传组件 |
    | Leon\BswBundle\Module\Form\Entity\Week::class | 周(期)选择器 |
    
    如何使用（Filter模块实质上也是Form的组合）:  
    
    ```php
    // php中直接实例化，然后对实例进行配置即可；
    // 模块端直接传入对象进行渲染即可；
    // Admin类项目的数据交互过程已经封装好可直接使用。
    ```

* **Hook**

    > 在数据处理的过程中，往往很多场景需要对数据进行双向处理，比如价格字段，在存入的时候希望存入分单位，在显示的时候希望渲染为元单位；或用户手机号在数据库存为加密，但却需要正常显示的时候。这个时候钩子就很容易应付这类无趣的业务，如果你要实现自己的钩子，请继承基类 `Leon\BswBundle\Module\Hook\Hook`。
    
    当前实现的钩子:  
    
    > 正向功能: 当数据从元转为想要的结果时为正向（从数据看读取后转为渲染格式）
    > 反向功能: 当把渲染数据转为元数据时为反向（将表单渲染的数据写入数据库前的处理）
    
    | 钩子 | 正向功能 | 反向功能 |
    | ------- | ------- | ------- |
    | Aes::class | AES解密 | AES加密 |
    | ByteGB::class | byte转GB | GB转byte |
    | ByteMB::class | byte转MB | MB转byte |
    | DefaultDatetime::class | - | 为空时取当前日期/时间 |
    | DefaultTimestamp::class | - | 为空时取当前时间戳 |
    | Enums::class | 枚举类型key转value | 枚举类型value转key |
    | EnumTrans::class | 使用enum包翻译 | - |
    | FieldsTrans::class | 使用fields包翻译 | - |
    | FileSize::class | 将byte转为人性化描述 | - |
    | HourDay::class | 小时转天 | 天转小时 |
    | HourDuration::class | 小时转人性化描述 | - |
    | HtmlUbb::class | ubb转html | html转ubb |
    | Json::class | json串转数组 | 数组转json串 |
    | JsonStringify::class | json串转人性化描述 | - |
    | MbGB::class | MB转GB | GB转MB |
    | MessagesTrans::class | 使用messages包翻译 | - |
    | Money::class | 金额缩小100倍 | 金额放大100倍 |
    | MoneyStringify::class | 金额转人性化描述 | - |
    | Rate::class | 数值转百分比 | - |
    | RateStringify::class | 数值转人性化描述百分比 | - |
    | Safety::class | 过滤html | 过滤html |
    | SeoTrans::class | 使用seo包翻译 | - |
    | Times::class | 次数转人性化描述 | - |
    | Timestamp::class | 时间戳转日期 | 日期转时间戳 |
    | TwigTrans::class | 使用twig包翻译 | - |
    | UrlCode::class | url解码 | url编码 |

    如何使用:  
    
    ```php
    // 在 Annotation 中使用
    /**
     * @BswAnnotation\Preview(hook={0:BswHook\Money::class})
     * @BswAnnotation\Persistence(hook={0:BswHook\Money::class})
     */
     protected $money = 0;
     
     // 在流函数中使用
     public function previewAnnotation() :array
     {
         return [
            'money' => [
                'hook' => [Money::class]
            ]
        ];
     }
     
     # !! 是的你可能发现了一个规律，hook 的值为一个数组，每个字段可以对应多个钩子，钩子是按顺序执行的。
     
     // 如果你使用 Aes 钩子的时候你可能会有这样一个疑问，Aes 加密的 iv 等参数如何设置:  
     // 你可以通过全局流函数配置钩子参数
     
     /**
     * @param array $args
     *
     * @return array
     */
    protected function hookerExtraArgs(array $args = []): array
    {
        return Helper::merge(
            [
                Aes::class           => [
                    'aes_iv'     => $this->parameter('aes_iv'),
                    'aes_key'    => $this->parameter('aes_key'),
                    'aes_method' => $this->parameter('aes_method'),
                    'plaintext'  => $this->plaintextSensitive,
                ],
                Timestamp::class     => [
                    'persistence_newly_empty' => time(),
                ],
                HourDuration::class  => [
                    'digit' => [
                        'year'  => $this->fieldLang('Year'),
                        'month' => $this->fieldLang('Month'),
                        'day'   => $this->fieldLang('Day'),
                        'hour'  => $this->fieldLang('Hour'),
                    ],
                ],
                Enums::class         => [
                    'trans' => $this->translator,
                ],
                MessagesTrans::class => [
                    'trans' => $this->translator,
                ],
                TwigTrans::class     => [
                    'trans' => $this->translator,
                ],
                FieldsTrans::class   => [
                    'trans' => $this->translator,
                ],
                EnumTrans::class     => [
                    'trans' => $this->translator,
                ],
                SeoTrans::class      => [
                    'trans' => $this->translator,
                ],
            ],
            $args
        );
    }
    
    // 又或者你项目的99%的Aes都是通过全局配置的参数进行处理，但是还是有1%的字段需要用另外一个秘钥对进行加解密，那我们该如何配置:  
    
    // 在 Annotation 中这样配置个性化参数
    /**
     * @BswAnnotation\Preview(hook={BswHook\Aes::class:{"aes_iv": "good", "aes_key": "job"}})
     * @BswAnnotation\Persistence(hook={BswHook\Aes::class:{"aes_iv": "good", "aes_key": "job"}})
     */
     protected $money = 0;
     
     // 在流函数中这样配置个性化参数
     public function previewAnnotation() :array
     {
         return [
            'money' => [
                'hook' => [
                    Aes::class => [
                        'aes_iv' => 'good',
                        'aes_key' => 'job',
                    ]
                ]
            ]
        ];
     }
     
     # !! 或者你又有了疑问，假如我当下有个数据，我就想这个数据进行钩子操作该如何写:  
     
     $list = [
         ['orderId' => 1001008, 'money' => 99800],
         ['orderId' => 1001009, 'money' => 16800],
         ['orderId' => 1001010, 'money' => 9900],
     ];
     
     $list = $this->web->hooker(
         [
             Money::class => ['money'],
         ],
         $list,
         true, # true 为反向, false 为正向
         null, # before hook handler 钩子前自定义处理
         null, # after hook handler 钩子后自定义处理
         []    # 额外参数
     );
    
    ```
    
* **Validator**

    > 参数验证用于所有类项目，如果你需要自定义验证器，请集成基类 `Leon\BswBundle\Module\Validator\Validator`。
    
    框架已经支持的验证器有:  
    
    | 钩子 | 功能 |
    | ------- | ------- |
    | Leon\BswBundle\Module\Validator\Entity\Arr::class | 为数组 |
    | Leon\BswBundle\Module\Validator\Entity\Between::class | 数值在m和n之间 |
    | Leon\BswBundle\Module\Validator\Entity\Def::class | 设置默认值 |
    | Leon\BswBundle\Module\Validator\Entity\Difference::class | 不等于(同 JustNot) |
    | Leon\BswBundle\Module\Validator\Entity\Email::class | 为email格式 |
    | Leon\BswBundle\Module\Validator\Entity\Endpoint::class | 为endpoint格式（ip:port） |
    | Leon\BswBundle\Module\Validator\Entity\Gt::class | 大于 |
    | Leon\BswBundle\Module\Validator\Entity\Gte::class | 大于等于 |
    | Leon\BswBundle\Module\Validator\Entity\IdString::class | id串 |
    | Leon\BswBundle\Module\Validator\Entity\In::class | 在数组value中 |
    | Leon\BswBundle\Module\Validator\Entity\InKey::class | 在数组的key中 |
    | Leon\BswBundle\Module\Validator\Entity\InLength::class | 长度为可选范围中 |
    | Leon\BswBundle\Module\Validator\Entity\Ip::class | 为ip格式 |
    | Leon\BswBundle\Module\Validator\Entity\Json::class | 为json串 |
    | Leon\BswBundle\Module\Validator\Entity\Just::class | 等于 |
    | Leon\BswBundle\Module\Validator\Entity\JustNot::class | 不等于 |
    | Leon\BswBundle\Module\Validator\Entity\Length::class | 长度为 |
    | Leon\BswBundle\Module\Validator\Entity\Limit::class | 长度在m和n之间 |
    | Leon\BswBundle\Module\Validator\Entity\Lt::class | 小于 |
    | Leon\BswBundle\Module\Validator\Entity\Lte::class | 小于等于 |
    | Leon\BswBundle\Module\Validator\Entity\Max::class | 最大 |
    | Leon\BswBundle\Module\Validator\Entity\Min::class | 最小 |
    | Leon\BswBundle\Module\Validator\Entity\MysqlBigint::class | mysql大整数 |
    | Leon\BswBundle\Module\Validator\Entity\MysqlInt::class | mysql整数 |
    | Leon\BswBundle\Module\Validator\Entity\MysqlMediumint::class | mysql中整数 |
    | Leon\BswBundle\Module\Validator\Entity\MysqlSmallint::class | mysql小整数 |
    | Leon\BswBundle\Module\Validator\Entity\MysqlTinyint::class | mysql迷你整数 |
    | Leon\BswBundle\Module\Validator\Entity\MysqlUnsBigint::class | mysql大整数(无符号) |
    | Leon\BswBundle\Module\Validator\Entity\MysqlUnsInt::class | mysql整数(无符号) |
    | Leon\BswBundle\Module\Validator\Entity\MysqlUnsMediumint::class | mysql中整数(无符号) |
    | Leon\BswBundle\Module\Validator\Entity\MysqlUnsSmallint::class | mysql小整数(无符号) |
    | Leon\BswBundle\Module\Validator\Entity\MysqlUnsTinyint::class | mysql迷你整数(无符号) |
    | Leon\BswBundle\Module\Validator\Entity\NotBlank::class | 不为空串 |
    | Leon\BswBundle\Module\Validator\Entity\NotEmpty::class | 不为空值 |
    | Leon\BswBundle\Module\Validator\Entity\Numeric::class | 数值 |
    | Leon\BswBundle\Module\Validator\Entity\Order::class | 为排序类型 |
    | Leon\BswBundle\Module\Validator\Entity\Password::class | 符合密码规则 |
    | Leon\BswBundle\Module\Validator\Entity\Phone::class | 手机号格式 |
    | Leon\BswBundle\Module\Validator\Entity\Replace::class | 替换处理 |
    | Leon\BswBundle\Module\Validator\Entity\Required::class | 必须设置 |
    | Leon\BswBundle\Module\Validator\Entity\Rsa::class | Rsa解密（需配置参数） |
    | Leon\BswBundle\Module\Validator\Entity\Same::class | 等于(同 Just) |
    | Leon\BswBundle\Module\Validator\Entity\Str::class | 字符串 |
    | Leon\BswBundle\Module\Validator\Entity\StringToArray::class | 字符串处理成数组 |
    | Leon\BswBundle\Module\Validator\Entity\Trim::class | 处理边缘 |
    | Leon\BswBundle\Module\Validator\Entity\Truncate::class | 截断 |
    | Leon\BswBundle\Module\Validator\Entity\UnsInteger::class | 无符号整数 |
    | Leon\BswBundle\Module\Validator\Entity\Url::class | url格式 |

    如何使用:  
    
    ```php
    # 在 Persistence Annotation 中使用 (可用像下面一样使用数组)
    /**
     * @BswAnnotation\Persistence(rules="required|trim,+|phone")
     */
    protected $phone;
  
    # 在 Input Annotation 中使用 (可用像上面一样使用字符串)
    /**
     * @BswAnnotation\Input(rules={"required", "trim":["+"], "phone"})
     */
    public function registerApi() {}
  
    # 在流函数中使用
    public function persistenceAnnotation() :array 
    {
        return [
            'phone' => [
                //'rules' => 'required|trim,+|phone',
                'rules' => [
                    'required',
                    'trim' => ['+'],
                    'phone'
                ]
            ],
        ];
    }
  
    # !! 如果我想手动验证一个数据该怎么办:  
    $rules = []; // 同上，可字符串可数组
    $result = $this->validator('phone', '15011112222', $rules);
  
    if ($result === false) {
        dd($this->web->pop()); // error info
    }
    ```
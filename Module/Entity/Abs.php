<?php

namespace Leon\BswBundle\Module\Entity;

class Abs
{
    const NORMAL = 1;
    const CLOSE  = 0;
    const YES    = 1;
    const NO     = 0;

    const CODE_BASIC     = 1000024;
    const CODE_DIST      = 'gz8xjdt3h7rcypfvewkm4aun2'; // 25
    const CODE_DIST_FULL = 'mct63kg0il5bp9uv17eryzs2wnja4xfodqh8'; // 36

    const POS_TOP    = 'top';
    const POS_RIGHT  = 'right';
    const POS_BOTTOM = 'bottom';
    const POS_LEFT   = 'left';
    const POS_CENTER = 'center';
    const POS_MIDDLE = 'middle';

    const BK_DISPLAY_ARGS = 1;
    const BK_TWIG_ARGS    = 2;

    const PERSISTENCE_TOTAL_COLUMN = 24;
    const PERSISTENCE_LABEL_COLUMN = 4;

    const BEGIN_REQUEST = 'Begin request';
    const BEGIN_VALID   = 'Begin valid';
    const BEGIN_LOGIC   = 'Begin logic';
    const BEGIN_API     = 'Begin third api';
    const BEGIN_INIT    = 'Begin init';
    const END_REQUEST   = 'End request';
    const END_VALID     = 'End valid';
    const END_LOGIC     = 'End logic';
    const END_API       = 'End third api';
    const END_INIT      = 'End init';

    const TAG_MESSAGE     = 'message';
    const TAG_TIPS        = 'tips';
    const TAG_HISTORY     = 'history';
    const TAG_FALLBACK    = 'fallback';
    const TAG_PREVIEW     = 'preview';
    const TAG_PERSISTENCE = 'persistence';
    const TAG_FILTER      = 'filter';
    const TAG_LOGIC       = 'logic';

    const TAG_TYPE_NOTICE      = 'notification';
    const TAG_TYPE_MESSAGE     = 'message';
    const TAG_TYPE_CONFIRM     = 'confirm';
    const TAG_CLASSIFY_SUCCESS = 'success';
    const TAG_CLASSIFY_INFO    = 'info';
    const TAG_CLASSIFY_WARNING = 'warning';
    const TAG_CLASSIFY_ERROR   = 'error';

    const FLAG_SQL_ERROR = 'An exception occurred while executing';
    const IMAGE_SUFFIX   = ['gif', 'jpg', 'jpeg', 'png'];
    const IMAGE_SIZE_MAX = 'MAX';

    const MYSQL_TINYINT_MIN       = -(2 ** 8) / 2;
    const MYSQL_TINYINT_MAX       = +(2 ** 8) / 2 - 1;
    const MYSQL_TINYINT_UNS_MIN   = +(1);
    const MYSQL_TINYINT_UNS_MAX   = +(2 ** 8) - 1;
    const MYSQL_SMALLINT_MIN      = -(2 ** 16) / 2;
    const MYSQL_SMALLINT_MAX      = +(2 ** 16) / 2 - 1;
    const MYSQL_SMALLINT_UNS_MIN  = +(1);
    const MYSQL_SMALLINT_UNS_MAX  = +(2 ** 16) - 1;
    const MYSQL_MEDIUMINT_MIN     = -(2 ** 24) / 2;
    const MYSQL_MEDIUMINT_MAX     = +(2 ** 24) / 2 - 1;
    const MYSQL_MEDIUMINT_UNS_MIN = +(1);
    const MYSQL_MEDIUMINT_UNS_MAX = +(2 ** 24) - 1;
    const MYSQL_INT_MIN           = -(2 ** 32) / 2;
    const MYSQL_INT_MAX           = +(2 ** 32) / 2 - 1;
    const MYSQL_INT_UNS_MIN       = +(1);
    const MYSQL_INT_UNS_MAX       = +(2 ** 32) - 1;
    const MYSQL_BIGINT_MIN        = -(2 ** 64) / 2;
    const MYSQL_BIGINT_MAX        = +(2 ** 64) / 2 - 1;
    const MYSQL_BIGINT_UNS_MIN    = +(1);
    const MYSQL_BIGINT_UNS_MAX    = +(2 ** 64) - 1;

    const MYSQL_TINYINT    = 'tinyint';
    const MYSQL_SMALLINT   = 'smallint';
    const MYSQL_MEDIUMINT  = 'mediumint';
    const MYSQL_INT        = 'int';
    const MYSQL_INTEGER    = 'integer';
    const MYSQL_BIGINT     = 'bigint';
    const MYSQL_CHAR       = 'char';
    const MYSQL_VARCHAR    = 'varchar';
    const MYSQL_TINYTEXT   = 'tinytext';
    const MYSQL_TEXT       = 'text';
    const MYSQL_MEDIUMTEXT = 'mediumtext';
    const MYSQL_LONGTEXT   = 'longtext';
    const MYSQL_DATE       = 'date';
    const MYSQL_TIME       = 'time';
    const MYSQL_YEAR       = 'year';
    const MYSQL_DATETIME   = 'datetime';
    const MYSQL_TIMESTAMP  = 'timestamp';
    const MYSQL_FLOAT      = 'float';
    const MYSQL_DOUBLE     = 'double';
    const MYSQL_DECIMAL    = 'decimal';
    const MYSQL_JSON       = 'json';

    const T_BOOL     = 'bool';
    const T_BOOLEAN  = 'boolean';
    const T_INT      = 'int';
    const T_INTEGER  = 'integer';
    const T_FLOAT    = 'float';
    const T_DOUBLE   = 'double';
    const T_STRING   = 'string';
    const T_ARRAY    = 'array';
    const T_OBJECT   = 'object';
    const T_CALLABLE = 'callable';
    const T_RESOURCE = 'resource';
    const T_NULL     = 'null';
    const T_MIXED    = 'mixed';
    const T_NUMBER   = 'number';
    const T_NUMERIC  = 'numeric';
    const T_CALLBACK = 'callback';
    const T_VOID     = 'void';
    const T_JSON     = 'json';

    const T_ARRAY_MIXED = 'mixed';
    const T_ARRAY_INDEX = 'index';
    const T_ARRAY_ASSOC = 'assoc';

    const ASSERT_EMPTY = 'empty';
    const ASSERT_ISSET = 'isset';

    const FORM_DATA_SPLIT      = '~';
    const VALIDATION_IF_SET    = '~';
    const ENTITY_KEY_TRIM      = "\x00*";
    const ENTER                = "\n";
    const OAUTH2_GRANT_TYPE_CC = 'client_credentials';
    const OAUTH2_TOKEN_TYPE    = 'bearer';
    const DOCTRINE_DEFAULT     = 'default';
    const TMP_PATH             = '/tmp';
    const PK                   = 'id';
    const SORT                 = 'sort';
    const ORDER                = 'order';
    const MIN_ADMIN_ROLE       = -10;
    const TPL_SUFFIX           = '.html.twig';
    const FORMAT_JSON          = 'json';
    const FORMAT_HTML          = 'html';
    const AUTO                 = 'auto';
    const VERIFY_JSON          = 'http://www.bejson.com/kim.htm';

    const MEDIA_XS  = 575;
    const MEDIA_SM  = 576;
    const MEDIA_MD  = 768;
    const MEDIA_LG  = 992;
    const MEDIA_XL  = 1200;
    const MEDIA_XXL = 1600;

    const DAY_BEGIN        = '00:00:00';
    const DAY_END          = '23:59:59';
    const TR_NO            = '_no';
    const TR_ACT           = '_action';
    const SELECT_ALL_KEY   = 'ALL';
    const SELECT_ALL_VALUE = 'ALL';
    const SORT_ASC         = 'ASC';
    const SORT_DESC        = 'DESC';
    const SORT_AUTO        = 'AUTO';
    const SORT_ASC_LONG    = 'ascend';
    const SORT_DESC_LONG   = 'descend';
    const NIL              = '(Nil)';
    const DIRTY            = '(Dirty)';
    const NOT_SET          = '(NotSet)';
    const NOT_FILE         = '(NotExists)';
    const SECRET           = '(Secret)';
    const SRC_CSS          = 'css';
    const SRC_JS           = 'js';
    const SELECT           = 'SELECT'; // doctrine QueryBuilder 0
    const DELETE           = 'DELETE'; // doctrine QueryBuilder 1
    const UPDATE           = 'UPDATE'; // doctrine QueryBuilder 2
    const INSERT           = 'INSERT';

    const FN_INIT                    = 'init';
    const FN_BEFORE_BOOTSTRAP        = 'beforeBootstrap';
    const FN_BOOTSTRAP               = 'bootstrap';
    const FN_AFTER_BOOTSTRAP         = 'afterBootstrap';
    const FN_ENTITY_PREVIEW_HINT     = 'entityPreviewHint';
    const FN_ENTITY_PERSISTENCE_HINT = 'entityPersistenceHint';
    const FN_ENTITY_FILTER_HINT      = 'entityFilterHint';
    const FN_PREVIEW_HINT            = 'previewTailorHint';
    const FN_PERSISTENCE_HINT        = 'persistenceTailorHint';
    const FN_API_DOC_FLAG            = 'apiDocFlag';
    const FN_API_DOC_OUTPUT          = 'apiDocOutput';
    const FN_RESPONSE_KEYS           = 'responseKeys';
    const FN_RESPONSE_KEYS_AJAX      = 'responseKeysAjax';
    const FN_BLANK_VIEW              = 'blankViewHandler';
    const FN_SIGN_FAILED             = 'signFailedLogger';
    const FN_BEFORE_RESPONSE         = 'beforeResponse';
    const FN_BEFORE_RESPONSE_CODE    = 'beforeResponseCode';
    const FN_BEFORE_DISPLAY          = 'beforeDisplay';
    const FN_STRICT_AUTH             = 'strictAuthorization';
    const FN_EXTRA_CONFIG            = 'extraConfig';
    const FN_HOOKER_ARGS             = 'hookerExtraArgs';
    const FN_VALIDATOR_ARGS          = 'validatorExtraArgs';
    const FN_UPLOAD_OPTIONS          = 'uploadOptionsHandler';

    const FMT_YEAR_ONLY       = 'Y';
    const FMT_MONTH_ONLY      = 'm';
    const FMT_DAY_ONLY        = 'd';
    const FMT_WEEK_ONLY       = 'w';
    const FMT_HOUR_ONLY       = 'H';
    const FMT_MINUTE_ONLY     = 'i';
    const FMT_SECOND_ONLY     = 's';
    const FMT_MONTH           = 'Y-m';
    const FMT_DAY             = 'Y-m-d';
    const FMT_DAY_SIMPLE      = 'Ymd';
    const FMT_DAY2            = 'Y-n-j';
    const FMT_WEEK            = 'Y-m-d-w';
    const FMT_HOUR            = 'Y-m-d H';
    const FMT_MINUTES         = 'Y-m-d H:i';
    const FMT_MINUTE          = 'H:i';
    const FMT_SECOND          = 'H:i:s';
    const FMT_FULL            = 'Y-m-d H:i:s';
    const FMT_MIC             = 'Y-m-d H:i:s.u';
    const FMT_MONTH_FIRST_DAY = 'Y-m-01';
    const FMT_MONTH_LAST_DAY  = 'Y-m-t';

    const PG_CURRENT_PAGE = 'current_page';
    const PG_PAGE_SIZE    = 'page_size';
    const PG_TOTAL_PAGE   = 'total_page';
    const PG_TOTAL_ITEM   = 'total_item';
    const PG_ITEMS        = 'items';

    const PAY_PLATFORM_SIN    = 1;
    const PAY_PLATFORM_WALL   = 2;
    const PAY_PLATFORM_PSN    = 3;
    const PAY_PLATFORM_APPLE  = 4;
    const PAY_PLATFORM_WX     = 5;
    const PAY_PLATFORM_ALI    = 6;
    const PAY_PLATFORM_GOOGLE = 7;

    const PAY_STATE_CLOSE     = 0;
    const PAY_STATE_WAIT_USER = 1;
    const PAY_STATE_WAIT_CALL = 2;
    const PAY_STATE_ERROR     = 48;
    const PAY_STATE_FAIL      = 49;
    const PAY_STATE_DONE      = 50;
    const PAY_STATE_REFUND    = 51;

    const USER_PLAIN    = 1;
    const USER_INTERNAL = 2;
    const USER_PEND     = 3;

    const USER_TYPE_PHONE    = 1;
    const USER_TYPE_EMAIL    = 2;
    const USER_TYPE_WX       = 3;
    const USER_TYPE_QQ       = 4;
    const USER_TYPE_GITEE    = 5;
    const USER_TYPE_GITHUB   = 6;
    const USER_TYPE_SINA     = 7;
    const USER_TYPE_DING     = 8;
    const USER_TYPE_BAIDU    = 9;
    const USER_TYPE_CODING   = 10;
    const USER_TYPE_OSCHINA  = 11;
    const USER_TYPE_ALIPAY   = 12;
    const USER_TYPE_TAOBAO   = 13;
    const USER_TYPE_GOOGLE   = 14;
    const USER_TYPE_FACEBOOK = 15;
    const USER_TYPE_DOUYIN   = 16;
    const USER_TYPE_LINKED   = 17;
    const USER_TYPE_MS       = 18;
    const USER_TYPE_MI       = 19;
    const USER_TYPE_DEVICE   = 99;

    const BIND_THIRD_TO_PHONE  = 1;
    const BIND_THIRD_TO_EMAIL  = 2;
    const BIND_DEVICE_TO_PHONE = 3;
    const BIND_DEVICE_TO_EMAIL = 4;
    const BIND_PHONE_TO_THIRD  = 5;
    const BIND_PHONE_TO_DEVICE = 6;
    const BIND_EMAIL_TO_THIRD  = 7;
    const BIND_EMAIL_TO_DEVICE = 8;

    const PERIOD_YEAR    = 1;
    const PERIOD_QUARTER = 2;
    const PERIOD_MONTH   = 3;
    const PERIOD_WEEK    = 4;
    const PERIOD_DAY     = 5;
    const PERIOD_HOUR    = 6;
    const PERIOD_MINUTE  = 7;
    const PERIOD_SECOND  = 8;
    const PERIOD_MS      = 9;

    const WX_PAY_INSIDE = 'JSAPI';
    const WX_PAY_QR     = 'NATIVE';
    const WX_PAY_APP    = 'APP';
    const WX_PAY_H5     = 'MWEB';

    const CLD_ALI   = 1;
    const CLD_TX    = 2;
    const CLD_AWS   = 3;
    const CLOUD_ALI = 'ali';
    const CLOUD_TX  = 'tx';
    const CLOUD_AWS = 'aws';

    const DOMESTIC = 'domestic';
    const ABROAD   = 'abroad';

    const MESSAGE_BOOTSTRAP = 1;
    const MESSAGE_CAROUSEL  = 2;
    const MESSAGE_NOTICE    = 3;
    const MESSAGE_POPUP     = 4;

    const REQ_GET    = 'GET';
    const REQ_POST   = 'POST';
    const REQ_PATCH  = 'PATCH';
    const REQ_DELETE = 'DELETE';
    const REQ_HEAD   = 'HEAD';
    const REQ_ALL    = 'GET|POST|HEAD';

    const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    const CONTENT_TYPE_JSON = 'application/json;charset=utf-8';

    const SCHEME_HTTP  = 'http';
    const SCHEME_HTTPS = 'https';

    const APP_TYPE_API      = 'api';
    const APP_TYPE_WEB      = 'web';
    const APP_TYPE_FRONTEND = 'frontend';
    const APP_TYPE_BACKEND  = 'backend';

    const SELECTOR_CHECKBOX = 'checkbox';
    const SELECTOR_RADIO    = 'radio';

    const TIME_SECOND = 1;
    const TIME_MINUTE = self::TIME_SECOND * 60;
    const TIME_HOUR   = self::TIME_MINUTE * 60;
    const TIME_DAY    = self::TIME_HOUR * 24;
    const TIME_WEEK   = self::TIME_DAY * 7;
    const TIME_MONTH  = self::TIME_DAY * 30;
    const TIME_YEAR   = self::TIME_MONTH * 12;

    const V_NOTHING     = 0;   // do nothing
    const V_SIGN        = 1;   // signature
    const V_SHOULD_AUTH = 2;   // should authorization
    const V_MUST_AUTH   = 4;   // must authorization
    const V_STRICT_AUTH = 8;   // strict authorization
    const V_AJAX        = 16;  // ajax request
    const V_ACCESS      = 32;  // access control

    const V_USER         = self::V_SIGN | self::V_SHOULD_AUTH;              // sign、should
    const V_USER_STRICT  = self::V_USER | self::V_STRICT_AUTH;              // sign、should、strict
    const V_LOGIN        = self::V_USER | self::V_MUST_AUTH;                // sign、should、must
    const V_LOGIN_STRICT = self::V_LOGIN | self::V_STRICT_AUTH;             // sign、should、must、strict

    const VW_USER         = self::V_USER ^ self::V_SIGN;                    // should
    const VW_USER_STRICT  = self::V_USER_STRICT ^ self::V_SIGN;             // should、strict
    const VW_LOGIN        = self::V_LOGIN ^ self::V_SIGN;                   // should、must
    const VW_LOGIN_ACCESS = self::V_LOGIN | self::V_ACCESS;                 // should、must、access
    const VW_LOGIN_STRICT = self::V_LOGIN_STRICT ^ self::V_SIGN;            // should、must、strict
    const VW_LOGIN_AS     = self::VW_LOGIN_ACCESS | self::V_STRICT_AUTH;    // should、must、access、strict

    const VD_OS     = 1;
    const VD_UA     = 2;
    const VD_DEVICE = 4;
    const VD_ALL    = self::VD_OS | self::VD_UA | self::VD_DEVICE;

    const OS_FULL         = 0;
    const OS_ANDROID      = 1;
    const OS_IOS          = 2;
    const OS_WINDOWS      = 3;
    const OS_MAC          = 4;
    const OS_WEB          = 5;
    const OS_ANDROID_TV   = 6;
    const OS_MAC_OFFICIAL = 7;

    const CAPTCHA_SMS              = 1;
    const CAPTCHA_EMAIL            = 2;
    const SNS_SCENE_SIGN_IN        = 1;
    const SNS_SCENE_SIGN_UP        = 2;
    const SNS_SCENE_PASSWORD       = 3;
    const SNS_SCENE_BIND           = 4;
    const SNS_SCENE_AGENT_SIGN_IN  = 5;
    const SNS_SCENE_AGENT_SIGN_UP  = 6;
    const SNS_SCENE_AGENT_PASSWORD = 7;
    const SNS_SCENE_AGENT_WITHDRAW = 8;
    const SNS_PEND_USER            = 100;

    const VG_NEWLY  = 'newly';
    const VG_MODIFY = 'modify';

    const JS_MOMENT               = 'npm;moment/min/moment.min.js';
    const JS_VUE                  = 'npm;vue/dist/vue.js';
    const JS_VUE_MIN              = 'npm;vue/dist/vue.min.js';
    const JS_ANT_D                = 'npm;ant-design-vue/dist/antd.js';
    const JS_ANT_D_MIN            = 'npm;ant-design-vue/dist/antd.min.js';
    const JS_ELE                  = 'npm;element-ui/lib/index.js';
    const JS_TIP                  = 'npm;tippy.js/dist/tippy.all.min.js';
    const JS_JQUERY               = 'npm;jquery/dist/jquery.min.js';
    const JS_RSA                  = 'npm;jsencrypt/bin/jsencrypt.min.js';
    const JS_COPY                 = 'npm;clipboard/dist/clipboard.min.js';
    const JS_SORTABLE             = 'npm;sortablejs/Sortable.min.js';
    const JS_CROPPER              = 'npm;cropper/dist/cropper.min.js';
    const JS_CHART                = 'npm;echarts/dist/echarts.min.js';
    const JS_CHART_MAP_CHINA      = 'npm;echarts/map/js/china.js';
    const JS_CHART_DARK           = 'npm;echarts/theme/dark.js';
    const JS_CHART_INFOGRAPHIC    = 'npm;echarts/theme/infographic.js';
    const JS_CHART_MACARONS       = 'npm;echarts/theme/macarons.js';
    const JS_CHART_ROMA           = 'npm;echarts/theme/roma.js';
    const JS_CHART_SHINE          = 'npm;echarts/theme/shine.js';
    const JS_CHART_VINTAGE        = 'npm;echarts/theme/vintage.js';
    const JS_CHART_CHALK          = 'diy;echart/chalk.keep.js';
    const JS_CHART_ESSOS          = 'diy;echart/essos.keep.js';
    const JS_CHART_HALLOWEEN      = 'diy;echart/halloween.keep.js';
    const JS_CHART_PURPLE_PASSION = 'diy;echart/purple-passion.keep.js';
    const JS_CHART_WALDEN         = 'diy;echart/walden.keep.js';
    const JS_CHART_WESTEROS       = 'diy;echart/westeros.keep.js';
    const JS_CHART_WONDERLAND     = 'diy;echart/wonderland.keep.js';
    const JS_CONSOLE              = 'npm;vconsole/dist/vconsole.min.js';
    const JS_PHOTOS               = 'npm;photoswipe/dist/photoswipe.min.js';
    const JS_FANCY_BOX            = 'npm;@fancyapps/fancybox/dist/jquery.fancybox.min.js';
    const JS_LAZY_LOAD            = 'npm;layzr.js/dist/layzr.js';
    const JS_FOUNDATION           = 'diy;foundation.js';
    const JS_BSW                  = 'diy;bsw.js';
    const JS_WEB                  = 'diy;web.js';
    const JS_EDITOR               = 'npm;@ckeditor/ckeditor5-build-classic/build/ckeditor.js';
    const JS_EDITOR_CUSTOM        = 'diy;third/ckeditor5-custom.js';
    const CHART_DEFAULT_THEME     = 'westeros';

    const JS_LANG = [
        'cn' => 'diy;lang/cn.js',
        'en' => 'diy;lang/en.js',
    ];

    const CSS_ANT_D     = 'npm;ant-design-vue/dist/antd.min.css';
    const CSS_ELE       = 'npm;element-ui/lib/theme-chalk/index.css';
    const CSS_ANIMATE   = 'npm;animate.css/animate.min.css';
    const CSS_CROPPER   = 'npm;cropper/dist/cropper.min.css';
    const CSS_PHOTOS    = 'npm;photoswipe/dist/photoswipe.css';
    const CSS_FANCY_BOX = 'npm;@fancyapps/fancybox/dist/jquery.fancybox.min.css';
    const CSS_BSW       = 'diy;bsw.css';
    const CSS_WEB       = 'diy;web.css';
    const CSS_EDITOR    = 'diy;third/ckeditor5.css';

    const RULES_REQUIRED = ['required' => true, 'message' => '{{ field }} Required'];

    // slot 变量模板
    const SLOT_VARIABLES = "value, record, index";

    // slot 不为空白表达式
    const SLOT_NOT_BLANK = '(({:value} !== "") && ({:value} !== null) && ({:value} !== false))';

    // slot 包裹容器模板
    const SLOT_CONTAINER = "<div slot='{uuid}' slot-scope='{Abs::SLOT_VARIABLES}'>{tpl}</div>";

    // slot 包裹容器模板 (支持html)
    const SLOT_HTML_CONTAINER = "<div slot='{uuid}' slot-scope='{Abs::SLOT_VARIABLES}'><div v-html='{:value}'></div></div>";

    // 空数据展示模板
    const TPL_NIL = "<div class='bsw-disable'>{Abs::NIL}</div>";

    // [配合]空数据展示模板
    const TPL_ELSE_NIL = "<div v-else>{Abs::TPL_NIL}</div>";

    // 脏数据展示模板
    const TPL_DIRTY = "<div class='bsw-disable'>{Abs::DIRTY}</div>";

    // [配合]脏数据展示模板
    const TPL_ELSE_DIRTY = "<div v-else>{Abs::TPL_DIRTY}</div>";

    // 未设置展示模板
    const TPL_NOT_SET = "<div class='bsw-disable'>{Abs::NOT_SET}</div>";

    // 非文件展示模板
    const TPL_NOT_FILE = "<div class='bsw-disable'>{Abs::NOT_FILE}</div>";

    // 普通 dress 模板
    const TPL_DRESS = "<a-tag color='{dress}'>{value}</a-tag>";

    // 粉色 dress 模板
    const TPL_DRESS_PINK = "<a-tag color='pink'>{value}</a-tag>";

    // 红色 dress 模板
    const TPL_DRESS_RED = "<a-tag color='red'>{value}</a-tag>";

    // 橙色 dress 模板
    const TPL_DRESS_ORANGE = "<a-tag color='orange'>{value}</a-tag>";

    // 绿色 dress 模板
    const TPL_DRESS_GREEN = "<a-tag color='green'>{value}</a-tag>";

    // 青色 dress 模板
    const TPL_DRESS_CYAN = "<a-tag color='cyan'>{value}</a-tag>";

    // 蓝色 dress 模板
    const TPL_DRESS_BLUE = "<a-tag color='blue'>{value}</a-tag>";

    // 紫色 dress 模板
    const TPL_DRESS_PURPLE = "<a-tag color='purple'>{value}</a-tag>";

    // 无 dress 枚举模板
    const TPL_ENUM_0_DRESS = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-long-text'>{value}</div>{Abs::TPL_ELSE_DIRTY}";

    // 单 dress 枚举模板
    const TPL_ENUM_1_DRESS = "<a-tag v-if='{Abs::SLOT_NOT_BLANK}' :color='{dress}'>{value}</a-tag>{Abs::TPL_ELSE_DIRTY}";

    // 多 dress 枚举模板
    const TPL_ENUM_2_DRESS = "<a-tag v-if='{Abs::SLOT_NOT_BLANK}' color='{dress}'>{value}</a-tag>{Abs::TPL_ELSE_DIRTY}";

    // 对立面枚举
    const TPL_ENUM_STATE = "<a-badge v-if='{Abs::SLOT_NOT_BLANK}' :status='{dress}' :text='{enum}'></a-badge>{Abs::TPL_ELSE_DIRTY}";

    // 代码模板
    const RENDER_CODE = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-code bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";

    // 图标模板
    const RENDER_ICON = "<div v-if='{Abs::SLOT_NOT_BLANK}'><a-icon v-if='{:value}.substring(0, 1) == \"a\"' :type='{:value}.substring(2)'></a-icon><b-icon v-else :type='{:value}.substring(2)'></b-icon> {value}</div>{Abs::TPL_ELSE_NIL}";

    // 禁用状态模板
    const RENDER_DISABLE = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-disable bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";

    // 文本模板 (标识空)
    const RENDER_TEXT = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";

    // 图片
    const RENDER_IMAGE = "<a v-if='{Abs::SLOT_NOT_BLANK}' :href='{:value}' class='bsw-preview-image' data-fancybox='preview' :data-caption='{:value}'><img :src='{:value}'></a>{Abs::TPL_ELSE_NIL}";

    // 链接
    const RENDER_LINK = "<div v-if='{Abs::SLOT_NOT_BLANK}'><span class='bsw-code bsw-long-text'><a class='bsw-preview-link' :href='{:value}' target='_blank'>✪</a>{value}</span></div>{Abs::TPL_ELSE_NIL}";

    // 密文 (统一)
    const RENDER_SECRET_1 = "<div class='bsw-disable'>{Abs::SECRET}</div>";

    // 密文 (标识空)
    const RENDER_SECRET_2 = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-disable'>{Abs::SECRET}</div>{Abs::TPL_ELSE_NIL}";

    // html 文本模板
    const HTML_TEXT = "<div class='bsw-long-text'>{value}</div>";

    // html pre 标签
    const HTML_PRE = "<pre class='bsw-pre bsw-long-text'>{value}</pre>";

    // html 粉色模板
    const HTML_PINK = "<div class='ant-tag ant-tag-has-color' style='background-color: #eb2f96;'>{value}</div>";

    // html 红色模板
    const HTML_RED = "<div class='ant-tag ant-tag-has-color' style='background-color: #f5222d;'>{value}</div>";

    // html 橙色模板
    const HTML_ORANGE = "<div class='ant-tag ant-tag-has-color' style='background-color: #fa8c16;'>{value}</div>";

    // html 绿色模板
    const HTML_GREEN = "<div class='ant-tag ant-tag-has-color' style='background-color: #52c41a;'>{value}</div>";

    // html 青色模板
    const HTML_CYAN = "<div class='ant-tag ant-tag-has-color' style='background-color: #13c2c2;'>{value}</div>";

    // html 蓝色模板
    const HTML_BLUE = "<div class='ant-tag ant-tag-has-color' style='background-color: #1890ff;'>{value}</div>";

    // html 紫色模板
    const HTML_PURPLE = "<div class='ant-tag ant-tag-has-color' style='background-color: #722ed1;'>{value}</div>";

    // html 灰色模板
    const HTML_GRAY = "<div class='ant-tag ant-tag-has-color' style='background-color: #d6d6d6;'>{value}</div>";

    // 危险权限
    const DANGER_ACCESS = 'Dangerous permission, please be careful';
}
<?php

namespace Leon\BswBundle\Module\Entity;

class Abs
{
    /**
     * Mixed
     */
    const FORM_DATA_SPLIT   = ',';
    const VALIDATION_IF_SET = '~';
    const ENTITY_KEY_TRIM   = "\x00*";
    const ENTER             = "\n";
    const DOCTRINE_DEFAULT  = 'default';
    const TMP_PATH          = '/tmp';
    const PK                = 'id';
    const SORT              = 'sort';
    const ORDER             = 'order';
    const AUTO              = 'auto';
    const TAG_SQL_ERROR     = 'An exception occurred while executing';
    const RULES_REQUIRED    = ['required' => true, 'message' => '{{ field }} Required'];
    const LINE_DASHED       = '<div class="ant-divider ant-divider-horizontal ant-divider-dashed" style="margin: 10px 0;"></div>';
    const BK_RENDER_ARGS    = 1;

    const CODE_BASIC     = 1000024;
    const CODE_DIST      = 'gz8xjdt3h7rcypfvewkm4aun2'; // 25
    const CODE_DIST_FULL = 'mct63kg0il5bp9uv17eryzs2wnja4xfodqh8'; // 36
    const CHAR_DIST_CN   = '０１２３４５６７８９ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ－　：．，／％＃！＠＆（）＜＞＂＇？［］｛｝＼｜＋＝＿＾￥￣｀';
    const CHAR_DIST_EN   = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz- :.,/%#!@&()<>"\'?[]{}\\|+=_^￥~`';

    const TR_NO                    = '__no';
    const TR_ACT                   = '__action';
    const HOOKER_FLAG_FIELDS       = '__fields';
    const HOOKER_FLAG_ACME         = '__acme';
    const HOOKER_FLAG_ENUMS_SUFFIX = '__suffix';
    const HOOKER_FLAG_ENUMS_INFO   = '__info';
    const RECORD_LOGGER_EFFECT     = '__effect';
    const RECORD_LOGGER_EXTRA      = '__extra';

    /**
     * About frontend & source
     */
    const MEDIA_MIN = 375;
    const MEDIA_XS  = 575;
    const MEDIA_SM  = 576;
    const MEDIA_MD  = 768;
    const MEDIA_LG  = 992;
    const MEDIA_XL  = 1200;
    const MEDIA_XXL = 1600;

    const SHAPE_MODAL  = 'modal';
    const SHAPE_DRAWER = 'drawer';

    const TAG_TYPE_NOTICE  = 'notification';
    const TAG_TYPE_MESSAGE = 'message';
    const TAG_TYPE_CONFIRM = 'confirm';

    const TAG_CLASSIFY_SUCCESS = 'success';
    const TAG_CLASSIFY_INFO    = 'info';
    const TAG_CLASSIFY_WARNING = 'warning';
    const TAG_CLASSIFY_ERROR   = 'error';

    const RESULT_STATUS_SUCCESS = 'success';
    const RESULT_STATUS_ERROR   = 'error';
    const RESULT_STATUS_INFO    = 'info';
    const RESULT_STATUS_WARNING = 'warning';
    const RESULT_STATUS_403     = '403';
    const RESULT_STATUS_404     = '404';
    const RESULT_STATUS_500     = '500';

    const SIZE_SMALL   = 'small';
    const SIZE_DEFAULT = 'default';
    const SIZE_MIDDLE  = 'middle';
    const SIZE_LARGE   = 'large';

    const SCENE_COMMON = 'common';
    const SCENE_NORMAL = 'normal';
    const SCENE_IFRAME = 'iframe';

    const MODE_DEFAULT  = 'default';
    const MODE_MULTIPLE = 'multiple';
    const MODE_TAGS     = 'tags';
    const MODE_BOX      = 'combobox';

    const SEARCH_VALUE = 'value';
    const SEARCH_LABEL = 'children';
    const SEARCH_TITLE = 'title';

    const TYPE_BUTTON   = 'button';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_COLOR    = 'color';
    const TYPE_DATE     = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_LOCAL    = 'datetime-local';
    const TYPE_EMAIL    = 'email';
    const TYPE_FILE     = 'file';
    const TYPE_HIDDEN   = 'hidden';
    const TYPE_IMAGE    = 'image';
    const TYPE_MONTH    = 'month';
    const TYPE_NUMBER   = 'number';
    const TYPE_PASSWORD = 'password';
    const TYPE_RADIO    = 'radio';
    const TYPE_RANGE    = 'range';
    const TYPE_RESET    = 'reset';
    const TYPE_SEARCH   = 'search';
    const TYPE_SUBMIT   = 'submit';
    const TYPE_TEL      = 'tel';
    const TYPE_TEXT     = 'text';
    const TYPE_TIME     = 'time';
    const TYPE_URL      = 'url';
    const TYPE_WEEK     = 'week';

    const LIST_TYPE_TEXT     = 'text';
    const LIST_TYPE_IMG      = 'picture';
    const LIST_TYPE_IMG_CARD = 'picture-card';

    const THEME_PRIMARY       = 'primary';
    const THEME_DASHED        = 'dashed';
    const THEME_DANGER        = 'danger';
    const THEME_LINK          = 'link';
    const THEME_DEFAULT       = 'default';
    const THEME_BSW_PRIMARY   = 'bsw-primary bsw-btn';
    const THEME_BSW_SECONDARY = 'bsw-secondary bsw-btn';
    const THEME_BSW_SUCCESS   = 'bsw-success bsw-btn';
    const THEME_BSW_DANGER    = 'bsw-danger bsw-btn';
    const THEME_BSW_WARNING   = 'bsw-warning bsw-btn';
    const THEME_BSW_INFO      = 'bsw-info bsw-btn';
    const THEME_BSW_LIGHT     = 'bsw-light bsw-btn';
    const THEME_BSW_DARK      = 'bsw-dark bsw-btn';

    const CHECKED_STRATEGY_ALL      = 'bsw.d.TreeSelect.SHOW_ALL';
    const CHECKED_STRATEGY_PARENT   = 'bsw.d.TreeSelect.SHOW_PARENT';
    const CHECKED_STRATEGY_CHILDREN = 'bsw.d.TreeSelect.SHOW_CHILD';

    const JS_MOMENT         = 'npm;moment/min/moment.min.js';
    const JS_VUE            = 'npm;vue/dist/vue.js';
    const JS_VUE_MIN        = 'npm;vue/dist/vue.min.js';
    const JS_ANT_D          = 'npm;ant-design-vue/dist/antd.js';
    const JS_ANT_D_LANG     = 'npm;ant-design-vue/dist/antd-with-locales.js';
    const JS_ANT_D_MIN      = 'npm;ant-design-vue/dist/antd.min.js';
    const JS_ANT_D_LANG_MIN = 'npm;ant-design-vue/dist/antd-with-locales.min.js';
    const JS_ELE            = 'npm;element-ui/lib/index.js';
    const JS_TIP            = 'npm;tippy.js/dist/tippy.all.min.js';
    const JS_JQUERY         = 'npm;jquery/dist/jquery.min.js';
    const JS_RSA            = 'npm;jsencrypt/bin/jsencrypt.min.js';
    const JS_COPY           = 'npm;clipboard/dist/clipboard.min.js';
    const JS_SORTABLE       = 'npm;sortablejs/Sortable.min.js';
    const JS_CROPPER        = 'npm;cropper/dist/cropper.min.js';
    const JS_CHART          = 'npm;echarts/dist/echarts.min.js';
    const JS_CHART_THEME    = 'diy;echart/westeros.keep.js';
    const JS_CONSOLE        = 'npm;vconsole/dist/vconsole.min.js';
    const JS_PHOTOS         = 'npm;photoswipe/dist/photoswipe.min.js';
    const JS_FANCY_BOX      = 'npm;@fancyapps/fancybox/dist/jquery.fancybox.min.js';
    const JS_LAZY_LOAD      = 'npm;layzr.js/dist/layzr.js';
    const JS_FULL_SCREEN    = 'npm;screenfull/dist/screenfull.js';
    const JS_FOUNDATION     = 'diy;foundation.js';
    const JS_BSW            = 'diy;bsw.js';
    const JS_WEB            = 'diy;web.js';
    const JS_EDITOR         = 'npm;@ckeditor/ckeditor5-build-decoupled-document/build/ckeditor.js';
    const JS_EDITOR_CUSTOM  = 'diy;third/ckeditor5-custom.js';

    const JS_LANG = [
        'cn' => 'diy;lang/cn.js',
        'hk' => 'diy;lang/hk.js',
        'en' => 'diy;lang/en.js',
    ];

    const JS_MOMENT_LANG = [
        'cn' => 'npm;moment/locale/zh-cn.js',
        'hk' => 'npm;moment/locale/zh-hk.js',
        'en' => 'npm;moment/locale/es-us.js',
    ];

    const JS_EDITOR_LANG = [
        'cn' => 'npm;@ckeditor/ckeditor5-build-decoupled-document/build/translations/zh-cn.js',
        'hk' => 'npm;@ckeditor/ckeditor5-build-decoupled-document/build/translations/zh.js',
        'en' => 'npm;@ckeditor/ckeditor5-build-decoupled-document/build/translations/en-gb.js',
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

    /**
     * About framework
     */
    const HTML_SUFFIX = '.html';
    const TPL_SUFFIX  = '.twig';

    const SRC_CSS = 'css';
    const SRC_JS  = 'js';

    const DOMESTIC = 'domestic';
    const ABROAD   = 'abroad';

    const ASSERT_EMPTY = 'empty';
    const ASSERT_ISSET = 'isset';

    const SELECT_ALL_KEY   = 'ALL';
    const SELECT_ALL_VALUE = 'ALL';

    const VALIDATOR_GROUP_NEWLY  = 'newly';
    const VALIDATOR_GROUP_MODIFY = 'modify';

    const OAUTH2_TOKEN_TYPE    = 'bearer';
    const OAUTH2_GRANT_TYPE_CC = 'client_credentials';

    const IMAGE_SIZE_MAX = 'MAX';
    const IMAGE_SUFFIX   = ['gif', 'jpg', 'jpeg', 'png'];

    const HEX_SECOND_MINUTE = 60;
    const HEX_MINUTE_HOUR   = 60;
    const HEX_HOUR_DAY      = 24;
    const HEX_DAY_WEEK      = 7;
    const HEX_DAY_MONTH     = 30;
    const HEX_DAY_YEAR      = 360;
    const HEX_MONTH_YEAR    = 12;
    const HEX_CENT_YUAN     = 100;
    const HEX_SIZE          = 1024;
    const HEX_SIZE_2        = self::HEX_SIZE ** 2;
    const HEX_SIZE_3        = self::HEX_SIZE ** 3;

    const TAG_MESSAGE        = 'message';
    const TAG_MODAL          = 'modal';
    const TAG_RESULT         = 'result';
    const TAG_HISTORY        = 'history';
    const TAG_FALLBACK       = 'fallback';
    const TAG_SESSION_LANG   = 'lang';
    const TAG_SEO_ACME_KEY   = 'seo_acme';
    const TAG_PREVIEW        = 'preview';
    const TAG_PERSISTENCE    = 'persistence';
    const TAG_FILTER         = 'filter';
    const TAG_ROLL           = 'rollback:';
    const TAG_ROLL_VALIDATOR = 'rollback:validator';
    const TAG_VALIDATOR      = 'validator';
    const TAG_INSERT         = 'insert';
    const TAG_DELETE         = 'delete';
    const TAG_UPDATE         = 'update';
    const TAG_SELECT         = 'select';
    const TAG_TRANS          = 'transactional:';
    const TAG_TRANS_BEFORE   = 'transactional:before';
    const TAG_TRANS_AFTER    = 'transactional:after';

    const POS_TOP    = 'top';
    const POS_RIGHT  = 'right';
    const POS_BOTTOM = 'bottom';
    const POS_LEFT   = 'left';
    const POS_CENTER = 'center';
    const POS_MIDDLE = 'middle';

    const SELECT = 'SELECT'; // doctrine QueryBuilder 0
    const DELETE = 'DELETE'; // doctrine QueryBuilder 1
    const UPDATE = 'UPDATE'; // doctrine QueryBuilder 2
    const INSERT = 'INSERT';

    const PERSISTENCE_TOTAL_COLUMN = 24;
    const PERSISTENCE_LABEL_COLUMN = 4;

    const BEGIN_REQUEST   = 'Begin request';
    const BEGIN_CONSTRUCT = 'Begin construct';
    const END_CONSTRUCT   = 'End construct';
    const BEGIN_INIT      = 'Begin init';
    const END_INIT        = 'End init';
    const BEGIN_LOGIC     = 'Begin logic';
    const BEGIN_VALID     = 'Begin valid';
    const END_VALID       = 'End valid';
    const END_LOGIC       = 'End logic';
    const END_REQUEST     = 'End request';
    const BEGIN_API       = 'Begin third api';
    const END_API         = 'End third api';

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

    const DOC_TAG_RIGHT = '¹';
    const DOC_TAG_WRONG = 'º';
    const DOC_TAG_TREE  = '└ ';
    const DOC_TAG_LINE  = '``›››››``';

    const FORMAT_JSON = 'json';
    const FORMAT_HTML = 'html';
    const FORMAT_XML  = 'xml';

    const DAY_BEGIN  = '00:00:00';
    const _DAY_BEGIN = ' 00:00:00';
    const DAY_END    = '23:59:59';
    const _DAY_END   = ' 23:59:59';

    const SORT_ASC       = 'ASC';
    const SORT_DESC      = 'DESC';
    const SORT_AUTO      = 'AUTO';
    const SORT_ASC_LONG  = 'ascend';
    const SORT_DESC_LONG = 'descend';

    const NIL         = '(Nil)';
    const DIRTY       = '(Dirty)';
    const NOT_SET     = '(NotSet)';
    const NOT_FILE    = '(NotExists)';
    const SECRET      = '(Secret)';
    const UNKNOWN     = '(Unknown)';
    const UNALLOCATED = '(Unallocated)';
    const COMMON      = '(Common)';

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
    const FN_BEFORE_RENDER           = 'beforeRender';
    const FN_STRICT_AUTH             = 'strictAuthorization';
    const FN_EXTRA_CONFIG            = 'extraConfig';
    const FN_HOOKER_ARGS             = 'hookerExtraArgs';
    const FN_VALIDATOR_ARGS          = 'validatorExtraArgs';
    const FN_UPLOAD_OPTIONS          = 'uploadOptionsHandler';
    const FN_BEFORE_LOGIC            = 'beforeLogic';

    const FMT_YEAR_ONLY       = 'Y';
    const FMT_MONTH_ONLY      = 'm';
    const FMT_DAY_ONLY        = 'd';
    const FMT_WEEK_ONLY       = 'w';
    const FMT_HOUR_ONLY       = 'H';
    const FMT_MINUTE_ONLY     = 'i';
    const FMT_SECOND_ONLY     = 's';
    const FMT_MONTH           = 'Y-m';
    const FMT_MONTH_SIMPLE    = 'Ym';
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

    const PG_PAGE              = 'page';
    const PG_CURRENT_PAGE      = 'current_page';
    const PG_PAGE_SIZE         = 'page_size';
    const PG_TOTAL_PAGE        = 'total_page';
    const PG_TOTAL_ITEM        = 'total_item';
    const PG_ITEMS             = 'items';
    const PG_PAGE_SIZE_OPTIONS = [10, 20, 30, 50, 100];

    const MULTIPLE_PER       = 50;
    const PAGE_DEFAULT_SIZE  = 30;
    const PAGE_DEFAULT_RANGE = 10;

    const REQ_GET     = 'GET';
    const REQ_POST    = 'POST';
    const REQ_PATCH   = 'PATCH';
    const REQ_PUT     = 'PUT';
    const REQ_DELETE  = 'DELETE';
    const REQ_HEAD    = 'HEAD';
    const REQ_SYMFONY = 'SYMFONY_ROUTE';
    const REQ_ALL     = 'GET|POST|HEAD';

    const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    const CONTENT_TYPE_JSON = 'application/json;charset=utf-8';

    const SCHEME_HTTP  = 'http';
    const SCHEME_HTTPS = 'https';

    const APP_TYPE_API      = 'api';
    const APP_TYPE_WEB      = 'web';
    const APP_TYPE_FRONTEND = 'frontend';
    const APP_TYPE_BACKEND  = 'backend';

    const SELECTOR_CHECKBOX      = 'checkbox';
    const SELECTOR_RADIO         = 'radio';
    const SELECTOR_MODE_MULTIPLE = 'multiple';
    const SELECTOR_MODE_SINGLE   = 'single';

    const TIME_SECOND = 1;
    const TIME_MINUTE = self::TIME_SECOND * self::HEX_SECOND_MINUTE;
    const TIME_HOUR   = self::TIME_MINUTE * self::HEX_MINUTE_HOUR;
    const TIME_DAY    = self::TIME_HOUR * Abs::HEX_HOUR_DAY;
    const TIME_WEEK   = self::TIME_DAY * Abs::HEX_DAY_WEEK;
    const TIME_MONTH  = self::TIME_DAY * Abs::HEX_DAY_MONTH;
    const TIME_YEAR   = self::TIME_DAY * Abs::HEX_DAY_YEAR;

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

    const MODULE_MENU_SORT        = 10;
    const MODULE_HEADER_SORT      = 20;
    const MODULE_CRUMBS_SORT      = 30;
    const MODULE_TABS_SORT        = 40;
    const MODULE_FILTER_SORT      = 50;
    const MODULE_WELCOME_SORT     = 60;
    const MODULE_OPERATE_SORT     = 70;
    const MODULE_DATA_SORT        = 80;
    const MODULE_PREVIEW_SORT     = 90;
    const MODULE_PERSISTENCE_SORT = 90;
    const MODULE_CHART_SORT       = 90;
    const MODULE_AWAY_SORT        = 90;
    const MODULE_FOOTER_SORT      = 100;
    const MODULE_MODAL_SORT       = 110;
    const MODULE_DRAWER_SORT      = 120;
    const MODULE_RESULT_SORT      = 130;

    /**
     * About logic
     */
    const YES = 1;
    const NO  = 0;

    const NORMAL = 1;
    const CLOSE  = 0;

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

    const MESSAGE_BOOTSTRAP = 1;
    const MESSAGE_CAROUSEL  = 2;
    const MESSAGE_NOTICE    = 3;
    const MESSAGE_POPUP     = 4;

    const CAPTCHA_SMS   = 1;
    const CAPTCHA_EMAIL = 2;

    const SNS_SCENE_SIGN_IN        = 1;
    const SNS_SCENE_SIGN_UP        = 2;
    const SNS_SCENE_PASSWORD       = 3;
    const SNS_SCENE_BIND           = 4;
    const SNS_SCENE_AGENT_SIGN_IN  = 5;
    const SNS_SCENE_AGENT_SIGN_UP  = 6;
    const SNS_SCENE_AGENT_PASSWORD = 7;
    const SNS_SCENE_AGENT_WITHDRAW = 8;
    const SNS_PEND_USER            = 100;

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

    const PERIOD_AUTO    = 0;
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

    const CLD_ALI = 1;
    const CLD_TX  = 2;
    const CLD_AWS = 3;
    const CLD_GLE = 4;
    const CLD_HW  = 5;

    const CLOUD_ALI = 'ali';
    const CLOUD_TX  = 'tx';
    const CLOUD_AWS = 'aws';
    const CLOUD_GLE = 'gle';
    const CLOUD_HW  = 'hw';

    /**
     * TPL_ / RENDER_ 开头的渲染模板支持ANT-D语法
     */
    const SLOT_VARIABLES      = "value, record, index";
    const SLOT_NOT_BLANK      = '(({:value} !== "") && ({:value} !== null) && ({:value} !== false))';
    const SLOT_CONTAINER      = "<div class='bsw-td-{field}' slot='{uuid}' slot-scope='{Abs::SLOT_VARIABLES}'>{tpl}</div>";
    const SLOT_HTML_CONTAINER = "<div class='bsw-td-{field}' slot='{uuid}' slot-scope='{Abs::SLOT_VARIABLES}'><div v-html='{:value}'></div></div>";

    const TPL_NIL              = "<div class='bsw-disable'>{Abs::NIL}</div>";
    const TPL_ELSE_NIL         = "<div v-else>{Abs::TPL_NIL}</div>";
    const TPL_DIRTY            = "<div class='bsw-disable'>{Abs::DIRTY}</div>";
    const TPL_ELSE_DIRTY       = "<div v-else>{Abs::TPL_DIRTY}</div>";
    const TPL_NOT_SET          = "<div class='bsw-disable'>{Abs::NOT_SET}</div>";
    const TPL_ELSE_NOT_SET     = "<div v-else>{Abs::TPL_NOT_SET}</div>";
    const TPL_NOT_FILE         = "<div class='bsw-disable'>{Abs::NOT_FILE}</div>";
    const TPL_ELSE_NOT_FILE    = "<div v-else>{Abs::TPL_NOT_FILE}</div>";
    const TPL_SECRET           = "<div class='bsw-disable'>{Abs::SECRET}</div>";
    const TPL_ELSE_SECRET      = "<div v-else>{Abs::TPL_SECRET}</div>";
    const TPL_UNKNOWN          = "<div class='bsw-disable'>{Abs::UNKNOWN}</div>";
    const TPL_ELSE_UNKNOWN     = "<div v-else>{Abs::TPL_UNKNOWN}</div>";
    const TPL_UNALLOCATED      = "<div class='bsw-disable'>{Abs::UNALLOCATED}</div>";
    const TPL_ELSE_UNALLOCATED = "<div v-else>{Abs::TPL_UNALLOCATED}</div>";
    const TPL_COMMON           = "<div class='bsw-disable'>{Abs::COMMON}</div>";
    const TPL_ELSE_COMMON      = "<div v-else>{Abs::TPL_COMMON}</div>";

    const TPL_SCALAR_DRESS       = "<a-tag v-if='{Abs::SLOT_NOT_BLANK}' color='{dress}'>{value}</a-tag>{Abs::TPL_ELSE_NIL}";
    const TPL_ENUM_WITHOUT_DRESS = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-long-text'>{value}</div>{Abs::TPL_ELSE_DIRTY}";
    const TPL_ENUM_ONE_DRESS     = "<a-tag v-if='{Abs::SLOT_NOT_BLANK}' color='{dress}'>{value}</a-tag>{Abs::TPL_ELSE_DIRTY}";
    const TPL_ENUM_MANY_DRESS    = "<a-tag v-if='{Abs::SLOT_NOT_BLANK}' :color='{dress}'>{value}</a-tag>{Abs::TPL_ELSE_DIRTY}";
    const TPL_ENUM_STATUS_DRESS  = "<a-badge v-if='{Abs::SLOT_NOT_BLANK}' :status='{dress}' :text='{enum}'></a-badge>{Abs::TPL_ELSE_DIRTY}";

    const RENDER_CODE          = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-code bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";
    const RENDER_CODE_FULL     = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-code full bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";
    const RENDER_DISABLE       = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-disable bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";
    const RENDER_TEXT          = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-long-text'>{value}</div>{Abs::TPL_ELSE_NIL}";
    const RENDER_SECRET_1      = "<div class='bsw-disable'>{Abs::SECRET}</div>";
    const RENDER_SECRET_2      = "<div v-if='{Abs::SLOT_NOT_BLANK}' class='bsw-disable'>{Abs::SECRET}</div>{Abs::TPL_ELSE_NIL}";
    const RENDER_ROUND_PERCENT = "<a-progress type='circle' :percent='{:value}' :width='50' :stroke-width='6'></a-progress>";
    const RENDER_BAR_PERCENT   = "<a-progress type='line' :percent='{:value}' size='small' :stroke-width='6' status='active' ></a-progress>";
    const RENDER_NUM_PERCENT   = '<a-statistic :value="{:value}"><template slot="suffix"><span> / 100</span></template></a-statistic>';
    const RENDER_STAR          = '<a-rate :default-value="{:value}" allow-half disabled></a-rate>';
    const RENDER_AVATAR        = '<a-avatar size="large" :src="{:value}"></a-avatar>';
    const RENDER_COUNT         = '<a-statistic :value="{:value}"></a-statistic>';
    const RENDER_MONEY         = '<a-statistic :value="{:value}" :precision="2"></a-statistic>';
    const RENDER_CD_MS         = '<a-statistic-countdown :value="{:value} * 1000" format="HH:mm:ss:SSS"></a-statistic-countdown>';
    const RENDER_CD_DAY        = '<a-statistic-countdown :value="{:value} * 1000" format="HH:mm:ss"></a-statistic-countdown>';
    const RENDER_CD_LONG       = '<a-statistic-countdown :value="{:value} * 1000" format="D/HH:mm:ss"></a-statistic-countdown>';
    const RENDER_ICON          = "part/render-icon.html";
    const RENDER_IMAGE         = "part/render-image.html";
    const RENDER_LINK          = "part/render-link.html";

    /**
     * HTML_ / TEXT_ 开头的渲染模板仅支持普通渲染
     */
    const HTML_TEXT      = "<div class='bsw-long-text'>{value}</div>";
    const HTML_PRE       = "<pre class='bsw-pre bsw-long-text'>{value}</pre>";
    const HTML_CODE      = "<div class='bsw-code bsw-long-text'>{value}</div>";
    const HTML_CODE_FULL = "<div class='bsw-code full bsw-long-text'>{value}</div>";

    const HTML_PINK   = "<div class='ant-tag ant-tag-has-color' style='background-color:#eb2f96;'>{value}</div>";
    const HTML_RED    = "<div class='ant-tag ant-tag-has-color' style='background-color:#f5222d;'>{value}</div>";
    const HTML_ORANGE = "<div class='ant-tag ant-tag-has-color' style='background-color:#fa8c16;'>{value}</div>";
    const HTML_GREEN  = "<div class='ant-tag ant-tag-has-color' style='background-color:#52c41a;'>{value}</div>";
    const HTML_CYAN   = "<div class='ant-tag ant-tag-has-color' style='background-color:#13c2c2;'>{value}</div>";
    const HTML_BLUE   = "<div class='ant-tag ant-tag-has-color' style='background-color:#1890ff;'>{value}</div>";
    const HTML_PURPLE = "<div class='ant-tag ant-tag-has-color' style='background-color:#722ed1;'>{value}</div>";
    const HTML_GRAY   = "<div class='ant-tag ant-tag-has-color' style='background-color:#d6d6d6;'>{value}</div>";

    const HTML_PINK_TEXT   = "<div class='ant-tag ant-tag-has-color' style='color:#eb2f96;'>{value}</div>";
    const HTML_RED_TEXT    = "<div class='ant-tag ant-tag-has-color' style='color:#f5222d;'>{value}</div>";
    const HTML_ORANGE_TEXT = "<div class='ant-tag ant-tag-has-color' style='color:#fa8c16;'>{value}</div>";
    const HTML_GREEN_TEXT  = "<div class='ant-tag ant-tag-has-color' style='color:#52c41a;'>{value}</div>";
    const HTML_CYAN_TEXT   = "<div class='ant-tag ant-tag-has-color' style='color:#13c2c2;'>{value}</div>";
    const HTML_BLUE_TEXT   = "<div class='ant-tag ant-tag-has-color' style='color:#1890ff;'>{value}</div>";
    const HTML_PURPLE_TEXT = "<div class='ant-tag ant-tag-has-color' style='color:#722ed1;'>{value}</div>";
    const HTML_GRAY_TEXT   = "<div class='ant-tag ant-tag-has-color' style='color:#d6d6d6;'>{value}</div>";

    const TEXT_PINK   = "<span style='color:#eb2f96;'>{value}</span>";
    const TEXT_RED    = "<span style='color:#f5222d;'>{value}</span>";
    const TEXT_ORANGE = "<span style='color:#fa8c16;'>{value}</span>";
    const TEXT_GREEN  = "<span style='color:#52c41a;'>{value}</span>";
    const TEXT_CYAN   = "<span style='color:#13c2c2;'>{value}</span>";
    const TEXT_BLUE   = "<span style='color:#1890ff;'>{value}</span>";
    const TEXT_PURPLE = "<span style='color:#722ed1;'>{value}</span>";
    const TEXT_GRAY   = "<span style='color:#d6d6d6;'>{value}</span>";
}
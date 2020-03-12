<?php

namespace Leon\BswBundle\Component;

use ZipArchive;
use Leon\BswBundle\Module\Entity\Abs;
use BadFunctionCallException;
use InvalidArgumentException;
use Exception;

class Helper
{
    /**
     * Regex for variable
     *arrayMap
     *
     * @param string $add
     *
     * @return string
     */
    public static function reg4var(string $add = null): string
    {
        return "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff{$add}]+)\}/";
    }

    /**
     * Special for char
     *
     * @param string $add
     *
     * @return string
     */
    public static function special4char(string $add = null): string
    {
        return "`-=[];'\,.//~!@#$%^&*()_+{}:\"|<>?·【】；’、，。、！￥…（）—：“《》？{$add}";
    }

    /**
     * Dict for decimal
     *
     * @param string $add
     *
     * @return string
     */
    public static function dict4dec(string $add = null): string
    {
        return "AWi{$add}2QFN3VqUC4xPDazgXEOut1feMLdTbHK9sZrRJv5j7pcy8SkmYl60oBwIGnh";
    }

    /**
     * String length
     *
     * @param string $content
     * @param string $encoding
     *
     * @return int
     */
    public static function strLen(?string $content, string $encoding = 'utf-8'): int
    {
        return mb_strlen($content, $encoding);
    }

    /**
     * Singleton
     *
     * @param callable $logicHandler
     * @param array    $params
     *
     * @return mixed
     */
    public static function singleton(callable $logicHandler, array $params = null)
    {
        static $container = [];

        if (is_null($params)) {
            $params = self::backtrace(1, ['function', 'args']);
        }

        $key = md5(json_encode($params));

        if (!array_key_exists($key, $container)) {
            $container[$key] = call_user_func_array($logicHandler, [$params]);
        }

        return $container[$key];
    }

    /**
     * Dump for object, array, string
     *
     * @param mixed  $var
     * @param bool   $exit
     * @param bool   $strict
     * @param bool   $echo
     * @param string $tag
     *
     * @return mixed
     */
    public static function dump($var, bool $exit = true, bool $strict = false, bool $echo = true, string $tag = 'pre')
    {
        $startTag = $tag ? "<{$tag}>" : null;
        $endTag = $tag ? "</{$tag}>" : null;

        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = $startTag . htmlspecialchars($output, ENT_QUOTES) . $endTag;
            } else {
                $output = $startTag . print_r($var, true) . $endTag;
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = $startTag . htmlspecialchars($output, ENT_QUOTES) . $endTag;
            }
        }

        if (!$echo) {
            return $output;
        }

        echo($output);
        $exit && exit();

        return null;
    }

    /**
     * Number format for money
     *
     * @param mixed  $number
     * @param string $tpl
     *
     * @return string
     */
    public static function money($number, string $tpl = '￥%s'): string
    {
        return sprintf($tpl, Helper::numberFormat($number, 2));
    }

    /**
     * Array map for handle items
     *
     * @param array           $target
     * @param string|callable $handler
     *
     * @return array
     */
    public static function arrayMap(array $target, $handler): array
    {
        foreach ($target as &$item) {
            if (is_callable($handler)) {
                $item = $handler($item);
            } else {
                if (!is_scalar($item)) {
                    continue;
                }
                $item = sprintf($handler, $item);
            }
        }

        return $target;
    }

    /**
     * Array map for handle items
     *
     * @param array  $target
     * @param string $tpl
     *
     * @return array
     */
    public static function arrayMapDouble(array $target, string $tpl): array
    {
        foreach ($target as &$item) {
            if (is_scalar($item)) {
                $item = sprintf($tpl, $item, $item);
            }
        }

        return $target;
    }

    /**
     * Array map key for handle items
     *
     * @param array           $target
     * @param string|callable $handler
     *
     * @return array
     */
    public static function arrayMapKey(array $target, $handler): array
    {
        $_target = [];
        foreach ($target as $key => $item) {
            if (is_callable($handler)) {
                $key = $handler($key);
            } else {
                $key = sprintf($handler, $key);
            }
            $_target[$key] = $item;
        }

        return $_target;
    }

    /**
     * Pull multiple work for more-dimensional array
     *
     * @param array $target
     * @param array $pull
     * @param bool  $popTarget
     * @param mixed $default
     *
     * @return array
     */
    public static function arrayPull(array &$target, array $pull, bool $popTarget = false, $default = null): array
    {
        $_target = [];
        foreach ($pull as $oldKey => $newKey) {
            $oldKey = is_numeric($oldKey) ? $newKey : $oldKey;

            if (is_null($value = $target[$oldKey] ?? null) && is_null($default)) {
                if ($popTarget && array_key_exists($oldKey, $target)) {
                    unset($target[$oldKey]);
                }
                continue;
            }

            $_target[$newKey] = $value ?? $default;
            if ($popTarget) {
                unset($target[$oldKey]);
            }
        }

        return $_target;
    }

    /**
     * Pull one and unset it
     *
     * @param array  $target
     * @param string $key
     *
     * @return mixed|null
     */
    public static function dig(array &$target, string $key)
    {
        $item = $target[$key] ?? null;
        unset($target[$key]);

        return $item;
    }

    /**
     * Pop items and unset they
     *
     * @param array $target
     * @param array $items
     *
     * @return array
     */
    public static function arrayPop(array &$target, array $items): array
    {
        $value = [];
        foreach ($items as $item) {
            $value[$item] = self::dig($target, $item);
        }

        return $value;
    }

    /**
     * Remove items and unset they
     *
     * @param array $target
     * @param array $items
     *
     * @return array
     */
    public static function arrayRemove(array $target, array $items): array
    {
        foreach ($items as $item) {
            self::dig($target, $item);
        }

        return $target;
    }

    /**
     * Strengthen for array_column
     *
     * @param array  $target
     * @param mixed  $valueKeys
     * @param string $keyKey
     *
     * @return array
     */
    public static function arrayColumn(array $target, $valueKeys, string $keyKey = null): array
    {
        if (!is_null($keyKey) && is_string($valueKeys)) {
            return array_column($target, $valueKeys, $keyKey);
        }

        $_target = [];
        foreach ($target as $key => $item) {
            $_key = $keyKey ? ($item[$keyKey] ?? $key) : $key;
            if (is_string($valueKeys)) {
                $_target[$_key] = $item[$valueKeys] ?? null;
            } elseif (is_array($valueKeys)) {
                $_target[$_key] = self::arrayPull($item, $valueKeys);
            } elseif ($valueKeys === true) {
                $_target[$_key] = $item;
            } elseif ($valueKeys === false) {
                self::arrayPop($item, [$keyKey]);
                $_target[$_key] = $item;
            }
        }

        return $_target;
    }

    /**
     * Cover default with manual
     *
     * @param array $default
     * @param array $manual
     * @param bool  $obligateNull
     *
     * @return array
     */
    public static function manualCoverDefault(array $default, array $manual, bool $obligateNull = false): array
    {
        $real = array_intersect(array_keys($default), array_keys($manual));
        $real = self::arrayPull($manual, $real);

        if ($obligateNull) {
            return array_merge(self::arrayValuesSetTo($default, null), $real);
        }

        return $real;
    }

    /**
     * Function backtrace
     *
     * @param int    $index
     * @param mixed  $fields
     * @param string $key
     *
     * @return mixed
     */
    public static function backtrace(int $index = 1, $fields = true, string $key = null)
    {
        // exclude self
        $index += 1;

        $backTrance = debug_backtrace();
        if (!empty($fields)) {
            $backTrance = self::arrayColumn($backTrance, $fields, $key);
        }

        if ($index < 1) {
            return $backTrance;
        }

        return $backTrance[$index] ?? false;
    }

    /**
     * helloWorld to hello_world
     *
     * @param string $str
     * @param string $split
     *
     * @return string
     */
    public static function camelToUnder(string $str, string $split = '_'): string
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "{$split}\\0", $str), $split));
    }

    /**
     * hello_world to helloWorld
     *
     * @param string $str
     * @param bool   $small Camel of case
     * @param string $split
     *
     * @return string
     */
    public static function underToCamel(string $str, bool $small = true, string $split = '_'): string
    {
        $str = str_replace($split, self::enSpace(), $str);
        $str = ucwords($str);
        $str = str_replace(self::enSpace(), null, $str);

        return $small ? lcfirst($str) : $str;
    }

    /**
     * Array key helloWorld to hello_world
     *
     * @param array  $source
     * @param string $split
     *
     * @return array
     */
    public static function keyCamelToUnder(array $source, string $split = '_'): array
    {
        $_source = [];
        foreach ($source as $key => $value) {
            $_source[self::camelToUnder($key, $split)] = $value;
        }

        return $_source;
    }

    /**
     * Array value helloWorld to hello_world
     *
     * @param array  $source
     * @param string $split
     *
     * @return array
     */
    public static function valueCamelToUnder(array $source, string $split = '_'): array
    {
        foreach ($source as $key => $value) {
            $source[$key] = self::camelToUnder($value, $split);
        }

        return $source;
    }

    /**
     * Array key hello_world to helloWorld
     *
     * @param array  $source
     * @param bool   $small Camel of case
     * @param string $split
     *
     * @return array
     */
    public static function keyUnderToCamel(array $source, bool $small = true, string $split = '_'): array
    {
        $_source = [];
        foreach ($source as $key => $value) {
            $_source[self::underToCamel($key, $small, $split)] = $value;
        }

        return $_source;
    }

    /**
     * Array value hello_world to helloWorld
     *
     * @param array  $source
     * @param bool   $small Camel of case
     * @param string $split
     *
     * @return array
     */
    public static function valueUnderToCamel(array $source, bool $small = true, string $split = '_'): array
    {
        foreach ($source as $key => $value) {
            $source[$key] = self::underToCamel($value, $small, $split);
        }

        return $source;
    }

    /**
     * GBK double byte
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gbkDoubleByte(string $str): bool
    {
        return preg_match('/[\x{00}-\x{ff}]/u', $str) > 0;
    }

    /**
     * GBK ASCII
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gbkAscii(string $str): bool
    {
        return preg_match('/[\x{20}-\x{7f}]/u', $str) > 0;
    }

    /**
     * GB2312 chinese
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gb2312Chinese(string $str): bool
    {
        return preg_match('/[\x{a1}-\x{ff}]/u', $str) > 0;
    }

    /**
     * GBK chinese
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gbkChinese(string $str): bool
    {
        return preg_match('/[\x{80}-\x{ff}]/u', $str) > 0;
    }

    /**
     * UTF8 chinese
     *
     * @param string $str
     *
     * @return bool
     */
    public static function utf8Chinese(string $str): bool
    {
        return preg_match('/[\x{4e00}-\x{9fa5}]/u', $str) > 0;
    }

    /**
     * hello_world to Hello world
     *
     * @param string $str
     * @param string $replace
     *
     * @return string
     */
    public static function stringToLabel(string $str, string $replace = '-_'): string
    {
        if (self::utf8Chinese($str)) {
            return $str;
        }

        $str = self::camelToUnder($str, $replace[0] ?? ' ');
        $str = str_replace(self::split($replace), self::enSpace(), $str);
        $str = ucfirst(strtolower($str));

        return $str;
    }

    /**
     * Get namespace without class name
     *
     * @param string $namespace
     * @param string $replace
     *
     * @return string
     */
    public static function nsName(string $namespace, string $replace = null): string
    {
        $cls = explode('\\', $namespace);
        array_pop($cls);

        return str_replace($replace, null, implode('\\', $cls));
    }

    /**
     * Get class name without namespace
     *
     * @param string $namespace
     * @param string $replace
     *
     * @return string
     */
    public static function clsName(string $namespace, string $replace = null): string
    {
        $cls = explode('\\', $namespace);

        return str_replace($replace, null, end($cls));
    }

    /**
     * Merge items to object
     *
     * @param array ...$items
     *
     * @return mixed
     */
    public static function objects(...$items)
    {
        foreach ($items as &$item) {
            $item = (array)$item;
        }

        return (object)array_merge(...$items);
    }

    /**
     * Directory iterator
     *
     * @param string    $path
     * @param array    &$tree
     * @param callable  $fileCall
     * @param callable  $dirCall
     *
     * @return void
     */
    public static function directoryIterator(
        string $path,
        array &$tree,
        callable $fileCall = null,
        callable $dirCall = null
    ) {
        if (!is_dir($path) || !($handler = opendir($path))) {
            return;
        }

        while (false !== ($item = readdir($handler))) {

            if ($item == '.' || $item == '..') {
                continue;
            }

            $filePath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($filePath)) {

                $result = $filePath;
                is_callable($dirCall) && $result = call_user_func_array($dirCall, [$filePath, $item, $path]);

                if (!$result) {
                    continue;
                }

                if (is_array($result) && isset($result['key']) && isset($result['value'])) {
                    $item = $result['key'];
                    $result = $result['value'];
                }

                $tree[$item] = $tree[$item] ?? [];
                self::directoryIterator($result, $tree[$item], $fileCall, $dirCall);

            } else {

                $result = $filePath;
                is_callable($fileCall) && $result = call_user_func_array($fileCall, [$filePath, $item, $path]);

                if (!$result) {
                    continue;
                }

                if (is_array($result) && isset($result['key']) && isset($result['value'])) {
                    $tree[$result['key']] = $result['value'];
                } elseif ($result) {
                    $tree[] = $result;
                }
            }
        }

        closedir($handler);
    }

    /**
     * Zip directory
     *
     * @param string $directory
     * @param string $zipFilePath
     *
     * @return mixed
     */
    public static function archiveDirectory(string $directory, string $zipFilePath = null)
    {
        $zip = new ZipArchive();

        $dirInfo = pathinfo($directory);
        $zipFilePath = $zipFilePath ?: $dirInfo['dirname'] . DIRECTORY_SEPARATOR . $dirInfo['basename'] . '.zip';

        if (true !== $zip->open($zipFilePath, ZipArchive::CREATE)) {
            return 'create zip file failed';
        }

        if (is_array($directory)) {
            foreach ($directory as $localName => $file) {
                $zip->addFile($file, is_numeric($localName) ? null : $localName);
            }
        } else {
            $tree = [];
            self::directoryIterator(
                $directory,
                $tree,
                function ($file) use ($directory, $zip) {
                    $localName = str_replace($directory, null, $file);
                    $localName = DIRECTORY_SEPARATOR . ltrim($localName, DIRECTORY_SEPARATOR);
                    $zip->addFile($file, $localName);
                }
            );
        }

        return $zip->close();
    }

    /**
     * Get the tree by array
     *
     * @param array  $items
     * @param string $id
     * @param string $pid
     * @param string $subName
     *
     * @return array
     */
    public static function tree(array $items, string $id = 'id', string $pid = 'pid', string $subName = 'sub'): array
    {
        if (empty($items)) {
            return [];
        }

        $items = self::arrayColumn($items, true, $id);
        $tree = [];
        foreach ($items as $item) {
            if (!empty($items[$item[$pid]])) {
                $items[$item[$pid]][$subName][] = &$items[$item[$id]];
            } else {
                $tree[] = &$items[$item[$id]];
            }
        }

        return $tree;
    }

    /**
     * Merges more arrays into one recursively
     *
     * @param array ...$items
     *
     * @return array
     * @license modify from yii2framework
     */
    public static function merge(...$items): array
    {
        $target = array_shift($items);

        if (empty($items)) {
            return $target;
        }

        foreach ($items as $next) {
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $target)) {
                        $target[] = $v;
                    } else {
                        $target[$k] = $v;
                    }
                } elseif (is_array($v) && isset($target[$k]) && is_array($target[$k])) {
                    $target[$k] = self::merge($target[$k], $v);
                } else {
                    $target[$k] = $v;
                }
            }
        }

        return $target;
    }

    /**
     * Strong like self::merge
     *
     * @param bool  $assocOnly
     * @param bool  $transpose
     * @param bool  $lowerNull
     * @param array ...$items
     *
     * @return array
     */
    public static function merge2(bool $assocOnly, bool $transpose, bool $lowerNull = true, ...$items): array
    {
        $target = array_shift($items);

        if (empty($items)) {
            return $target;
        }

        foreach ($items as $next) {
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $target)) {
                        $target[] = $v;
                    } else {
                        $target[$k] = $v;
                    }
                } elseif (is_array($v) && isset($target[$k]) && is_array($target[$k])) {
                    if ($assocOnly && Helper::typeofArray($v, Abs::T_ARRAY_INDEX)) {
                        $target[$k] = $v;
                    } else {
                        $_items = [$target[$k], $v];
                        $transpose && $_items = array_reverse($_items);
                        $target[$k] = self::merge2($assocOnly, $transpose, $lowerNull, ...$_items);
                    }
                } else {
                    if (!$lowerNull || ($lowerNull && !is_null($v))) {
                        $target[$k] = $v;
                    }
                }
            }
        }

        return $target;
    }

    /**
     * Merge the more-dimensional
     *
     * @param array $target
     * @param array $from
     *
     * @return array
     */
    public static function mergeTheSecond(array $target, array $from): array
    {
        foreach ($from as $key => $item) {
            $targetItem = $target[$key] ?? [];
            $targetItem = $targetItem ? ((array)$targetItem) : [];

            if (is_array($item)) {
                $target[$key] = array_merge($targetItem, $item);
            } else {
                $target[$key] = $item;
            }
        }

        return $target;
    }

    /**
     * Print json code with format
     *
     * @param mixed $json
     *
     * @return string
     */
    public static function formatPrintJson($json): string
    {
        if (is_array($json)) {
            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
        }

        $result = null;
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = self::enSpace(1, true);
        $newLine = PHP_EOL;
        $prevChar = null;
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);
            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else {
                if (($char == '}' || $char == ']') && $outOfQuotes) {
                    $result .= $newLine;
                    $pos--;
                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }
            }
            // Add the character to the result string.
            $result .= $char;
            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            $prevChar = $char;
        }

        return $result;
    }

    /**
     * Get current url
     *
     * @return string
     */
    public static function currentUrl(): string
    {
        $scheme = 'http';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            $scheme = 'https';
        }

        $url = "{$scheme}://{$_SERVER['HTTP_HOST']}";
        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }

    /**
     * Parse url to items
     *
     * @param string $url
     *
     * @return array
     */
    public static function getUrlItems(string $url = null): array
    {
        $url = $url ?: self::currentUrl();
        $items = parse_url($url);
        $items['port'] = $items['port'] ?? '80';

        $port = $items['port'] == '80' ? null : ":{$items['port']}";
        $path = $items['path'] ?? null;

        $items['base_url'] = "{$items['scheme']}://{$items['host']}{$port}{$path}";

        $items['params'] = [];
        if (isset($items['query'])) {
            parse_str($items['query'], $items['params']);
        }

        return $items;
    }

    /**
     * Add params for url
     *
     * @param array  $setParams
     * @param string $url
     *
     * @return string
     */
    public static function addParamsForUrl(array $setParams, string $url = null): string
    {
        $url = $url ?: self::currentUrl();
        $items = self::getUrlItems($url);

        $setParams = self::merge($items['params'], $setParams);
        $url = trim("{$items['base_url']}?" . http_build_query($setParams), '?');

        return $url;
    }

    /**
     * Strip the param of the url
     *
     * @param array  $unsetParams
     * @param string $url
     *
     * @return string
     */
    public static function unsetParamsForUrl(array $unsetParams, string $url = null): string
    {
        $url = $url ?: self::currentUrl();
        $items = self::getUrlItems($url);

        foreach ($unsetParams as $val) {
            unset($items['params'][$val]);
        }

        $url = trim("{$items['base_url']}?" . http_build_query($items['params']), '?');

        return $url;
    }

    /**
     * Build url query
     *
     * @param array  $params
     * @param string $url
     *
     * @return null|string
     */
    public static function httpBuildQuery(array $params = null, string $url = null)
    {
        if (empty($params)) {
            return $url;
        }

        $query = http_build_query($params);
        $url .= strpos($url, '?') === false ? "?{$query}" : "&{$query}";

        return trim($url, '&?');
    }

    /**
     * Build url query in order
     *
     * @param array  $params
     * @param string $url
     *
     * @return null|string
     */
    public static function httpBuildQueryOrderly(array $params = null, string $url = null)
    {
        if (empty($params)) {
            return $url;
        }

        $query = null;
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $query .= rtrim($query, '&') . $value;
            } else {
                $query .= ("{$key}={$value}&");
            }
        }

        $query = rtrim($query, '&');
        $url .= (strpos($url, '?') !== false) ? "&{$query}" : "?{$query}";

        return trim($url, '&?');
    }

    /**
     * cURL
     *
     * @param string   $url
     * @param string   $type
     * @param array    $params
     * @param callable $optionHandler
     * @param string   $contentType
     * @param bool     $async
     *
     * @return mixed
     * @throws
     */
    public static function cURL(
        string $url,
        string $type = Abs::REQ_GET,
        array $params = null,
        callable $optionHandler = null,
        string $contentType = Abs::CONTENT_TYPE_FORM,
        bool $async = false
    ) {
        $options = [];

        // https
        if (strpos($url, 'https') === 0) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        // enabled sync
        if ($async) {
            $options[CURLOPT_NOSIGNAL] = true;
            $options[CURLOPT_TIMEOUT_MS] = 100;
        }

        // enabled show header
        $options[CURLOPT_HEADER] = false;

        if ($type === Abs::REQ_HEAD) {
            $options[CURLOPT_NOBODY] = true;
            $options[CURLOPT_HEADER] = true;
        }

        // enabled auto show return info
        $options[CURLOPT_RETURNTRANSFER] = true;

        // connect
        $options[CURLOPT_FRESH_CONNECT] = true;
        $options[CURLOPT_FORBID_REUSE] = true;

        // method
        $options[CURLOPT_CUSTOMREQUEST] = $type;

        // url
        if ($type === Abs::REQ_GET) {
            $options[CURLOPT_URL] = call_user_func_array([self::class, 'httpBuildQuery'], [$params, $url]);
        } else {
            $options[CURLOPT_URL] = $url;
        }

        if ($contentType) {
            $options[CURLOPT_HTTPHEADER] = ["Content-Type: {$contentType}"];
        }

        // use method POST
        if (strtoupper($type === Abs::REQ_POST)) {
            $options[CURLOPT_POST] = true;
            if (!empty($params)) {
                if ($contentType == Abs::CONTENT_TYPE_FORM) {
                    $options[CURLOPT_POSTFIELDS] = http_build_query($params);
                } elseif ($contentType == Abs::CONTENT_TYPE_JSON) {
                    $options[CURLOPT_POSTFIELDS] = json_encode($params);
                } else {
                    $options[CURLOPT_POSTFIELDS] = $params;
                }
            }
        }

        // init
        $curl = curl_init();

        // callback
        if ($optionHandler) {
            $options = call_user_func_array($optionHandler, [$options]);
        }

        curl_setopt_array($curl, $options);
        $content = curl_exec($curl);

        if ($content === false) {
            throw new Exception("cURL error for url {$url} with " . curl_error($curl));
        }

        return $content;
    }

    /**
     * New item difference to old
     *
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    public static function newDifferenceOld(array $old, array $new)
    {
        $intersect = array_intersect($new, $old);
        $add = array_diff($new, $intersect);
        $del = array_diff($old, $intersect);

        return [
            $add,
            $del,
        ];
    }

    /**
     * Create Sign
     *
     * @param array  $param
     * @param string $signKey
     *
     * @return array
     */
    public static function createSign(array $param, string $signKey = 'api_sign'): array
    {
        $param = http_build_query($param);
        parse_str($param, $params);
        ksort($params);

        $params[$signKey] = strtoupper(sha1(self::strReverse(md5(json_encode($param)))));

        return $params;
    }

    /**
     * Validation Sign
     *
     * @param array  $param
     * @param string $signKey
     *
     * @return bool
     */
    public static function validateSign(array $param, string $signKey = 'api_sign'): bool
    {
        if (empty($param[$signKey])) {
            return false;
        }

        $_sign = self::dig($param, $signKey);
        $sign = self::createSign($param, $signKey);

        return strcmp($sign[$signKey], $_sign) === 0;
    }

    /**
     * Signature
     *
     * @param array  $args
     * @param string $salt
     * @param int    $time
     *
     * @return array
     */
    public static function signature(array $args, string $salt, int $time = null): array
    {
        $time = $time ?? time();

        $args['time'] = $time;
        krsort($args);

        $sign = [];
        foreach ($args as $key => $value) {
            array_push($sign, "{$key} is {$value}");
        }

        $sign = implode(' and ', $sign) . " & {$salt}";
        $sign = strtolower(md5($sign));

        return compact('time', 'sign');
    }

    /**
     * Transformation image to base64
     *
     * @param string $filePath
     *
     * @return string
     */
    public static function imageToBase64(string $filePath): string
    {
        $image = fread(fopen($filePath, 'r'), filesize($filePath));

        $mime = getimagesize($filePath)['mime'];
        $base64 = chunk_split(base64_encode($image));

        return "data:{$mime};base64,{$base64}";
    }

    /**
     * Save base64 to image
     *
     * @param string $base64
     * @param string $filePath
     *
     * @return mixed
     */
    public static function base64ToImage(string $base64, string $filePath)
    {
        $base64 = preg_replace('/^(data:\s*image\/(\w+);base64,)/', null, $base64);
        $base64 = base64_decode($base64);

        return file_put_contents($filePath, $base64);
    }

    /**
     * Cal the size of the thumb
     *
     * @param int $thumbW
     * @param int $thumbH
     * @param int $originalW
     * @param int $originalH
     *
     * @return array
     */
    public static function calThumb(int $thumbW, int $thumbH, int $originalW, int $originalH): array
    {
        $thumbRadio = $thumbW / $thumbH;
        $imgRadio = $originalW / $originalH;

        $left = $top = 0;

        if ($thumbRadio > $imgRadio) {
            $height = $thumbH;
            $width = $originalW * ($thumbH / $originalH);
            $left = ($thumbW - $width) / 2;
        } else {
            $width = $thumbW;
            $height = $originalH * ($thumbW / $originalW);
            $top = ($thumbH - $height) / 2;
        }

        return array_map('intval', compact('width', 'height', 'left', 'top'));
    }

    /**
     * Get the suffix
     *
     * @param string $filename
     * @param bool   $point
     *
     * @return string
     */
    public static function getSuffix(string $filename, bool $point = false): string
    {
        $filename = parse_url($filename, PHP_URL_PATH);
        $suffix = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return $point ? ($suffix ? ".{$suffix}" : '') : $suffix;
    }

    /**
     * Rename file
     *
     * @param string $source
     * @param mixed  $name
     *
     * @return false|string
     */
    public static function renameFile(string $source, $name)
    {
        if (strpos($name, '/') !== false) {
            return rename($source, $name) ? $name : false;
        }

        $item = pathinfo($source);
        $name = is_int($name) ? str_pad($name, 3, 0, STR_PAD_LEFT) : $name;
        $name = "{$item['dirname']}/{$name}" . ($item['extension'] ? ".{$item['extension']}" : '');

        return rename($source, $name) ? $name : false;
    }

    /**
     * Handler comma string to array
     *
     * @param string $string
     * @param bool   $unique
     * @param bool   $filter
     * @param string $handler
     * @param string $separator
     * @param string $search
     *
     * @return array
     */
    public static function stringToArray(
        string $string,
        bool $unique = true,
        bool $filter = true,
        ?string $handler = 'trim',
        string $separator = ',',
        string $search = '，'
    ): array {

        $full = '０１２３４５６７８９ＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ－　：．，／％＃！＠＆（）＜＞＂＇？［］｛｝＼｜＋＝＿＾￥￣｀';
        $half = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz- :.,/%#!@&()<>"\'?[]{}\\|+=_^￥~`';

        $search = $search ?? self::split($full);
        $separator = $separator ?? str_split($half);

        $string = str_replace($search, $separator, $string);

        $result = explode($separator, $string);
        $result = $unique ? array_unique($result) : $result;
        $result = $filter ? array_filter($result) : $result;

        if ($handler && function_exists($handler)) {
            return array_map($handler, $result);
        }

        return $result;
    }

    /**
     * String pad to left to fixed length
     *
     * @param string $target
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function strPadLeftLength(string $target, int $length, string $pad = '0'): string
    {
        $target = substr($target, -$length);
        $target = str_pad($target, $length, $pad, STR_PAD_LEFT);

        return $target;
    }

    /**
     * String pad to right to fixed length
     *
     * @param string $target
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function strPadRightLength(string $target, int $length, string $pad = '0'): string
    {
        $target = substr($target, -$length);
        $target = str_pad($target, $length, $pad, STR_PAD_RIGHT);

        return $target;
    }

    /**
     * Generate number multiple
     *
     * @param int $begin
     * @param int $end
     * @param int $limit
     *
     * @return array
     */
    public static function generateNumberMultiple(int $begin, int $end, int $limit): array
    {
        $randArr = range($begin, $end);
        shuffle($randArr);

        return array_slice($randArr, 0, $limit);
    }

    /**
     * Create a uuid
     *
     * @param string $hyphen
     *
     * @return string
     */
    public static function gUid($hyphen = null)
    {
        $id = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = $hyphen ?: chr(45);

        return substr($id, 5, 3)
            . substr($id, 0, 5) . $hyphen
            . substr($id, 10, 2)
            . substr($id, 8, 2) . $hyphen
            . substr($id, 14, 2)
            . substr($id, 12, 2) . $hyphen
            . substr($id, 16, 4) . $hyphen
            . substr($id, 20, 12);
    }

    /**
     * Generate order number
     *
     * @param int $platform
     * @param int $method
     * @param int $uid
     *
     * @return string
     */
    public static function generateOrderNumber(int $platform, int $method, int $uid): string
    {
        return self::strPadLeftLength($platform, 1)
            . self::strPadLeftLength($method, 2)
            . self::strPadLeftLength($uid, 5)
            . self::strPadLeftLength(self::milliTime(true), 3)
            . date('Hd')
            . self::strPadLeftLength(rand(0, 99999), 5);
    }

    /**
     * Generate unique
     *
     * @param string $custom
     *
     * @return string
     */
    public static function generateUnique(string $custom = null): string
    {
        return uniqid(getmypid() . $custom . mt_rand(), true);
    }

    /**
     * Generate ticket - digit 18
     *
     * @param string $channel
     * @param int    $uid
     * @param string $custom
     *
     * @return string
     */
    public static function generateTicket(string $channel, int $uid, string $custom = null): string
    {
        $uuid = self::generateUnique($custom);

        return self::strPadLeftLength($channel, 2)
            . substr($uuid, -5)
            . self::strPadLeftLength(strrev($uid), 7)
            . substr($uuid, 1, 4);
    }

    /**
     * Generate token
     *
     * @param int    $fromBase
     * @param int    $toBase
     * @param string $custom
     *
     * @return string
     */
    public static function generateToken(int $fromBase = 18, int $toBase = 36, string $custom = null): string
    {
        return base_convert(self::generateUnique($custom), $fromBase, $toBase);
    }

    /**
     * Generate token fixed digit
     *
     * @param int    $length
     * @param string $custom
     *
     * @return string
     */
    public static function generateFixedToken(int $length = 18, string $custom = null): string
    {
        $token = strrev(self::generateToken(18, 36, $custom));
        $token = substr($token, 0, $length);

        return $token;
    }

    /**
     * Generates an unique access token.
     *
     * @param int $length
     *
     * @return string
     * @throws
     */
    public static function generateAccessToken(int $length = 40): string
    {
        $half = $length / 2;

        if (function_exists('random_bytes')) {
            $randomData = random_bytes($half);
            if ($randomData !== false && strlen($randomData) === $half) {
                return bin2hex($randomData);
            }
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes($half);
            if ($randomData !== false && strlen($randomData) === $half) {
                return bin2hex($randomData);
            }
        }

        // Last resort which you probably should just get rid of:
        $randomData = null
            . mt_rand()
            . microtime(true)
            . mt_rand()
            . uniqid(mt_rand(), true)
            . mt_rand();

        return substr(hash('sha512', $randomData), 0, $length);
    }

    /**
     * Generate token multiple
     *
     * @param int    $digit
     * @param int    $total
     * @param string $custom
     *
     * @return array
     */
    public static function generateTokenMultiple(int $digit, int $total, string $custom = null): array
    {
        $count = 0;
        $box = [];

        if ($digit > 26) {
            return $box;
        }

        while ($count < $total) {
            $code = self::generateToken(18, 36, $custom);
            $code = strtoupper(substr($code, 0, $digit));

            if (strlen($code) == $digit && !isset($box[$code])) {
                $box[$code] = true;
                $count++;
            }
        }

        return array_keys($box);
    }

    /**
     * Handler first and last
     *
     * @param array  $target
     * @param string $firstKey
     * @param string $lastKey
     */
    public static function sendToBothEnds(array &$target, string $firstKey = null, string $lastKey = null)
    {
        if (!empty($firstKey) && isset($target[$firstKey])) {
            $first = $target[$firstKey] ?? null;
            unset($target[$firstKey]);
            $target = array_merge([$firstKey => $first], $target);
        }

        if (!empty($lastKey) && isset($target[$lastKey])) {
            $last = $target[$lastKey] ?? null;
            unset($target[$lastKey]);
            $target = array_merge($target, [$lastKey => $last]);
        }
    }

    /**
     * Multiple to one-dimensional
     *
     * @param array $items
     *
     * @return array
     */
    public static function multipleToOne(array $items): array
    {
        $arr = [];
        foreach ($items as $key => $val) {
            if (is_array($val)) {
                $arr = array_merge($arr, self::multipleToOne($val));
            } else {
                $arr[] = $val;
            }
        }

        return $arr;
    }

    /**
     * Is int numeric
     *
     * @param mixed $num
     *
     * @return bool
     */
    public static function isIntNumeric($num): bool
    {
        return is_numeric($num) && intval($num) == $num;
    }

    /**
     * Is float numeric
     *
     * @param mixed $num
     *
     * @return bool
     */
    public static function isFloatNumeric($num): bool
    {
        return is_numeric($num) && !self::isIntNumeric($num);
    }

    /**
     * Numeric value
     *
     * @param mixed $num
     *
     * @return float|int
     */
    public static function numericValue($num)
    {
        if (!is_numeric($num)) {
            return $num;
        }

        if (self::isIntNumeric($num)) {
            return intval($num);
        }

        if (self::isNumberBetween($num, PHP_INT_MIN, PHP_INT_MAX)) {
            return floatval($num);
        }

        return $num;
    }

    /**
     * Numeric values
     *
     * @param array $params
     *
     * @return array
     */
    public static function numericValues(array $params): array
    {
        foreach ($params as &$value) {
            $value = self::numericValue($value);
        }

        return $params;
    }

    /**
     * Is mobile
     *
     * @return bool
     */
    public static function isMobile(): bool
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';

        if (preg_match(
            '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i',
            strtolower($_SERVER['HTTP_USER_AGENT'] ?? null)
        )) {
            $mobile_browser++;
        }

        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(
                    strtolower($_SERVER['HTTP_ACCEPT'] ?? null),
                    'application/vnd.wap.xhtml+xml'
                ) !== false)) {
            $mobile_browser++;
        }

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }

        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['ALL_HTTP'] ?? null), 'operamini') !== false) {
            $mobile_browser++;
        }

        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT'] ?? null), 'windows') !== false) {
            $mobile_browser = 0;
        }

        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT'] ?? null), 'windows phone') !== false) {
            $mobile_browser++;
        }

        return $mobile_browser > 0;
    }

    /**
     * Add tag for table field
     *
     * @param string $field
     *
     * @return string
     */
    public static function tableFieldAddTag(string $field): string
    {
        if (false !== strpos($field, '`')) {
            return $field;
        }

        if (false !== strpos($field, '.')) {
            $field = str_replace('.', '`.`', $field);
        }

        return "`{$field}`";
    }

    /**
     * Add alias for table field
     *
     * @param string $field
     * @param string $alias
     *
     * @return string
     */
    public static function tableFieldAddAlias(string $field, string $alias): string
    {
        return strpos($field, '.') === false ? "{$alias}.{$field}" : trim($field, '.');
    }

    /**
     * Table name to alias
     *
     * @param string $table
     *
     * @return string
     */
    public static function tableNameToAlias(string $table): string
    {
        $table = Helper::clsName($table);
        $words = explode('_', self::camelToUnder($table));

        $alias = '';
        foreach ($words as $word) {
            $alias .= $word[0];
        }

        return $alias;
    }

    /**
     * Table name to alias
     *
     * @param string $table
     *
     * @return null|string
     */
    public static function tableNameFromCls(string $table): string
    {
        return self::camelToUnder(Helper::clsName($table));
    }

    /**
     * Remove alias for table field
     *
     * @param string $field
     * @param string $asKeyWord
     *
     * @return string
     */
    public static function tableFieldDelAlias(string $field, string $asKeyWord = ' AS '): string
    {
        $field = str_replace('`', null, $field);
        if (false !== strpos($field, '.')) {
            $field = explode('.', $field)[1];
        }

        if (false !== strpos($field, $asKeyWord)) {
            $field = explode($asKeyWord, $field)[1];
        }

        $asKeyWord = strtolower($asKeyWord);
        if (false !== strpos($field, $asKeyWord)) {
            $field = explode($asKeyWord, $field)[1];
        }

        return trim($field);
    }

    /**
     * Millisecond
     *
     * @param bool $decimal
     *
     * @return int
     */
    public static function milliTime($decimal = false): int
    {
        $milli = intval(microtime(true) * 1000);

        return $decimal ? $milli % 1000 : $milli;
    }

    /**
     * Microsecond
     *
     * @param bool $decimal
     *
     * @return int
     */
    public static function microTime($decimal = false): int
    {
        [$micro, $second] = explode(self::enSpace(), microtime());
        $micro *= 1000000;

        if ($decimal) {
            return $micro;
        }

        return intval("{$second}{$micro}");
    }

    /**
     * Log runtime cost
     *
     * @param string $scene
     * @param string $tpl
     *
     * @return array
     */
    public static function cost(string $scene, string $tpl = null): array
    {
        static $sceneFirst;
        static $scenePrevious = 'init';
        static $costHistory = [];

        if (!isset($sceneFirst)) {
            $sceneFirst = $scene;
        }

        $costCurrent = $costHistory[$scene] = Helper::milliTime();
        $costPrevious = $costHistory[$scenePrevious] ?? $costCurrent;
        $costFirst = $sceneFirst ? $costHistory[$sceneFirst] : $costCurrent;

        $scenePrevious = $scene;

        $costMilli = $costCurrent - $costFirst;
        $chunkCostMilli = $costCurrent - $costPrevious;
        $chunkCostString = chunk_split($costCurrent, 10, '.');

        $tpl = $tpl ?: '-->> {second} (cost: {cost}) in {scene}';
        $tpl = str_replace(
            ['{scene}', '{second}', '{cost}'],
            [$scene, $chunkCostString, $chunkCostMilli],
            $tpl
        );

        return [$tpl, $costMilli];
    }

    /**
     * Get the micro time
     *
     * @param string $format
     * @param float  $timestamp
     *
     * @return false|string
     */
    public static function date($format = Abs::FMT_MIC, $timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = microtime(true);
        }

        $time = floor($timestamp);
        $micro = round(($timestamp - $time) * 1000);
        $micro = str_pad($micro, 3, 0, STR_PAD_LEFT);

        $format = str_replace('u', $micro, $format);

        return date($format, $time);
    }

    /**
     * Get the minute begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampMinute($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_MINUTES . ':00', $timestamp);
        $begin = strtotime($date);

        return [
            $begin,
            $begin + Abs::TIME_MINUTE - 1,
        ];
    }

    /**
     * Get the hour begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampHour($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_HOUR . ':00:00', $timestamp);
        $begin = strtotime($date);

        return [
            $begin,
            $begin + Abs::TIME_HOUR - 1,
        ];
    }

    /**
     * Get the day begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampDay($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_DAY, $timestamp);
        $begin = strtotime($date);

        return [
            $begin,
            $begin + Abs::TIME_DAY - 1,
        ];
    }

    /**
     * Get the week begin and end day
     *
     * @param string $date
     *
     * @return array
     */
    public static function dateWeek($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_WEEK, $timestamp);
        [$Y, $m, $d, $w] = explode('-', $date);

        return [
            "{$Y}-{$m}-" . ($d - $w + 1),
            "{$Y}-{$m}-" . ($d - $w + 7),
        ];
    }

    /**
     * Get the week begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampWeek($date = null)
    {
        [$head, $tail] = self::dateWeek($date);

        return [
            strtotime("{$head} " . Abs::DAY_BEGIN),
            strtotime("{$tail} " . Abs::DAY_END),
        ];
    }

    /**
     * Get the month begin and end day
     *
     * @param string $date
     *
     * @return array
     */
    public static function dateMonth($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_MONTH_LAST_DAY, $timestamp);
        [$Y, $m, $t] = explode('-', $date);

        return [
            "{$Y}-{$m}-01",
            "{$Y}-{$m}-{$t}",
        ];
    }

    /**
     * Get the month begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampMonth($date = null)
    {
        [$head, $tail] = self::dateMonth($date);

        return [
            strtotime("{$head} " . Abs::DAY_BEGIN),
            strtotime("{$tail} " . Abs::DAY_END),
        ];
    }

    /**
     * Get the quarter begin and end day
     *
     * @param string $date
     *
     * @return array
     */
    public static function dateQuarter($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_MONTH, $timestamp);
        [$Y, $m] = explode('-', $date);

        $season = ceil((date('n', strtotime($date))) / 3);

        $headMonth = $season * 3 - 3 + 1;
        $tailMonth = $season * 3;
        $tailDay = date('t', strtotime("{$Y}-{$m}-01"));

        return [
            "{$Y}-{$headMonth}-01",
            "{$Y}-{$tailMonth}-{$tailDay}",
        ];
    }

    /**
     * Get the quarter begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampQuarter($date = null)
    {
        [$head, $tail] = self::dateQuarter($date);

        return [
            strtotime("{$head} " . Abs::DAY_BEGIN),
            strtotime("{$tail} " . Abs::DAY_END),
        ];
    }

    /**
     * Get the year begin and end day
     *
     * @param string $date
     *
     * @return array
     */
    public static function dateYear($date = null)
    {
        $timestamp = $date ? strtotime($date) : time();
        $Y = date(Abs::FMT_YEAR_ONLY, $timestamp);

        return [
            "{$Y}-01-01",
            "{$Y}-12-31",
        ];
    }

    /**
     * Get the year begin and end timestamp
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampYear($date = null)
    {
        [$head, $tail] = self::dateYear($date);

        return [
            strtotime("{$head} " . Abs::DAY_BEGIN),
            strtotime("{$tail} " . Abs::DAY_END),
        ];
    }

    /**
     * Get before N begin and end day
     *
     * @param int    $n
     * @param string $date
     *
     * @return array
     */
    public static function dateBeforeN(int $n, $date = null)
    {
        $n -= 1;
        $timestamp = $date ? strtotime($date) : time();

        $to = date(Abs::FMT_DAY, $timestamp);
        $from = date(Abs::FMT_DAY, strtotime("-{$n} days", $timestamp));

        return [
            $from,
            $to,
        ];
    }

    /**
     * Get after N begin and end day
     *
     * @param int    $n
     * @param string $date
     *
     * @return array
     */
    public static function dateAfterN(int $n, $date = null)
    {
        $n -= 1;
        $timestamp = $date ? strtotime($date) : time();

        $from = date(Abs::FMT_DAY, $timestamp);
        $to = date(Abs::FMT_DAY, strtotime("+{$n} days", $timestamp));

        return [
            $from,
            $to,
        ];
    }

    /**
     * Check type for callable
     *
     * @param mixed  $data
     * @param mixed  $type
     * @param string $info
     *
     * @return void
     * @throws
     */
    public static function callReturnType($data, $type, string $info = null)
    {
        $type = (array)$type;
        $dataType = strtolower(gettype($data));
        $info = $info ?: 'the callback';

        if (!in_array($dataType, $type)) {
            $type = implode('/', $type);
            throw new BadFunctionCallException("{$info} should return `{$type}` but got `{$dataType}`");
        }
    }

    /**
     * Check type for class
     *
     * @param object $object
     * @param string $class
     * @param string $info
     *
     * @return void
     * @throws
     */
    public static function objectInstanceOf($object, string $class, string $info = null)
    {
        $info = $info ?: 'the class';

        if (!is_object($object)) {
            $type = gettype($object);
            throw new BadFunctionCallException("{$info} should be instance of `{$class}` but got `{$type}`");
        }

        if (!$object instanceof $class) {
            $nowClass = get_class($object);
            throw new BadFunctionCallException("{$info} should be instance of `{$class}` but got `{$nowClass}`");
        }
    }

    /**
     * Perfect date key for array
     *
     * @param array  $list
     * @param string $from
     * @param string $to
     * @param mixed  $default
     * @param string $format
     * @param int    $step
     * @param bool   $sort
     *
     * @return array
     * @throws
     */
    public static function perfectDateKeys(
        array $list,
        string $from,
        string $to,
        $default = 0,
        string $format = Abs::FMT_DAY,
        int $step = Abs::TIME_DAY,
        bool $sort = true
    ) {
        if (!($from = strtotime($from)) || !($to = strtotime($to))) {
            throw new InvalidArgumentException('Param `from` and `to` must be date string');
        }

        if ($from >= $to) {
            [$from, $to] = [$to, $from];
        }

        $dayTime = $from;
        while ($dayTime <= $to) {
            $day = date($format, $dayTime);
            if (!isset($list[$day])) {
                $list[$day] = $default;
            }
            $dayTime += $step;
        }

        $sort && ksort($list);

        return $list;
    }

    /**
     * Perfect int key for array
     *
     * @param array $list
     * @param int   $from
     * @param int   $to
     * @param mixed $default
     * @param int   $step
     * @param bool  $sort
     *
     * @return array
     * @throws
     */
    public static function perfectIntKeys(
        array $list,
        int $from,
        int $to,
        $default = 0,
        int $step = 1,
        bool $sort = true
    ) {
        if ($from >= $to) {
            throw new InvalidArgumentException('Param `from` must less than `to`');
        }

        $counter = $from;
        while ($counter <= $to) {
            if (!isset($list[$counter])) {
                $list[$counter] = $default;
            }
            $counter += $step;
        }

        $sort && ksort($list);

        return $list;
    }

    /**
     * Recursion cut string
     *
     * @param string       $string
     * @param array|string $rule
     * @param string       $splitBy
     *
     * @return string
     * @example :
     *          string: $url = http://www.w3school.com.cn/php/func_array_slice.asp
     *          one: get the `func`
     *          $result = $obj->cutString($url, ['/^0^desc', '_^0']);
     *          two: get the `asp`
     *          $result = $obj->cutString($url, '.^0^desc');
     */
    public static function cutString(string $string, $rule, string $splitBy = '^'): string
    {
        foreach ((array)$rule as $val) {
            $detail = explode($splitBy, $val);
            $string = explode($detail[0], $string);
            if (!empty($detail[2]) && strtolower($detail[2]) == 'desc') {
                $key = count($string) - $detail[1] - 1;
                $string = $string[$key] ?? false;
            } else {
                $string = $string[$detail[1]] ?? false;
            }
            if ($string === false) {
                break;
            }
        }

        return $string;
    }

    /**
     * Trim pos when begin
     *
     * @param string $content
     * @param string $pos
     *
     * @return bool|string
     */
    public static function strPosBeginTrim(string $content, string $pos)
    {
        if (strpos($content, $pos) !== 0) {
            return false;
        }

        return substr($content, self::strLen($pos));
    }

    /**
     * Replace pos when begin
     *
     * @param array $contentMap
     * @param array $posMap
     *
     * @return array
     */
    public static function strPosBeginReplace(array $contentMap, array $posMap): array
    {
        foreach ($contentMap as &$content) {
            foreach ($posMap as $pos => $prefix) {
                if (strpos($content, $pos) === 0) {
                    $content = sprintf($prefix, substr($content, self::strLen($pos)));
                    break;
                }
            }
        }

        return $contentMap;
    }

    /**
     * Get color value
     *
     * @param bool $well
     *
     * @return string
     */
    public static function colorValue(bool $well = false): string
    {
        $colors = [];
        for ($i = 0; $i < 6; $i++) {
            $colors[] = dechex(rand(0, 15));
        }

        return ($well ? '#' : null) . implode('', $colors);
    }

    /**
     * Split the string with nil
     *
     * @param string $string
     *
     * @return array
     */
    public static function split(string $string): array
    {
        // if only utf-8 use str_split best.
        preg_match_all('/[\s\S]/u', $string, $array);

        return $array[0];
    }

    /**
     * Reverse string with chinese
     *
     * @param string $string
     *
     * @return string
     */
    public static function strReverse(string $string): string
    {
        return implode('', array_reverse(self::split($string)));
    }

    /**
     * Filter the special char
     *
     * @param string       $string
     * @param string|array $specialChar
     *
     * @return string
     */
    public static function filterSpecialChar(string $string, $specialChar = null): string
    {
        $specialChar = $specialChar ?: self::special4char();

        if (!is_array($specialChar)) {
            $specialChar = self::split($specialChar);
        }

        foreach ($specialChar as $char) {
            $string = str_replace($char, null, $string);
        }

        return $string;
    }

    /**
     * Sort for two-dimensional
     *
     * @param array  $arr
     * @param string $key
     * @param string $mode
     *
     * @return array
     */
    public static function sortArray($arr, $key, string $mode = Abs::SORT_ASC): array
    {
        $keysValue = $newArray = [];
        foreach ($arr as $k => $v) {
            $keysValue[$k] = $v[$key];
        }

        switch (ucwords($mode)) {
            case Abs::SORT_ASC :
                asort($keysValue);
                break;

            default :
                arsort($keysValue);
                break;
        }

        reset($keysValue);
        foreach ($keysValue as $k => $v) {
            $newArray[$k] = $arr[$k];
        }

        return $newArray;
    }

    /**
     * Sort array with count items
     *
     * @param array  $target
     * @param string $mode
     *
     * @return array
     */
    public static function sortArrayWithCount(array $target, string $mode = Abs::SORT_DESC): array
    {
        $assist = [];
        foreach ($target as $key => $item) {
            if (!is_array($item)) {
                return $target;
            }
            $assist[count($item)] = $key;
        }

        $newTarget = [];
        $mode = strtoupper($mode);
        $mode === Abs::SORT_DESC ? krsort($assist) : ksort($assist);

        foreach ($assist as $key) {
            $newTarget[$key] = $target[$key];
        }

        return $newTarget;
    }

    /**
     * Natural sort for array keys
     *
     * @param array $target
     *
     * @return array
     */
    public static function keyNaturalSort(array $target): array
    {
        $keys = array_keys($target);
        natsort($keys);

        return self::arrayPull($target, $keys);
    }

    /**
     * Appoint keys from array
     *
     * @param array $target
     * @param null  $appoint
     *
     * @return array|string|null
     */
    public static function arrayAppoint(array $target, $appoint = null)
    {
        if (is_null($appoint)) {
            return $target;
        }

        if (is_array($appoint)) {
            return self::arrayPull($target, $appoint);
        }

        return $target[$appoint] ?? null;
    }

    /**
     * Substring
     *
     * @param string $str
     * @param int    $start
     * @param int    $length
     * @param string $charset
     * @param string $suffix
     *
     * @return string
     */
    public static function mSubStr(
        string $str,
        int $start,
        int $length,
        string $charset = 'utf-8',
        string $suffix = '..'
    ): string {
        if (function_exists('mb_substr')) {
            $slice = mb_substr($str, $start, $length, $charset);
        } else {
            if (function_exists('iconv_substr')) {
                $slice = iconv_substr($str, $start, $length, $charset);
                if (false === $slice) {
                    $slice = null;
                }
            } else {
                $re['utf-8'] = '/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/';
                $re['gb2312'] = '/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/';
                $re['gbk'] = '/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/';
                $re['big5'] = '/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/';
                preg_match_all($re[$charset], $str, $match);
                $slice = join(null, array_slice($match[0], $start, $length));
            }
        }

        return !empty($suffix) ? $slice . $suffix : $slice;
    }

    /**
     * Get the rand string
     *
     * @param int    $len
     * @param string $type alphabet/number/upper-alphabet/lower-alphabet/mixed/captcha
     * @param string $addChars
     *
     * @return string
     */
    public static function randString(int $len = 6, string $type = 'captcha', string $addChars = null): string
    {
        $str = null;
        switch ($type) {
            case 'alphabet' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz{$addChars}";
                break;
            case 'number' :
                $chars = str_repeat('0123456789', 3);
                break;
            case 'upper-alphabet' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ{$addChars}";
                break;
            case 'lower-alphabet' :
                $chars = "abcdefghijklmnopqrstuvwxyz{$addChars}";
                break;
            case 'mixed' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789{$addChars}";
                break;
            default :
                // Remove alphabet `OLl` and number `01`
                $chars = "ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789{$addChars}";
                break;
        }

        if ($len > 10) {
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }

        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $len);
        } else {
            for ($i = 0; $i < $len; $i++) {
                $str .= self::mSubStr($chars, floor(mt_rand(0, self::strLen($chars) - 1)), 1, 'utf-8', false);
            }
        }

        return $str;
    }

    /**
     * Is we chat browser
     *
     * @return bool
     */
    public static function weChatBrowser(): bool
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    /**
     * Is ali pay browser
     *
     * @return bool
     */
    public static function aliPayBrowser(): bool
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        return strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false;
    }

    /**
     * Object to array
     *
     * @param mixed $obj
     *
     * @return array
     */
    public static function objectToArray($obj): array
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * Array to object
     *
     * @param $arr
     *
     * @return mixed
     */
    public static function arrayToObject($arr)
    {
        return json_decode(json_encode($arr));
    }

    /**
     * Object to array (only public and protected)
     *
     * @param mixed $entity
     *
     * @return array
     */
    public static function entityToArray($entity)
    {
        $attributes = [];
        foreach ((array)$entity as $key => $value) {
            $attributes[ltrim($key, Abs::ENTITY_KEY_TRIM)] = $value;
        }

        return $attributes;
    }

    /**
     * Get items by keys from array or object
     *
     * @param mixed        $target
     * @param array|string $keys
     *
     * @return array|string
     */
    public static function getItems($target, $keys)
    {
        if (is_object($target)) {
            $target = self::entityToArray($target);
        }

        if (!is_array($target)) {
            return null;
        }

        if (is_scalar($keys)) {
            return $target[$keys] ?? null;
        }

        return self::arrayPull($target, $keys);
    }

    /**
     * Set items to array or object
     *
     * @param mixed $target
     * @param array $source
     *
     * @return mixed
     */
    public static function setItems($target, array $source)
    {
        if (is_array($target)) {
            return array_merge($target, $source);
        }

        if (!is_object($target)) {
            return $target;
        }

        foreach ($source as $key => $val) {
            $target->{$key} = $val;
        }

        return $target;
    }

    /**
     * Unique for more-dimensional
     *
     * @param array $data
     *
     * @return array
     */
    public static function moreDimensionArrayUnique(array $data): array
    {
        $data = array_map('serialize', $data);
        $data = array_unique($data);
        $data = array_map('deSerialize', $data);

        return $data;
    }

    /**
     * Set key use value for two-dimensional
     *
     * @param array  $target
     * @param string $valueKey
     *
     * @return array
     */
    public static function setKeyUseValue(array $target, string $valueKey): array
    {
        $items = array_column($target, $valueKey);
        $target = array_combine($items, $target);

        return [
            $target,
            $items,
        ];
    }

    /**
     * String replace once only
     *
     * @param string $needle
     * @param string $replace
     * @param string $haystack
     *
     * @return string
     */
    public static function strReplaceOnce(string $needle, string $replace, string $haystack): string
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, self::strLen($needle));
    }

    /**
     * Get text width and height
     *
     * @param string $str
     * @param string $fonts
     * @param int    $size
     * @param float  $gap
     *
     * @return array
     */
    public static function textWidthPx(string $str, string $fonts, int $size = 14, float $gap = 1): array
    {
        $box = imagettfbbox($size, 0, $fonts, $str);

        $width = abs($box[4] - $box[0]);
        $height = abs($box[5] - $box[1]);

        return [
            $width * $gap,
            $height * $gap,
        ];
    }

    /**
     * Parse json string
     *
     * @param string $target
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function parseJsonString(string $target, $default = null)
    {
        if (empty($target) || !is_string($target)) {
            return $default ?? $target;
        }

        $result = json_decode($target, true);
        $result = $result ?? ($default ?? $target);

        return $result;
    }

    /**
     * Space for en
     *
     * @param int  $n
     * @param bool $tab
     *
     * @return string
     */
    public static function enSpace(int $n = 1, bool $tab = false): string
    {
        return str_repeat(' ', $tab ? $n * 4 : $n);
    }

    /**
     * Space for cn
     *
     * @param int  $n
     * @param bool $tab
     *
     * @return string
     */
    public static function cnSpace(int $n = 1, bool $tab = false): string
    {
        return str_repeat('　', $tab ? $n * 4 : $n);
    }

    /**
     * decimal to n
     *
     * @param int    $number
     * @param int    $add
     * @param string $dict
     *
     * @return string
     */
    public static function decimal2n(int $number, int $add = 10000024, string $dict = null): string
    {
        $number += $add;
        $dict = $dict ?: self::dict4dec();
        $dict = str_replace(self::enSpace(), null, $dict);

        $to = self::strLen($dict);
        $result = null;

        do {
            $result = $dict[bcmod($number, $to)] . $result;
            $number = bcdiv($number, $to);
        } while ($number > 0);

        return ltrim($result, '0');
    }

    /**
     * n to decimal
     *
     * @param string $number
     * @param int    $add
     * @param string $dict
     *
     * @return int
     */
    public static function n2decimal(string $number, int $add = 10000024, string $dict = null): int
    {
        $number = strval($number);
        $dict = $dict ?: self::dict4dec();
        $dict = str_replace(self::enSpace(), null, $dict);

        $from = self::strLen($dict);
        $len = self::strLen($number);

        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $number[$i]);
            $result = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $result);
        }

        return intval($result) - $add;
    }

    /**
     * Join items string by split
     *
     * @access public
     *
     * @param string $split
     * @param array  ...$items
     *
     * @return string
     */
    public static function joinString(string $split, ...$items): string
    {
        $total = count($items) - 1;
        $_items = [];

        foreach ($items as $key => $value) {

            if (is_array($value)) {
                $value = implode($split, $value);
            }

            if (empty($value)) {
                continue;
            }

            if ($key == 0) {
                $_items[] = rtrim($value, $split);
            } elseif ($key == $total - 1) {
                $_items[] = ltrim($value, $split);
            } else {
                $_items[] = trim($value, $split);
            }
        }

        return implode($split, $_items);
    }

    /**
     * Set array values
     *
     * @param array $target
     * @param mixed $value
     * @param bool  $isValue
     *
     * @return array
     */
    public static function arrayValuesSetTo(array $target, $value, bool $isValue = false): array
    {
        $key = $isValue ? array_values($target) : array_keys($target);
        $value = array_fill(0, count($key), $value);

        return array_combine($key, $value);
    }

    /**
     * Handler for sql items when in
     *
     * @param mixed $items
     *
     * @return array
     */
    public static function sqlInItems($items): array
    {
        if (!is_array($items)) {
            $items = self::stringToArray($items);
        }

        $bind = rtrim(str_repeat('?, ', count($items)), ', ');
        $items = array_values($items);

        return [$bind, $items];
    }

    /**
     * Handler for dql items when in
     *
     * @param mixed  $items
     * @param string $flag
     *
     * @return array
     */
    public static function dqlInItems($items, string $flag = ':'): array
    {
        if (!is_array($items)) {
            $items = self::stringToArray($items);
        }

        $bind = $args = [];
        $random = self::generateToken(8, 36);

        foreach ($items as $key => $val) {
            $name = "_{$key}_{$random}";
            array_push($bind, "{$flag}{$name}");
            $args[$name] = self::numericValue($val);
        }

        return [implode(', ', $bind), $args];
    }

    /**
     * Array length
     *
     * @param array  $target
     * @param bool   $valueMode
     * @param string $valueKey
     *
     * @return array
     */
    public static function arrayLength(array $target, bool $valueMode = false, string $valueKey = null): array
    {
        if (!$valueMode) {
            $array = array_keys($target);
        } elseif (is_array(current($target))) {
            $array = array_column($target, $valueKey);
        } else {
            $array = array_values($target);
        }

        return array_map(
            function ($v) {
                return self::strLen($v);
            },
            $array
        );
    }

    /**
     * Assert flag
     *
     * @param int $flags
     * @param int $flag
     *
     * @return bool
     */
    public static function bitFlagAssert(int $flags, int $flag): bool
    {
        return (($flags & $flag) == $flag);
    }

    /**
     * Edit flag
     *
     * @param int  $flags
     * @param int  $flag
     * @param bool $value
     *
     * @return int
     */
    public static function bitFlagEdit(int $flags, int $flag, bool $value): int
    {
        if ($value) {
            $flags |= $flag;
        } else {
            $flags &= ~$flag;
        }

        return $flags;
    }

    /**
     * Replace document with variables
     *
     * @param string $document
     * @param array  $variables
     * @param string $add
     *
     * @return string
     */
    public static function docVarReplace(string $document, array $variables, string $add = ':'): string
    {
        preg_match_all(self::reg4var($add), $document, $result);

        if (empty($result) || empty(current($result))) {
            return $document;
        }

        foreach ($result[1] as $var) {
            $document = str_replace("{{$var}}", $variables[$var] ?? null, $document);
        }

        return $document;
    }

    /**
     * Get variables from document
     *
     * @param string $document
     * @param string $add
     *
     * @return array
     */
    public static function docVarGet(string &$document, string $add = ':'): array
    {
        preg_match_all(self::reg4var($add), $document, $result);

        if (empty($result) || empty(current($result))) {
            return [];
        }

        $variables = [];
        foreach ($result[1] as $var) {
            if (strpos($var, $add) === false) {
                continue;
            }

            $result = explode($add, $var);
            $type = array_shift($result);
            $name = array_shift($result);

            $variables[$type][$name] = $result;
            $document = trim(str_replace("{{$var}}", null, $document));
        }

        return $variables;
    }

    /**
     * Int to bytes
     *
     * @param int  $number
     * @param bool $bigEndian
     *
     * @return array
     */
    public static function intToBytes(int $number, bool $bigEndian = false): array
    {
        $bytes = [];
        $bytes[0] = ($number & 0xff);
        $bytes[1] = ($number >> 8 & 0xff);
        $bytes[2] = ($number >> 16 & 0xff);
        $bytes[3] = ($number >> 24 & 0xff);

        return $bigEndian ? array_reverse($bytes) : $bytes;
    }

    /**
     * Bytes to int with position
     *
     * @param array $bytes
     * @param int   $position
     *
     * @return int
     */
    public static function bytesToInt(array $bytes, int $position): int
    {
        $val = $bytes[$position + 3] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 2] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 1] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position] & 0xff;

        return $val;
    }

    /**
     * Short string to bytes
     *
     * @param string $target
     *
     * @return array
     */
    public static function shortToBytes(string $target): array
    {
        $bytes = [];
        $bytes[0] = ($target & 0xff);
        $bytes[1] = ($target >> 8 & 0xff);

        return $bytes;
    }

    /**
     * Bytes to short string with position
     *
     * @param array $bytes
     * @param int   $position
     *
     * @return int
     */
    public static function bytesToShort(array $bytes, int $position)
    {
        $val = $bytes[$position + 1] & 0xff;
        $val = $val << 8;
        $val |= $bytes[$position] & 0xff;

        return $val;
    }

    /**
     * String to bytes
     *
     * @param string $target
     *
     * @return array
     */
    public static function stringToBytes(string $target): array
    {
        $len = self::strLen($target);
        $bytes = [];
        for ($i = 0; $i < $len; $i++) {
            if (ord($target[$i]) >= 128) {
                $byte = ord($target[$i]) - 256;
            } else {
                $byte = ord($target[$i]);
            }
            $bytes[] = $byte;
        }

        return $bytes;
    }

    /**
     * Bytes to string
     *
     * @param array $bytes
     *
     * @return string
     */
    public static function bytesToString(array $bytes): string
    {
        $string = null;
        foreach ($bytes as $ch) {
            $string .= chr($ch);
        }

        return $string;
    }

    /**
     * Insert array to array with position
     *
     * @param array $source
     * @param int   $position
     * @param array $insertArray
     *
     * @return array
     */
    public static function arrayInsert(array $source, int $position, array $insertArray): array
    {
        $beginPart = array_splice($source, 0, $position);

        return array_merge($beginPart, $insertArray, $source);
    }

    /**
     * Insert array to array with assoc
     *
     * @param array  $source
     * @param string $position
     * @param array  $insertArray
     * @param bool   $before
     *
     * @return array
     */
    public static function arrayInsertAssoc(
        array $source,
        string $position,
        array $insertArray,
        bool $before = false
    ): array {
        $offset = array_search($position, array_keys($source));
        if ($offset === false) {
            return array_merge($source, $insertArray);
        }

        if (!$offset) {
            return $before ? array_merge($insertArray, $source) : array_merge($source, $insertArray);
        }

        $beginPart = array_splice($source, 0, $before ? $offset : $offset + 1);

        return array_merge($beginPart, $insertArray, $source);
    }

    /**
     * Get client IP address
     *
     * @param int  $type 0:IP 1:IPv4
     * @param bool $adv  Advance mode
     *
     * @return mixed
     */
    public static function getClientIp($type = 0, bool $adv = false)
    {
        static $ip = null;
        $type = $type ? 1 : 0;

        if ($ip !== null) {
            return $ip[$type];
        }

        $get = function ($ip) use ($type) {

            if (empty($ip)) {
                return null;
            }

            // Check ip address
            $long = sprintf('%u', ip2long($ip));
            $ip = $long ? [
                $ip,
                $long,
            ] : [
                '0.0.0.0',
                0,
            ];

            return $ip[$type];
        };

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);

                return $get($ip);
            }

            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            return $get($ip);
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];

            return $get($ip);
        }

        return null;
    }

    /**
     * N-dimension array to one
     *
     * @param array  $items
     * @param string $key
     * @param string $split
     *
     * @return array
     */
    public static function nDimension2one(array $items, string $key = null, string $split = '.'): array
    {
        $result = [];
        foreach ($items as $_key => $item) {
            $_key = ($key ? $key . $split : null) . $_key;
            if (!is_array($item)) {
                $result[$_key] = $item;
            } else {
                $result = array_merge($result, self::nDimension2one($item, $_key, $split));
            }
        }

        return $result;
    }

    /**
     * One-dimension array to n
     *
     * @param array  $items
     * @param string $split
     *
     * @return array
     */
    public static function oneDimension2n(array $items, string $split = '.'): array
    {
        $result = [];
        $build = function (&$target, $key, $value) use (&$build, $split) {
            if (empty($key)) {
                return null;
            }
            $_key = array_shift($key);
            if (!isset($target[$_key])) {
                $target[$_key] = empty($key) ? $value : [];
            }
            $build($target[$_key], $key, $value);
        };

        foreach ($items as $key => $item) {
            $key = explode($split, $key);
            $build($result, $key, $item);
        }

        return $result;
    }

    /**
     * Set/Get variable
     *
     * @param string $key
     * @param mixed  $value
     * @param string $split
     *
     * @return mixed
     * @throws
     */
    public static function variable(string $key, $value = null, string $split = '.')
    {
        static $variable = [];

        $keys = explode($split, $key);
        $len = count($keys);

        // for get
        if (!isset($value)) {

            $var = $variable;
            for ($i = 0; $i < $len; $i++) {
                $k = $keys[$i];
                if (!isset($keys[$i + 1])) {
                    $var = $var[$k] ?? null;
                    break;
                }
                $var = $var[$k] ?? [];
            }

            return $var;
        }

        $var = &$variable;

        // for set
        for ($i = 0; $i < $len; $i++) {

            $k = $keys[$i];
            if (!isset($keys[$i + 1])) {
                $var[$k] = $value;
                break;
            }

            if (!isset($var[$k])) {
                $var[$k] = [];
                $var = &$var[$k];
                continue;
            }

            if (!is_array($var[$k])) {
                return false;
            }

            $var = &$var[$k];
        }

        return true;
    }

    /**
     * String end with substring
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function strEndWith(string $haystack, string $needle): bool
    {
        $len = strlen($needle);
        $end = substr($haystack, strlen($haystack) - $len, $len);

        return $end == $needle;
    }

    /**
     * Print array
     *
     * @param array  $source
     * @param string $boundary
     * @param string $indicate
     * @param string $split
     * @param string $highOrder
     *
     * @return string
     */
    public static function printArray(
        array $source,
        string $boundary = '[%s]',
        string $indicate = ':',
        string $split = ',',
        string $highOrder = null
    ): string {

        $print = [];
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $value = $highOrder ?: self::printArray($value, $boundary, $indicate, $split, $highOrder);
            }
            $key = $indicate ? "{$key}{$indicate}" : null;
            array_push($print, "{$key}{$value}");
        }

        $print = implode($split, $print);
        if (!$boundary) {
            return $print;
        }

        return sprintf($boundary, $print);
    }

    /**
     * Recursion value handler
     *
     * @param array    $source
     * @param callable $handler
     * @param array    $ignoreKeys
     *
     * @return array
     */
    public static function recursionValueHandler(array $source, callable $handler, array $ignoreKeys = []): array
    {
        foreach ($source as $key => &$value) {

            if ($ignoreKeys && in_array($key, $ignoreKeys)) {
                continue;
            }

            if (is_scalar($value)) {
                $value = call_user_func_array($handler, [$value]);
            } else {
                $value = self::recursionValueHandler($value, $handler);
            }
        }

        return $source;
    }

    /**
     * Last item for array
     *
     * @param array &$source
     *
     * @return array
     */
    public static function arrayLastItem(array &$source): array
    {
        end($source);
        $item = [key($source), current($source)];
        reset($source);

        return $item;
    }

    /**
     * Check is url already
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isUrlAlready(string $str): bool
    {
        return strpos($str, 'http') === 0 || strpos($str, '//') === 0;
    }

    /**
     * Human times
     *
     * @param int $times
     *
     * @return string
     */
    public static function humanTimes(int $times): string
    {
        if ($times < 10000) {
            return (string)$times;
        } elseif ($times < 100000) {
            return self::numberFormat($times / 1000, 1, ',') . 'k';
        }

        return self::numberFormat($times / 10000, 1, ',') . 'w';
    }

    /**
     * Human size
     *
     * @param int $byte
     *
     * @return string
     */
    public static function humanSize(int $byte): string
    {
        $signed = $byte < 0 ? '-' : null;
        $byte = abs($byte);

        $map = [
            'TB' => 4,
            'GB' => 3,
            'MB' => 2,
            'KB' => 1,
            'B'  => 0,
        ];

        foreach ($map as $unit => $power) {
            $size = 1024 ** $power;
            if ($byte >= $size) {
                $byte = self::numberFormat($byte / $size, 1, ',');

                return "{$signed}{$byte}{$unit}";
            }
        }

        return "{$signed}{$byte}B";
    }

    /**
     * Secret string
     *
     * @param string $content
     * @param string $secret
     * @param array  $map
     *
     * @return string
     */
    public static function secretString(string $content, string $secret = '*', array $map = []): string
    {
        $content = self::split(trim($content));
        $length = count($content);

        $begin = $map[$length][0] ?? null;
        $end = $map[$length][1] ?? null;

        if ($length === 0) {
            $content = [$secret, $secret];
        } elseif ($length === 1) {
            $content = [$content[0], $secret];
        } elseif ($length === 2) {
            $content = [$content[0], $secret, $content[1]];
        } elseif ($length <= 5) {
            $repeat = array_fill(0, 1, $secret);
            array_splice($content, $begin ?? 1, -($end ?? 1), $repeat);
        } elseif ($length < 10) {
            $repeat = array_fill(0, 2, $secret);
            array_splice($content, $begin ?? 2, -($end ?? 2), $repeat);
        } else {
            $repeat = array_fill(0, 3, $secret);
            array_splice($content, $begin ?? 3, -($end ?? 3), $repeat);
        }

        return implode('', $content);
    }

    /**
     * Get args for pagination
     *
     * @param array $args
     * @param int   $pageSize
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function pageArgs(array $args, int $pageSize = 20): array
    {
        extract($args);

        $paging = $paging ?? false;
        if (!is_bool($paging)) {
            throw new InvalidArgumentException('Variable `paging` should be boolean');
        }

        $page = $page ?? 1;
        if (!is_int($page) || $page < 1) {
            throw new InvalidArgumentException('Variable `page` should be integer and gte 1');
        }

        $limit = $limit ?? $pageSize;
        if (!is_int($limit) || $limit < 0) {
            throw new InvalidArgumentException('Variable `limit` should be integer and gte 0');
        }

        $offset = ($page - 1) * $limit;

        return compact('paging', 'page', 'limit', 'offset');
    }

    /**
     * Is extend class
     *
     * @param mixed  $target
     * @param string $className
     * @param bool   $allowSelf
     *
     * @return bool
     */
    public static function extendClass($target, string $className, bool $allowSelf = false): bool
    {
        $subClassName = is_object($target) ? get_class($target) : $target;

        if (!class_exists($subClassName)) {
            return false;
        }

        if ($allowSelf && $subClassName == $className) {
            return true;
        }

        if (is_subclass_of($subClassName, $className)) {
            return true;
        }

        return false;
    }

    /**
     * Create annotation object string
     *
     * @param array $options
     * @param bool  $inner
     * @param bool  $boundary
     *
     * @return string
     */
    public static function annotationJsonString(array $options, bool $inner = false, bool $boundary = true): string
    {
        $items = [];
        foreach ($options as $key => $value) {

            if ($inner) {
                $key = is_numeric($key) ? $key : "\"{$key}\"";
            }

            $eq = $inner ? ':' : '=';

            if (is_bool($value)) {
                array_push($items, "{$key}{$eq}" . ($value ? 'true' : 'false'));
            } elseif (is_numeric($value)) {
                array_push($items, "{$key}{$eq}{$value}");
            } elseif (is_string($value) && strpos($value, '::')) {
                array_push($items, "{$key}{$eq}{$value}");
            } elseif (is_scalar($value)) {
                array_push($items, "{$key}{$eq}\"{$value}\"");
            } elseif (is_array($value)) {
                array_push($items, "{$key}{$eq}" . self::annotationJsonString($value, true));
            }
        }

        $stringify = implode(', ', $items);
        if ($boundary) {
            return "{{$stringify}}";
        }

        return $stringify;
    }

    /**
     * Print php array to string
     *
     * @param array $item
     *
     * @return string
     */
    public static function printPhpArray(array $item): string
    {
        $stringify = var_export($item, true);

        $patterns = [
            "/NULL/"                                       => 'null',
            "/\'([\\\a-zA-Z0-9]+)(::)([\\\a-zA-Z0-9]+)\'/" => '$1$2$3',
        ];

        $stringify = preg_replace(array_keys($patterns), array_values($patterns), $stringify);

        $stringify = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $stringify);
        $stringify = preg_split("/\r\n|\n|\r/", $stringify);
        $stringify = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $stringify);
        $stringify = join(PHP_EOL, array_filter(["["] + $stringify));

        return $stringify;
    }

    /**
     * Typeof array
     *
     * @param array  $target
     * @param string $exceptType
     *
     * @return string|bool
     */
    public static function typeofArray(array $target, string $exceptType = null)
    {

        $count = count($target);
        $same = array_intersect_key($target, range(0, $count - 1));

        if (count($same) == $count) {
            $type = Abs::T_ARRAY_INDEX;
        } elseif (empty($same)) {
            $type = Abs::T_ARRAY_ASSOC;
        } else {
            $type = Abs::T_ARRAY_MIXED;
        }

        return isset($exceptType) ? ($type === $exceptType) : $type;
    }

    /**
     * Calculation earth spherical distance
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     *
     * @return int
     */
    public static function calEarthSphericalDistance(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        /**
         * Approximate radius of earth in meters
         */

        $earthRadius = 6367000;

        /**
         * Convert these degrees to radians to work with the formula
         */

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        /**
         * Using the Haversine formula calculate the distance
         *
         * @see http://en.wikipedia.org/wiki/Haversine_formula
         */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return intval($calculatedDistance);
    }

    /**
     * Data handler for group by date and os
     *
     * @param array    $groupByDateAndOsList
     * @param array    $map
     * @param array    $osMap
     * @param string   $fromDay
     * @param string   $toDay
     * @param callable $totalHandler
     *
     * @return array
     */
    public static function groupByDateAndOsDataHandler(
        array $groupByDateAndOsList,
        array $map,
        array $osMap,
        string $fromDay,
        string $toDay,
        callable $totalHandler = null
    ): array {

        $map = array_merge(
            [
                'date'  => 'date',
                'os'    => 'os',
                'total' => ['default' => 'total'],
            ],
            $map
        );

        $deviceOsData = [];
        $full = $osMap[Abs::OS_FULL];

        foreach ($osMap as $os => $osInfo) {
            foreach ($map['total'] as $line => $cnt) {
                $deviceOsData[$line][$osMap[$os]] = [];
            }
        }

        foreach ($groupByDateAndOsList as $item) {

            $os = $item[$map['os']];
            $date = $item[$map['date']];

            foreach ($map['total'] as $line => $cnt) {

                $total = floatval($item[$cnt]);
                $total = $totalHandler ? $totalHandler($total, $cnt) : $total;

                if (!isset($deviceOsData[$line][$full][$date])) {
                    $deviceOsData[$line][$full][$date] = 0;
                }

                $deviceOsData[$line][$full][$date] += $total;
                $deviceOsData[$line][$osMap[$os]][$date] = $total;
            }
        }

        $title = [];
        $_deviceOsData = [];

        foreach ($deviceOsData as $line => &$items) {
            foreach ($items as $key => &$item) {
                if ($fromDay !== $toDay) {
                    $item = self::perfectDateKeys($item, $fromDay, $toDay);
                }
                if ($key == $full) {
                    $title = array_keys($item);
                }
                $_deviceOsData[$line][$key] = array_values($item);
            }
        }

        return [$title, $_deviceOsData, $deviceOsData];
    }

    /**
     * Data handler for group by os
     *
     * @param array    $groupByOsList
     * @param array    $map
     * @param array    $osMap
     * @param callable $totalHandler
     *
     * @return array
     */
    public static function groupByOsDataHandler(
        array $groupByOsList,
        array $map,
        array $osMap,
        callable $totalHandler = null
    ): array {

        $map = array_merge(
            [
                'os'    => 'os',
                'total' => ['default' => 'total'],
            ],
            $map
        );

        $deviceOsData = [];
        $full = $osMap[Abs::OS_FULL];

        foreach ($osMap as $os => $osInfo) {
            foreach ($map['total'] as $line => $cnt) {
                $deviceOsData[$line][$osMap[$os]] = 0;
            }
        }

        foreach ($groupByOsList as $item) {

            $os = $item[$map['os']];
            foreach ($map['total'] as $line => $cnt) {

                $total = floatval($item[$cnt]);
                $total = $totalHandler ? $totalHandler($total, $cnt) : $total;

                if (!isset($deviceOsData[$line][$full])) {
                    $deviceOsData[$line][$full] = 0;
                }

                $deviceOsData[$line][$full] += $total;
                $deviceOsData[$line][$osMap[$os]] = $total;
            }
        }

        return $deviceOsData;
    }

    /**
     * Number between min and max
     *
     * @param mixed $number
     * @param float $min
     * @param float $max
     *
     * @return mixed
     */
    public static function numberBetween($number, float $min, float $max)
    {
        if (!is_numeric($number)) {
            return $min;
        }

        $number = min($max, $number);
        $number = max($min, $number);

        return $number;
    }

    /**
     * Is number between min and max
     *
     * @param mixed $number
     * @param float $min
     * @param float $max
     *
     * @return bool
     */
    public static function isNumberBetween($number, float $min, float $max): bool
    {
        if (!is_numeric($number)) {
            return false;
        }

        return (($number >= $min) && ($number <= $max));
    }

    /**
     * Number format
     *
     * @param number $number
     * @param int    $decimals
     * @param string $thousandsSep
     *
     * @return float|string
     */
    public static function numberFormat($number, int $decimals = 2, string $thousandsSep = '')
    {
        $number = number_format($number, $decimals, '.', $thousandsSep);
        $number = $thousandsSep ? $number : floatval($number);

        return $number;
    }

    /**
     * @param string ...$datetime
     *
     * @return array
     */
    public static function boundaryDateTime(string ...$datetime): array
    {
        $datetime = array_map('strtotime', $datetime);
        asort($datetime);

        $min = date(Abs::FMT_FULL, current($datetime));
        $max = date(Abs::FMT_FULL, end($datetime));

        return [$min, $max];
    }
}
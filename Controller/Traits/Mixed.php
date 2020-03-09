<?php

namespace Leon\BswBundle\Controller\Traits;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Leon\BswBundle\Component\AwsSDK;
use Leon\BswBundle\Component\Download;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\IpRegionDB;
use Leon\BswBundle\Component\IpRegionDAT;
use Leon\BswBundle\Component\IpRegionIPDB;
use Leon\BswBundle\Component\UploadItem;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDevice;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
use Leon\BswBundle\Module\Error\Entity\ErrorOS;
use Leon\BswBundle\Module\Error\Entity\ErrorThirdService;
use Leon\BswBundle\Module\Error\Entity\ErrorUA;
use Leon\BswBundle\Module\Exception\FileNotExistsException;
use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Gregwar\Captcha\CaptchaBuilder;
use ParagonIE\ConstantTime\Base32;
use Qcloud\Sms\SmsSingleSender;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use GeoIp2\Database\Reader;
use Monolog\Logger;
use OSS\Core\OssException;
use OSS\OssClient;
use App\Kernel;
use OTPHP\TOTP;
use Exception;
use Mailgun\Mailgun;
use Telegram\Bot\Api;

/**
 * @property AbstractController  $container
 * @property Kernel              $kernel
 * @property Session             $session
 * @property Logger              $logger
 * @property TranslatorInterface $translator
 */
trait Mixed
{
    /**
     * Valid device args
     *
     * @param int $type
     *
     * @return true|Response
     * @throws
     */
    protected function validDevice(int $type = Abs::VD_ALL)
    {
        if (Helper::bitFlagAssert($type, Abs::VD_OS) && empty($this->header->os)) {
            return $this->failed(new ErrorOS());
        }

        if (Helper::bitFlagAssert($type, Abs::VD_UA) && empty($this->header->ua)) {
            return $this->failed(new ErrorUA());
        }

        if (Helper::bitFlagAssert($type, Abs::VD_DEVICE) && empty($this->header->device)) {
            return $this->failed(new ErrorDevice());
        }

        return true;
    }

    /**
     * Create qr code
     *
     * @param string $content
     * @param int    $qrWidth
     * @param string $level
     * @param int    $qrMargin
     * @param array  $fColor
     * @param array  $bColor
     * @param string $logoFile
     * @param int    $logoWidth
     *
     * @return QrCode
     * @throws
     */
    public function createQrCode(
        string $content,
        int $qrWidth = 250,
        int $qrMargin = 10,
        ?string $level = null,
        ?array $fColor = null,
        ?array $bColor = null,
        ?string $logoFile = null,
        ?int $logoWidth = null
    ) {

        $qrCode = new QrCode($content);
        $qrCode->setSize($qrWidth);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin($qrMargin);
        $qrCode->setEncoding('utf-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel($level ?: ErrorCorrectionLevel::MEDIUM));
        $qrCode->setForegroundColor($fColor ?: ['r' => 70, 'g' => 70, 'b' => 70]);
        $qrCode->setBackgroundColor($bColor ?: ['r' => 255, 'g' => 255, 'b' => 255]);

        if (!empty($logoFile) && realpath($logoFile)) {
            $qrCode->setLogoPath($logoFile);
            $logoWidth = $logoWidth ?: $qrWidth / 4;
            $qrCode->setLogoWidth($logoWidth);
        }

        $qrCode->setValidateResult(false);

        return $qrCode;
    }

    /**
     * Time-Based One-Time Password Algorithm (T.O.T.P)
     *
     * @param string $uid
     * @param int    $digits
     * @param int    $time
     *
     * @return array
     */
    public function TOTPToken(string $uid, int $digits = 6, int $time = null): array
    {
        $epoch = 0;
        $second = 120;
        $time = $time ?? $this->cnf->totp_token_second ?? $second;
        $time = $time ?: $second;

        $secret = Base32::encode($this->parameter('salt') . $uid);
        $token = TOTP::create($secret, $time, 'sha1', $digits, $epoch);

        $now = time();
        $timeCode = intval(($now - $epoch) / $time);
        $from = $timeCode * $time;
        $to = $from + $time;

        return [
            $token->at($now),
            date(Abs::FMT_FULL, $from),
            date(Abs::FMT_FULL, $to),
        ];
    }

    /**
     * Ali cloud for sms
     *
     * @param string $nationCode
     * @param string $phone
     * @param string $signature
     * @param string $tplCode
     * @param array  $args
     * @param string $app
     *
     * @return Response|bool
     * @throws
     */
    public function smsAli(
        string $nationCode,
        string $phone,
        string $signature,
        string $tplCode,
        array $args,
        string $app = null
    ) {

        $client = AlibabaCloud::accessKeyClient(
            $this->parameterInOrderByEmpty(["ali_sms_key{$app}", 'ali_key']),
            $this->parameterInOrderByEmpty(["ali_sms_secret{$app}", 'ali_secret'])
        );

        $client->asDefaultClient();
        $client->regionId($this->parameter("ali_sms_region{$app}"));

        try {

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->timeout($this->cnf->curl_timeout_second)
                ->connectTimeout($this->cnf->curl_timeout_second)
                ->options(
                    [
                        'query' => [
                            'PhoneNumbers'  => "{$nationCode}{$phone}",
                            'SignName'      => $signature,
                            'TemplateCode'  => $tplCode,
                            'TemplateParam' => json_encode($args, JSON_UNESCAPED_UNICODE),
                        ],
                    ]
                )
                ->request()
                ->toArray();

            $message = $result['Message'];

            if ($message != 'OK') {
                $this->logger->error("Ali sms error, {$message}", $result);

                return $this->failed(new ErrorThirdService(), $message);
            }

            return true;

        } catch (ClientException|ServerException $e) {

            $message = $e->getErrorMessage();
            $this->logger->error("Ali sms exception, {$message}");

            return $this->failed(new ErrorException(), $message);
        }
    }

    /**
     * Tx cloud for sms
     *
     * @param string $nationCode
     * @param string $phone
     * @param string $content
     * @param string $app
     *
     * @return Response|bool
     * @throws
     */
    public function smsTx(string $nationCode, string $phone, string $content, string $app = null)
    {
        try {

            $sender = new SmsSingleSender(
                $this->parameterInOrderByEmpty(["tx_sms_key{$app}", 'tx_key']),
                $this->parameterInOrderByEmpty(["tx_sms_secret{$app}", 'tx_secret'])
            );

            $result = $sender->send(0, $nationCode, $phone, $content);
            $result = Helper::parseJsonString($result);

            $message = $result['errmsg'] ?? null;
            if ($message != 'OK') {
                $this->logger->error("Tx sms error, {$message}", $result);

                return $this->failed(new ErrorThirdService(), $message);
            }

            return true;

        } catch (Exception $e) {

            $message = $e->getMessage();
            $this->logger->error("Tx sms exception, {$message}");

            return $this->failed(new ErrorException(), $message);
        }
    }

    /**
     * Aws cloud for sms
     *
     * @param string $nationCode
     * @param string $phone
     * @param string $content
     * @param string $class
     *
     * @return Response|bool
     * @throws
     */
    public function smsAws(string $nationCode, string $phone, string $content, string $class = AwsSDK::class)
    {
        /**
         * @var AwsSDK $aws
         */
        $aws = $this->component($class);
        [$_, $error] = current($aws->smsSender(["{$nationCode}{$phone}"], $content));

        if ($error) {
            $this->logger->error("Aws sms error, {$error}");

            return $this->failed(new ErrorThirdService(), $error);
        }

        return true;
    }

    /**
     * Aws cloud for email
     *
     * @param string $email
     * @param string $title
     * @param string $content
     * @param string $class
     *
     * @return Response|bool
     * @throws
     */
    public function emailAws(string $email, string $title, string $content, string $class = AwsSDK::class)
    {
        /**
         * @var AwsSDK $aws
         */
        $aws = $this->component($class);

        [$_, $error] = $aws->mailSender([$email], $title, $content);

        if ($error) {
            $this->logger->error("Aws sns error, {$error}");

            return $this->failed(new ErrorThirdService(), $error);
        }

        return true;
    }

    /**
     * [Simple Mail Transfer Protocol] for email
     *
     * @param string $email
     * @param string $title
     * @param string $content
     *
     * @return Response|bool
     * @throws
     */
    public function emailSMTP(string $email, string $title, string $content)
    {
        $mail = new PHPMailer(true);

        try {

            $index = $this->parameter('smtp_index');
            $host = $this->parameters('smtp_host');
            $total = count($host);
            $index = ($index < 0 || $index >= $total) ? rand(0, $total - 1) : $index;

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $this->parameters('smtp_host')[$index];
            $mail->SMTPAuth = true;
            $mail->Username = $this->parameters('smtp_sender')[$index];
            $mail->Password = $this->parameters('smtp_secret')[$index];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $this->parameters('smtp_port')[$index];

            // recipients
            $mail->setFrom($this->parameters('smtp_sender')[$index]);
            $mail->addAddress($email);

            // content
            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body = $content;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->send();

            return true;

        } catch (Exception $e) {

            $error = "{$mail->ErrorInfo}, {$e->getMessage()}";
            $this->logger->error("SMTP email error, {$error}");

            return $this->failed(new ErrorThirdService(), 'Email send failed');
        }
    }

    /**
     * MailGun for email
     *
     * @param string $email
     * @param string $title
     * @param string $content
     *
     * @return Response|bool
     * @throws
     */
    public function emailGun(string $email, string $title, string $content)
    {
        $mg = Mailgun::create(
            $this->parameter('mail_gun_key'),
            $this->parameter('mail_gun_endpoint', 'https://api.mailgun.net')
        );

        try {
            $host = $this->parameter('mail_gun_host');
            $result = $mg->messages()->send(
                $host,
                [
                    'from'    => $this->parameter('mail_gun_from') . "@{$host}",
                    'to'      => $email,
                    'subject' => $title,
                    'html'    => $content,
                ]
            );
        } catch (Exception $e) {
            $this->logger->error("Mail-gun email error, {$e->getMessage()}");

            return $this->failed(new ErrorThirdService(), 'Email send failed');
        }

        return true;
    }

    /**
     * Get file path (in order)
     *
     * @param string $fileName
     * @param string $dirName
     * @param string $bundle
     *
     * @return string
     * @throws
     */
    public function getFilePathInOrder(string $fileName, string $dirName = 'mixed', string $bundle = 'LeonBswBundle')
    {
        $dirName = trim($dirName, '/');
        $path = $this->kernel->getProjectDir();
        $file = "{$path}/{$dirName}/{$fileName}";

        if (file_exists($file)) {
            return $file;
        }

        $path = $this->kernel->getBundle($bundle)->getPath();
        $file = "{$path}/Resources/{$dirName}/{$fileName}";

        if (file_exists($file)) {
            return $file;
        }

        throw new FileNotExistsException("{$file} is not found");
    }

    /**
     * Get file path
     *
     * @param string $fileName
     * @param string $dirName
     * @param string $bundle
     *
     * @return string
     * @throws
     */
    public function getFilePath(string $fileName, string $dirName = 'mixed', ?string $bundle = 'LeonBswBundle')
    {
        $dirName = trim($dirName, '/');

        if ($bundle) {
            $path = $this->kernel->getBundle($bundle)->getPath();
            $file = "{$path}/Resources/{$dirName}/{$fileName}";
        } else {
            $path = $this->kernel->getProjectDir();
            $file = "{$path}/{$dirName}/{$fileName}";
        }

        if (file_exists($file)) {
            return $file;
        }

        throw new FileNotExistsException("{$file} is not found");
    }

    /**
     * Ip to region with .db file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2regionDB(string $ip, string $filename = 'ip2region.free.db'): array
    {
        $file = $this->getFilePathInOrder($filename);
        $location = (new IpRegionDB($file))->btreeSearch($ip);
        [$country, $region, $province, $city, $isp] = array_fill(0, 5, '');

        if (!empty($location)) {
            // Country|Region|Province|City|ISP
            $location = $location['region'];
            [$country, $region, $province, $city, $isp] = explode('|', $location);
        }

        return compact('location', 'country', 'region', 'province', 'city', 'isp');
    }

    /**
     * Ip to region with .dat file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2regionDAT(string $ip, string $filename = 'ip2region.qqzeng.dat'): array
    {
        $file = $this->getFilePathInOrder($filename);
        $location = (new IpRegionDAT($file))->get($ip);
        [$country, $province, $city, $area, $isp, $id] = array_fill(0, 6, '');

        if ($location) {
            // Continents|Country|Province|City|Area|ISP|Zoning|EnCountry|Code|Longitude|Dimension
            [$_, $country, $province, $city, $area, $isp, $id] = explode('|', $location);
        }

        return compact('location', 'country', 'province', 'city', 'area', 'isp', 'id');
    }

    /**
     * Ip to region with .mmdb file
     *
     * @param string $ip
     * @param string $filename
     * @param string $lang
     *
     * @return array
     * @throws
     */
    public function ip2regionMMDB(
        string $ip,
        string $filename = 'ip2region.maxmind.mmdb',
        string $lang = 'zh-CN'
    ): array {

        $file = $this->getFilePathInOrder($filename);
        [$location, $country, $province, $city] = array_fill(0, 4, '');

        try {
            $reader = new Reader($file);
            $record = $reader->city($ip);
        } catch (Exception $e) {
            return compact('location', 'country', 'province', 'city');
        }

        $country = $record->country->names[$lang] ?? '';
        $province = $record->mostSpecificSubdivision->names[$lang] ?? '';
        $city = $record->city->names[$lang] ?? '';
        $location = "{$country}|{$province}|{$city}";

        return compact('location', 'country', 'province', 'city');
    }

    /**
     * Ip to region with .ipdb file
     *
     * @param string $ip
     * @param string $filename
     * @param string $lang
     *
     * @return array
     * @throws
     */
    public function ip2regionIPDB(
        string $ip,
        string $filename = 'ip2region.ipip.ipdb',
        string $lang = 'CN'
    ): array {

        $file = $this->getFilePathInOrder($filename);
        [$location, $country, $province, $city] = array_fill(0, 4, '');

        try {
            $location = (new IpRegionIPDB($file))->findMap($ip, $lang);
        } catch (Exception $e) {
            return compact('location', 'country', 'province', 'city');
        }

        $country = $location['country_name'] ?? '';
        $province = $location['region_name'] ?? '';
        $city = $location['city_name'] ?? '';

        $location = "{$country}|{$province}|{$city}";

        return compact('location', 'country', 'province', 'city');
    }

    /**
     * Ip to position with .ipdb file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2positionIPDB(string $ip, string $filename = 'ip2region.ipip.ipdb'): array
    {
        $file = $this->getFilePathInOrder($filename);
        [$latitude, $longitude] = array_fill(0, 2, 0);

        try {
            $info = (new IpRegionIPDB($file))->findMap($ip, 'CN');
        } catch (Exception $e) {
            return compact('latitude', 'longitude');
        }

        $latitude = floatval($info['latitude'] ?? 0);
        $longitude = floatval($info['longitude'] ?? 0);

        return compact('latitude', 'longitude');
    }

    /**
     * Ip to info with .ipdb file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2infoIPDB(string $ip, string $filename = 'ip2region.ipip.ipdb'): array
    {
        $file = $this->getFilePathInOrder($filename);
        [$post, $mobile] = array_fill(0, 2, 0);
        $country = '';

        try {
            $info = (new IpRegionIPDB($file))->findMap($ip, 'CN');
        } catch (Exception $e) {
            return compact('post', 'mobile', 'country');
        }

        $post = intval($info['china_admin_code'] ?? 0);
        $mobile = intval($info['idd_code'] ?? 0);
        $country = $info['country_code'] ?? '';

        return compact('post', 'mobile', 'country');
    }

    /**
     * Get correct region by special province
     *
     * @param array $location
     * @param array $specialProvince
     *
     * @return string
     */
    public function getCorrectRegion(array $location, array $specialProvince = ['香港', '澳门', '台湾']): string
    {
        $region = $location['country'];
        if ($specialProvince && in_array($location['province'], $specialProvince)) {
            $region = $location['province'];
        }

        return $region;
    }

    /**
     * Attachment handler for preview
     *
     * @param object|array $item
     * @param string       $key
     * @param array        $pull
     * @param bool         $unsetPullKeys
     *
     * @return array|object
     */
    public function attachmentPreviewHandler(
        $item,
        string $key = 'file_url',
        array $pull = ['deep', 'filename'],
        bool $unsetPullKeys = true
    ) {
        if (empty($item)) {
            return $item;
        }

        $isObject = is_object($item);
        if ($isObject) {
            $item = Helper::entityToArray($item);
        }

        $args = Helper::arrayPull($item, $pull, $unsetPullKeys);
        $path = Helper::joinString('/', ...array_values($args));

        if (!empty($path) && strpos($path, 'http') !== 0) {
            $path = $this->perfectUrl($this->cnf->host_file) . $path;
        }

        $item[$key] = $path;

        return $isObject ? (object)$item : $item;
    }

    /**
     * Check captcha
     *
     * @param string $input
     *
     * @return bool
     */
    public function checkCaptcha(string $input): bool
    {
        $captcha = $this->session->get($this->skCaptcha);
        $this->logger->debug("Number captcha in server {$captcha} and user input {$input}");

        return (new CaptchaBuilder($captcha))->testPhrase($input);
    }

    /**
     * @param UploadItem $file
     *
     * @return UploadItem
     * @throws
     */
    public function ossUpload(UploadItem $file): UploadItem
    {
        $fileName = "{$file->savePath}/{$file->saveName}";
        if (!$this->parameter('upload_to_oss')) {
            return $file;
        }

        try {

            $ossClient = new OssClient(
                $this->parameterInOrderByEmpty(['ali_oss_key', 'ali_key']),
                $this->parameterInOrderByEmpty(['ali_oss_secret', 'ali_secret']),
                $this->parameter('ali_oss_endpoint')
            );

            $ossClient->setConnectTimeout($this->cnf->curl_timeout_second * 20);
            $ossClient->setTimeout($this->cnf->curl_timeout_second * 20);

            $ossClient->uploadFile(
                $this->parameter('ali_oss_bucket'),
                $fileName,
                $file->file
            );

        } catch (OssException $e) {

            $this->logger->error("Ali oss upload error: {$e->getMessage()}");

            return $file;
        }

        // remove local file
        if ($this->cnf->rm_local_file_when_oss ?? false) {
            @unlink($file->file);
        }

        return $file;
    }

    /**
     * Get upload option with flag
     *
     * @param string $flag
     *
     * @return array
     */
    public function uploadOptionByFlag(string $flag): array
    {
        $default = [
            'max_size'     => 128 * 1024,
            'suffix'       => [],
            'mime'         => [],
            'pic_sizes'    => [[10, 'max'], [10, 'max']],
            'save_replace' => false,
            'root_path'    => $this->parameter('file'),
            'save_name_fn' => ['uniqid'],
            'save_path_fn' => function () use ($flag) {
                return $flag;
            },
        ];

        return $this->dispatchMethod(Abs::FN_UPLOAD_OPTIONS, $default, [$flag, $default]);
    }

    /**
     * Get worksheet for excel
     *
     * @param string  $file
     * @param string  $sheet
     * @param boolean $write
     *
     * @return array
     * @throws
     */
    protected function excelSheet(string $file, string $sheet = null, bool $write = false)
    {
        // create reader
        $type = IOFactory::identify($file);
        $reader = IOFactory::createReader($type);
        $spreadsheet = $reader->load($file);

        if ($write) {
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->setTitle($sheet ?: Abs::SELECT_ALL_VALUE);
        } else {
            $worksheet = $sheet ? $spreadsheet->getSheetByName($sheet) : $spreadsheet->getActiveSheet();
            if (empty($worksheet)) {
                throw new Exception(
                    $sheet ? "Sheet `{$sheet}` not exists in the document." : 'No sheet in the document.'
                );
            }
        }

        return [$spreadsheet, $worksheet];
    }

    /**
     * Read data from excel
     *
     * @param string  $file
     * @param array   $fieldsMap
     * @param integer $limit
     * @param integer $offset
     * @param integer $dataBeginLine
     * @param string  $sheet
     *
     * @return array
     * @throws
     */
    public function excelReader(
        string $file,
        array $fieldsMap,
        int $limit = 0,
        int $offset = 0,
        int $dataBeginLine = 3,
        string $sheet = null
    ) {

        /**
         * @var Worksheet $worksheet
         */
        [$_, $worksheet] = $this->excelSheet($file, $sheet);

        // get highest
        $maxRow = $worksheet->getHighestRow(); // row
        $maxCol = $worksheet->getHighestColumn(); // col

        $beginLine = $offset + $dataBeginLine;
        if ($maxRow < $beginLine) {
            throw new Exception('No data in the document.');
        }

        // list field
        $field = [];
        for ($col = 'A'; $col <= $maxCol; $col++) {
            $value = $worksheet->getCell("{$col}1")->getValue();
            if (empty($value)) {
                break;
            }
            $field[$col] = $fieldsMap[$value] ?? $value;
        }

        // check field exists
        $diff = array_diff($fieldsMap, $field);
        if (!empty($diff)) {
            $diffField = current($diff);
            throw new Exception("Field `{$diffField}` not exists in the document.");
        }

        if (empty($field)) {
            throw new Exception('No field in the document.');
        }

        // max col
        $maxField = key(array_reverse($field));

        $data = [];
        $i = 1;
        $fieldsMapValue = array_values($fieldsMap);
        for ($row = $beginLine; $row <= $maxRow; $row++) {
            for ($col = 'A'; $col <= $maxCol; $col++) {
                if ($col > $maxField) {
                    break;
                }
                if (!in_array($field[$col], $fieldsMapValue)) {
                    break;
                }
                $data[$row][$field[$col]] = $worksheet->getCell("{$col}{$row}")->getValue();
            }

            $i++;
            if ($limit > 0 && $i > $limit) {
                break;
            }
        }

        return $data;
    }

    /**
     * Write data to excel
     *
     * @param array   $data
     * @param string  $file
     * @param array   $fields
     * @param array   $fieldsLabel
     * @param array   $fieldsKvp
     * @param integer $offset
     * @param integer $dataBeginLine
     * @param string  $sheet
     *
     * @return void
     * @throws
     */
    public function excelWriter(
        array $data,
        string $file,
        array $fields,
        array $fieldsLabel = [],
        array $fieldsKvp = [],
        int $offset = 0,
        int $dataBeginLine = 3,
        string $sheet = null
    ) {

        /**
         * @var Spreadsheet $spreadsheet
         * @var Worksheet   $worksheet
         */
        [$spreadsheet, $worksheet] = $this->excelSheet($file, $sheet, !$offset);

        // title style
        $worksheet->getStyle('A1:Z1')->applyFromArray(['font' => ['bold' => true]]);

        // data style
        $beginLine = $offset + $dataBeginLine;
        $maxRow = count($data) + $beginLine;
        $worksheet->getStyle("A1:Z{$maxRow}")->applyFromArray(
            [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]
        );

        $worksheet->getDefaultColumnDimension()->setWidth(30);
        $worksheet->getDefaultRowDimension()->setRowHeight(28);

        // check field exists
        $isObject = is_object($data[0]);
        $firstData = Helper::entityToArray(current($data));

        $diff = array_diff($fields, array_keys($firstData));
        if (!empty($diff)) {
            $diffField = current($diff);
            throw new Exception("Field `{$diffField}` not exists in the data.");
        }

        foreach ($data as &$item) {
            if ($isObject) {
                $item = Helper::entityToArray($item);
            }
            $item = Helper::arrayPull($item, $fields);
            foreach ($fieldsKvp as $key => $kvp) {
                $item[$key] = $kvp[$item[$key]] ?? $item[$key];
            }
        }

        // write title
        $col = 'A';
        $_fields = [];
        foreach ($fields as $value) {
            if (!$offset) {
                $lang = $fieldsLabel[$value] ?? $value;
                $lang = $this->translator->trans($lang, [], 'fields');
                $this->excelCellRender($worksheet->getCell("{$col}1"), $lang);
            }
            $_fields[$col] = $value;
            $col++;
        }

        // write data
        $row = $beginLine;
        foreach ($data as $record) {
            foreach ($_fields as $col => $name) {
                $this->excelCellRender($worksheet->getCell("{$col}{$row}"), $record[$name] ?? null);
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($file);
    }

    /**
     * @param Cell         $cell
     * @param array|string $item
     *
     * @return Cell
     * @throws
     */
    protected function excelCellRender(Cell $cell, $item): Cell
    {
        if (is_scalar($item)) {
            return $cell->setValue($item);
        }

        if (!is_array($item)) {
            return $cell->setValue(null);
        }

        $style = [];
        if ($backgroundColor = Helper::dig($item, 'background-color')) {
            $style['fill'] = [
                'fillType'   => Fill::FILL_SOLID,
                'rotation'   => 0,
                'startColor' => [
                    'rgb' => $backgroundColor,
                ],
                'endColor'   => [
                    'rgb' => $backgroundColor,
                ],
            ];
        }

        $cell->getStyle()->applyFromArray($style);
        $cell->setValue($item['value'] ?? null);

        return $cell;
    }

    /**
     * Excel downloader
     *
     * @param array  $list
     * @param array  $fields
     * @param array  $fieldsLabel
     * @param array  $fieldsKvp
     * @param string $filename
     *
     * @return void
     * @throws
     */
    public function excelDownloader(array $list, array $fields, array $fieldsLabel, array $fieldsKvp, string $filename)
    {
        // filename
        $time = date('YmdHis');
        $filename = "{$filename}-{$time}.xlsx";

        // save to tpm
        $file = Abs::TMP_PATH . "/{$filename}";
        if (!file_exists($file)) {
            $writer = new Xlsx(new Spreadsheet());
            $writer->save($file);
        }

        $this->excelWriter($list, $file, $fields, $fieldsLabel, $fieldsKvp);

        $down = new Download();
        $down->download($file, $filename);
    }

    /**
     * Get telegram bot
     *
     * @return Api
     * @throws
     */
    public function telegram(): Api
    {
        $telegram = new Api($this->parameter('telegram_bot_token'));
        $telegram->setTimeOut($this->cnf->curl_timeout_second);

        return $telegram;
    }

    /**
     * Send message to telegram users
     *
     * @param string|array $receiver
     * @param string       $message
     * @param Api|null     $telegram
     *
     * @return array
     */
    public function telegramSendMessage($receiver, string $message, ?Api $telegram = null): array
    {
        if (!is_array($receiver)) {
            $receiver = Helper::stringToArray($receiver, true, true, 'intval');
        }

        $error = [];
        $telegram = $telegram ?? $this->telegram();

        foreach ($receiver as $user) {
            try {
                $telegram->sendMessage(['chat_id' => $user, 'text' => $message, 'parse_mode' => 'Markdown']);
            } catch (Exception $e) {
                $message = "BotError: [{$user}] {$e->getMessage()}";
                $this->logger->error($message);
                array_push($error, $message);
            }
        }

        return [$error, $receiver];
    }
}
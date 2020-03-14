<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\CommandException;
use Leon\BswBundle\Module\Interfaces\CommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class BswInitCommand extends Command implements CommandInterface
{
    use BswFoundation;

    protected $api = false;

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'doctrine'           => [null, InputOption::VALUE_OPTIONAL, 'Doctrine database flag'],
            'force'              => [null, InputOption::VALUE_OPTIONAL, 'Force init again'],
            'app'                => [null, InputOption::VALUE_OPTIONAL, 'App flag for scaffold suffix'],
            'api'                => [null, InputOption::VALUE_OPTIONAL, 'Is api project?', 'no'],
            'scheme-bsw'         => [null, InputOption::VALUE_OPTIONAL, 'Bsw scheme required?', 'yes'],
            'scheme-extra'       => [null, InputOption::VALUE_OPTIONAL, 'Extra scheme path'],
            'scheme-only'        => [null, InputOption::VALUE_OPTIONAL, 'Only scheme split by comma'],
            'scheme-start-only'  => [null, InputOption::VALUE_OPTIONAL, 'Only scheme start with string'],
            'scheme-force'       => [null, InputOption::VALUE_OPTIONAL, 'Force rebuild scheme'],
            'scaffold-need'      => [null, InputOption::VALUE_OPTIONAL, 'Scaffold need?', 'yes'],
            'scaffold-cover'     => [null, InputOption::VALUE_OPTIONAL, 'Scaffold file rewrite?', 12],
            'scaffold-path'      => [null, InputOption::VALUE_OPTIONAL, 'Scaffold file save path'],
            'scaffold-namespace' => [
                null,
                InputOption::VALUE_OPTIONAL,
                'Scaffold namespace for Controller\Entity\Repository',
            ],
            'acme'               => [
                null,
                InputOption::VALUE_OPTIONAL,
                'Acme controller class for preview/persistence/filter annotation hint',
            ],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'init',
            'info'    => 'Project initialization',
        ];
    }

    /**
     * @return array
     */
    protected function devJmsSerializerCnf(): array
    {
        return [
            'jms_serializer' => [
                'visitors' => [
                    'json' => [
                        'options' => [
                            'JSON_PRETTY_PRINT',
                            'JSON_UNESCAPED_SLASHES',
                            'JSON_PRESERVE_ZERO_FRACTION',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function prodJmsSerializerCnf(): array
    {
        return [
            'jms_serializer' => [
                'visitors' => [
                    'json' => [
                        'options' => [
                            'JSON_UNESCAPED_SLASHES',
                            'JSON_PRESERVE_ZERO_FRACTION',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function cacheCnf(): array
    {
        return [
            'framework' => [
                'cache' => [
                    'app'                    => 'cache.adapter.redis',
                    'default_redis_provider' => '%env(REDIS_URL)%',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function fosRestCnf(): array
    {
        if ($this->api) {
            return ['fos_rest' => null];
        }

        return [
            'fos_rest' => [
                'service'         => ['serializer' => null],
                'routing_loader'  => ['default_format' => 'json'],
                'format_listener' => [
                    'rules' => [
                        [
                            'path'             => '^/',
                            'prefer_extension' => true,
                            'fallback_format'  => 'json',
                            'priorities'       => ['json'],
                        ],
                    ],
                ],
                'exception'       => [
                    'exception_controller' => 'App\Controller\AcmeController::showExceptionAction',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function frameworkCnf(): array
    {
        return [
            'framework' => [
                'session'         => [
                    'gc_maxlifetime'  => 86400,
                    'cookie_lifetime' => 86400,
                    'cookie_secure'   => 'auto',
                    'cookie_samesite' => 'lax',
                ],
                'csrf_protection' => true,
                'ide'             => 'phpstorm://open?file=%%f&line=%%l',
                'secret'          => '%env(APP_SECRET)%',
                'php_errors'      => ['log' => true],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function jmsSerializerCnf(): array
    {
        return [
            'jms_serializer' => [
                'visitors' => [
                    'json' => [
                        'options' => [
                            'JSON_PRETTY_PRINT',
                            'JSON_UNESCAPED_UNICODE',
                        ],
                    ],
                    'xml'  => ['format_output' => '%kernel.debug%'],
                ],
            ],
        ];
    }


    /**
     * @return array
     */
    protected function sncRedisCnf(): array
    {
        return [
            'snc_redis' => [
                'clients' => [
                    'default' => [
                        'type'  => 'predis',
                        'alias' => 'default',
                        'dsn'   => '%env(REDIS_URL)%',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function translationCnf(): array
    {
        return [
            'framework' => [
                'default_locale' => '%locale%',
                'translator'     => [
                    'default_path' => '%kernel.project_dir%/translations',
                    'fallbacks'    => ['%locale%'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function twigCnf(): array
    {
        return [
            'twig' => [
                'paths'                => [
                    '%kernel.project_dir%/templates',
                    '%kernel.project_dir%/vendor/jtleon/bsw-bundle/Resources/views',
                ],
                'default_path'         => '%kernel.project_dir%/templates',
                'debug'                => '%kernel.debug%',
                'strict_variables'     => '%kernel.debug%',
                'exception_controller' => 'Leon\BswBundle\Controller\BswBackendController::showExceptionAction',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function annotationCnf(): array
    {
        return [
            'controllers' => [
                'resource' => '../../src/Controller/',
                'type'     => 'annotation',
            ],
            'kernel'      => [
                'resource' => '../../src/Kernel.php',
                'type'     => 'annotation',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function routesCnf(): array
    {
        return [
            'leon_bsw_bundle' => [
                'resource' => '@LeonBswBundle/Controller',
                'type'     => 'annotation',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function servicesCnf(): array
    {
        $signSalt = Helper::randString(16, 'mixed');
        $aesKey = Helper::randString(16, 'mixed');
        $debugDevil = Helper::randString(16, 'mixed');

        return [
            'parameters' => [
                'locale'                     => 'en',
                'version'                    => '1.0.0',
                'salt'                       => $signSalt,
                'platform_sms'               => 'aws',
                'platform_email'             => 'aws',
                'telegram_bot_token'         => '',
                'telegram_hooks_host'        => '',
                'backend_with_google_secret' => false,
                'aes_key'                    => $aesKey,
                'aes_iv'                     => $aesKey,
                'aes_method'                 => 'AES-128-CBC',
                'jwt_issuer'                 => 'jwt-issuer',
                'jwt_type'                   => 'hmac',
                'bd_dwz_token'               => 'baidu-dwz-token',
                'ali_key'                    => 'ali-key',
                'ali_secret'                 => 'ali-secret',
                'ali_sms_key'                => '',
                'ali_sms_secret'             => '',
                'ali_sms_region'             => 'ali-sms-region',
                'ali_oss_key'                => '',
                'ali_oss_secret'             => '',
                'ali_oss_bucket'             => 'ali-oss-bucket',
                'ali_oss_endpoint'           => 'ali-oss-endpoint',
                'tx_key'                     => 'tx-key',
                'tx_secret'                  => 'tx-secret',
                'tx_sms_key'                 => '',
                'tx_sms_secret'              => '',
                'aws_region'                 => 'aws-region',
                'aws_key'                    => 'aws-key',
                'aws_secret'                 => 'aws-secret',
                'aws_email'                  => 'aws-sender@gmail.com',
                'smtp_host'                  => 'smtp.qq.com',
                'smtp_port'                  => 587,
                'smtp_sender'                => 'smtp-sender@qq.com',
                'smtp_secret'                => 'smtp-secret',
                'component'                  => [],
                'cnf'                        => [
                    'app_logo'              => '/img/logo.svg',
                    'app_ico'               => '/img/favicon.ico',
                    'app_name'              => 'Custom Application',
                    'host'                  => '//api.custom.com',
                    'host_official'         => 'http://www.custom.com',
                    'host_file'             => 'http://file.custom.com',
                    'debug_devil'           => $debugDevil,
                    'cache_default_expires' => 10,
                    'debug_uuid'            => '_',
                    'debug_cost'            => true,
                ],
            ],
            'services'   => [],
        ];
    }

    /**
     * Execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $this->options($input);
        $project = $this->kernel->getProjectDir();
        $dumper = new Dumper();

        $this->api = $params['api'] === 'yes';

        $doneFile = "{$project}/.done-init";
        if (!$params['force'] && file_exists($doneFile)) {
            throw new CommandException('The command can only be executed once');
        }

        /**
         * Config
         */
        $config = [
            'devJmsSerializer'  => "{$project}/config/packages/dev/jms_serializer.yaml",
            'prodJmsSerializer' => "{$project}/config/packages/prod/jms_serializer.yaml",
            'cache'             => "{$project}/config/packages/cache.yaml",
            'fosRest'           => "{$project}/config/packages/fos_rest.yaml",
            'framework'         => "{$project}/config/packages/framework.yaml",
            'jmsSerializer'     => "{$project}/config/packages/jms_serializer.yaml",
            'sncRedis'          => "{$project}/config/packages/snc_redis.yaml",
            'translation'       => "{$project}/config/packages/translation.yaml",
            'twig'              => "{$project}/config/packages/twig.yaml",
            'annotation'        => "{$project}/config/routes/annotations.yaml",
            'routes'            => "{$project}/config/routes.yaml",
            'services'          => "{$project}/config/services.yaml",
        ];

        foreach ($config as $name => $file) {

            $fileContent = Yaml::parseFile($file) ?? [];
            $customContent = $this->{"{$name}Cnf"}();

            $content = Helper::merge2(true, false, true, $customContent, $fileContent);
            file_put_contents($file, $dumper->dump($content, 4, 0, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));

            $output->writeln("<info>  [Merge] {$file} </info>");
        }

        /**
         * Document
         */
        $documentFileList = [];
        Helper::directoryIterator(__DIR__ . '/document', $documentFileList);
        $documentFileList = Helper::multipleToOne($documentFileList);

        foreach ($documentFileList as $file) {

            $targetFile = str_replace(__DIR__, $project, $file);
            @mkdir(pathinfo($targetFile, PATHINFO_DIRNAME), 0755, true);

            if (!file_exists($targetFile)) {
                copy($file, $targetFile);
                $output->writeln("<info>   [Copy] {$targetFile} </info>");
            }
        }

        /**
         * Table scheme
         */
        $schemeFileList = [];
        $paths = ($params['scheme-bsw'] == 'yes' ? (__DIR__ . '/scheme') : null);
        $schemePath = explode(PATH_SEPARATOR, $paths . PATH_SEPARATOR . $params['scheme-extra']);

        foreach ($schemePath as $path) {
            Helper::directoryIterator(
                $path,
                $schemeFileList,
                function ($file) {
                    return strpos($file, '.sql') === false ? false : $file;
                }
            );
        }

        $pdo = $this->pdo($params['doctrine'] ?: 'default');
        $database = $pdo->getDatabase();

        $schemeOnly = Helper::stringToArray($params['scheme-only']);
        $schemeStartOnly = $params['scheme-start-only'];
        $scaffoldNeed = ($params['scaffold-need'] === 'yes');

        foreach ($schemeFileList as $sqlFile) {

            $table = pathinfo($sqlFile, PATHINFO_FILENAME);
            if ($schemeOnly && !in_array($table, $schemeOnly)) {
                continue;
            }

            if ($schemeStartOnly && strpos($table, $schemeStartOnly) !== 0) {
                continue;
            }

            $output->write(Abs::ENTER);

            $exists = $this->pdo()->fetchArray("SHOW TABLES WHERE Tables_in_{$database} = '{$table}'");
            $record = $exists && current($pdo->fetchArray("SELECT COUNT(*) FROM {$table}"));
            if (!$record || $params['scheme-force'] === 'yes') {
                $sql = file_get_contents($sqlFile);
                $sql = str_replace('{TABLE_NAME}', $table, $sql);
                $pdo->exec($sql);
                $output->writeln("<info>  Scheme:  [ReBuild] {$database}.{$table} </info>");
            } else {
                $output->writeln("<info>  Scheme:  [NotBlank] {$database}.{$table} </info>");
            }

            // Entity & Repository
            if ($scaffoldNeed) {
                $this->getApplication()->find('bsw:scaffold')->run(
                    new ArrayInput(
                        [
                            '--table'     => $table,
                            '--app'       => $params['app'],
                            '--cover'     => $params['scaffold-cover'] ?: 'no',
                            '--path'      => $params['scaffold-path'] ?: null,
                            '--namespace' => $params['scaffold-namespace'] ?: null,
                            '--acme'      => $params['acme'],
                        ]
                    ),
                    $output
                );
            }
        }

        file_put_contents($doneFile, date(Abs::FMT_FULL));
        $output->writeln("<info> \n project initialization done\n </info>");
    }
}
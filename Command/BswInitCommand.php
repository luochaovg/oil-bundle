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

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'doctrine'           => [null, InputOption::VALUE_OPTIONAL, 'Doctrine database flag'],
            'force'              => [null, InputOption::VALUE_OPTIONAL, 'Force init again'],
            'app'                => [null, InputOption::VALUE_OPTIONAL, 'App flag for scaffold suffix'],
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
    protected function servicesCnf(): array
    {
        $signSalt = Helper::randString(16, 'mixed');
        $aesKey = Helper::randString(16, 'mixed');
        $debugDevil = Helper::randString(16, 'mixed');

        return [
            'parameters' => [
                'locale'           => 'en',
                'version'          => '1.0.0',
                'salt'             => $signSalt,
                'platform_sms'     => 'aws',
                'platform_email'   => 'aws',
                'aes_key'          => $aesKey,
                'aes_iv'           => $aesKey,
                'aes_method'       => 'AES-128-CBC',
                'jwt_issuer'       => 'jwt-issuer',
                'jwt_type'         => 'hmac',
                'bd_dwz_token'     => 'baidu-dwz-token',
                'ali_key'          => 'ali-key',
                'ali_secret'       => 'ali-secret',
                'ali_sms_key'      => '',
                'ali_sms_secret'   => '',
                'ali_sms_region'   => 'ali-sms-region',
                'ali_oss_key'      => '',
                'ali_oss_secret'   => '',
                'ali_oss_bucket'   => 'ali-oss-bucket',
                'ali_oss_endpoint' => 'ali-oss-endpoint',
                'tx_key'           => 'tx-key',
                'tx_secret'        => 'tx-secret',
                'tx_sms_key'       => '',
                'tx_sms_secret'    => '',
                'aws_region'       => 'aws-region',
                'aws_key'          => 'aws-key',
                'aws_secret'       => 'aws-secret',
                'aws_email'        => 'aws-sender@gmail.com',
                'smtp_host'        => 'smtp.qq.com',
                'smtp_port'        => 587,
                'smtp_sender'      => 'smtp-sender@qq.com',
                'smtp_secret'      => 'smtp-secret',
                'component'        => [],
                'cnf'              => [
                    'app_logo'              => '/img/custom.svg',
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
    protected function frameworkCnf(): array
    {
        return [
            'framework' => [
                'session'         => [
                    'gc_maxlifetime'  => 86400,
                    'cookie_lifetime' => 86400,
                ],
                'csrf_protection' => true,
                'ide'             => 'phpstorm://open?file=%%f&line=%%l',
            ],
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

        $doneFile = "{$project}/.done-init";
        if (!$params['force'] && file_exists($doneFile)) {
            throw new CommandException('The command can only be executed once');
        }

        /**
         * Config
         */
        $config = [
            'services'  => "{$project}/config/services.yaml",
            'cache'     => "{$project}/config/packages/cache.yaml",
            'framework' => "{$project}/config/packages/framework.yaml",
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
        $command = $this->getApplication()->find('bsw:scaffold');

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
                $command->run(
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
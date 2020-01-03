<?php

namespace Leon\BswBundle\Controller\Traits;

use App\Kernel;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Validator\Validator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Exception;

/**
 * @property AbstractFOSRestController $container
 * @property Kernel                    $kernel
 * @property TranslatorInterface       $translator
 */
trait ApiDocument
{
    /**
     * @inheritdoc
     * @return array
     */
    public function apiDocFlag()
    {
        return [
            'AUTH'  => 'Must authorization',
            'USER'  => 'Should authorization',
            'AJAX'  => 'Should be ajax request',
            'TOKEN' => 'Should update token',
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function apiDocOutputPage()
    {
        return [
            Abs::PG_CURRENT_PAGE => ['type' => 'int'],
            Abs::PG_PAGE_SIZE    => ['type' => 'int'],
            Abs::PG_TOTAL_PAGE   => ['type' => 'int'],
            Abs::PG_TOTAL_ITEM   => ['type' => 'int'],
            Abs::PG_ITEMS        => ['type' => 'object[]'],
        ];
    }

    /**
     * Api error bill
     *
     * @param array $paths
     *
     * @return array
     */
    public function apiErrorBill(array $paths = []): array
    {
        return $this->caching(
            function () use ($paths) {

                $bill = [];
                $errorBill = $this->classBill($paths, 'Error');

                foreach ($errorBill as $error) {

                    /**
                     * @var Error $e
                     */
                    $e = new $error();

                    $code = $e->code4logic();
                    if (isset($bill[$code])) {
                        throw new Exception("Error code {$code} has repeat in {$error}");
                    }

                    $description = $this->translator->trans($e->description(), [], 'messages', 'cn');
                    $bill[$code] = [
                        'tiny'        => $e->tiny(),
                        'description' => $description,
                    ];
                }

                ksort($bill);

                return $bill;
            }
        );
    }

    /**
     * Api validator bill
     *
     * @param string $lang
     * @param array  $paths
     *
     * @return array
     */
    public function apiValidatorBill(string $lang, array $paths = []): array
    {
        return $this->caching(
            function () use ($lang, $paths) {

                $bill = [];
                $validatorBill = $this->classBill($paths, 'Validator');

                foreach ($validatorBill as $validator) {

                    /**
                     * @var Validator $v
                     */
                    $v = new $validator(null, [], $this->translator, $lang);

                    $rule = Helper::camelToUnder(Helper::clsName($validator));
                    $bill[$rule] = $this->translator->trans($v->description(), [], 'messages', 'cn');
                }

                return $bill;
            }
        );
    }

    /**
     * List class bill
     *
     * @param array  $extraPath
     * @param string $module
     *
     * @return array
     * @throws
     */
    protected function classBill(array $extraPath = [], string $module): array
    {
        $paths = array_merge(
            [
                'LeonBswBundle' => [
                    'bundle'    => true,
                    'namespace' => 'Leon\BswBundle\Module\{module}\Entity',
                    'path'      => '{path}/Module/{module}/Entity',
                ],
                'CurrentApp'    => [
                    'bundle'    => false,
                    'namespace' => 'App\Module\{module}',
                    'path'      => '{path}/src/Module/{module}',
                ],
            ],
            $extraPath,
            $this->parameter('module_extra_path', [])
        );

        $_paths = [];

        foreach ($paths as $key => $item) {
            if (!isset($item['bundle']) || !isset($item['namespace']) || !isset($item['path'])) {
                throw new Exception("Keys bundle/namespace/path must in config `module_extra_path` items");
            }

            if ($item['bundle']) {
                $_path = $this->kernel->getBundle($key)->getPath();
            } else {
                $_path = $this->kernel->getProjectDir();
            }

            $namespace = str_replace(['{path}', '{module}'], [$_path, $module], $item['namespace']);
            $path = str_replace(['{path}', '{module}'], [$_path, $module], $item['path']);

            if (file_exists($path)) {
                $namespace = '\\' . trim($namespace, '\\') . '\\';
                $_paths[$namespace] = rtrim($path, '/') . '/';
            }
        }

        $classBill = [];
        foreach ($_paths as $ns => $path) {
            Helper::directoryIterator(
                $path,
                $classBill,
                function ($file) use ($ns) {
                    if (strpos($file, '.php') === false) {
                        return false;
                    }

                    $class = pathinfo($file, PATHINFO_FILENAME);
                    $class = "{$ns}{$class}";

                    if (!class_exists($class)) {
                        return false;
                    }

                    return $class;
                }
            );
        }

        return $classBill;
    }
}
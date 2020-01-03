<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Annotation\Entity\AccessControl;
use Leon\BswBundle\Annotation\Entity\Filter;
use Leon\BswBundle\Annotation\Entity\Output;
use Leon\BswBundle\Annotation\Entity\Persistence;
use Leon\BswBundle\Annotation\Entity\Preview;
use Leon\BswBundle\Annotation\Entity\Mixed as MixedAnnotation;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Entity\Enum;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Leon\BswBundle\Annotation\Entity\Input;
use Leon\BswBundle\Annotation\AnnotationConverter;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @property AbstractController $container
 */
trait Annotation
{
    /**
     * Annotation converter
     *
     * @param string $class
     * @param bool   $new
     *
     * @return AnnotationConverter
     * @throws
     */
    public function annotation(string $class, bool $new = false): AnnotationConverter
    {
        static $pool = [];

        if (!class_exists($class)) {
            throw new AnnotationException("Annotation class not exists ({$class})");
        }

        $converter = "{$class}Converter";
        if (!class_exists($converter)) {
            throw new AnnotationException("Annotation converter class not exists ({$converter})");
        }

        if ($new) {
            return new $converter();
        }

        if (!isset($pool[$class])) {
            $pool[$class] = new $converter();
        }

        return $pool[$class];
    }

    /**
     * Input annotation parse
     *
     * @param string $class
     * @param string $method
     *
     * @return array
     */
    public function getInputAnnotation(string $class, string $method): array
    {
        $route = $this->getRouteCollection(true);
        $http = current($route[$class][$method]['http']);

        return $this->caching(
            function () use ($class, $method, $http) {

                $annotation = $this->annotation(Input::class)->resolveMethod(
                    $class,
                    $method,
                    ['method' => $http]
                );

                return $annotation[Input::class] ?? [];
            }
        );
    }

    /**
     * Output annotation parse
     *
     * @param string $class
     * @param string $method
     *
     * @return array
     */
    public function getOutputAnnotation(string $class, string $method): array
    {
        return $this->caching(
            function () use ($class, $method) {

                $list = $this->annotation(Output::class)->resolveMethod($class, $method);
                $list = $list[Output::class] ?? [];
                $_list = [];

                /**
                 * @var Output $item
                 */
                foreach ($list as $item) {
                    if (empty($item->extra)) {
                        $prefix = $item->prefix ? "{$item->prefix}." : null;
                        $_list["{$prefix}{$item->field}"] = [
                            'type'   => $item->type,
                            'label'  => $item->label,
                            'trans'  => $item->trans,
                            'tab'    => $item->tab,
                            'enum'   => $item->enum,
                            'prefix' => $item->prefix,
                        ];
                        continue;
                    }

                    if (!$item->extra) {
                        continue;
                    }

                    $fn = Abs::FN_API_DOC_OUTPUT . ucfirst($item->extra);
                    if (!method_exists($class, $fn)) {
                        continue;
                    }

                    $extra = [];
                    $output = call_user_func([$class, $fn]) ?? [];
                    $item->tab = $item->tab ?? 0;

                    foreach ($output as $field => $meta) {

                        $trans = $meta['trans'] ?? true;
                        $label = $meta['label'] ?? $field;
                        $tab = $meta['tab'] ?? 0;

                        $meta['label'] = $trans ? Helper::stringToLabel($label) : $label;
                        $meta['trans'] = $trans;
                        $meta['tab'] = $tab + $item->tab + (($tab && $item->tab) ? 1 : 0);
                        $meta['enum'] = $meta['enum'] ?? ($item->enum[$field] ?? []);

                        $prefix = $item->prefix ? "{$item->prefix}." : null;
                        $extra["{$prefix}{$field}"] = array_merge(['type' => Abs::T_STRING], $meta);
                    }

                    if ($item->position == Abs::POS_BOTTOM) {
                        $_list = array_merge($_list, $extra);
                    } elseif ($item->position == Abs::POS_TOP) {
                        $_list = array_merge($extra, $_list);
                    } else {
                        $_list = Helper::arrayInsertAssoc($_list, $item->position, $extra);
                    }
                }

                return $_list;
            }
        );
    }

    /**
     * Preview annotation parse
     *
     * @param string $class
     * @param string $enumClass
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getPreviewAnnotation(string $class, string $enumClass, string $property = null): array
    {
        if (!Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class should extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $enumClass, $property) {

                $converter = $this->annotation(Preview::class);
                $converter->setAnnotationClass(
                    [
                        Type::class,
                        Length::class,
                    ]
                );

                $annotation = $converter->resolveProperty($class, $property, ['enumClass' => $enumClass]);
                $properties = [];
                foreach ($annotation as $attribute => $item) {
                    if (empty($item[Preview::class])) {
                        continue;
                    }
                    $properties[$attribute] = (array)end($item[Preview::class]);
                }

                return $properties;
            }
        );
    }

    /**
     * Persistence annotation parse
     *
     * @param string $class
     * @param string $enumClass
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getPersistenceAnnotation(string $class, string $enumClass, string $property = null): array
    {
        if (!Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class should extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $enumClass, $property) {

                $converter = $this->annotation(Persistence::class);
                $converter->setAnnotationClass(
                    [
                        Type::class,
                    ]
                );

                $annotation = $converter->resolveProperty($class, $property, ['enumClass' => $enumClass]);
                $properties = [];
                foreach ($annotation as $attribute => $item) {
                    if (empty($item[Persistence::class])) {
                        continue;
                    }
                    $properties[$attribute] = (array)end($item[Persistence::class]);
                }

                return $properties;
            }
        );
    }

    /**
     * Filter annotation parse
     *
     * @param string $class
     * @param string $enumClass
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getFilterAnnotation(string $class, string $enumClass, string $property = null): array
    {
        if (!Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class should extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $enumClass, $property) {

                $converter = $this->annotation(Filter::class);
                $converter->setAnnotationClass(
                    [
                        Type::class,
                    ]
                );

                $annotation = $converter->resolveProperty($class, $property, ['enumClass' => $enumClass]);
                $properties = [];
                foreach ($annotation as $attribute => $item) {
                    if (empty($item[Filter::class])) {
                        continue;
                    }
                    foreach ($item[Filter::class] as $index => $filter) {
                        $properties["{$attribute}_{$index}"] = (array)$filter;
                    }
                }

                return $properties;
            }
        );
    }

    /**
     * Access control annotation parse
     *
     * @param string $class
     *
     * @return array
     * @throws
     */
    public function getAccessControlAnnotation(string $class): array
    {
        return $this->caching(
            function () use ($class) {

                $converter = $this->annotation(AccessControl::class);
                $annotation = $converter->resolveMethod($class);

                $access = [];
                foreach ($annotation['annotation'] as $method => $item) {
                    $access[$method] = current(current($item));
                }

                return [$annotation['document']['info'], $access];
            }
        );
    }

    /**
     * Mixed annotation parse
     *
     * @param string $class
     * @param string $enumClass
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getMixedAnnotation(string $class, string $enumClass, string $property = null): array
    {
        if (!Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class should extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $enumClass, $property) {

                $converter = $this->annotation(MixedAnnotation::class);
                $annotation = $converter->resolveProperty($class, $property, ['enumClass' => $enumClass]);
                $properties = [];

                foreach ($annotation as $attribute => $item) {
                    if (empty($item[MixedAnnotation::class])) {
                        continue;
                    }
                    $properties[$attribute] = (array)end($item[MixedAnnotation::class]);
                }

                return $properties;
            }
        );
    }
}
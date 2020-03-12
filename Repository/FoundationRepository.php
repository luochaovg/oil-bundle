<?php

namespace Leon\BswBundle\Repository;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;
use Leon\BswBundle\Module\Exception\EntityException;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Module\Traits as MT;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository as SFRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManager;
use Throwable;

abstract class FoundationRepository extends SFRepository
{
    use MT\Init,
        MT\Magic,
        MT\Message;

    /**
     * @const int
     */
    const MULTIPLE_PER = 50;

    /**
     * @const int
     */
    const PAGE_SIZE = 30;

    /**
     * @const int
     */
    const PAGE_RANGE = 10;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Expr
     */
    protected $expr;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $pk;

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * MasterRepository constructor.
     *
     * @param ContainerInterface  $container
     * @param ManagerRegistry     $registry
     * @param ManagerRegistry     $doctrine
     * @param ValidatorInterface  $validator
     * @param TranslatorInterface $translator
     * @param LoggerInterface     $logger
     *
     * @throws
     */
    public function __construct(
        ContainerInterface $container,
        ManagerRegistry $registry,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        if (!isset($this->entity)) {
            $this->entity = str_replace('Repository', 'Entity', static::class);
            $this->entity = substr($this->entity, 0, -6);
        }

        if (!class_exists($this->entity)) {
            throw new EntityException("Entity not exits `{$this->entity}`");
        }

        parent::__construct($registry, $this->entity);

        $this->container = $container;
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->translator = $translator;
        $this->logger = $logger;

        $this->em = $this->getEntityManager();
        $this->expr = new Expr();
        $this->pk = $this->_class->getSingleIdentifierColumnName();

        $this->init();
    }

    /**
     * Get instance for query
     *
     * @return QueryBuilder
     * @throws
     */
    protected function query(): QueryBuilder
    {
        return $this->createQueryBuilder(Helper::tableNameToAlias($this->entity));
    }

    /**
     * Get entity manager
     *
     * @return EntityManager
     * @throws
     */
    protected function em(): EntityManager
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        return $this->em;
    }

    /**
     * @return Connection
     */
    protected function pdo(): Connection
    {
        return $this->em()->getConnection();
    }

    /**
     * Get validator error
     *
     * @param ConstraintViolationListInterface $error
     * @param int                              $index
     *
     * @return string
     */
    protected function error(ConstraintViolationListInterface $error, int $index = 0): string
    {
        $fields = $error->get($index)->getPropertyPath();
        $fields = Helper::stringToArray($fields);

        foreach ($fields as $key => $field) {
            $field = Helper::stringToLabel($field);
            $field = $this->translator->trans($field, [], 'fields');
            $fields[$key] = $field;
        }

        $fields = implode(', ', $fields);
        $message = $error->get($index)->getMessage();
        $message = $this->translator->trans($message, [], 'messages');

        $this->logger->error("Persistence error with field `{$fields}` in {$this->entity}, {$message}");

        return "{$message} ({$fields})";
    }

    /**
     * Persistence
     *
     * @param FoundationEntity $entity
     * @param array            $attributes
     * @param array|null       $group
     *
     * @return false|int
     * @throws
     */
    protected function persistence(FoundationEntity $entity, array $attributes, ?array $group = null)
    {
        $entity->attributes($attributes);

        // validator
        $error = $this->validator->validate($entity, null, $group);
        if (count($error)) {
            return $this->push($this->error($error), ValidatorException::class);
        }

        $em = $this->em();

        // persistence
        try {
            $em->persist($entity);
            $em->flush();
            $em->clear();
        } catch (Throwable $e) {
            return $this->push($e->getMessage());
        }

        return $entity->{$this->pk};
    }

    /**
     * @return string
     */
    public function pk(): string
    {
        return $this->pk;
    }

    /**
     * Transactional
     *
     * @param callable $logic
     * @param bool     $throw
     *
     * @return false|mixed|null
     * @throws
     */
    public function transactional(callable $logic, bool $throw = true)
    {
        $em = $this->em();
        $em->beginTransaction();

        try {
            $result = call_user_func_array($logic, [$this]);
            $em->flush();
            $em->commit();

            return $result === false ? true : $result;

        } catch (Throwable $error) {

            $em->close();
            $em->rollBack();

            $message = "{$error->getMessage()} in {$error->getFile()} line {$error->getLine()}";
            $this->logger->warning("Transactional process failed, {$message}");

            if ($error instanceof ValidatorException) {
                return $this->push($error->getMessage(), ValidatorException::class);
            }

            if ($throw) {
                throw new RepositoryException($error->getMessage());
            }

            return false;
        }
    }

    /**
     * Newly
     *
     * @param array $attributes
     *
     * @return false|int
     * @throws
     */
    public function newly(array $attributes)
    {
        return $this->persistence(new $this->entity, $attributes, [Abs::VG_NEWLY]);
    }

    /**
     * Newly multiple
     *
     * @param array $batch
     * @param int   $per
     *
     * @return false|int
     * @throws
     */
    public function newlyMultiple(array $batch, int $per = null)
    {
        return $this->transactional(
            function () use ($batch, $per) {

                $i = 0;
                $per = ($per ?? self::MULTIPLE_PER);
                $em = $this->em();

                foreach ($batch as $record) {

                    /**
                     * @var FoundationEntity $entity
                     */
                    $entity = new $this->entity;
                    $entity->attributes($record);

                    // validator
                    $error = $this->validator->validate($entity, null, [Abs::VG_NEWLY]);
                    if (count($error)) {
                        throw new ValidatorException($this->error($error));
                    }

                    $i++;
                    $em->persist($entity);

                    if ($i % $per === 0) {
                        $em->flush();
                        $em->clear();
                    }
                }

                $em->flush();
                $em->clear();

                return count($batch);
            }
        );
    }

    /**
     * Newly or modify
     *
     * @param array    $criteria
     * @param array    $attributes
     * @param callable $exists
     *
     * @return false|int
     * @throws
     */
    public function newlyOrModify(array $criteria, array $attributes, callable $exists = null)
    {
        $record = $this->findOneBy($criteria);
        $group = [Abs::VG_MODIFY];

        if (empty($record)) {

            // newly
            $record = new $this->entity;
            $group = [Abs::VG_NEWLY];
            $attributes = array_merge($criteria, $attributes);

        } elseif ($exists) {

            // modify
            $attributes = call_user_func_array($exists, [$record, $attributes]);
            Helper::callReturnType($attributes, Abs::T_ARRAY, 'Newly or modify handler');
        }

        /**
         * @var FoundationEntity $record
         */

        return $this->persistence($record, $attributes, $group);
    }

    /**
     * Away
     *
     * @param array $criteria
     *
     * @return false|int
     * @throws
     */
    public function away(array $criteria)
    {
        $batch = $this->findBy($criteria);

        return $this->transactional(
            function () use ($batch) {

                $em = $this->em();
                foreach ($batch as $entity) {
                    $em->remove($entity);
                }

                $em->flush();
                $em->clear();

                return count($batch);
            }
        );
    }

    /**
     * Modify
     *
     * @param array $criteria
     * @param array $attributes
     * @param int   $per
     *
     * @return false|int
     * @throws
     */
    public function modify(array $criteria, array $attributes, int $per = null)
    {
        $batch = $this->findBy($criteria);

        return $this->transactional(
            function () use ($batch, $attributes, $per) {

                $i = 0;
                $per = ($per ?? self::MULTIPLE_PER);
                $em = $this->em();

                foreach ($batch as $entity) {

                    /**
                     * @var FoundationEntity $entity
                     */
                    $entity->attributes($attributes);

                    // validator
                    $error = $this->validator->validate($entity, null, [Abs::VG_MODIFY]);
                    if (count($error)) {
                        throw new ValidatorException($this->error($error));
                    }

                    $i++;
                    $em->persist($entity);

                    if ($i % $per === 0) {
                        $em->flush();
                        $em->clear();
                    }
                }

                $em->flush();
                $em->clear();

                return count($batch);
            }
        );
    }

    /**
     * Get query builder
     *
     * @param array &$filter
     *
     * @return QueryBuilder
     * @throws
     */
    protected function getQueryBuilder(array &$filter)
    {
        extract($filter);

        /*
         * Create
         */

        $model = $this->em()->createQueryBuilder();

        $table = $from ?? $this->entity;
        $alias = $alias ?? Helper::tableNameToAlias($table);

        /*
         * From
         */

        $model->from($table, $alias);

        /*
         * Where
         */

        $where = $where ?? [];
        if (!is_array($where)) {
            throw new RepositoryException('Variable `where` should be array if configured');
        }

        $exprNamespace = Expr::class;
        $where = array_filter($where);

        foreach ($where as $expr) {

            if (!is_string($expr) && !is_object($expr)) {
                throw new RepositoryException("Items of variable `where` must string or object");
            }

            if (is_object($expr) && Helper::nsName(get_class($expr)) !== $exprNamespace) {
                throw new RepositoryException("Items of variable `where` must namespaces `{$exprNamespace}`");
            }

            $model->andWhere($expr);
        }

        /*
         * Set
         */

        $set = $set ?? [];
        if (!is_array($set)) {
            throw new RepositoryException('Variable `set` should be array if configured');
        }

        $set = array_filter($set);
        foreach ($set as $field => $value) {
            $model->set($field, $value);
        }

        /*
         * Group
         */

        if (isset($group)) {
            $model->groupBy($group);
        }

        /*
         * Join
         */

        $join = $join ?? [];
        if (!is_array($join)) {
            throw new RepositoryException('Variable `join` should be array if configured');
        }

        $joinMode = ['left', 'inner'];
        $join = array_filter($join);

        foreach ($join as $_alias => $item) {

            if (!is_array($item)) {
                throw new RepositoryException('Item of variable `join` should be array');
            }

            if (empty($item['entity'])) {
                throw new RepositoryException('Item `entity` of variable `join` must configure');
            }

            $entity = $item['entity'];
            $mode = $item['type'] ?? $joinMode[0];
            if (!in_array($mode, $joinMode)) {
                throw new RepositoryException('Item `type` of variable `join` invalid');
            }

            $mode = "{$mode}Join";
            $join[$_alias]['alias'] = $_alias;

            $onLeft = $item['left'] ?? [];
            $onRight = $item['right'] ?? [];

            if (!is_array($onLeft) || !is_array($onRight) || count($onLeft) != count($onRight)) {
                throw new RepositoryException(
                    'Items `left & right` of variable `join` should be array and same number'
                );
            }

            if (empty($onLeft)) {
                $joinTable = lcfirst(Helper::clsName($entity));
                array_push($onLeft, "{$alias}.{$joinTable}" . ucfirst($this->pk));
                array_push($onRight, "{$_alias}.{$this->pk}");
            }

            $joinOn = [];
            foreach (array_combine($onLeft, $onRight) as $left => $right) {
                $joinOn[] = "{$left} = {$right}";
            }

            // join sub query
            if (is_array($entity)) {
                $_model = $this->getQueryBuilder($entity);
                $entity = " ({$_model->getDQL()}) ";
            }

            $model->{$mode}($entity, $_alias, Expr\Join::WITH, implode(' AND ', $joinOn));
        }

        /*
         * Method
         */

        $method = $method ?? Abs::SELECT;

        /*
         * Select
         */

        if (!isset($select)) {
            $select = array_merge([$alias], array_keys($join));
        } elseif (!is_array($select)) {
            throw new RepositoryException('Variable `select` should be array if configured');
        }

        if ($method === Abs::SELECT) {

            $aliasEntity = array_column($join, 'entity', 'alias');
            $aliasEntity[$alias] = $this->entity;
            $aliasLength = count($aliasEntity);

            $_select = [];
            $select = array_unique(array_filter($select));

            foreach ($select as $name) {
                if (!isset($aliasEntity[$name]) || $aliasLength == 1) {
                    array_push($_select, $name);
                    continue;
                }

                $fields = array_keys(Helper::entityToArray(new $aliasEntity[$name]));
                $fields = Helper::arrayMap($fields, "{$name}.%s");
                $_select = array_merge($_select, $fields);
            }

            $model->select($_select);

        } elseif ($method === Abs::DELETE) {

            /*
             * Delete
             */

            $model->delete($table, $alias);

        } elseif ($method === Abs::UPDATE) {

            /*
             * Update
             */

            $model->update($table, $alias);

        }

        /*
         * Sort to last when eq 0 or NULL
         */

        $sortMode = [Abs::SORT_ASC, Abs::SORT_DESC, null];

        $sort = $sort ?? [];
        if (!is_array($sort)) {
            throw new RepositoryException('Variable `sort` should be array if configured');
        }

        $sort = array_filter($sort);
        foreach ($sort as $field => $mode) {

            if (!in_array($mode, $sortMode)) {
                throw new RepositoryException("Item `{$field}` of variable `sort` invalid");
            }

            $index = ($mode == Abs::SORT_ASC ? PHP_INT_MAX : 0);
            $sortName = str_replace('.', '_', strtoupper($field) . "_FOR_SORT");
            $model->addSelect(
                "CASE WHEN {$field} = 0 OR {$field} IS NULL THEN {$index} ELSE {$field} END AS HIDDEN {$sortName}"
            );
            $model->addOrderBy($sortName, $mode);
        }

        /*
         * Order
         */

        $order = $order ?? [];
        if (!is_array($order)) {
            throw new RepositoryException('Variable `order` should be array if configured');
        }

        $order = array_filter($order);
        foreach ($order as $field => $mode) {

            if (!in_array($mode, $sortMode)) {
                throw new RepositoryException("Item `{$field}` of variable `sort` invalid");
            }

            $model->addOrderBy($field, $mode);
        }

        /*
         * Page
         */

        $pageArgs = Helper::pageArgs(
            [
                'paging' => $paging ?? false,
                'page'   => $page ?? 1,
                'limit'  => $limit ?? static::PAGE_SIZE,
            ],
            self::PAGE_SIZE
        );

        extract($pageArgs);

        /*
         * Args
         */

        $args = $args ?? [];
        if (!is_array($args)) {
            throw new RepositoryException('Variable `args` should be array if configured');
        }

        $typeMode = [true => Types::INTEGER, false => Types::STRING];
        $args = array_filter($args);

        foreach ($args as $key => $item) {
            if (!is_array($item) || !isset($item[0])) {
                throw new RepositoryException(
                    'Item of variable `args` value should be array and index 0 configured'
                );
            }

            $type = $item[1] ?? true;
            $model->setParameter($key, $item[0], $typeMode[$type] ?? $type);
        }

        /*
         * Custom
         */

        if (isset($query) && is_callable($query)) {
            call_user_func_array($query, [&$model]);
        }

        /*
         * Hint
         */

        $hint = $hint ?? false;
        if (!is_bool($hint) && !is_int($hint)) {
            throw new RepositoryException('Variable `hint` should be integer/boolean if configured');
        }

        if ($hint === true) {
            $hintModel = clone $model;

            $hintModel->setFirstResult(null);
            $hintModel->setMaxResults(null);
            $hintModel->resetDQLParts(['select']);

            if (empty($hintModel->getDQLPart('where'))) {
                $hintModel->resetDQLPart('join');
            }

            $hintModel->select(["count({$alias}.{$this->pk})"]);
            $count = $hintModel->getQuery()->getOneOrNullResult();
            $hint = intval(current($count));
        }

        /*
         * Debug
         */

        if ($debug ?? false) {

            $dql = $model->getDQL();
            $keywords = [
                'INSERT',
                'DELETE',
                'UPDATE',
                'SELECT',
                'FROM',
                'LEFT JOIN',
                'RIGHT JOIN',
                'SET',
                'WHERE',
                'GROUP BY',
                'HAVING',
                'ORDER BY',
                'LIMIT',
                'OFFSET',
            ];
            foreach ($keywords as $keyword) {
                $dql = str_replace(" {$keyword}", "\n{$keyword}", $dql);
            }

            dump($dql, $model->getParameters());
            exit(ErrorDebugExit::CODE);
        }

        $filter = array_merge(
            $filter,
            compact('table', 'alias', 'select', 'limit', 'paging', 'page', 'offset', 'hint')
        );

        return $model;
    }

    /**
     * Set filters
     *
     * @param array ...$filter
     *
     * @return FoundationRepository
     */
    public function filters(array ...$filter): FoundationRepository
    {
        $this->filter = Helper::merge(...$filter);

        return $this;
    }

    /**
     * Get filters
     *
     * @param array ...$filter
     *
     * @return array
     */
    public function getFilters(array ...$filter): array
    {
        $filter = Helper::merge($this->filter, ...$filter);
        $this->filter = [];

        return $filter;
    }

    /**
     * Lister
     *
     * @param array $filter
     * @param int   $hydrationMode
     *
     * @return array|object
     * @throws
     */
    public function lister(array $filter, $hydrationMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $filter = $this->getFilters($filter, ['method' => Abs::SELECT]);
        $query = $this->getQueryBuilder($filter)->getQuery();

        if (!$filter['paging']) {
            $query->setFirstResult($filter['offset']);

            if ($filter['limit']) {
                $query->setMaxResults($filter['limit']);
            }

            if ($filter['limit'] === 1) {
                return $query->getOneOrNullResult($hydrationMode);
            }

            return $query->getResult($hydrationMode);
        }

        $options = [];
        if (!empty($filter['group'])) {
            $options = array_merge($options, ['distinct' => false, 'wrap-queries' => true]);
        }

        /**
         * @var AbstractPagination|SlidingPagination $pagination
         */
        $query->setHydrationMode($hydrationMode);
        $pagination = $this->container->get('knp_paginator')->paginate(
            $query,
            $filter['page'],
            $filter['limit'],
            $options
        );

        // page range
        $pagination->setPageRange(static::PAGE_RANGE);

        if (is_int($filter['hint'])) {
            $pagination->setTotalItemCount($filter['hint']);
        }

        // create item
        $totalItem = $pagination->getTotalItemCount();

        return [
            Abs::PG_CURRENT_PAGE => $pagination->getCurrentPageNumber(),
            Abs::PG_PAGE_SIZE    => $filter['limit'],
            Abs::PG_TOTAL_PAGE   => ceil($totalItem / $filter['limit']),
            Abs::PG_TOTAL_ITEM   => $totalItem,
            Abs::PG_ITEMS        => $pagination->getItems(),
        ];
    }

    /**
     * List key-value pair
     *
     * @param array           $valueFields
     * @param string          $key
     * @param callable|string $handler
     * @param array           $filter
     *
     * @return array
     */
    public function kvp(array $valueFields, string $key = Abs::PK, $handler = null, array $filter = []): array
    {
        $valueFields = Helper::arrayMap($valueFields, 'kvp.%s');
        array_push($valueFields, "kvp.{$key}");

        $list = $this->filters($filter)->lister(
            [
                'limit'  => 0,
                'alias'  => 'kvp',
                'select' => $valueFields,
                'where'  => [$this->expr->eq('kvp.state', ':state')],
                'args'   => ['state' => [Abs::NORMAL]],
            ]
        );

        $list = Helper::arrayColumn($list, false, $key);

        if (empty($handler)) {
            $handler = array_fill(0, count($valueFields) - 1, '%s');
            $handler = implode(' ', $handler);
        }

        $_list = [];
        foreach ($list as $key => $item) {
            if (is_callable($handler)) {
                $_list[$key] = $handler($item, $key);
            } else {
                $_list[$key] = sprintf((string)$handler, ...array_values($item));
            }
        }

        return $_list;
    }

    /**
     * Updater
     *
     * @param array $filter
     *
     * @return false|int
     * @throws
     */
    public function updater(array $filter)
    {
        $filter = $this->getFilters($filter, ['method' => Abs::UPDATE]);
        $query = $this->getQueryBuilder($filter)->getQuery();

        try {
            return $query->getResult();
        } catch (Throwable $e) {
            return $this->push($e->getMessage());
        }
    }

    /**
     * Deleter
     *
     * @param array $filter
     *
     * @return false|int
     * @throws
     */
    public function deleter(array $filter)
    {
        $filter = $this->getFilters($filter, ['method' => Abs::DELETE]);
        $query = $this->getQueryBuilder($filter)->getQuery();

        try {
            return $query->getResult();
        } catch (Throwable $e) {
            return $this->push($e->getMessage());
        }
    }
}
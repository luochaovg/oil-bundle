<?php

namespace Leon\BswBundle\Component;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Leon\BswBundle\Module\Entity\Abs;
use Exception;

class Fetch
{
    /**
     * @var string
     */
    protected $sql;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @var string
     */
    protected $splitChar;

    /**
     * Fetch constructor.
     *
     * @param bool $debug
     */
    public function __construct(bool $debug = false)
    {
        $this->splitChar = $debug ? Abs::ENTER : Helper::enSpace();
    }

    /**
     * Organize sql
     *
     * @param string $sql
     * @param mixed  $param
     * @param bool   $integer
     *
     * @return Fetch
     */
    public function sql(string $sql, $param = null, bool $integer = false)
    {
        $sql = trim($sql, $this->splitChar);
        $this->sql = trim("{$this->sql}{$this->splitChar}{$sql}", $this->splitChar);

        if (!is_null($param)) {
            $type = $integer ? ParameterType::INTEGER : ParameterType::STRING;
            if (is_array($param)) {
                $this->params = array_merge($this->params, $param);
                $this->types = array_merge($this->types, array_fill(0, count($param), $type));
            } else {
                array_push($this->params, $param);
                array_push($this->types, $type);
            }
        }

        return $this;
    }

    /**
     * Get collect
     *
     * @param Connection $pdo
     * @param bool       $able
     * @param int        $page
     * @param int        $limit
     * @param callable   $handler
     *
     * @return array
     * @throws
     */
    public function collect(
        Connection $pdo,
        bool $able = true,
        int $page = null,
        int $limit = null,
        callable $handler = null
    ) {
        $all = $this->get();

        /**
         * Handler for items
         *
         * @param array $items
         *
         * @return array
         */
        $handleItems = function (array $items) use ($handler) {

            if (!$handler) {
                return $items;
            }

            foreach ($items as &$item) {
                $item = call_user_func_array($handler, [$item]);
            }

            return $items;
        };

        if (!$able) {
            $this->reset();

            return $handleItems($pdo->fetchAll(...$all));
        }

        // need page
        $pagination = $this->pagination($page, $limit);

        $allItems = $all;
        $allItems[0] = "SELECT COUNT(*) AS total FROM ({$allItems[0]}) AS _PAGE";

        $totalItem = $pdo->fetchAll(...$allItems);
        $totalItem = intval(current($totalItem)['total'] ?: 0);
        $this->reset();

        return [
            Abs::PG_CURRENT_PAGE => $page,
            Abs::PG_PAGE_SIZE    => $limit,
            Abs::PG_TOTAL_PAGE   => ceil($totalItem / $limit),
            Abs::PG_TOTAL_ITEM   => $totalItem,
            Abs::PG_ITEMS        => $handleItems($pdo->fetchAll(...$pagination)),
        ];
    }

    /**
     * Pagination
     *
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws
     */
    protected function pagination(int $page = null, int $limit = null): array
    {
        $page = $page < 1 ? 1 : $page;
        $limit = abs($limit);

        if ($page < 1 || $limit < 1) {
            throw new Exception('Both `page` and `limit` should greater than 0');
        }

        $offset = ($page - 1) * $limit;

        $this->sql('LIMIT ?', $limit, true);
        $this->sql('OFFSET ?', $offset, true);

        return $this->get();
    }

    /**
     * Get source
     *
     * @return array
     */
    protected function get(): array
    {
        return [$this->sql, $this->params, $this->types];
    }

    /**
     * Reset source
     *
     * @return Fetch
     */
    public function reset(): Fetch
    {
        $this->sql = null;
        $this->params = [];
        $this->types = [];

        return $this;
    }

    /**
     * Debug
     *
     * @param bool $exit
     */
    public function debug(bool $exit = true)
    {
        dump($this->sql, $this->params, $this->types);
        $exit && exit(0);
    }
}
<?php

namespace Leon\BswBundle\Command;

use App\Module\Entity\Enum;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Repository\BswCommandQueueRepository;
use Symfony\Component\Console\Input\InputOption;

class BswExportPreviewCommand extends ExportCsvCommand
{
    /**
     * @var int
     */
    protected $limit = 100;

    /**
     * @var BswCommandQueueRepository
     */
    protected $missionRepo;

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'mission',
            'keyword' => 'export-preview',
            'info'    => 'Export preview for route',
        ];
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return array_merge(
            parent::args(),
            [
                'entity' => [null, InputOption::VALUE_REQUIRED, 'Entity namespace'],
                'query'  => [null, InputOption::VALUE_REQUIRED, 'Filter query'],
            ]
        );
    }

    /**
     * @throws
     */
    public function init()
    {
        $this->missionRepo = $this->repo(BswCommandQueue::class);
    }

    /**
     * @return bool
     */
    public function pass(): bool
    {
        return true;
    }

    /**
     * @param object $params
     *
     * @return object
     */
    public function params($params)
    {
        $params->entity = base64_decode($params->entity);
        $params->query = Helper::stringToObject($params->query);

        return $params;
    }

    /**
     * @return string|null
     */
    public function entity(): ?string
    {
        return $this->params->entity;
    }

    /**
     * @return array
     * @throws
     */
    public function filter(): array
    {
        return $this->params->query;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    private function enumParser(array $record): array
    {
        $enum = [];
        $enumClass = Enum::class;
        $prefixTable = strtoupper(Helper::tableNameFromCls($this->entity()));

        foreach ($record as $field => $value) {
            $label = strtoupper(Helper::camelToUnder($field));
            if (defined($first = "{$enumClass}::{$label}")) {
                $enum[$field] = $this->web->enumLang(constant($first));
                continue;
            }
            if (defined($second = "{$enumClass}::{$prefixTable}_{$label}")) {
                $enum[$field] = $this->web->enumLang(constant($second));
                continue;
            }
        }

        return $enum;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function handleRecord(array $record): array
    {
        static $enum;
        if (!isset($enum)) {
            $enum = $this->enumParser($record);
        }

        foreach ($record as $field => $value) {
            if (isset($enum[$field])) {
                $record[$field] = $enum[$field][$value];
            }
        }

        return $record;
    }

    /**
     * @param float $percent
     *
     * @return bool
     */
    public function percent(float $percent): bool
    {
        return !!$this->missionRepo->modify(['id' => $this->params->args->id], ['donePercent' => $percent]);
    }

    /**
     * @return void
     */
    public function done()
    {
        if ($this->params->receiver && $this->params->csv) {
            $this->web->telegramSendDocument($this->params->receiver, $this->params->csv);
        }
    }
}
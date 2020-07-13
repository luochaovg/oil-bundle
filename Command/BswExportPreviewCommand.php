<?php

namespace Leon\BswBundle\Command;

use App\Module\Entity\Enum;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Pinyin;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswCommandQueueRepository;
use Symfony\Component\Console\Input\InputOption;
use Exception;

class BswExportPreviewCommand extends ExportCsvCommand
{
    /**
     * @var int
     */
    protected $limit = 5000;

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
            'info'    => 'Export preview by filter',
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

        $title = Pinyin::getPinyin($params->args->title, ' ');
        $title = str_replace(' ', null, ucwords($title));
        $params->csv = "{$title}.csv";

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
     * @return array
     * @throws
     */
    private function enumParser(): array
    {
        $extraArgs = [
            'enumClass'          => Enum::class,
            'doctrinePrefix'     => $this->web->parameter('doctrine_prefix'),
            'doctrinePrefixMode' => $this->web->parameter('doctrine_prefix_mode'),
        ];

        $previewAnnotation = $this->web->getPreviewAnnotation($this->entity(), $extraArgs);
        $enum = Helper::arrayColumn($previewAnnotation, 'enum');
        $enum = array_filter($enum);

        return $enum;
    }

    /**
     * @param array $record
     *
     * @return array|false
     */
    public function handleRecord(array $record)
    {
        static $enum;
        if (!isset($enum)) {
            $enum = $this->enumParser();
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
        return !!$this->missionRepo->modify([Abs::PK => $this->params->args->id], ['donePercent' => $percent]);
    }

    /**
     * @param int $page
     *
     * @return void
     */
    public function done(int $page)
    {
        // Send file to telegram
        if ($this->params->receiver && $page) {
            $this->web->telegramSendDocument($this->params->receiver, $this->params->csv);
        }

        // Upload by manual
        $file = Helper::getFileForUpload($this->params->csv);
        $options = $this->web->uploadOptionByFlag('bsw-export', true);

        try {
            $file = $this->web->uploadOneCore($file, $options);
        } catch (Exception $e) {
            $this->output->writeln("<error> Manual upload file error: {$e->getMessage()} </error>");
        }

        $this->missionRepo->modify([Abs::PK => $this->params->args->id], ['fileAttachmentId' => $file->id]);
    }
}
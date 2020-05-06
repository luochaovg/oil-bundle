<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Symfony\Component\Console\Input\InputOption;

class BswExportPreviewCommand extends ExportCsvCommand
{
    /**
     * @var int
     */
    protected $limit = 300;

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
                'route'    => [null, InputOption::VALUE_REQUIRED, 'The route for export'],
                'filter'   => [null, InputOption::VALUE_REQUIRED, 'Filter query'],
                'receiver' => [null, InputOption::VALUE_OPTIONAL, 'Receiver telegram id, split by comma'],
            ]
        );
    }

    /**
     * @return bool
     */
    public function pass(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function header(): array
    {
        return [];
    }

    /**
     * @param object $params
     *
     * @return object
     */
    public function params($params)
    {
        $params->filter = Helper::jsonArray64($params->filter);

        return $params;
    }

    /**
     * @return void
     */
    public function done()
    {
        parent::done();

        $receiver = $this->params->receiver ?: $this->config('telegram_receiver_export_traffic');
        $this->web->telegramSendDocument($receiver, $this->params->csv);
    }

    /**
     * @return array
     */
    public function lister(): array
    {
        dd($this->params);

        return [];
    }
}
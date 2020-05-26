<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Interfaces\CommandInterface;
use Leon\BswBundle\Repository\BswCommandQueueRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class BswMissionCommand extends Command implements CommandInterface
{
    use BswFoundation;

    /**
     * @return array
     */
    public function args(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'mission',
            'info'    => 'Consumption mission queue',
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
        /**
         * @var BswCommandQueueRepository $mission
         */
        $missionRepo = $this->repo(BswCommandQueue::class);
        $mission = $missionRepo->lister(
            [
                'limit' => 0,
                'where' => [$this->expr->eq('bcq.state', ':state')],
                'args'  => ['state' => [1]],
                'order' => ['bcq.resourceNeed' => Abs::SORT_ASC],
            ]
        );

        if (!$mission) {
            return;
        }

        $maxId = max(array_column($mission, 'id'));
        foreach ($mission as $key => &$m) {
            if ($m['cronType'] == 2) {
                if (date($m['cronDateFormat']) == $m['cronDateValue']) {
                    $m['level'] = 0 + $m['id'];
                } else {
                    unset($mission[$key]);
                }
            } else {
                $m['level'] = $maxId + $m['id'];
            }
        }

        $queue = [];
        $queueResourceNeed = 0;

        $mission = Helper::sortArray($mission, 'level');
        foreach ($mission as $m) {
            if ($m['cronType'] == 2) {
                array_push($queue, $m);
                $queueResourceNeed += $m['resourceNeed'];
            } elseif ($queueResourceNeed < 30) {
                array_push($queue, $m);
                $queueResourceNeed += $m['resourceNeed'];
            }
        }

        if (!$queue) {
            return $output->writeln("<info> None mission in queue after election </info>");
        }

        foreach ($queue as $m) {

            $condition = Helper::parseJsonString($m['condition']);
            if (!empty($condition['args'])) {
                $args = Helper::jsonArray64($condition['args']);
            }

            $condition['receiver'] = $m['telegramReceiver'];
            $condition['args'] = array_merge($args ?? [], $m);

            $_condition = [];
            foreach ($condition as $key => $value) {
                $key = strpos($key, '-') === 0 ? $key : "--{$key}";
                $value = is_array($value) ? Helper::jsonStringify64($value) : $value;
                $_condition[$key] = $value;
            }

            if ($condition['receiver']) {
                $now = date(Abs::FMT_FULL);
                $this->web->telegramSendMessage(
                    $condition['receiver'],
                    "`[{$now}]` Mission running {*id*: `{$m['id']}`, *title*: {$m['title']}}",
                    ['mode' => 'Markdown']
                );
            }

            // begin
            $missionRepo->modify(['id' => $m['id']], ['state' => 2]);

            // run command
            $command = $this->getApplication()->find($m['command']);
            $date = date('Y-m-d H:i');

            try {
                $status = $command->run(new ArrayInput($_condition), $output);
                if ($status === 0) {
                    if ($m['cronReuse']) {
                        $attributes = ['state' => 1, 'donePercent' => 0, 'remark' => "[{$date}] execute success"];
                    } else {
                        $attributes = ['state' => 3];
                    }
                } else {
                    $attributes = ['state' => 4, 'remark' => "[{$date}] exit status: {$status}"];
                }
            } catch (Exception $e) {
                $attributes = ['state' => 4, 'remark' => "[{$date}] {$e->getMessage()}"];
            }

            $missionRepo->modify(['id' => $m['id']], $attributes);

            // send telegram message
            if ($condition['receiver']) {
                $now = date(Abs::FMT_FULL);
                $this->web->telegramSendMessage(
                    $condition['receiver'],
                    "`[{$now}]` Mission done {*id*: `{$m['id']}`, *title*: {$m['title']}}",
                    ['mode' => 'Markdown']
                );
            }
        }
    }
}
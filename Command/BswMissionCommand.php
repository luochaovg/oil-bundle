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
                'limit' => 1,
                'where' => [$this->expr->eq('bcq.state', ':state')],
                'args'  => ['state' => [1]],
                'order' => ['bcq.resourceNeed' => Abs::SORT_ASC],
            ]
        );

        if (!$mission) {
            $output->writeln("<info> None mission in queue </info>");

            return;
        }

        $condition = Helper::parseJsonString($mission['condition']);
        $flag = $mission['remark'] ?: Helper::generateFixedToken(8);
        $condition['--args'] = base64_encode(json_encode(['flag' => $flag]));

        $command = $this->getApplication()->find($mission['command']);
        $command->run(new ArrayInput($condition), $output);

        $receiver = $this->config('telegram_receiver_command_queue');
        if ($receiver) {
            $this->web->telegramSendMessage($receiver, "Mission in process, flag is {$flag}.");
        }
    }
}
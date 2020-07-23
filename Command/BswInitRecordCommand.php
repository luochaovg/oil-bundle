<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\CommandException;
use Leon\BswBundle\Module\Interfaces\CommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BswInitRecordCommand extends Command implements CommandInterface
{
    use BswFoundation;

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'doctrine'       => [null, InputOption::VALUE_OPTIONAL, 'Doctrine database flag'],
            'force'          => [null, InputOption::VALUE_OPTIONAL, 'Force init record again', 'no'],
            'admin-phone'    => [null, InputOption::VALUE_OPTIONAL, 'Admin phone number', '18011112222'],
            'admin-name'     => [null, InputOption::VALUE_OPTIONAL, 'Admin name', 'Master'],
            'admin-password' => [null, InputOption::VALUE_REQUIRED, 'Admin password', 'BSW@2020bbssww'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'init-record',
            'info'    => 'Project initialization record',
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

        $doneFile = "{$project}/.done-init-record";
        if ($params['force'] !== 'yes' && file_exists($doneFile)) {
            throw new CommandException('The command can only be executed once');
        }

        $pdo = $this->pdo($params['doctrine'] ?: 'default');

        // bsw_admin_user

        $pdo->exec('TRUNCATE bsw_admin_user');
        $pdo->insert(
            'bsw_admin_user',
            [
                'phone'    => $params['admin-phone'],
                'name'     => $params['admin-name'],
                'password' => $this->web->password($params['admin-password']),
            ]
        );

        // bsw_admin_menu

        $menu = [
            [0, '', 'b:icon-set', '系统设置', 99],
            [1, 'app_bsw_admin_menu_preview', 'b:icon-navlist', '菜单管理', 9901],
            [1, 'app_bsw_config_preview', 'b:icon-form', '项目配置', 9902],
            [1, 'app_bsw_admin_role_preview', 'b:icon-bussinessman', '后台角色', 9903],
            [1, 'app_bsw_admin_user_preview', 'b:icon-atm', '后台用户', 9904],
        ];

        $pdo->exec('TRUNCATE bsw_admin_menu');
        foreach ($menu as $item) {
            $value = array_combine(['menu_id', 'route_name', 'icon', 'value', 'sort'], $item);
            $pdo->insert('bsw_admin_menu', $value);
        }

        file_put_contents($doneFile, date(Abs::FMT_FULL));
        $output->writeln("<info> \n Project record initialization done.\n </info>");
    }
}
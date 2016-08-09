<?php
namespace DAG\Appetize\Deploy\Command;

use DAG\Appetize\Deploy\API\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ProtectAllBuildsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('disable-all')
            ->addArgument('token', InputArgument::REQUIRED, 'The token provided by Appetize.io');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = new Api($input->getArgument('token'));
        $builds = $api->fetchAll();

        foreach ($builds as $build) {
            $api->protectBuild($build['publicKey']);
        }

        return;
    }
}

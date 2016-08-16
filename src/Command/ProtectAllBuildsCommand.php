<?php
namespace DAG\Appetize\Deploy\Command;

use DAG\Appetize\Deploy\API\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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

        $progress = new ProgressBar($output, count($builds));
        $buildChangedCount = 0;

        foreach ($builds as $build) {
            if (!isset($build['protectedByAccount']) || !$build['protectedByAccount']) {
                $api->protectBuild($build['publicKey']);
                $buildChangedCount++;
            }
            $progress->advance();
            $progress->display();
        }

        $output->writeln("");

        $output->writeln(sprintf('%d builds were changed', $buildChangedCount));
    }
}

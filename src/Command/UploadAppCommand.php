<?php
namespace DAG\Appetize\Deploy\Command;

use DAG\Appetize\Deploy\API\UploadApi;
use DAG\Appetize\Deploy\Archive\AndroidArchive;
use DAG\Appetize\Deploy\Archive\IOSArchive;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UploadAppCommand
 */
final class UploadAppCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('upload')
            ->addArgument('app-path', InputArgument::REQUIRED, 'The path to the .app or .apk')
            ->addArgument('platform', InputArgument::REQUIRED, 'The platform. Either "ios" or "android"')
            ->addArgument('token', InputArgument::REQUIRED, 'The token provided by Appetize.io')
            ->addOption('public-key', null, InputOption::VALUE_REQUIRED, 'A public key to upload to the same app');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // For iOS we have to upload a zip that contains the .app bundle
        // For Android we upload the .apk directly

        $platform = $input->getArgument('platform');
        $appPath = $input->getArgument('app-path');

        if ($platform == 'ios' || $platform == 'android') {
            $output->writeln(sprintf('App found : "%s"', $appPath));

            if ($platform == 'ios') {
                $bundleArchive = new IOSArchive();
                $uploadFilePath = $bundleArchive->create($appPath);
                $output->writeln(sprintf('Archive created in "%s"', $uploadFilePath));
            } else {
                $uploadFilePath = $appPath;
            }

            $uploadApi = new UploadApi();
            $response = $uploadApi->upload(
                $uploadFilePath,
                $input->getArgument('token'),
                $platform,
                $input->getOption('public-key')
            );

            $output->writeln('Upload success');

            $table = new Table($output);
            $table->setHeaders(['Response info', 'Value']);
            $table->addRow(['Public key', $response->getPublicKey()]);
            $table->addRow(['App URL', $response->getAppURL()]);
            $table->render();
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid "%s" platform given', $platform));
        }
    }
}

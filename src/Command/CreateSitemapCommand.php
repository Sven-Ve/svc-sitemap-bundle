<?php

declare(strict_types=1);

/*
 * This file is part of the SvcSitemap bundle.
 *
 * (c) 2026 Sven Vetter <dev@sv-systems.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Command;

use Svc\SitemapBundle\Sitemap\SitemapCreator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * console command for creating sitemap.xml.
 */
#[AsCommand(
    name: 'svc:sitemap:create_xml',
    description: 'Create the sitemap.xml file',
    hidden: false,
    aliases: ['svc_sitemap:create_xml'],
)]
class CreateSitemapCommand extends Command
{
    use LockableTrait;

    public function __construct(
        private readonly SitemapCreator $sitemapCreator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
          ->addOption('path', 'P', InputOption::VALUE_REQUIRED, 'Directory of the sitemap file')
          ->addOption('file', 'F', InputOption::VALUE_REQUIRED, 'Filename of the sitemap file')
          ->addOption('gzip', 'G', InputOption::VALUE_NEGATABLE, 'GZIP the sitemap file', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->lock()) {
            $io->caution('The command is already running in another process.');

            return Command::FAILURE;
        }

        $io->title('Create sitemap');

        try {
            list($urlCount, $realName) = $this->sitemapCreator->writeSitemapXML(
                $input->getOption('path'),
                $input->getOption('file'),
                $input->getOption('gzip')
            );
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            $this->release();

            return Command::FAILURE;
        }

        $io->success("$urlCount urls written in $realName");

        $this->release();

        return Command::SUCCESS;
    }
}

<?php

/*
 * This file is part of the SvcSitemapBundle package.
 *
 * (c) Sven Vetter <https://github.com/Sven-Ve/svc-sitemap-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\SitemapBundle\Command;

use Svc\SitemapBundle\Robots\RobotsCreator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * console command for creating robots.txt.
 */
#[AsCommand(
  name: 'svc:robots.txt:create',
  description: 'Create the robots.txt file',
  hidden: false,
  aliases: ['svc_sitemap:create_robots.txt'],
)]
class CreateRobotsTxtCommand extends Command
{
  use LockableTrait;

  public function __construct(
    private readonly RobotsCreator $robotsCreator,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->addOption('path', 'P', InputOption::VALUE_REQUIRED, 'Directory of the robots.txt file')
      ->addOption('file', 'F', InputOption::VALUE_REQUIRED, 'Filename of the robots.txt file')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    if (!$this->lock()) {
      $io->caution('The command is already running in another process.');

      return Command::FAILURE;
    }

    $io->title('Create robots.txt');

    try {
      list($urlCount, $realName) = $this->robotsCreator->writeRobotsTxt(
        $input->getOption('path'),
        $input->getOption('file'),
      );
    } catch (\Exception $e) {
      $io->error($e->getMessage());

      $this->release();

      return Command::FAILURE;
    }

    $io->success("$urlCount user agents written in $realName");

    $this->release();

    return Command::SUCCESS;
  }
}

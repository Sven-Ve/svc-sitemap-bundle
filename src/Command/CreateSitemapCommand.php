<?php

namespace Svc\SitemapBundle\Command;

use Svc\SitemapBundle\Exception\LogExceptionInterface;
use Svc\SitemapBundle\Service\SitemapCreator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * command to fill locations.
 *
 * @author Sven Vetter <dev@sv-systems.com>
 */
#[AsCommand(
  name: 'svc:sitemap:create_xml',
  description: 'Create the sitemap.xml file',
  hidden: false
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
      ->addOption('force', 'f', InputOption::VALUE_NONE, 'Reload all empty countries');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    if (!$this->lock()) {
      $io->caution('The command is already running in another process.');

      return Command::FAILURE;
    }

    $io->title('Create sitemap.xml');
    $force = $input->getOption('force');

    try {
      $urlCount = $this->sitemapCreator->writeSitemapXML();
    } catch (LogExceptionInterface $e) {
      $io->error($e->getReason());

      $this->release();

      return Command::FAILURE;
    } catch (\Exception $e) {
      $io->error($e->getMessage());

      $this->release();

      return Command::FAILURE;
    }

    $io->success("$urlCount urls written in sitemap.xml");

    $this->release();

    return Command::SUCCESS;
  }
}

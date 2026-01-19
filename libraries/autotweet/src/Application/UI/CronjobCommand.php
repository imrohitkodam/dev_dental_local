<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

namespace PerfectPublisher\Application\UI;

use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Joomla\CMS\Language\Text;

\defined('JPATH_PLATFORM') || die;

class CronjobCommand extends AbstractCommand
{
    protected static $defaultName = 'perfect-publisher:cronjob';

    private $ioStyle;

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->configureIO($input, $output);
        $this->ioStyle->title('Execute social posts publishing tasks');

        // Disable caching.
        $config = \Joomla\CMS\Factory::getConfig();
        $config->set('caching', 0);
        $config->set('cache_handler', 'file');

        // Starting Indexer.
        $this->ioStyle->comment(Text::_('AUTOTWEET_CLI_STARTING_PROCESS'));

        // Remove the script time limit.
        @set_time_limit(0);

        // Initialize the time value
        $timetrack = microtime(true);

        $this->publish();

        // Total reporting.
        $this->ioStyle->comment(Text::sprintf('AUTOTWEET_CLI_PROCESS_COMPLETE', round(microtime(true) - $timetrack, 3)));

        return Command::SUCCESS;
    }

    private function configureIO(InputInterface $input, OutputInterface $output)
    {
        $this->ioStyle = new SymfonyStyle($input, $output);
    }

    protected function configure(): void
    {
        $help = "<info>%command.name%</info> execute social posts publishing tasks
		\nUsage: <info>php %command.full_name%</info>";

        $this->setDescription('Execute social posts publishing tasks');
        $this->setHelp($help);
    }

    private function publish()
    {
        $max_posts = \EParameter::getComponentParam(CAUTOTWEETNG, 'max_posts', 1);
        \PostShareManager::postQueuedMessages($max_posts);

        $instance = \CronjobHelper::getInstance();
        $instance->publishPosts();

        if (\EParameter::getComponentParam(CAUTOTWEETNG, 'feeds_enabled', false)) {
            \FeedLoaderHelper::importFeeds();
        }

        $instance->contentPolling();
    }
}

<?php
/**
 * Tags Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2022, numero2 - Agentur für digitales Marketing GbR
 */

namespace numero2\TagsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Contao\Database;
use Contao\System;


class PurgeContaoTagsCommand extends Command
{

    // use : vendor/bin/contao-console numero2:purgeContaoTags
    protected static $defaultName = 'numero2:purgeContaoTags';
    protected static $defaultDescription = 'Delete unused Tags';

    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
        $this->framework->initialize();

        parent::__construct();
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $db=$this->framework->createInstance(Database::getInstance());

        $sql='DELETE tl_tags FROM tl_tags 
            LEFT JOIN tl_tags_rel ON tl_tags.id = tl_tags_rel.tag_id
            WHERE tl_tags_rel.tag_id IS NULL';
        $db->execute($sql);
        System::getContainer()->get('monolog.logger.contao.cron')->info('Purged Contao-tags via console');
        $output->writeln('Unused Tags deleted');
        return Command::SUCCESS;
    }

}
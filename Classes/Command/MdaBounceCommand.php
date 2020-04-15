<?php

namespace Ecodev\Newsletter\Command;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Newsletter function for handling bounce mails
 */
class MdaBounceCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this
            ->setDescription('Bounce mail handling MDA for fetchmail')
            ->addArgument(
                'rawmailsource',
                InputArgument::OPTIONAL,
                'Raw mail source'
            );
    }

    /**
     * Executes the command for handling the mail source
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $output = 'Handling raw mail source.';

        // Read piped mail raw source
        $content = '';
        if ($filename = $input->hasArgument('rawmailsource')) {
            $content = $input->getArgument('rawmailsource');
        } else if (0 === ftell(STDIN)) {
            while (!feof(STDIN)) {
                $content .= fread(STDIN, 1024);
            }
        } else {
            throw new \RuntimeException("Raw mail source missing: Provide it as argument or pipe it from fetchmail to STDIN.");
        }

        // Dispatch it to analyze its bounce level an take appropriate action
        $bounceHandler = GeneralUtility::makeInstance(
            'Ecodev\\Newsletter\\BounceHandler',
            $content
        );
        $bounceHandler->dispatch();

        $io->text($output);
    }
}

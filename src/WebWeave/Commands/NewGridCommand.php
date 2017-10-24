<?php

namespace WebWeave\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use WebWeave\Templates\Template;
use WebWeave\Utils\Utils;

class NewGridCommand extends Command
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem $fs
     */
    protected $fs;

    protected $targetModule;

    protected $event;

    protected $observerName;

    protected $observerInstance;

    protected function configure()
    {
        $this->setName("grid:new")
            ->setDescription("Creates a new grid (Models need to be made first)");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->fs = new Filesystem();
        $helper = $this->getHelper('question');

        $utils = new Utils();
        $modules = $utils->getAllModules();

        $question = new ChoiceQuestion(
            'Which module should we use?',
            $modules,
            0
        );

        $question->setErrorMessage('Module %s is invalid.');

        $this->targetModule = $helper->ask($input, $output, $question);

        $output->writeln("First we will create the route");

        $question = new Question('Please enter the name of the bundle');

        $routeName = $helper->ask($input, $output, $question);

    }


}
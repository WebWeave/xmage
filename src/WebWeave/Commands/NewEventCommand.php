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
use WebWeave\Templates\Template;
use WebWeave\Utils\Utils;

class NewEventCommand extends Command
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
        $this->setName("new:event")
            ->setDescription("Creates a new event")
            ->addArgument("event", InputArgument::REQUIRED, "Event name")
            ->addArgument("observer_name", InputArgument::REQUIRED, "Observer name")
            ->addArgument("observer_instance", InputArgument::REQUIRED, "Observer Instace");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->event = $input->getArgument('event');
        $this->observerName = $input->getArgument('observer_name');
        $this->observerInstance = $input->getArgument('observer_instance');

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

        if (!$this->checkIfFirst()) {

            $EventsTemplate = new Template();
            $EventsTemplate->setTemplate('events.xml.html');
            $EventsTemplate->setVars($this->event, 'EVENT');
            $EventsTemplate->setVars($this->observerName, 'OBSERVER_NAME');
            $EventsTemplate->setVars($this->observerInstance, 'OBSERVER');

            $this->fs->dumpFile('app/code/'.str_replace('_', '/', $this->targetModule).'/etc/events.xml', $EventsTemplate->currentTemplate);

        }
    }

    protected function checkIfFirst()
    {
        return $this->fs->exists('app/code/' . str_replace('_', '/', $this->targetModule) . '/etc/events.xml');
    }

}
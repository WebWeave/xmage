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
use WebWeave\Utils\Utils;
use WebWeave\Templates\Template;

/**
 * Class NewModelsCommand
 * Creates new modules
 */
class NewModelsCommand extends Command
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem $fs
     */
    protected $fs;

    protected $table;

    protected $targetModule;

    protected $targetName;

    protected $primaryKey;


    protected function configure()
    {
        $this->setName("models:new:all")
            ->setDescription("Creates models and collection for table")
            ->addArgument("Table", InputArgument::REQUIRED, "Table name")
            ->addArgument("Primary Key", InputArgument::REQUIRED, "Primary Key")
            ->addArgument("Model Name", InputArgument::REQUIRED, "Model Name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fs = new Filesystem();
        $this->table = $input->getArgument("Table");
        $this->targetName = $input->getArgument("Model Name");
        $this->primaryKey = $input->getArgument('Primary Key');

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

        $this->createModel();
        $this->createResourceModel();
        $this->createCollection();

        $output->writeln("Models, resourceModels and collection created. :)");
    }

    protected function createModel()
    {
        $vendor_package = explode("_", $this->targetModule);

        $modelTemplate = new Template();

        $modelTemplate->setTemplate('Model.php.html');
        $modelTemplate->setVars($vendor_package[0], 'VENDOR');
        $modelTemplate->setVars($vendor_package[1], 'PACKAGE');
        $modelTemplate->setVars($this->targetName, 'MODEL_NAME');
        $modelTemplate->setVars($vendor_package[0]."\\".$vendor_package[1].'\Model\ResourceModel\\'.$this->targetName, 'RESOURCE_MODEL');

        $this->fs->dumpFile('app/code/'.$vendor_package[0].'/'.$vendor_package[1].'/Model/'.$this->targetName.'.php', $modelTemplate->currentTemplate);

    }

    protected function createResourceModel()
    {
        $vendor_package = explode("_", $this->targetModule);

        $resourceModelTemplate = new Template();

        $resourceModelTemplate->setTemplate('ResourceModel.php.html');
        $resourceModelTemplate->setVars($vendor_package[0], 'VENDOR');
        $resourceModelTemplate->setVars($vendor_package[1], 'PACKAGE');
        $resourceModelTemplate->setVars($this->targetName, 'MODEL_NAME');
        $resourceModelTemplate->setVars($this->table, 'TABLE_NAME');
        $resourceModelTemplate->setVars($this->primaryKey, 'PRIMARY_KEY');

        $this->fs->dumpFile('app/code/'.$vendor_package[0].'/'.$vendor_package[1].'/Model/ResourceModel/'.$this->targetName.'.php', $resourceModelTemplate->currentTemplate);

    }

    protected function createCollection()
    {
        $vendor_package = explode("_", $this->targetModule);

        $collectionTemplate = new Template();

        $collectionTemplate->setTemplate('Collection.php.html');
        $collectionTemplate->setVars($vendor_package[0], 'VENDOR');
        $collectionTemplate->setVars($vendor_package[1], 'PACKAGE');
        $collectionTemplate->setVars($this->targetName, 'MODEL_NAME');

        $this->fs->dumpFile('app/code/'.$vendor_package[0].'/'.$vendor_package[1].'/Model/ResourceModel/'.$this->targetName.'/Collection.php', $collectionTemplate->currentTemplate);
    }

}
<?php

namespace WebWeave\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use WebWeave\Templates\Template;

/**
 * Class NewModuleCommand
 * Creates new modules
 */
class NewModuleCommand extends Command
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem $fs
     */
    protected $fs;

    protected $vendor;

    protected $package;

    protected function configure()
    {
        $this->setName("module:new")
            ->setDescription("Creates a new module")
            ->addArgument("Vendor", InputArgument::REQUIRED, "Vendor name")
            ->addArgument("Package", InputArgument::REQUIRED, "Package name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->vendor = $input->getArgument('Vendor');
        $this->package = $input->getArgument('Package');

        $this->fs = new Filesystem();

        $this->createPackageDirs();

        $this->createModuleFiles();

        $output->writeln("Module created :)");

    }

    protected function createModuleFiles()
    {
        $registrationTemplate = new Template();
        $moduleTemplate = new Template();

        $registrationTemplate->setTemplate('registration.php.html');
        $registrationTemplate->setVars($this->vendor, 'VENDOR');
        $registrationTemplate->setVars($this->package, 'PACKAGE');

        $moduleTemplate->setTemplate('module.xml.html');
        $moduleTemplate->setVars($this->vendor, 'VENDOR');
        $moduleTemplate->setVars($this->package, 'PACKAGE');

        $this->fs->dumpFile('app/code/'.$this->vendor.'/'.$this->package.'/registration.php', $registrationTemplate->currentTemplate);
        $this->fs->dumpFile('app/code/'.$this->vendor.'/'.$this->package.'/etc/module.xml', $moduleTemplate->currentTemplate);
    }

    protected function createPackageDirs()
    {
        try {
            $this->fs->mkdir('app/code/'.$this->vendor.'/'.$this->package);
        } catch (IOException $IOException) {
            $IOException->getMessage();
        }
    }

}
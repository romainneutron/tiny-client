<?php

namespace Tiny\Command;

use Symfony\Component\Console\Command\Command as SymfoCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Config extends SymfoCommand
{
    protected $apiKey;
    
    protected function configure()
    {
        $this->setDescription("Edit your tinypng api key");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sampleConfFile = __DIR__ . '/../../../config/api.key.conf.sample.yml';
        $confFile = __DIR__ . '/../../../config/api.key.conf.yml';
        
        $dialog = $this->getHelperSet()->get('dialog');
        
        $this->apiKey = $dialog->ask(
            $output,
            '<question>Please enter your tinypng API KEY</question>',
            'YOUR_API_KEY'
        );
        
        try {
            if (!file_exists($confFile) && false === @copy($sampleConfFile, $confFile)) {
                throw new \Exception('Error wile copying the configuration file');
            }
        
            $config = Yaml::parse($confFile);
        
            $config['api_key'] = $this->apiKey;

            file_put_contents($confFile, Yaml::dump($config));
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                "<error>Your api key could not be saved."
                . " Reason is #%s</error>",
                $e->getMessage()
            ));
            
            return 1;
        }
        
        $output->writeln("<info>Your api key has been successfully saved</info>");
        
        return 0;
    }
    
    public function getApiKey() 
    {
        return $this->apiKey;
    }
}

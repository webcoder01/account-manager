<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

class DuplicateBudgetCommand extends Command
{
    protected static $defaultName = 'app:duplicate-budget';
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Duplicate budget(s) for the next month')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime();
        $io = new SymfonyStyle($input, $output);
        $con = $this->em->getConnection();
        $stmt = $con->prepare('SELECT * FROM mo.duplicate_budget(:date)');
        $stmt->bindValue('date', $date->format('Y-m-d'), \PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchAll()[0];
        $stmt->closeCursor();

        if(0 < $count['duplicate_budget']) {
            $io->success($count['duplicate_budget'] . ' rows have been created.');
        }
        else {
            $io->caution('No row have been created.');
        }
    }
}

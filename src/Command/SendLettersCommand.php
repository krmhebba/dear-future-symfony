<?php

namespace App\Command;

use App\Repository\LetterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-letters',
    description: 'Vérifie les dates et marque les lettres comme envoyées',
)]
class SendLettersCommand extends Command
{
    private $letterRepository;
    private $entityManager;

    public function __construct(LetterRepository $letterRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->letterRepository = $letterRepository;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $now = new \DateTimeImmutable();

        $letters = $this->letterRepository->findBy(['isSent' => false]);
        
        $count = 0;

        foreach ($letters as $letter) {
            if ($letter->getSendDate() <= $now) {
                $letter->setIsSent(true); 
                $count++;
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d lettres ont été délivrées vers le futur !', $count));

        return Command::SUCCESS;
    }
}
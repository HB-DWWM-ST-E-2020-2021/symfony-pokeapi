<?php

namespace App\Command;

use App\PokeAPI\AbstractApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractDataCommand extends Command
{
    private string $element;
    private AbstractApi $abstractApi;
    private EntityManagerInterface $em;

    public function __construct(string $element)
    {
        parent::__construct();
        $this->element = $element;
    }

    /** @required */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    protected function setAbstractApi(AbstractApi $abstractApi): void
    {
        $this->abstractApi = $abstractApi;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->throwIfCannotLoad();
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        if ($this->abstractApi->checkIfCollectionIsInitialized()) {
            $io->success(sprintf('"%s" already loaded. You can start using your application.', $this->element));
        } else {
            $io->note(sprintf('Start loading "%s" from PokeAPI...', $this->element));
            $collection = $this->abstractApi->getCollection();
            $count = count($collection);

            $io->note(sprintf(
                'Collection of "%s" loaded. %d "%s" to charges in DB.',
                $this->element,
                $count,
                $this->element
            ));

            $io->progressStart($count);
            foreach ($collection as $key => $element) {
                $this->em->persist($this->abstractApi->convertPokeApiToElement($element));
                $io->progressAdvance();

                if (0 === $key % 10) {
                    $this->em->flush();
                }
            }

            $this->em->flush();
            $io->progressFinish();

            $io->success(sprintf(
                '%d "%s" fully charged in your DB. You can start using your application.',
                $count,
                $this->element
            ));
        }

        return Command::SUCCESS;
    }

    /**
     * Override this method to check if elements can be loaded.
     */
    protected function throwIfCannotLoad(): void
    {
    }
}

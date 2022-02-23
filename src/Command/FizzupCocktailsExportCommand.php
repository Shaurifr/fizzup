<?php

namespace App\Command;

use App\Repository\CocktailRepository;
use League\Csv\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

class FizzupCocktailsExportCommand extends Command
{
    protected static $defaultName = 'fizzup:cocktails:export';
    protected static $defaultDescription = 'Exporte les cocktails en BDD au format csv';

    private CocktailRepository $cocktailRepository;
    private string $projectDir;

    public function __construct(
        CocktailRepository $cocktailRepository,
        string $projectDir
    )
    {
        $this->cocktailRepository = $cocktailRepository;
        $this->projectDir = $projectDir;
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('userIds', InputArgument::IS_ARRAY, 'Identifiant d\'un utilisateur')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'limite de cocktails dans l\'export')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($userIds = $input->getArgument('userIds')) {
            $io->note(sprintf('on n\'exporte que les cocktails du.es user.s %s.', implode(', ', $userIds)));
        } else {
            $io->note('on exporte les cocktails de tous les users.');
        }

        if ($limit = $input->getOption('limit')) {
            $io->note('You asked for '.$limit.' cocktails maximum.');
        }

        $cocktails = $this->cocktailRepository->findCocktailsByUser($userIds, $limit);

        // $records ira dans le fichier au format csv
        $records = [];
        // générer un csv.
        $headers = [
            'id',
            'name',
            'price',
            'hasAlcohol',
            'origin',
            'user',
        ];
        $records[] = $headers;
        $rows = [];
        foreach ($cocktails as $cocktail) {
            $row = [
                $cocktail->getId(),
                $cocktail->getName(),
                $cocktail->getPrice(),
                $cocktail->getHasAlcohol(),
                $cocktail->getOrigin(),
                $cocktail->getUser() ? $cocktail->getUser()->getFirstName() : '',
            ];
            $rows[] = $row;
            $records[] = $row;
        }
        $io->table($headers, $rows);

        // écrire dans un fichier le csv
        $path = $this->projectDir.'/public/csv/'.Uuid::v4().'.csv'; // chemin accessible depuis le web
        $writer = Writer::createFromPath($path, 'w+');
        $writer->insertAll($records);

        $io->success('All the cocktails have been exported');

        return Command::SUCCESS;
    }
}

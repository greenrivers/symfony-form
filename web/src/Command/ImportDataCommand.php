<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\Data;
use App\Serializer\Serializer;
use App\Service\FileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-data',
    description: 'Import data from CSV file.',
)]
class ImportDataCommand extends Command
{
    private const FILE_PATH_OPTION_NAME = 'filePath';

    public function __construct(
        private readonly FileService         $fileService,
        private readonly MessageBusInterface $messageBus,
        private readonly Serializer          $serializer
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::FILE_PATH_OPTION_NAME, InputArgument::REQUIRED, 'Path to CSV file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument(self::FILE_PATH_OPTION_NAME);

        if ($filePath) {
            if (!$this->fileService->fileExists($filePath)) {
                throw new FileNotFoundException('File not found.');
            }

            if (!$this->fileService->checkExtension($filePath, 'csv')) {
                throw new ExtensionFileException('Invalid file extension.');
            }

            $file = $this->fileService->getCsvFile($filePath);
            $reader = $this->fileService->getReader($file);
            $numberOfRows = $this->fileService->getNumberOfRows($file);

            $progressBar = new ProgressBar($output, $numberOfRows - 1);
            $progressBar->start();

            foreach ($reader as $row) {
                $data = $this->serializer->denormalize($row, Data::class);
                $this->messageBus->dispatch($data);
                $progressBar->advance();
            }

            $progressBar->finish();
        }

        $io->success('File import success.');

        return Command::SUCCESS;
    }
}

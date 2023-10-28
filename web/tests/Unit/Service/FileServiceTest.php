<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\FileService;
use LimitIterator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;

class FileServiceTest extends TestCase
{
    private FileService $fileService;

    private vfsStreamDirectory $rootDir;

    private string $filePath;

    protected function setUp(): void
    {
        $this->rootDir = vfsStream::setup();
        $structure = [
            'files' => [
                'test.csv' => $this->getCsvContent()
            ]
        ];
        vfsStream::create($structure, $this->rootDir);
        $this->filePath = vfsStream::url('root/files/test.csv');

        $filesystem = new Filesystem();
        $this->fileService = new FileService($filesystem);
    }

    /**
     * @covers FileService::fileExists
     */
    public function testFileExists(): void
    {
        $this->assertTrue($this->fileService->fileExists($this->filePath));
    }

    /**
     * @covers FileService::checkExtension
     */
    public function testCheckExtension(): void
    {
        $this->assertEquals('csv', $this->fileService->checkExtension($this->filePath, 'csv'));
    }

    /**
     * @covers FileService::getCsvFile
     */
    public function testGetCsvFile(): void
    {
        $streamFile = $this->rootDir->getChild('root/files/test.csv');
        $streamFileContent = $streamFile->getContent();

        $file = $this->fileService->getCsvFile($this->filePath);
        $content = $file->fread($file->getSize());

        $this->assertInstanceOf(SplFileObject::class, $file);
        $this->assertEquals($streamFileContent, $content);
    }

    /**
     * @covers FileService::getNumberOfRows
     */
    public function testGetNumberOfRows(): void
    {
        $file = new SplFileObject($this->filePath, 'r');

        $this->assertEquals(2, $this->fileService->getNumberOfRows($file));
    }

    /**
     * @covers FileService::getReader
     */
    public function testGetReader(): void
    {
        $file = new SplFileObject($this->filePath, 'r');

        $this->assertInstanceOf(LimitIterator::class, $this->fileService->getReader($file));
    }

    private function getCsvContent(): string
    {
        $data = [
            [
                'id',
                'product',
                'category',
                'manufacturer_company',
                'manufacturer_tax_id',
                'manufacturer_city',
                'manufacturer_postcode',
                'manufacturer_street',
                'manufacturer_street_number'
            ],
            [
                '1',
                'Test product',
                '99.99',
                'Test category',
                'Test manufacturer company',
                '123456789',
                'Test manufacturer city',
                '12-345',
                'Test manufacturer street',
                '10'
            ]
        ];

        return implode(
            PHP_EOL,
            array_map(static fn($row) => implode(',', $row), $data)
        );
    }
}

<?php

/*
 * Import CSV Class
 */
namespace App\Imports;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Illuminate\Support\Collection;

/**
 * version 1.0
 */
class ImportCSV
{
    private string $path;
    private string $delimeter;

    /**
     * @param $path
     * @param string $delimeter
     */
    public function __construct($path, string $delimeter = ',')
    {
        $this->path = $path;
        $this->delimeter = $delimeter;
    }

    /**
     * @return Collection
     *
     * @throws IOException
     * @throws ReaderNotOpenedException
     */
    public function readFile(): Collection
    {
        $reader = ReaderEntityFactory::createCSVReader();
        $reader->setFieldDelimiter($this->delimeter);
        $reader->open($this->path);

        $data = [];
        $first = true;
        $keys = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($first) {
                    $keys = $row->toArray();
                    $first = false;
                } else {
                    $data[] = array_combine($keys, $row->toArray());
                }
            }
        }

        return collect($data);
    }
}

<?php

declare(strict_types=1);

namespace Slam\PhpSpreadsheetHelper;

use Countable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class Table implements Countable
{
    private Worksheet $activeSheet;
    private ?int $dataRowStart = null;
    private int $rowStart;
    private int $rowEnd;
    private int $rowCurrent;
    private int $columnStart;
    private int $columnEnd;
    private int $columnCurrent;
    private string $heading;
    private iterable $data;
    private ?ColumnCollectionInterface $columnCollection = null;
    private bool $freezePanes                            = true;
    private int $fontSize                                = 8;
    private ?int $rowHeight                              = null;
    private bool $textWrap                               = false;
    private ?array $writtenColumnTitles                  = null;
    private ?int $count                                  = null;

    public function __construct(Worksheet $activeSheet, int $row, int $column, string $heading, iterable $data)
    {
        $this->activeSheet = $activeSheet;

        $this->rowStart   =
        $this->rowEnd     =
        $this->rowCurrent =
            $row
        ;

        $this->columnStart   =
        $this->columnEnd     =
        $this->columnCurrent =
            $column
        ;

        $this->heading = $heading;

        $this->data = $data;
    }

    public function getActiveSheet(): Worksheet
    {
        return $this->activeSheet;
    }

    public function getDataRowStart(): ?int
    {
        return $this->dataRowStart;
    }

    public function flagDataRowStart(): void
    {
        $this->dataRowStart = $this->rowCurrent;
    }

    public function getRowStart(): int
    {
        return $this->rowStart;
    }

    public function getRowEnd(): int
    {
        return $this->rowEnd;
    }

    public function getRowCurrent(): int
    {
        return $this->rowCurrent;
    }

    public function incrementRow(): void
    {
        ++$this->rowCurrent;

        $this->rowEnd = \max($this->rowEnd, $this->rowCurrent);
    }

    public function getColumnStart(): int
    {
        return $this->columnStart;
    }

    public function getColumnEnd(): int
    {
        return $this->columnEnd;
    }

    public function getColumnCurrent(): int
    {
        return $this->columnCurrent;
    }

    public function incrementColumn(): void
    {
        ++$this->columnCurrent;

        $this->columnEnd = \max($this->columnEnd, $this->columnCurrent);
    }

    public function resetColumn(): void
    {
        $this->columnCurrent = $this->columnStart;
    }

    public function getHeading(): string
    {
        return $this->heading;
    }

    public function getData(): iterable
    {
        return $this->data;
    }

    public function setColumnCollection(?ColumnCollectionInterface $columnCollection): void
    {
        $this->columnCollection = $columnCollection;
    }

    public function getColumnCollection(): ?ColumnCollectionInterface
    {
        return $this->columnCollection;
    }

    public function setFreezePanes(bool $freezePanes): void
    {
        $this->freezePanes = $freezePanes;
    }

    public function getFreezePanes(): bool
    {
        return $this->freezePanes;
    }

    public function setFontSize(int $fontSize): void
    {
        $this->fontSize = $fontSize;
    }

    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    public function setRowHeight(?int $rowHeight): void
    {
        $this->rowHeight = $rowHeight;
    }

    public function getRowHeight(): ?int
    {
        return $this->rowHeight;
    }

    public function setTextWrap(bool $textWrap): void
    {
        $this->textWrap = $textWrap;
    }

    public function getTextWrap(): bool
    {
        return $this->textWrap;
    }

    public function setWrittenColumnTitles(?array $writtenColumnTitles): void
    {
        $this->writtenColumnTitles = $writtenColumnTitles;
    }

    public function getWrittenColumnTitles(): ?array
    {
        return $this->writtenColumnTitles;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function count(): int
    {
        if (null === $this->count) {
            throw new Exception\RuntimeException('Workbook must set count on table');
        }

        return $this->count;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function splitTableOnNewWorksheet(): self
    {
        $newTable = new self(
            $this->activeSheet->getParent()->createSheet(),
            0,
            $this->getColumnStart(),
            $this->getHeading(),
            $this->getData()
        );
        $newTable->setColumnCollection($this->getColumnCollection());
        $newTable->setFreezePanes($this->getFreezePanes());

        return $newTable;
    }
}

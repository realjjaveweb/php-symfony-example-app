<?php

declare(strict_types=1);

namespace App\Repository\Common;

trait MetadataAccess
{
    public function getTableName(): string
    {
        return $this->getClassMetadata()->getTableName();
    }

    public function getColumnName(string $fieldName): string
    {
        return $this->getClassMetadata()->getColumnName($fieldName);
    }
}

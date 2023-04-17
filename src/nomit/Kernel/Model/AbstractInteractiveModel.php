<?php

namespace nomit\Kernel\Model;

use nomit\Database\Table\Selection;
use nomit\Exception\LogicException;

abstract class AbstractInteractiveModel extends AbstractModel implements InteractiveModelInterface
{

    public function insert(array $data, string $table = null): int|bool
    {
        if(!$table) {
            if(!isset($this->table)) {
                throw new LogicException(sprintf('A table argument must be defined, either at the time of method calling or in the object itself via the "$table" property, in order for the "%s" command to be ran.', __METHOD__));
            }

            $table = $this->table;
        }

        if($this->database
            ->query('INSERT INTO ?name ?', $table, $data)
            ->getRowCount() > 0
        ) {
            return $this->database->getInsertId();
        }

        return false;
    }

    public function update(array $data, mixed $identifier, string $identifierKey = 'id', string $table = null): bool
    {
        if(!$table) {
            if(!isset($this->table)) {
                throw new LogicException(sprintf('A table argument must be defined, either at the time of method calling or in the object itself via the "$table" property, in order for the "%s" command to be ran.', __METHOD__));
            }

            $table = $this->table;
        }

        return $this->database
            ->query('UPDATE ?name SET ? WHERE ?', $table, $data, [
                $identifierKey => $identifier
            ])
            ->getRowCount() > 0;
    }

    public function delete(mixed $identifier, string $identifierKey = 'id', string $table = null): bool
    {
        if(!$table) {
            if(!isset($this->table)) {
                throw new LogicException(sprintf('A table argument must be defined, either at the time of method calling or in the object itself via the "$table" property, in order for the "%s" command to be ran.', __METHOD__));
            }

            $table = $this->table;
        }

        return $this->database
            ->query('DELETE FROM ?name WHERE ?', $table, [
                $identifierKey => $identifier
            ])
            ->getRowCount() > 0;
    }

    protected function limit(Selection $query, int $limit = null, int $index = 0): array
    {
        if($limit !== null) {
            $query->limit($limit, $index);
        }

        return $query->fetchAll();
    }

}
<?php

namespace IKTO\PgiMigrationDirectories\Adapter;

use IKTO\PgI\Database\ConvenientDatabaseInterface as PgiConvenientDatabaseInterface;
use IKTO\PgI\Database\DatabaseInterface as PgiDatabaseInterface;
use IKTO\PgMigrationDirectories\Adapter\ConnectionAdapterInterface;

class PgiConnectionAdapter implements ConnectionAdapterInterface
{
    /**
     * @var PgiDatabaseInterface|PgiConvenientDatabaseInterface
     */
    protected $dbh;

    public function __construct(PgiConvenientDatabaseInterface $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * {@inheritdoc}
     */
    public function openTransaction()
    {
        $this->dbh->beginWork();
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction()
    {
        $this->dbh->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()
    {
        $this->dbh->rollback();
    }

    /**
     * {@inheritdoc}
     */
    public function executeSqlCommand($sqlCommand)
    {
        $this->dbh->doQuery($sqlCommand);
    }

    /**
     * {@inheritdoc}
     */
    public function tableExists($tableName, $tableSchema = null)
    {
        $args = [$tableName];
        $sql = 'SELECT EXISTS (';
        $sql .= 'SELECT 1 FROM "information_schema"."tables" WHERE "table_name" = $1';
        if ($tableSchema) {
            $sql .= ' AND "table_schema" = $2';
            $args[] = $tableSchema;
        }
        $sql .= ')';

        [$exists] = $this->dbh->selectRowArray($sql, [], $args);

        return $exists;
    }

    /**
     * {@inheritdoc}
     */
    public function recordExists($criteria, $tableName, $tableSchema = null)
    {
        $this->populateWhere($criteriaSql, $criteriaArguments, $criteria);
        $sql = 'SELECT EXISTS (SELECT 1 FROM '.$this->getTableLiteral($tableName, $tableSchema).' WHERE '.implode(' AND ', $criteriaSql).')';
        [$exists] = $this->dbh->selectRowArray($sql, [], $criteriaArguments);

        return $exists;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordValues($fieldNames, $criteria, $tableName, $tableSchema = null)
    {
        $this->populateWhere($criteriaSql, $criteriaArguments, $criteria);
        $fieldsSql = [];
        foreach ($fieldNames as $fieldName) {
            $fieldsSql[] = '"'.$fieldName.'"';
        }
        $sql = 'SELECT '.implode(', ', $fieldsSql).' FROM '.$this->getTableLiteral($tableName, $tableSchema).' WHERE '.implode(' AND ', $criteriaSql);

        return $this->dbh->selectColArray($sql, [], $criteriaArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function insertRecord($values, $tableName, $tableSchema = null)
    {
        $fieldNamesSql = [];
        $fieldValuesSql = [];
        $fieldsArguments = [];
        $n = 1;
        foreach ($values as $fieldName => $fieldValue) {
            $fieldNamesSql[] = '"'.$fieldName.'"';
            $fieldValuesSql[] = '$'.($n++);
            $fieldsArguments[] = $fieldValue;
        }
        $sql = 'INSERT INTO '.$this->getTableLiteral($tableName, $tableSchema).' ('.implode(', ', $fieldNamesSql).') VALUES ('.implode(', ', $fieldValuesSql).')';
        $this->dbh->doQuery($sql, [], $fieldsArguments);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord($values, $criteria, $tableName, $tableSchema = null)
    {
        $fieldsSql = [];
        $fieldsArguments = [];
        $n = 1;
        foreach ($values as $fieldName => $fieldValue) {
            $fieldsSql[] = '"'.$fieldName.'" = $'.($n++);
            $fieldsArguments[] = $fieldValue;
        }
        $this->populateWhere($criteriaSql, $criteriaArguments, $criteria, $n);
        $sql = 'UPDATE '.$this->getTableLiteral($tableName, $tableSchema).' SET '.implode(', ', $fieldsSql).' WHERE '.implode(' AND ', $criteriaSql);
        $this->dbh->doQuery($sql, [], array_merge($fieldsArguments, $criteriaArguments));
    }

    /**
     * Gets table literal (for using in queries).
     *
     * @return string
     */
    protected function getTableLiteral($tableName, $tableSchemaName = null)
    {
        $tableLiteral = '"'.$tableName.'"';

        if ($tableSchemaName) {
            $tableLiteral = '"'.$tableSchemaName.'".'.$tableLiteral;
        }

        return $tableLiteral;
    }

    /**
     * @param array $sql
     * @param array $args
     * @param array $criteria
     * @param int $n
     */
    protected function populateWhere(&$sql, &$args, $criteria, $n = 1)
    {
        $sql = [];
        $args = [];
        foreach ($criteria as $field => $value) {
            $sql[] = '"'.$field.'" = $'.($n++);
            $args[] = $value;
        }
    }
}

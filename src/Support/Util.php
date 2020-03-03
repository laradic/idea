<?php

namespace Laradic\Idea\Support;

class Util
{

    /**
     * @param string                                                                   $table
     * @param \Illuminate\Database\Connection|\Illuminate\Database\ConnectionInterface $connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getPropertiesFromTable($table, $dates = [], $connection = null)
    {
        $connection = $connection ?? \DB::connection();
        $connection->getDoctrineSchemaManager();
        $schema = $connection->getDoctrineSchemaManager();
        $databasePlatform = $schema->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $platformName = $databasePlatform->getName();
        $customTypes = config("ide-helper.custom_db_types.{$platformName}", array());
        foreach ($customTypes as $yourTypeName => $doctrineTypeName) {
            $databasePlatform->registerDoctrineTypeMapping($yourTypeName, $doctrineTypeName);
        }

        $database = null;
        if (strpos($table, '.')) {
            [$database, $table] = explode('.', $table);
        }

        $columns = $schema->listTableColumns($table, $database);

        $properties = [];
        if ($columns) {
            foreach ($columns as $column) {
                $name = $column->getName();
                if (in_array($name, $dates)) {
                    $type = '\Illuminate\Support\Carbon';
                } else {
                    $type = $column->getType()->getName();
                    switch ($type) {
                        case 'string':
                        case 'text':
                        case 'date':
                        case 'time':
                        case 'guid':
                        case 'datetimetz':
                        case 'datetime':
                            $type = 'string';
                            break;
                        case 'integer':
                        case 'bigint':
                        case 'smallint':
                            $type = 'integer';
                            break;
                        case 'boolean':
                            switch (config('database.default')) {
                                case 'sqlite':
                                case 'mysql':
                                    $type = 'integer';
                                    break;
                                default:
                                    $type = 'boolean';
                                    break;
                            }
                            break;
                        case 'decimal':
                        case 'float':
                            $type = 'float';
                            break;
                        default:
                            $type = 'mixed';
                            break;
                    }
                }

                $comment = $column->getComment();
                $properties[] = [
                    'name' => $name,
                    'type' => $type,
                    'comment' => $comment,
                    'nullable' => !$column->getNotnull()
                ];

            }
        }
        return $properties;
    }
}
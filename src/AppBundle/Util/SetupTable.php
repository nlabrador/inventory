<?php

namespace AppBundle\Util;

class SetupTable
{
    private $fields;
    private $table_name;

    public function __construct($fields, $table_name) {
        $table_name = preg_replace('/\s+$/', '', $table_name);
        $table_name = preg_replace('/^\s+/', '', $table_name);
        $table_name = preg_replace('/\s/', '', strtolower($table_name));

        $this->fields = $fields;
        $this->table_name = $table_name;
    }

    public function getFieldType($field_type) {
        $fields = [
            'text'    => 'VARCHAR(255)',
            'date'    => 'DATE',
            'decimal' => 'DECIMAL',
            'integer' => 'INTEGER'
        ];

        return $fields[$field_type];
    }

    public function getCreateSql() {
        $create = sprintf(
            "CREATE TABLE %s(%s_id SERIAL PRIMARY KEY,",
            $this->table_name,
            $this->table_name
        );

        foreach ($this->fields as $field) {
            $create .= sprintf(
                "%s %s,",
                $field['field_name'],
                $this->getFieldType($field['field_type'])
            );
        }

        $create = preg_replace('/,$/', '', $create) . ');';

        return $create;
    }

    public function createTable($doctrine) {
         $em = $doctrine->getManager();

         $stmt = $em->getConnection()->prepare($this->getCreateSql());
         $stmt->execute();
    }

    public function createEntity($app_dir) {
        $entity_class = ucfirst($this->table_name);

        shell_exec("cd $app_dir && php bin/console doctrine:mapping:import --force AppBundle xml --from-database --filter=".$this->table_name);
        shell_exec("cd $app_dir && php bin/console doctrine:mapping:convert annotation ./src  --filter=$entity_class"); 
        shell_exec("cd $app_dir && php bin/console doctrine:generate:entities AppBundle/Entity/$entity_class"); 
        shell_exec("cd $app_dir && rm src/AppBundle/Entity/{$entity_class}.php~"); 
    }
}

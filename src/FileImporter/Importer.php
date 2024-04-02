<?php

namespace App\FileImporter;

use OpenSpout\Reader\XLSX\Reader;
use App\Models\Postindex;
use App\Database\SqlBuilder\Builder;

class Importer {

    protected Reader $reader;

    /**
     * @var string|null index column name of Model(Table).
     */
    protected ?string $model_index;

    /**
     * @var Builder sql builder.
     */
    protected Builder $insert_builder;

    /**
     * @var int excel row numeric key of index column of Model(Table).
     */
    protected int $index_numeric_key;

    /**
     * @var array migrate columns except columns specified here.
     */
    protected array $except_columns = ["from_api"];

    /**
     * @param    string		$path	Path to file for import
     * @param    string		$file_name	Filename for import
     * @param    int		$part_size_to_ins	Size of rows, when reached perform insert of data
     *                                          into database and reinitialize insert builder
     *                                          to add new rows(data)
     */
    function __construct(
        protected string $path,
        protected string $file_name,
        protected int $part_size_to_ins = 500
    ) {
        $this->insert_builder = Postindex::initInsert([], $this->except_columns);
        $this->index_numeric_key = $this->insert_builder->model()->indexExcelNumericKey();

        $this->model_index = $this->insert_builder->model()->index();
    }

    /**
     * Initialises(Sets) excel reader
     *
     * @returns  void
     */
    protected function setReader() {
        $this->reader = new Reader();
        $this->reader->open($this->path . $this->file_name);
    }

    /**
     * Performs insert into Database
     *
     * @param bool $reinitialize_insert_builder	Initialize new insert builder or not
     *
     * @returns  void
     */
    protected function insertIntoDB(bool $reinitialize_insert_builder = true) {
        if($this->insert_builder->isColumnsEmpty())
            return;

        $this->insert_builder->format(function($sql) {
            return rtrim($sql, ",");
        })->appendOnDuplicateKeyUpdate([], $this->except_columns);

        $this->insert_builder->execute();

        if($reinitialize_insert_builder)
            $this->insert_builder = Postindex::initInsert([], $this->except_columns);
    }

    /**
     * Performs import of file
     *
     * @returns  void
     */
    function run() {
        $this->setReader();

        $select_builder = Postindex::initSelect("GROUP_CONCAT(`post_code_of_post_office` SEPARATOR '|')")
            ->format(function($sql) {
                return $sql . " WHERE `from_api`= 1";
            });

        // Indexes in bar separated format of all rows that is added with API interface
        $all_api_rows_ids = $select_builder->fetchColumn();

        // Stores all excel indexes columns in comma separated string
        $excel_indexes = "";

        foreach ($this->reader->getSheetIterator() as $sheet) {
            $i = 0;
            foreach ($sheet->getRowIterator() as $row) {
                //  If length of processed rows equals or exceeds value of
                //  specified part size perform insertion into DB
                if($i >= $this->part_size_to_ins){
                    $this->insertIntoDB();
                    $i = 0;
                }

                $arr = $row->toArray();
                $index_value = trim($arr[$this->index_numeric_key]);

                // If post index is empty of belongs to row of DB which is added with API
                // stop processing this row and go to the next one
                if(
                    !is_numeric($index_value) || empty($index_value) ||
                    (
                        !empty($all_api_rows_ids) &&
                        str_contains($all_api_rows_ids, $index_value)
                    )
                )
                    continue;

                // Map excel row with numeric keys into array with string keys per column
                $mapped_assoc_arr = $this->insert_builder->model()->mapValuesToKeys($arr);

                // Append data to builder
                $this->insert_builder->appendData($mapped_assoc_arr, false, function($sql, $keys){
                    return $sql . " (" . $keys . "),";
                });

                // Collect indexes into string as comma separated list
                $excel_indexes .= (int)$mapped_assoc_arr[$this->model_index] . ",";

                $i++;
            }

            $this->insertIntoDB(false);
        }

        // Delete all rows which not in list indexes from excel and not added with API
        $excel_indexes = rtrim($excel_indexes, ",");
        $delete_builder = Postindex::initDelete()->append("
            WHERE `{$this->model_index}` NOT IN ({$excel_indexes}) AND `from_api` = 0
        ");

        $delete_builder->execute();
    }

}
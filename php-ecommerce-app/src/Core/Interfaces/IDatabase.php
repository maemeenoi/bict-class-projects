<?php
namespace Agora\Core\Interfaces;

interface IDatabase
{
    /**
     * Execute a query and return the results
     * @param string $sql
     * @return array
     */
    public function query($sql);

    /**
     * Execute a query without returning results
     * @param string $sql
     * @return bool
     */
    public function execute($sql);

    /**
     * Check if there was an error in the last operation
     * @return bool
     */
    public function isError();

    public function lastInsertID();

    public function prepare($sql);
}
<?php

namespace App\Models\Traits;

trait AlphaAndNumber
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // decide the connection dynamically
        $connection = $this->connectionType();

        if ($connection === 'mysql_alpha') {
            $this->keyType = 'string';
            $this->incrementing = false;
        } else {
            $this->keyType = 'int';
            $this->incrementing = true;
        }
    }
    /**
     * Logic to pick the right connection
     */
    protected function connectionType(): string
    {
        // Example: based on env(), config, tenant, or request
        return in_array(school_config('app.acronym'), ['almc'])
            ? 'mysql_alpha'
            : 'mysql_numbers';
    }
}

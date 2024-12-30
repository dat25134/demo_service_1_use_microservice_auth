<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be stored in the database.
     *
     * @var bool
     */
    protected $persisted = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'roles',
        'permissions'
    ];

    /**
     * Create a new model instance in memory only.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->exists = true; // Prevent save attempts
    }

    /**
     * Prevent any database operations
     */
    public function save(array $options = [])
    {
        return false;
    }

    /**
     * Prevent any database operations
     */
    public function delete()
    {
        return false;
    }
}

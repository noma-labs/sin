<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 */
final class Alimentazioni extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'alimentazione';

    protected $primaryKey = 'id';
}

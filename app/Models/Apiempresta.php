<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apiempresta extends Model
{
    use HasFactory;

    /**
     * Get file content
     * @param string $name
     * @param string $path
     * @return bool
     */
    public function getFile($name,$path)
    {
        try
        {
            return file_get_contents($path.'/'.$name);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();exit;
        }
    }
}

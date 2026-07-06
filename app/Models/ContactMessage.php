<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'email', 'subject', 'message', 'is_read'])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }
}

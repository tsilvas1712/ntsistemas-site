<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['visit_id', 'label', 'href', 'section', 'element'])]
class VisitClick extends Model
{
    public const UPDATED_AT = null;

    /** Máximo de cliques gravados por visita (evita abuso do endpoint). */
    public const LIMIT_PER_VISIT = 300;

    /** @return BelongsTo<Visit, $this> */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityInspection extends Model
{
    protected $table = 'quality_inspections';
    protected $primaryKey = 'InspectionID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'InspectionID', 'InspectionType', 'BatchID', 'InspectionDate',
        'Result', 'DefectsFound', 'TestResults', 'CAPA', 'InspectorID', 'Status',
    ];

    public function batch()
    {
        return $this->belongsTo(ProductionBatch::class, 'BatchID', 'BatchID');
    }
}

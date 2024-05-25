<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DB;

class AQSettlement extends Model
{
    protected $table = "aq_settlements";

    protected $fillable = ["middetail_id", "from_date", "to_date", "txn_hash", "paid_date", "payment_receipt"];

    protected $dates = ["from_date", "to_date", "created_at", "paid_date"];

    // * Relations ships
    public function mid(): BelongsTo
    {
        return $this->belongsTo(MIDDetail::class, "middetail_id")->select("id", "bank_name");
    }

    // * Advanced Search
    public function advacnedSearch(array $input)
    {
        $query = AQSettlement::select("*")->with("mid");
        if (isset($input["middetail_id"]) && $input["middetail_id"] != "") {
            $query->where("middetail_id", $input["middetail_id"]);
        }
        if ((isset($input['from_date']) && $input['from_date'] != '')) {
            $from_date = date('Y-m-d', strtotime($input['from_date']));
            $query = $query->where(DB::raw('DATE(from_date)'), '>=', $from_date);
        }

        if ((isset($input['to_date']) && $input['to_date'] != '')) {
            $to_date = date('Y-m-d', strtotime($input['to_date']));
            $query = $query->where(DB::raw('DATE(to_date)'), '>=', $to_date);
        }
        if (isset($input["txn_hash"]) && $input["txn_hash"] != "") {
            $query->where("txn_hash", $input["txn_hash"]);
        }

        return $query;
    }
}

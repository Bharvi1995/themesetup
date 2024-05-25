<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use DB;

class Sales extends Model
{
    protected $fillable = ["name", "email", "country_code", "rm_code", "status"];

    protected $dates = ["created_at"];

    // * relationships
    public function merchants(): HasMany
    {
        return $this->hasMany(User::class, "sales_id", "id");
    }


    // * Quesies
    public function advancedSearch(array $input)
    {
        $query = static::select("id", "name", "country_code", "email", "rm_code", "status", "created_at")->withCount("merchants");

        if (isset($input["email"]) && $input["email"] != "") {
            $query = $query->where("email", 'like', '%' . $input["email"] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $query = $query->where(DB::raw('DATE(created_at)'), '>=', $start_date);
        }

        if (isset($input['end_date']) && $input['end_date'] != '') {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $query = $query->where(DB::raw('DATE(created_at)'), '<=', $end_date);
        }

        if (isset($input["status"]) && $input["status"] != "") {
            $query = $query->where("status", $input["status"]);
        }

        return $query;
    }
}

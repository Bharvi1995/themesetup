<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminAction extends Model
{
    use SoftDeletes;
    public const LOGIN  = 1;
    public const LOGOUT = 2;
    public const UPDATE_PROFILE = 3;
    public const CREATE_ADMIN = 4;
    public const UPDATE_ADMIN = 5;
    public const DELETE_ADMIN = 6;
    public const CHANGE_ADMIN_STATUS = 7;
    public const EXPIRED_ADMIN_PASSWORD = 8;
    public const CREATE_REFERRAL_PARTNER = 9;
    public const UPDATE_REFERRAL_PARTNER = 10;
    public const DELETE_REFERRAL_PARTNER = 11;
    public const CHANGE_REFERRAL_PARTNER_STATUS = 12;
    public const REFERRAL_PARTNER_DOCUMNET_DOWNLOAD = 13;
    public const REFERRAL_PARTNER_AGREEMENT_STATUS = 14;
    public const GENERATE_PAYOUT_REPORT = 15;
    public const PAYOUT_REPORT_DOWNLOAD_EXCEL = 16;
    public const PAYOUT_REPORT_DELETE = 17;
    public const PAYOUT_REPORT_PAID = 18;
    public const PAYOUT_REPORT_SHOW = 19;
    public const PAYOUT_REPORT_UPLOAD_FILES = 20;
    public const PAYOUT_REPORT_GENERATE_PDF = 21;
    public const GENERATE_REFERRAL_PARTNER_REPORT = 22;
    public const REFERRAL_PARTNER_DOWNLOAD_EXCEL = 23;
    public const REFERRAL_PARTNER_DELETE = 24;
    public const REFERRAL_PARTNER_PAID = 25;
    public const REFERRAL_PARTNER_SHOW = 26;
    public const REFERRAL_PARTNER_UPLOAD_FILES = 27;
    public const REFERRAL_PARTNER_GENERATE_PDF = 28;
    public const ASSIGN_MID = 29;
    public const GENERATE_PAYOUT_REPORT_RP = 30;
    public const PAYOUT_REPORT_PAID_RP = 31;
    public const PAYOUT_REPORT_SHOW_RP = 32;
    public const PAYOUT_REPORT_DELETE_RP = 33;
    public const PAYOUT_REPORT_UPLOAD_FILES_RP = 34;
    public const PAYOUT_REPORT_GENERATE_PDF_RP = 35;
    public const REFERRAL_PARTNER_DOCUMENT_DELETE = 36;

    protected $fillable = [ 'title'];

    public function getData() {
        $data = static::select("admin_actions.*")
            ->orderBy("admin_actions.title")
            ->get();
        return $data;
    }
    
}

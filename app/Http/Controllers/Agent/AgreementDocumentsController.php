<?php

namespace App\Http\Controllers\Agent;

use Auth;
use Session;
use Validator;
use App\Agent;
use App\RpAgreementDocumentUpload;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Storage;

class AgreementDocumentsController extends AgentUserBaseController
{
	public function __construct()
    {
        parent::__construct();
        $this->agentUser = new Agent;
    }
}

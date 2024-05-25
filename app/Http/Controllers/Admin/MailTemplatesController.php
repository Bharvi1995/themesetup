<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\MailTemplates;
use Storage;
class MailTemplatesController extends AdminController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->mailTemplates = new MailTemplates;
        $this->moduleTitleS = 'Mail Templates';
        $this->moduleTitleP = 'admin.mailtemplate';
    }

    public function index(){
    	$data = $this->mailTemplates->getData();
        return view('admin.mailtemplate.index',compact('data'));
    }

    public function create()
    {
        return view('admin.mailtemplate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'email_subject' => 'required',
            'email_body' => 'required',
        ]);
        if($request->hasFile('email-template-files')){
            foreach ($input['email-template-files'] as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/email-template-files/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                $input['files'][] = $filePath;
            }
            $input['files'] = json_encode($input['files']);
        }
        // dd($input);
        unset($input['email-template-files']);
        $mailtemplate = $this->mailTemplates->storeData($input);
        notificationMsg('success', 'Mail Template created successfully!');
        return redirect()->route('mail-templates.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mailTemplates = $this->mailTemplates->findData($id);
        return view('admin.mailtemplate.edit', compact('mailTemplates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'email_subject' => 'required',
            'email_body' => 'required',
        ]);

        if($request->hasFile('email-template-files')){
            foreach ($input['email-template-files'] as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/email-template-files/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                $input['files'][] = $filePath;
            }
            $input['files'] = json_encode($input['files']);
        }

        unset($input['email-template-files']);
        $mailTemplates = $this->mailTemplates->findData($id);
        $this->mailTemplates->updateData($id, $input);
        notificationMsg('success', 'Mail Templates updated successfully!');
        return redirect()->route('mail-templates.index');
    }

    public function show($id)
    {
        $mailTemplates = MailTemplates::find($id);
        return view($this->moduleTitleP . '.show', compact('mailTemplates'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MailTemplates::find($id)->delete();
        notificationMsg('success', 'Mail Templates deleted successfully!');
        return redirect()->route('mail-templates.index');
    }
}

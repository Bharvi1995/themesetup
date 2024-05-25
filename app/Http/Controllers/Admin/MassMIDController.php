<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\MassMid;
use App\User;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MassMIDController extends AdminController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->moduleTitleS = 'Mass MID';
        $this->moduleTitleP = 'admin.massMID';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * @mass mid index view
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function index(Request $request)
    {
        $mass_mid = \DB::table('mass_mid')
            ->select('mass_mid.*', 'old_middetails.bank_name as old_bank_name', 'new_middetails.bank_name as new_bank_name')
            ->leftJoin('middetails as old_middetails', 'mass_mid.old_mid', 'old_middetails.id')
            ->leftJoin('middetails as new_middetails', 'mass_mid.new_mid', 'new_middetails.id')
            ->whereNull('mass_mid.deleted_at')
            ->orderBy('mass_mid.id', 'desc')
            ->paginate(10);

        return view($this->moduleTitleP . '.index', compact('mass_mid'));
    }

    /**
     * @mass mid create
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function create(Request $request)
    {
        $midData = \DB::table('middetails')
            ->where('mid_type', '1')
            ->whereNotIn('id', ['1', '2'])
            ->whereNull('deleted_at')
            ->get();

        $midtypes = config('midtype.name');

        return view($this->moduleTitleP . '.create', compact('midData', 'midtypes'));
    }

    /**
     * @mass mid store
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'change_type' => 'required|numeric',
            'old_mid' => 'required|numeric',
            'new_mid' => 'required|numeric|different:old_mid',
            'user_id' => 'required|array|min:1',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $column = config('midtype.column.'.$input['change_type']);

        if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {
            $user_id = User::whereNotNull($column)
                ->where($column, 'like', '%"'.$input['old_mid'].'"%')
                ->whereNotIn('mid', ['1', '2'])
                ->whereIn('id', $input['user_id'])
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();
        } else {
            $user_id = User::where($column, $input['old_mid'])
                ->whereNotIn('mid', ['1', '2'])
                ->whereIn('id', $input['user_id'])
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();
        }

        if (count($user_id) > 0) {

            // update into multiple id
            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                foreach($user_id as $id) {

                    $old_array = User::where('id', $id)->value($column);
                    $replaced_array = str_replace('"'.$input['old_mid'].'"', '"'.$input['new_mid'].'"', $old_array);
                    $replaced_array = str_replace('"0",', '', $replaced_array);
                    $replaced_array = str_replace(',"0"', '', $replaced_array);
                    $replaced_array = str_replace('"0"', '', $replaced_array);

                    User::where('id', $id)
                        ->update([
                            $column => $replaced_array
                        ]);
                }
            } else {
                User::whereIn('id', $user_id)
                    ->update([
                        $column => $input['new_mid']
                    ]);
            }

            MassMid::insert([
                'change_type' => $input['change_type'],
                'user_id' => json_encode($user_id),
                'old_mid' => $input['old_mid'],
                'new_mid' => $input['new_mid'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($input['new_mid'] == 0) {
                notificationMsg('success', count($user_id).' merchants MID will be removed.');
            } else {
                notificationMsg('success', count($user_id).' merchants updated to new MID.');
            }
        } else {
            notificationMsg('error', 'No merchants found for the old MID.');
        }

        return redirect()->route('mass-mid.index');
    }

    /**
     * @mass mid edit
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function edit($id)
    {
        $mass_mid = MassMid::where('mass_mid.id', $id)
            ->whereNull('mass_mid.deleted_at')
            ->first();
        
        if ($mass_mid) {
            $midData = \DB::table('middetails')
                ->where('mid_type', '1')
                ->whereNotIn('id', ['1', '2'])
                ->whereNull('deleted_at')
                ->get();

            $midtypes = config('midtype.name');

            return view($this->moduleTitleP . '.edit', compact('mass_mid', 'midData', 'midtypes'));
        } else {
            abort(404);
        }
    }

    /**
     * @mass mid update
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'new_mid' => 'required|numeric',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $mass_mid = MassMid::where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if ($mass_mid) {
            if ($mass_mid->new_mid == $input['new_mid']) {
                notificationMsg('error', 'You have not updated the new MID, please update new MID.');

                return redirect()->back();
            }

            $column = config('midtype.column.'.$mass_mid['change_type']);
            
            $old_user_id = json_decode($mass_mid['user_id'], 1);

            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                // if new mid_id is removed(0)
                if ($mass_mid['new_mid'] == 0) {
                    $new_user_id = $old_user_id;
                } else {
                    $new_user_id = User::whereNotNull($column)
                        ->where($column, 'like', '%"'.$mass_mid['new_mid'].'"%')
                        ->whereIn('id', $old_user_id)
                        ->whereNotIn('mid', ['1', '2'])
                        ->whereNull('deleted_at')
                        ->pluck('id')
                        ->toArray();
                }
            } else {
                $new_user_id = User::where($column, $mass_mid['new_mid'])
                    ->whereIn('id', $old_user_id)
                    ->whereNotIn('mid', ['1', '2'])
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
            }
            
            $diff = array_diff($old_user_id, $new_user_id);

            // mass_mid change new mid
            MassMid::where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'new_mid' => $input['new_mid'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // update into multiple id
            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                foreach($new_user_id as $new_id) {


                    // if new_mid was removed as 0
                    if ($mass_mid['new_mid'] == 0) {
                        $old_array = User::where('id', $new_id)->value($column);
                        $replaced_array = json_decode($old_array, 1);

                        // if old_array
                        if (is_array($replaced_array)) {
                            array_push($replaced_array, (string)$input['new_mid']);
                        } else {
                            $replaced_array = [(string)$input['new_mid']];
                        }

                    } else {
                        $old_array = User::where('id', $new_id)->value($column);
                        $replaced_array = str_replace('"'.$mass_mid['new_mid'].'"', '"'.$input['new_mid'].'"', $old_array);
                        $replaced_array = str_replace('"0",', '', $replaced_array);
                        $replaced_array = str_replace(',"0"', '', $replaced_array);
                        $replaced_array = str_replace('"0"', '', $replaced_array);
                    }

                    User::where('id', $new_id)
                        ->update([
                            $column => $replaced_array
                        ]);
                }
            } else {
                User::whereIn('id', $new_user_id)
                    ->update([
                        $column => $input['new_mid']
                    ]);
            }

            if (empty($diff)) {
                notificationMsg('success', count($new_user_id).' merchants updated to new MID.');
            } else {

                $changed_user_id = json_decode($mass_mid->changed_user_id);

                if (is_array($changed_user_id)) {
                    $new_changed_user_id = array_merge(json_decode($mass_mid->changed_user_id), $diff);
                } else {
                    $new_changed_user_id = $diff;
                }

                MassMid::where('id', $id)
                    ->whereNull('deleted_at')
                    ->update([
                        'user_id' => $new_user_id,
                        'changed_user_id' => json_encode(array_values($new_changed_user_id))
                    ]);
                
                if (count($new_user_id) > 0) {
                    notificationMsg('success', count($new_user_id).' merchants updated to new MID.');
                } else {
                    notificationMsg('error', 'No merchants found to revert to old MID.');
                }
            }
        } else {
            notificationMsg('error', 'Something went wrong, please refresh the page and try again.');
        }

        return redirect()->route('mass-mid.index');
    }

    /**
     * @mass mid destroy records
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function destroy($id)
    {
        try {
            MassMid::where('id', $id)
                ->delete();
            
            notificationMsg('success', 'Mass MID record deleted successfully.');
        } catch (\Exception $e) {
            notificationMsg('error', 'Something went wrong, please try again.');
        }

        return redirect()->route('mass-mid.index');
    }

    /**
     * @method mass mid getMerchants
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function getMerchants(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $validator = Validator::make($input, [
            'change_type' => 'required|numeric',
            'old_mid' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please check error messages.',
                'errors' => $errors,
            ]);
        }

        $column = config('midtype.column.'.$input['change_type']);

        if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {
            $users = User::select('users.id', 'users.email', 'applications.business_name')
                ->leftJoin('applications', 'users.id', 'applications.user_id')
                ->whereNotNull('users.'.$column)
                ->where('users.'.$column, 'like', '%"'.$input['old_mid'].'"%')
                ->whereNotIn('users.mid', ['1', '2'])
                ->whereNull('users.deleted_at')
                ->get()
                ->toArray();
        } else {
            $users = User::select('users.id', 'users.email', 'applications.business_name')
                ->leftJoin('applications', 'users.id', 'applications.user_id')
                ->where('users.'.$column, $input['old_mid'])
                ->whereNotIn('users.mid', ['1', '2'])
                ->whereNull('users.deleted_at')
                ->get()
                ->toArray();
        }

        if (count($users) > 0) {

            $html = view('admin.massMID.merchantList')->with('users', $users)->render();

            return response()->json([
                'status' => 'success',
                'html' => $html,
                'message' => count($users).' merchants are on selected MID.',
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'No merchants found for the old MID.',
            ]);
        }


        return view($this->moduleTitleP . '.create', compact('midData', 'midtypes'));
    }

    /**
     * @mass mid createConfirm
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function createConfirm(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $validator = Validator::make($input, [
            'change_type' => 'required|numeric',
            'old_mid' => 'required|numeric',
            'new_mid' => 'required|numeric|different:old_mid',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please check error messages.',
                'errors' => $errors,
            ]);
        }

        // if no user_id
        if (empty($input['user_id']) || is_null($input['user_id'])) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No merchants selected.',
            ]);
        }

        $column = config('midtype.column.'.$input['change_type']);

        if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {
            $user_id = User::whereNotNull($column)
                ->where($column, 'like', '%"'.$input['old_mid'].'"%')
                ->whereIn('id', $input['user_id'])
                ->whereNotIn('mid', ['1', '2'])
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();
        } else {
            $user_id = User::where($column, $input['old_mid'])
                ->whereIn('id', $input['user_id'])
                ->whereNotIn('mid', ['1', '2'])
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();
        }

        if (count($user_id) > 0) {

            if ($input['new_mid'] == 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => count($user_id).' merchants\' MID will be removed, Are you sure?',
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => count($user_id).' merchants will be updated to new MID, Are you sure?',
                ]);
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'No merchants found for the old MID.',
            ]);
        }
    }

    /**
     * @mass mid updateConfirm
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function updateConfirm(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $validator = Validator::make($input, [
            'change_type' => 'required|numeric',
            'id' => 'required|numeric',
            'old_mid' => 'required|numeric',
            'new_mid' => 'required|numeric|different:old_mid',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please check error messages.',
                'errors' => $errors,
            ]);
        }

        $mass_mid = MassMid::where('id', $input['id'])
            ->whereNull('deleted_at')
            ->first();

        if ($mass_mid) {
            if ($mass_mid->new_mid == $input['new_mid']) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'You have not updated the new MID, please update new MID.',
                ]);
            }

            $column = config('midtype.column.'.$mass_mid['change_type']);
            
            $old_user_id = json_decode($mass_mid['user_id'], 1);

            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                // if new mid_id is removed(0)
                if ($mass_mid['new_mid'] == 0) {
                    $new_user_id = $old_user_id;
                } else {
                    $new_user_id = User::whereNotNull($column)
                        ->where($column, 'like', '%"'.$mass_mid['new_mid'].'"%')
                        ->whereIn('id', $old_user_id)
                        ->whereNotIn('mid', ['1', '2'])
                        ->whereNull('deleted_at')
                        ->pluck('id')
                        ->toArray();
                }
            } else {
                $new_user_id = User::where($column, $mass_mid['new_mid'])
                    ->whereIn('id', $old_user_id)
                    ->whereNotIn('mid', ['1', '2'])
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
            }
            
            $diff = array_diff($old_user_id, $new_user_id);

            if (empty($diff)) {
                return response()->json([
                    'status' => 'success',
                    'message' => count($new_user_id).' merchants will be updated to new MID, Are you sure?',
                ]);
            } else {
                $changed_user_id = json_decode($mass_mid->changed_user_id);

                if (is_array($changed_user_id)) {
                    $new_changed_user_id = array_merge(json_decode($mass_mid->changed_user_id), $diff);
                } else {
                    $new_changed_user_id = $diff;
                }

                MassMid::where('id', $input['id'])
                    ->whereNull('deleted_at')
                    ->update([
                        'user_id' => $new_user_id,
                        'changed_user_id' => json_encode(array_values($new_changed_user_id))
                    ]);
                
                // if few merchans are manually changed to other mid
                if (count($new_user_id) > 0) {
                    return response()->json([
                        'status' => 'success',
                        'message' => count($new_user_id).' merchants will be updated to new MID, Are you sure?',
                    ]);
                // all merchans are changed to other mid
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'No merchants found to revert to old MID.',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please refresh the page and try again.',
            ]);
        }
    }

    /**
     * @mass mid revertConfirm
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function revertConfirm(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $validator = Validator::make($input, [
            'id' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please try again.',
                'errors' => $errors,
            ]);
        }

        $mass_mid = MassMid::where('id', $input['id'])
            ->whereNull('deleted_at')
            ->first();

        if ($mass_mid) {
            $column = config('midtype.column.'.$mass_mid['change_type']);

            $old_user_id = json_decode($mass_mid['user_id'], 1);

            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                // if new_mid was removed as 0
                if ($mass_mid['new_mid'] == 0) {
                    $new_user_id = $old_user_id;
                } else {
                    $new_user_id = User::whereNotNull($column)
                        ->where($column, 'like', '%"'.$mass_mid['new_mid'].'"%')
                        ->whereIn('id', $old_user_id)
                        ->whereNotIn('mid', ['1', '2'])
                        ->whereNull('deleted_at')
                        ->pluck('id')
                        ->toArray();
                }
            } else {
                $new_user_id = User::where($column, $mass_mid['new_mid'])
                    ->whereIn('id', $old_user_id)
                    ->whereNotIn('mid', ['1', '2'])
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
            }

            $diff = array_diff($old_user_id, $new_user_id);

            if (empty($diff)) {
                return response()->json([
                    'status' => 'success',
                    'message' => count($new_user_id).' merchants will be reverted to old MID, Are you sure?',
                ]);
            } else {
                $changed_user_id = json_decode($mass_mid->changed_user_id);

                if (is_array($changed_user_id)) {
                    $new_changed_user_id = array_merge(json_decode($mass_mid->changed_user_id), $diff);
                } else {
                    $new_changed_user_id = $diff;
                }

                MassMid::where('id', $input['id'])
                    ->whereNull('deleted_at')
                    ->update([
                        'user_id' => $new_user_id,
                        'changed_user_id' => json_encode(array_values($new_changed_user_id))
                    ]);
                
                // if few merchans are manually changed to other mid
                if (count($new_user_id) > 0) {
                    return response()->json([
                        'status' => 'success',
                        'message' => count($new_user_id).' merchants will be reverted to old MID, Are you sure?',
                    ]);
                // all merchans are changed to other mid
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'No merchants found to revert to old MID.',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please refresh the page and try again.',
            ]);
        }

    }

    /**
     * @mass mid revert
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function revert(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $mass_mid = MassMid::where('id', $input['id'])
            ->whereNull('deleted_at')
            ->first();

        if ($mass_mid) {
            $column = config('midtype.column.'.$mass_mid['change_type']);

            $old_user_id = json_decode($mass_mid['user_id'], 1);

            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                // if new_mid was removed as 0
                if ($mass_mid['new_mid'] == 0) {
                    $new_user_id = $old_user_id;
                } else {
                    $new_user_id = User::whereNotNull($column)
                        ->where($column, 'like', '%"'.$mass_mid['new_mid'].'"%')
                        ->whereIn('id', $old_user_id)
                        ->whereNotIn('mid', ['1', '2'])
                        ->whereNull('deleted_at')
                        ->pluck('id')
                        ->toArray();
                }
            } else {
                $new_user_id = User::where($column, $mass_mid['new_mid'])
                    ->whereIn('id', $old_user_id)
                    ->whereNotIn('mid', ['1', '2'])
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
            }

            // remove mid_record
            MassMid::where('id', $input['id'])
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);

            if (count($new_user_id) > 0) {

                // update into multiple id
                if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                    foreach($new_user_id as $new_id) {

                        // if new_mid was removed as 0
                        if ($mass_mid['new_mid'] == 0) {
                            $old_array = User::where('id', $new_id)->value($column);
                            $replaced_array = json_decode($old_array, 1);

                            // if old_array
                            if (is_array($replaced_array)) {
                                array_push($replaced_array, (string)$mass_mid['old_mid']);
                            } else {
                                $replaced_array = [(string)$mass_mid['old_mid']];
                            }

                        } else {
                            $old_array = User::where('id', $new_id)->value($column);
                            $replaced_array = str_replace('"'.$mass_mid['new_mid'].'"', '"'.$mass_mid['old_mid'].'"', $old_array);
                        }

                        User::where('id', $new_id)
                            ->update([
                                $column => $replaced_array
                            ]);
                    }
                } else {
                    User::whereIn('id', $new_user_id)
                        ->update([
                            $column => $mass_mid['old_mid']
                        ]);
                }

                notificationMsg('success', count($new_user_id).' merchants reverted to old MID.');
            } else {
                notificationMsg('error', 'No merchants found to revert to old MID.');
            }

            return redirect()->back();
        } else {
            abort(404);
        }
    }

    /**
     * @mass mid refresh
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function refresh($id)
    {
        $mass_mid = MassMid::where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        // mass_mid record found
        if ($mass_mid) {
            $column = config('midtype.column.'.$mass_mid['change_type']);
            
            $old_user_id = json_decode($mass_mid['user_id'], 1);

            // if multiple mass_mid change
            if (in_array($column, ['multiple_mid', 'multiple_mid_master']) ) {

                // if new mid_id is removed(0)
                if ($mass_mid['new_mid'] == 0) {
                    $new_user_id = $old_user_id;
                } else {
                    $new_user_id = User::whereNotNull($column)
                        ->where($column, 'like', '%"'.$mass_mid['new_mid'].'"%')
                        ->whereIn('id', $old_user_id)
                        ->whereNotIn('mid', ['1', '2'])
                        ->whereNull('deleted_at')
                        ->pluck('id')
                        ->toArray();
                }
            } else {
                $new_user_id = User::where($column, $mass_mid['new_mid'])
                    ->whereIn('id', $old_user_id)
                    ->whereNotIn('mid', ['1', '2'])
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
            }
            
            // changed user_id array
            $diff = array_diff($old_user_id, $new_user_id);

            if (empty($diff)) {
                notificationMsg('success', 'No merchant MID was changed.');
            } else {
                $changed_user_id = json_decode($mass_mid->changed_user_id);

                if (is_array($changed_user_id)) {
                    $new_changed_user_id = array_merge(json_decode($mass_mid->changed_user_id), $diff);
                } else {
                    $new_changed_user_id = $diff;
                }

                // move changed user_id from user_id to changed_user_id
                MassMid::where('id', $id)
                    ->whereNull('deleted_at')
                    ->update([
                        'user_id' => $new_user_id,
                        'changed_user_id' => json_encode(array_values($new_changed_user_id))
                    ]);
                
                // if few merchans are manually changed to other mid
                if (count($new_user_id) > 0) {
                    notificationMsg('success', count($diff).' merchants MID was changed.');
                // all merchans are changed to other mid
                } else {
                    notificationMsg('error', 'No merchants remained to revert on old MID.');
                }
            }
        } else {
            notificationMsg('error', 'Something went wrong, please try again.');
        }

        return redirect()->back();
    }

    /**
     * @method mass mid viewMerchants
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View
     */
    public function viewMerchants($id)
    {
        $user_json = MassMid::where('id', $id)
            ->whereNull('deleted_at')
            ->value('user_id');

        if ($user_json == null) {
            return response()->json([
                'status' => 'false',
                'message' => 'No merchants for this MID.',
            ]);
        }
        $user_id = json_decode($user_json, 1);

        $users = User::select('users.id', 'users.email', 'applications.business_name')
            ->leftJoin('applications', 'users.id', 'applications.user_id')
            ->whereIn('users.id', $user_id)
            ->whereNotIn('users.mid', ['1', '2'])
            ->whereNull('users.deleted_at')
            ->get()
            ->toArray();

        if (count($users) > 0) {

            $html = view('admin.massMID.merchantListModel')->with('users', $users)->render();

            return response()->json([
                'status' => 'success',
                'html' => $html,
                'message' => count($users).' merchants are on selected MID.',
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'No merchants found for the old MID.',
            ]);
        }


        return view($this->moduleTitleP . '.create', compact('midData', 'midtypes'));
    }
}

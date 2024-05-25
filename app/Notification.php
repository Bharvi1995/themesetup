<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Notification extends Model
{
	// use Cachable;
	/**
	 * The attribute that assign the database table.
	 *
	 * @var array
	 */
    protected $table = 'notifications';

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = array();

    protected $fillable = [
        'user_id',
        'sendor_id',
        'type',
        'title',
        'body',
        'url',
        'is_read'
    ];

    // ================================================
	/*  method : getNotifications
	* @ param  :
	* @ Description : get notifications for user and admin
	*/// ==============================================
	public function getNotifications($user_id = null, $type = 'user', $is_read = null, $limit = null)
	{
		$notifications = static::orderBy('id', 'desc')
    			->where('type', $type);
				if ($user_id != null) {
					$notifications = $notifications->where('user_id', $user_id);
				}
    			if ($is_read != null) {
    				$notifications = $notifications->where('is_read', $is_read);
    			}
    			if ($limit != null) {
    				$notifications = $notifications->limit($limit);
    			}
    			$notifications = $notifications->get();

    	return $notifications;
	}
}

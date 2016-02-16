<?php

namespace App;
use App\User;

/**
* 
*/
class ConstsAndFuncs
{
	// Constants
	const PERM_ADMIN = 1000;
	public static $THUMBNAILS = [1 => 'Photo', 2 => 'Video'];
	public static $FORMATS = [
		1 => 'Trắc nghiệm',
		2 => 'Điền từ',
		5 => 'Nối',
	];
	public static $FreeQuestionsForCrawler = 5;

	// Functions
	public static function permission_of($UserID){
		$user = User::find($USerID);
		if (count($user) < 1){
			return 0;
		}
		return $user['admin'];
	}

	public static function is_vip($user_id){
		$user = User::find($user_id);
		if (count($user) < 1){
			return false;
		}
		if ($user->vip < 1){
			return false;
		}
		// Go crazy with this. Cannot figure out why $user->expire_at returns a string while $user->created_at returns an \DateTime object.
		$oldExpire = new \DateTime($user->expire_at);
		$now = new \DateTime();
		if (($now->getTimestamp() - $oldExpire->getTimestamp()) > 0){
			// VIP was expired before.
			return false;
		}
		return true;
	}
}
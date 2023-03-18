<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Member extends Model
{
    protected $pk = 'id';
    protected $field = ['account'];
  
    public function listFriend()
    {
        return $this->hasOne(Friend::class, "friend_id", "id");
    }

    
}
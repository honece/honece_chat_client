<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Friend extends Model
{
    static function getMemberInfo()
    {
        return Member::
            hasWhere(
                'listFriend',
                ['member_id' => USER['id']]
            )
            ->where('status', 0)
            ->select()->toArray();
    }

}
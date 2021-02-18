<?php
namespace addons\csmding\library;

class CsmUtils
{

    public static function convertListColumn(&$list, $key, $dao, $daofieldname = 'name')
    {
        $ids = [];
        foreach ($list as $item) {
            $ids[] = $item->$key;
        }
        $keylist = $dao->where("id", "in", $ids)
            ->field("id,{$daofieldname}")
            ->select();
        // echo $dao->getLastSql();
        $keynames = [];
        foreach ($keylist as $keyrow) {
            $keynames['D' . $keyrow->id] = $keyrow->$daofieldname;
        }
        // var_dump($list);
        foreach ($list as $i => $item) {
            if(isset($keynames['D' . $item->$key])){
                $list[$i][$key] = $keynames['D' . $item->$key];
            }else{
                $list[$i][$key] = null;
            }
        }
    }

    public static function isNullOrBlank($str)
    {
        if ($str == null || $str == "") {
            return true;
        } else {
            return false;
        }
    }

    public static function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                $i += 5;
            } else if ($str[$i] == '%') {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
        }
        return $ret;
    }
}
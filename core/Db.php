<?php

class                               Db {
    private static                  $__instance = null;

    public static function          getInstance() {
        if (is_null(self::$__instance)) {
            try {
                $pdo = new PDO(Conf::get('pdo.dsn'), Conf::get('pdo.user'), Conf::get('pdo.pass'));
	            $pdo->exec("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	            self::$__instance = new DbHandle($pdo, Conf::get('pdo.prefix'));
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
        return self::$__instance;
    }

    public static function          close() {
        self::$__instance = null;
    }

    public static function          quote($str) {
        return Db::getInstance()->pdo()->quote($str);
    }

    public static function          lastId() {
        return Db::getInstance()->pdo()->lastInsertId();
    }

    public static function          get($query) {
        if (Conf::get('debug', false))
            $start = microtime(true);
        $req = Db::getInstance()->pdo()->query($query);
        if (Conf::get('debug', false))
            self::log($query, microtime(true) - $start, !$req ? true : false);
        if (!$req)
            return false;
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public static function          query($query) {
        if (Conf::get('debug', false))
            $start = microtime(true);
        $req = Db::getInstance()->pdo()->query($query);
        if (Conf::get('debug', false))
            self::log($query, microtime(true) - $start, !$req ? true : false);
        if (!$req)
            return false;
        return $req->fetchAll();
    }

    public static function          exec($query) {
        if (Conf::get('debug', false))
            $start = microtime(true);
        $ret = Db::getInstance()->pdo()->exec($query);
        if (Conf::get('debug', false))
            self::log($query, microtime(true) - $start);
        return $ret;
    }

    public static function          where($query = [], $join = ' AND ', $parent = '') {
        $str = [];

        foreach ($query as $k => $v) {
            if (is_array($v) && !in_array($k, ['$in', '$nin', '$and', '$or', '$not', '$nor']))
                $str[] = '('.Db::where($v, ' AND ', $k).')';
            else
                switch ($k) {
                    case '$like':
                        $str[] = '`'.$parent.'` LIKE '.Db::quote($v);
                        break;
                    case '$eq':
                        $str[] = '`'.$parent.'`='.Db::quote($v);
                        break;
                    case '$gt':
                        $str[] = '`'.$parent.'`>'.Db::quote($v);
                        break;
                    case '$gte':
                        $str[] = '`'.$parent.'`>='.Db::quote($v);
                        break;
                    case '$lt':
                        $str[] = '`'.$parent.'`<'.Db::quote($v);
                        break;
                    case '$lte':
                        $str[] = '`'.$parent.'`<='.Db::quote($v);
                        break;
                    case '$ne':
                        $str[] = '`'.$parent.'`!='.Db::quote($v);
                        break;
                    case '$in':
                        $els = [];
                        foreach ($v as $vv)
                            $els[] = Db::quote($vv);
                        $str[] = '`'.$parent.'` IN ('.implode(', ', $els).')';
                        break;
                    case '$nin':
                        $els = [];
                        foreach ($v as $vv)
                            $els[] = Db::quote($vv);
                        $str[] = '`'.$parent.'` NOT IN ('.implode(', ', $els).')';
                        break;
                    case '$or':
                        $str[] = '('.Db::where($v, ' OR ', $parent).')';
                        break;
                    case '$and':
                        $str[] = '('.Db::where($v, ' AND ', $parent).')';
                        break;
                    case '$not':
                        $str[] = '!('.Db::where($v, ' AND ', $parent).')';
                        break;
                    case '$nor':
                        $str[] = '!('.Db::where($v, ' OR ', $parent).')';
                        break;
                    default:
                        $str[] = '`'.$k.'`='.Db::quote($v);
                        break;
                }
        }

        return implode($join, $str);
    }

    public static function          order($query = []) {
        $str = [];
        foreach ($query as $k => $v) {
            $str[] = '`'.$k.'` '.($v > 0 ? 'ASC' : 'DESC');
        }
        return implode(', ', $str);
    }

    public static function          log($query, $time, $error = false) {
        $unit = 'ms';
        $time *= 1000;
        Conf::append('inspector.queries', [
            'query' => $query,
            'time' => number_format($time, 1, ',', '').$unit,
            'error' => $error
        ]);
    }
}

?>
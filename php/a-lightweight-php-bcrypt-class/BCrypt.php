<?php
class BCrypt
{
    /**
     * Work factor cost boundaries and default.
     *
     * @var const
     */
    const COST_MIN = 4;
    const COST_MAX = 31;
    const COST_DEFAULT = 12;
	
    /**
     * Generate a BCrypt salt.
     *
     * @param $salt An optional custom salt.
     * @return string
     */
    public static function salt($salt = null)
    {
        if (null !== $salt) {
            $salt = base64_encode(sha1($salt));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $salt = base64_encode(openssl_random_pseudo_bytes(16));
        } else {
            mt_srand();
            $salt = base64_encode(sha1(mt_rand() . uniqid() . time()));
        }
        
        return substr(strtr($salt, '+', '.'), 0, 22);
    }
    
    /**
     * Hash a password using BCrypt.
     *
     * @param $password The password to hash.
     * @param $cost The cost parameter. Must between 04 - 31. Default is 12.
     * @param $salt An optional custom salt.
     * @return string
     */
    public static function hash($password, $cost = self::COST_DEFAULT, $salt = null)
    {
        if (! is_int($cost)) {
            throw new \InvalidArgumentException('Work factor cost parameter must be an integer.');
        }
        
        if ($cost < self::COST_MIN) {
            $cost = self::COST_MIN;
        } elseif ($cost > self::COST_MAX) {
            $cost = self::COST_MAX;
        }

        $salt = (string) self::salt($salt);
        $cost = (string) str_pad($cost, 2, '0', STR_PAD_LEFT);
        return crypt($password, '$2a$' . $cost . '$' . $salt . '$');
    }
    
    /**
     * Compare a password to a hash.
     *
     * @param $password The password to compare.
     * @param $hash The password hash to compare.
     * @return boolean
     */    
    public static function compare($password, $hash)
    {
        return ($hash === crypt($password, $hash));
    }
}
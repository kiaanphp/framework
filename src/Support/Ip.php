<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Support;

/*
|---------------------------------------------------
| IP
|---------------------------------------------------
*/
class Ip {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
     
    /**
     * The length of IPv6 address in bits
     */
    const IPV6 = 6;
    const IPV6_ADDRESS_LENGTH = 128;

    /**
     * The length of IPv4 address in bits
     */
    const IPV4 = 4;
    const IPV4_ADDRESS_LENGTH = 32;

    /**
     * Gets the IP version. Does not perform IP address validation.
     *
     * @param string $ip the valid IPv4 or IPv6 address.
     * @return int [[IPV4]] or [[IPV6]]
     */
    public function version($ip)
    {
        return strpos($ip, ':') === false ? self::IPV4 : self::IPV6;
    }

    /**
     * Checks whether IP address or subnet $subnet is contained by $subnet.
     *
     * For example, the following code checks whether subnet `192.168.1.0/24` is in subnet `192.168.0.0/22`:
     *
     * ```php
     * Ip::inRange('192.168.1.0/24', '192.168.0.0/22'); // true
     * ```
     *
     * In case you need to check whether a single IP address `192.168.1.21` is in the subnet `192.168.1.0/24`,
     * you can use any of theses examples:
     *
     * ```php
     * Ip::inRange('192.168.1.21', '192.168.1.0/24'); // true
     * Ip::inRange('192.168.1.21/32', '192.168.1.0/24'); // true
     * ```
     *
     * @param string $subnet the valid IPv4 or IPv6 address or CIDR range, e.g.: `10.0.0.0/8` or `2001:af::/64`
     * @param string $range the valid IPv4 or IPv6 CIDR range, e.g. `10.0.0.0/8` or `2001:af::/64`
     * @return bool whether $subnet is contained by $range
     *
     * @throws NotSupportedException
     */
    public function inRange($subnet, $range)
    {
        list($ip, $mask) = array_pad(explode('/', $subnet), 2, null);
        list($net, $netMask) = array_pad(explode('/', $range), 2, null);

        $ipVersion = $this->version($ip);
        $netVersion = $this->version($net);
        if ($ipVersion !== $netVersion) {
            return false;
        }

        $maxMask = $ipVersion === self::IPV4 ? self::IPV4_ADDRESS_LENGTH : self::IPV6_ADDRESS_LENGTH;
        $mask = isset($mask) ? $mask : $maxMask;
        $netMask = isset($netMask) ? $netMask : $maxMask;

        $binIp = $this->ip2bin($ip);
        $binNet = $this->ip2bin($net);
        return substr($binIp, 0, $netMask) === substr($binNet, 0, $netMask) && $mask >= $netMask;
    }

    /**
     * Expands an IPv6 address to it's full notation.
     *
     * For example `2001:db8::1` will be expanded to `2001:0db8:0000:0000:0000:0000:0000:0001`
     *
     * @param string $ip the original valid IPv6 address
     * @return string the expanded IPv6 address
     */
    public function expandIPv6($ip)
    {
        $hex = unpack('H*hex', inet_pton($ip));
        return substr(preg_replace('/([a-f0-9]{4})/i', '$1:', $hex['hex']), 0, -1);
    }

    /**
     * Converts IP address to bits representation.
     *
     * @param string $ip the valid IPv4 or IPv6 address
     * @return string bits as a string
     * @throws Exception
     */
    public function ip2bin($ip)
    {
        $ipBinary = null;
        if ($this->version($ip) === self::IPV4) {
            $ipBinary = pack('N', ip2long($ip));
        } elseif (@inet_pton('::1') === false) {
            throw new \Exception('IPv6 is not supported by inet_pton()!');
        } else {
            $ipBinary = inet_pton($ip);
        }

        $result = '';
        for ($i = 0, $iMax = strlen($ipBinary); $i < $iMax; $i += 4) {
            $result .= str_pad(decbin(unpack('N', substr($ipBinary, $i, 4))[1]), 32, '0', STR_PAD_LEFT);
        }
        return $result;
    }

    /**
     * get client IP
     *
     * @param string $ip the valid IPv4 or IPv6 address
     * @return string bits as a string
     * @throws NotSupportedException
     */
    public function ip(): string
    {
        if (\array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $forwardedForItems = \explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            if (!empty($forwardedForItems)) {
                /** @noinspection ReturnNullInspection */
                return \array_pop($forwardedForItems);
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    /*
    * Get a domain name’s IP address
    *
    */
    public function host(string $domain)
    {
        return gethostbyname($domain);
    }

   /*
    * How to detect the MAC address of a device
    *
    */
    public function mac()
    {
        //Buffering the output
        ob_start();  
        
        //Getting configuration details 
        system('ipconfig /all');  
        
        //Storing output in a variable 
        $configdata = ob_get_contents();  
        
        // Clear the buffer  
        ob_clean();  
        
        //Extract only the physical address or Mac address from the output
        $mac = "Physical";  
        $pmac = strpos($configdata, $mac);
        
        // Get Physical Address  
        $macaddr = substr($configdata,($pmac+36),17);  
        
        //Return Mac Address  
        return $macaddr; 
    }

}
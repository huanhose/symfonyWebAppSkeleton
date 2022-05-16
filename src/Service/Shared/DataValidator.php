<?php
namespace App\Service\Shared;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Basic Validation functions 
 * Decouple these functions from framework or others libraries
 */
class DataValidator
{
    /**
     * GexExp patterns to validate an email like html5 browser 
     */
    private const PATTERN_EMAIL_HTML5 = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';

    /**
     * Check if it's an valid email
     *
     * @param string $email
     * @return boolean
     */
    public function isEmail(string $email):bool
    {
        return (1 == preg_match(self::PATTERN_EMAIL_HTML5, $email));            
    }

    /**
     * Check if a value is blank : 
     * - null
     * - ''
     * Optionally withn strci mode, an '   ' string is considered a blank value
     * 
     * @param [type] $value
     * @param boolean $strict If true, a string with only spaces is considered blank 
     * @return boolean
     */
    public function isBlank($value, $strict=false):bool
    {
        if (
            null === $value 
            || $value === '') {
            return true;
        } 

        if ($strict === true 
            && is_string($value) 
            && trim($value) === '') {
            return true;
        }

        return false;
    }
}
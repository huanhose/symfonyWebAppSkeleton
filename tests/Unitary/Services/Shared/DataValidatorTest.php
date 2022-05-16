<?php
namespace App\Tests\Unitary\Service\Shared;

use PHPUnit\Framework\TestCase;
use App\Service\Shared\DataValidator;

class DataValidatorTest extends TestCase
{
    /**
     * @dataProvider listValidEmailsProvider
     *
     * @param [type] $email
     * @return void
     */
    public function testListValidEmails($email)
    {
        $emailValidator = new DataValidator();
         
        $this->assertTrue($emailValidator->isEmail($email));
    }    

    /**
     * @dataProvider listWrongEmailsProvider
     *
     * @param [type] $email
     * @return void
     */
    public function testWrongEmails($email)
    {
        $emailValidator = new DataValidator();
         
        $this->assertFalse($emailValidator->isEmail($email));
    }    

    /**
     * Data provider: list of valid emails
     *
     * @return void
     */
    public function listValidEmailsProvider()
    {
        return [
            ['foo@gmail.com'],
            ['david.smith@subdomain.domain.uk']            
        ];
    }

    /**
     * Data provider list of wrong emails, values that aren't emails
     *
     * @return void
     */
    public function listWrongEmailsProvider()
    {
        return [
            ['12345'],
            ['aaaaaa'],
            ['laura@gmail'],
            ['@gmail'],
            ['jonas.smith'],
            ['http://www.me.com']
        ];
    }

}
<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\SimpleHydrator;
use UnitarumExample\Entity\User;

/**
 * Class SimpleHydratorTest
 * @package UnitarumTest
 */
class SimpleHydratorTest extends TestCase
{
    use GetProtectedTrait;

    public function testExtract()
    {
        $entity = new User();
        $entity->setName('Test');

        $hydrator = new SimpleHydrator();
        $result = $hydrator->extract($entity);

        $this->assertEquals(['name' => 'Test', 'id' => null, 'email' => null, 'md_5_hash' => null], $result);
    }

    public function testHydrate()
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@test.no'
        ];

        $user = new User();
        $user->setName('Test');
        $user->setEmail('test@test.no');

        $hydrator = new SimpleHydrator();
        $returnUser = $hydrator->hydrate($data, new User());

        $this->assertEquals($user, $returnUser);
    }

    public function testConvertNameTo()
    {
        $hydrator = new SimpleHydrator();
        $method = $this->getProtectedMethod(SimpleHydrator::class, 'convertNameTo');
        $return = $method->invokeArgs($hydrator, ['getUserId']);

        $this->assertEquals('user_id', $return);
    }

    public function testConvertNameFrom()
    {
        $hydrator = new SimpleHydrator();
        $method = $this->getProtectedMethod(SimpleHydrator::class, 'convertNameFrom');
        $return = $method->invokeArgs($hydrator, ['user_id']);

        $this->assertEquals('setUserId', $return);
    }

    public function testWhiteListForConvertNameTo()
    {
        $options = ['md5Hash' => 'md5_hash'];
        $hydrator = new SimpleHydrator($options);
        $method = $this->getProtectedMethod(SimpleHydrator::class, 'convertNameTo');
        $return = $method->invokeArgs($hydrator, ['getMd5Hash']);

        $this->assertEquals('md5_hash', $return);
    }
}

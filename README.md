# Unitarum
The library for providing and flexible changing fixtures for PHPUnit

## Default fixtures
Fixtures use entities. Entities must provide methods `getId` and `setId`.
The library propose interface `EntityIdentifierInterface` with this two methods if it need.

#### An entity's example
```php
class User
{
    protected $id;
    protected $name;
    protected $email;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}
```

#### A fixture's example:
```php
$entity = new Entity\User();
$entity->setId(1);
$entity->setName('Test');
$entity->setEmail('test@test.no');

return ['table_name' => $entity];
```

## PhpUnit test
In the test's start need initialize the `Unitarum` object with two parameters.
The first parameter is path to folder with fixtures. The second parameter is `dsn` connection string to `sqlite`. 

#### Test example
```php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    protected static $unitarum;

    public static function setUpBeforeClass()
    {
        self::$unitarum = new Unitarum([
            OptionsInterface::FIXTURE_FOLDER_OPTION => '../data',
            OptionsInterface::DSN_OPTION => 'sqlite:data/sqlite.db',
        ]);
    }
}
```

In the every test's cases need to start and rollback a transaction.

#### Test example
```php
class DatabaseTest extends TestCase
{
    protected static $unitarum;

    public static function setUpBeforeClass()
    {
        ...
    }
    
    public function setUp()
    {
        self::$unitarum->getDataBase()->startTransaction();
    }
    
    public function tearDown()
    {
        self::$unitarum->getDataBase()->rollbackTransaction();
    }
}
```

### Setup fixtures
In the test you can apply fixtures and change any data from it.
You can call methods from the initialized `unitarum` object.
Called method name must be equals name of fixture file. A method can get one parameter, is it `Entity`.
An entity can rewrite default data from fixture.  

#### Test example
```php
class DatabaseTest extends TestCase
{
    ...
    
    public function testApplication()
    {
        $user = new Entity\User();
        $user->setEmail('newemail@email.no');
        
        $role = new Entity\Role();
        $role->setRole('viewer');
        $role->setUserId($user->getId());
        
        self::$unitarum->user($user)->role($role);
        
        // test
        ...
    }
}
```
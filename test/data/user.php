<?php

$entity = new \UnitarumExample\Entity\User();
$entity->setId(1);
$entity->setName('Test');
$entity->setEmail('test@test.no');

return [\UnitarumTest\DataBaseTest::TEST_TABLE_USERS => $entity];
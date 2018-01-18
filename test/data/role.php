<?php

$entity = new \UnitarumExample\Entity\Role();
$entity->setId(1);
$entity->setUserId(1);
$entity->setRole('user');

return [\UnitarumTest\DataBaseTest::TEST_TABLE_ROLES => $entity];
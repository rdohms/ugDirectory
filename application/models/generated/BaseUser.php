<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $login
 * @property string $nick
 * @property string $name
 * @property string $email
 * @property string $url
 * @property string $bio
 * @property string $level
 * @property $Group
 * @property $Admin
 */
abstract class BaseUser extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('users');
    $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true));
    $this->hasColumn('login', 'string', 35, array('type' => 'string', 'length' => '35'));
    $this->hasColumn('nick', 'string', 60, array('type' => 'string', 'length' => '60'));
    $this->hasColumn('name', 'string', 80, array('type' => 'string', 'length' => '80'));
    $this->hasColumn('email', 'string', 150, array('type' => 'string', 'length' => '150'));
    $this->hasColumn('url', 'string', 200, array('type' => 'string', 'length' => '200'));
    $this->hasColumn('bio', 'string', null, array('type' => 'string'));
    $this->hasColumn('level', 'string', 3, array('type' => 'string', 'length' => '3'));
  }

  public function setUp()
  {
    $this->hasOne('Group', array('local' => 'id',
                                 'foreign' => 'user_responsible'));

    $this->hasMany('Admin', array('local' => 'id',
                                  'foreign' => 'user_id'));
  }
}
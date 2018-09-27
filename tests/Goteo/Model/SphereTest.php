<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Sphere;
use Goteo\Model\Sdg;


class SphereTest extends TestCase {
    private static $data = ['name' => 'test'];
    private static $sdg;

    public function testInstance() {
        $ob = new Sphere();
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $errors = [];
        $this->assertFalse($ob->validate($errors), implode("\n", $errors));
        $this->assertFalse($ob->save(), implode("\n", $errors));
    }

    public function testCreate() {
        $errors = [];
        $ob = new Sphere(self::$data);
        $this->assertTrue($ob->validate($errors), implode("\n", $errors));
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Sphere::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->{$key}, $val);
        }
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testIcon($ob) {
        $this->assertInstanceOf('\Goteo\Model\Image', $ob->getIcon());
        $this->assertTrue($ob->getIcon()->isAsset());
        $this->assertStringStartsWith(SRC_URL, $ob->getIcon()->getLink());
        $this->assertStringEndsWith("/img/sphere/{$ob->id}.png", $ob->getIcon()->getLink());
        $this->assertFalse($ob->setIcon('testimage.png')->getIcon()->isAsset());
        $this->assertStringEndsWith('testimage.png', $ob->getIcon()->getLink());
    }

    /**
     * @depends testCreate
     */
    public function testSdgRelationships($ob) {
        $errors = [];
        $sdg = new Sdg(['name' => 'sdg test sdg']);
        $this->assertTrue($sdg->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob->addSdgs($sdg));
        $sdgs = $ob->getSdgs();
        $this->assertCount(1, $sdgs);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $sdgs[0]);
        self::$sdg = $sdgs[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob->addSdgs($sdgs));
        $this->assertCount(1, $ob->getSdgs());
        //
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob->replaceSdgs($sdgs));
        $this->assertCount(1, $ob->getSdgs());
        return $ob;
    }

    /**
     * @depends testSdgRelationships
     */
    public function testRemoveSdgRelationships($ob) {
        $this->assertCount(1, $ob->getSdgs());
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob->removeSdgs(self::$sdg));
        $this->assertCount(0, $ob->getSdgs());
        // repeated unassignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Sphere', $ob->removeSdgs(self::$sdg));
        $this->assertCount(0, $ob->getSdgs());

    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());
        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting($ob) {
        $ob = Sphere::get($ob->id);
        $this->assertNull($ob);
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        Sdg::query("DELETE FROM sdg WHERE `id` = ?", self::$sdg);
    }
}

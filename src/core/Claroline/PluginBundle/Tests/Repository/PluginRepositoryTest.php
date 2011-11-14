<?php

namespace Claroline\PluginBundle\Repository;

use Claroline\PluginBundle\Entity\Plugin;
use Claroline\CommonBundle\Library\Testing\TransactionalTestCase;

class PluginRepositoryTest extends TransactionalTestCase
{
    /** @var Claroline\PluginBundle\Repository\PluginRepository */
    private $repository;

    public function setUp()
    {
        parent :: setUp();
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $em->getRepository('Claroline\PluginBundle\Entity\Plugin');
    }

    public function testCreatePluginInsertsNewPluginRecord()
    {
        $plugin = $this->buildPluginEntity(
            'VendorX\TestBundle\VendorXTestBundle',
            'ClarolinePlugin',
            'VendorX',
            'TestBundle',
            'Test',
            'Test description'
        );
        $this->repository->createPlugin($plugin);

        $plugin = $this->repository->findOneByBundleFQCN('VendorX\TestBundle\VendorXTestBundle');

        $this->assertEquals('ClarolinePlugin', $plugin->getType());
        $this->assertEquals('VendorX', $plugin->getVendorName());
        $this->assertEquals('TestBundle', $plugin->getBundleName());
        $this->assertEquals('Test', $plugin->getNameTranslationKey());
        $this->assertEquals('Test description', $plugin->getDescriptionTranslationKey());
    }

    public function testCreatePluginDoesntDuplicateExistingFQCN()
    {
        $this->setExpectedException('Claroline\PluginBundle\Repository\Exception\ModelException');

        $dummyPlugin = $this->buildPluginEntity('VendorX\TestBundle\VendorXTestBundle', '', '', '', '', '');
        $sameFQCNPlugin = $this->buildPluginEntity('VendorX\TestBundle\VendorXTestBundle', '', '', '', '', '');

        $this->repository->createPlugin($dummyPlugin);
        $this->repository->createPlugin($sameFQCNPlugin);
    }

    public function testDeletePluginRemovesPluginRecord()
    {
        $plugin = $this->buildPluginEntity('VendorX\TestBundle\VendorXTestBundle', '', '', '', '', '');
        $this->repository->createPlugin($plugin);
        $this->repository->deletePlugin('VendorX\TestBundle\VendorXTestBundle');

        $plugin = $this->repository->findOneByBundleFQCN('VendorX\TestBundle\VendorXTestBundle');
        $this->assertEquals(null, $plugin);
    }

    private function buildPluginEntity($fqcn, $type, $vendor, $bundle, $name, $desc)
    {
        $pluginEntity = new Plugin();
        $pluginEntity->setBundleFQCN($fqcn);
        $pluginEntity->setType($type);
        $pluginEntity->setVendorName($vendor);
        $pluginEntity->setBundleName($bundle);
        $pluginEntity->setNameTranslationKey($name);
        $pluginEntity->setDescriptionTranslationKey($desc);

        return $pluginEntity;
    }
}
<?php
use Hrgruri\Ricca\KeyChain;

class KeyChainTest extends TestCase
{
    public function testLoad()
    {
        $keychain = KeyChain::load("{$this->path}/key.json");
        $this->assertInstanceOf(KeyChain::class, $keychain);
    }

    public function testGet()
    {
        $keychain = KeyChain::load("{$this->path}/key.json");
        $this->assertInternalType('string', $keychain->get('slack'));
    }
}

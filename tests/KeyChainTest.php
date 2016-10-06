<?php
use Hrgruri\Ricca\KeyChain;

class KeyChainTest extends TestCase
{
    public function testLoad()
    {
        $keychain = KeyChain::load($this->file('key.json'));
        $this->assertInstanceOf(KeyChain::class, $keychain);
        $this->assertInternalType('string', $keychain->get('slack'));
    }
}

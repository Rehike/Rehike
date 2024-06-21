<?php

namespace BlueLibraries\Dns\Test\Unit\Records\Types\Txt;

use BlueLibraries\Dns\Records\ExtendedTxtRecords;
use BlueLibraries\Dns\Records\Types\Txt\DomainVerification;
use PHPUnit\Framework\TestCase;

class DomainVerificationTest extends TestCase
{

    protected DomainVerification $subject;

    public function setUp(): void
    {
        $this->subject = new DomainVerification([]);
        parent::setUp();
    }

    public function testGetTypeId()
    {
        $this->assertIsInt($this->subject->getTypeId());
    }

    public function testSetDataReturnsSameModel()
    {
        $this->assertSame(get_class($this->subject), get_class($this->subject->setData([])));
    }

    public function testGetHostDefaultNull()
    {
        $this->assertSame('', $this->subject->getHost());
    }

    public function testGetHostValue()
    {
        $value = 'test' . time() . '.com';
        $this->subject->setData(['host' => $value]);
        $this->assertSame($value, $this->subject->getHost());
    }

    public function testGetClass()
    {
        $this->assertSame('IN', $this->subject->getClass());
    }

    public function testGetClassValue()
    {
        $value = 'IN';
        $this->subject->setData(['class' => $value]);
        $this->assertSame($value, $this->subject->getClass());
    }

    public function testGetTtl()
    {
        $this->assertSame(0, $this->subject->getTtl());
    }

    public function testGetTtlValue()
    {
        $value = strval(time());
        $this->subject->setData(['ttl' => $value]);
        $this->assertSame((int)$value, $this->subject->getTtl());
    }

    public function testGetTxt()
    {
        $this->assertNull($this->subject->getTxt());
    }

    public function testGetIpValue()
    {
        $value = 'random text here';
        $this->subject->setData(['txt' => $value]);
        $this->assertSame($value, $this->subject->getTxt());
    }

    public function testToStringDefault()
    {
        $this->assertSame('0 IN TXT', $this->subject->toString());
    }

    public function testToStringComplete()
    {
        $this->subject->setData(
            [
                'ttl'  => 7200,
                'host' => 'test.com',
                'txt'  => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 IN TXT "text here"', $this->subject->toString());
    }

    public function testToStringCompleteWithChaosClass()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'CH',
                'host'  => 'test.com',
                'txt'   => 'text here'
            ]
        );
        $this->assertSame('test.com 7200 CH TXT "text here"', $this->subject->toString());
    }

    public function testGetEmptyText()
    {
        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'test.com',
                'txt'   => ''
            ]
        );
        $this->assertSame('test.com 7200 IN TXT ""', $this->subject->toString());
    }

    public function testGetExtendedTypeName()
    {
        $this->assertSame(ExtendedTxtRecords::DOMAIN_VERIFICATION, $this->subject->getTypeName());
    }

    public static function domainVerificationValidDataProvider(): array
    {
        return [
            ['google-site-verification=Mama-._Omida10', 'google'],
            ['facebook-domain-verification=BlueLibraries10', 'facebook'],
            ['cisco-ci-domain-verification=BlueLibraries10', 'cisco'],
            ['apple-domain-verification=BlueLibraries10', 'apple'],
            ['onetrust-domain-verification=BlueLibraries10', 'onetrust'],
            ['atlassian-domain-verification=BlueLibraries+/10', 'atlassian'],
            ['webexdomainverification.BlueLibraries=BlueLibraries10-', 'webex'],
            ['docusign=Mama-Omida', 'docusign'],
            ['MS=BlueLibraries1', 'office365'],
            ['globalsign-domain-verification=BlueLibraries-10_', 'globalsign'],
            ['e2ma-verification=BlueLibraries10', 'emma'],
            ['status-page-domain-verification=BlueLibraries10', 'atlassian'],
            ['mandrill_verify.', 'mailchimp'],
            ['ca3-BlueLibraries10', 'cloudflare'],
            ['docker-verification=Mama-Omida10', 'docker'],
            ['Dynatrace-site-verification=BlueLibraries-_10', 'dynatrace'],
            ['yandex-verification: Mama-_Omida10', 'yandex'],
            ['adobe-idp-site-verification=Mama-Omida10', 'adobe'],
            ['adobe-sign-verification=Mama-Omida10', 'adobe'],
            ['h1-domain-verification=Mama-Omida10', 'h1'],
            ['google-gws-recovery-domain-verification=2405', 'google'],
            ['smartsheet-site-validation=Mama-Omida10', 'smartsheet'],
            ['_github-challenge-Ana-Are_Mere-10=AreSi10Pere', 'github'],
            ['mongodb-site-verification=BlueLibraries10', 'mongodb'],
            ['amazonses:BlueLibraries=-/10', 'amazon-ses'],
            ['BlueLibraries10-=./.cloudfront.net', 'amazon-cloudfront'],
            ['pinterest-site-verification=Mama-Omida10=', 'pinterest'],
            ['stripe-verification=Mama-Omida10=', 'stripe'],
            ['miro-verification=BlueLibraries10', 'miro'],
            ['grive1ol-verification', null],
        ];
    }

    /**
     * @param string $txt
     * @param ?string $expectedProvider
     * @dataProvider domainVerificationValidDataProvider
     * @return void
     */
    public function testGetProviderValidProviders(string $txt, ?string $expectedProvider)
    {

        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'test.com',
                'txt'   => $txt
            ]
        );
        $this->assertSame($this->subject->getProvider(), $expectedProvider);
    }

    public static function domainVerificationValidDataValuesProvider(): array
    {
        return [
            ['google-site-verification=Mama-._Omida10', 'Mama-._Omida10'],
            ['facebook-domain-verification=BlueLibraries10', 'BlueLibraries10'],
            ['cisco-ci-domain-verification=BlueLibraries10', 'BlueLibraries10'],
            ['apple-domain-verification=BlueLibraries10', 'BlueLibraries10'],
            ['onetrust-domain-verification=BlueLibraries10', 'BlueLibraries10'],
            ['atlassian-domain-verification=BlueLibraries+/10', 'BlueLibraries+/10'],
            ['webexdomainverification.BlueLibraries=BlueLibraries10-', 'BlueLibraries10-'],
            ['docusign=Mama-Omida', 'Mama-Omida'],
            ['MS=BlueLibraries1', 'BlueLibraries1'],
            ['globalsign-domain-verification=BlueLibraries-10_', 'BlueLibraries-10_'],
            ['e2ma-verification=BlueLibraries10', 'BlueLibraries10'],
            ['status-page-domain-verification=BlueLibraries10', 'BlueLibraries10'],
            ['mandrill_verify.', 'mandrill_verify.'],
            ['ca3-BlueLibraries10', 'BlueLibraries10'],
            ['docker-verification=Mama-Omida10', 'Mama-Omida10'],
            ['Dynatrace-site-verification=BlueLibraries-_10', 'BlueLibraries-_10'],
            ['yandex-verification: Mama-_Omida10', 'Mama-_Omida10'],
            ['adobe-idp-site-verification=Mama-Omida10', 'Mama-Omida10'],
            ['adobe-sign-verification=Mama-Omida10', 'Mama-Omida10'],
            ['h1-domain-verification=Mama-Omida10', 'Mama-Omida10'],
            ['google-gws-recovery-domain-verification=2405', '2405'],
            ['smartsheet-site-validation=Mama-Omida10', 'Mama-Omida10'],
            ['_github-challenge-Ana-Are_Mere-10=AreSi10Pere', 'AreSi10Pere'],
            ['mongodb-site-verification=BlueLibraries10', 'BlueLibraries10'],
            ['amazonses:BlueLibraries=-/10', 'BlueLibraries=-/10'],
            ['BlueLibraries10-=./.cloudfront.net', 'BlueLibraries10-=./'],
            ['pinterest-site-verification=Mama-Omida10=', 'Mama-Omida10='],
            ['stripe-verification=Mama-Omida10=', 'Mama-Omida10='],
            ['miro-verification=BlueLibraries10', 'BlueLibraries10'],
            ['grive1ol-verification', 'grive1ol-verification'],
        ];
    }

    /**
     * @param string $txt
     * @param ?string $expectedValue
     * @dataProvider domainVerificationValidDataValuesProvider
     * @return void
     */
    public function testGetProviderValidProvidersValues(string $txt, ?string $expectedValue)
    {

        $this->subject->setData(
            [
                'ttl'   => 7200,
                'class' => 'IN',
                'host'  => 'test.com',
                'txt'   => $txt
            ]
        );
        $this->assertSame($this->subject->getValue(), $expectedValue);
    }

}

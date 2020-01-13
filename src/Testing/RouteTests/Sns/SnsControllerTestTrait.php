<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Sns;

use Antriver\LaravelSiteScaffolding\Testing\Traits\TestsSesNotificationsTrait;
use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Illuminate\Http\Request;

trait SnsControllerTestTrait
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = app(UserRepository::class);
    }

    private function assertUserEmailBounced(int $userId)
    {
        /** @var User $user */
        $user = $this->userRepository->findOrFail($userId);
        $this->assertTrue($user->emailBounced);
    }

    public function testSesBounceNotification()
    {
        $user = $this->seedUser(
            [
                'email' => 'bounce@simulator.amazonses.com'
            ]
        );

        $json = '{"Type":"Notification","MessageId":"dad68599-6341-5601-8b6f-d30caf93e7f0","TopicArn":"arn:aws:sns:us-east-1:006215690571:amirite-ses-bounce","Message":"{\"notificationType\":\"Bounce\",\"bounce\":{\"bounceType\":\"Permanent\",\"bounceSubType\":\"General\",\"bouncedRecipients\":[{\"emailAddress\":\"bounce@simulator.amazonses.com\",\"action\":\"failed\",\"status\":\"5.1.1\",\"diagnosticCode\":\"smtp; 550 5.1.1 user unknown\"}],\"timestamp\":\"2020-01-13T01:40:34.230Z\",\"feedbackId\":\"0100016f9c8f4af8-6aabbdbe-12e5-4145-a5a4-93202b21791b-000000\",\"remoteMtaIp\":\"35.173.153.29\",\"reportingMTA\":\"dsn; a8-60.smtp-out.amazonses.com\"},\"mail\":{\"timestamp\":\"2020-01-13T01:40:33.000Z\",\"source\":\"noreply@amirite.com\",\"sourceArn\":\"arn:aws:ses:us-east-1:006215690571:identity\/amirite.com\",\"sourceIp\":\"209.97.152.39\",\"sendingAccountId\":\"006215690571\",\"messageId\":\"0100016f9c8f47b5-fee65026-7356-4e0d-8f2c-eddc909f106f-000000\",\"destination\":[\"bounce@simulator.amazonses.com\"]}}","Timestamp":"2020-01-13T01:40:34.275Z","SignatureVersion":"1","Signature":"VffeJmplHtQI1n2q2wnpJaTOq9V0fhHWPVuuzz8aM31\/G89EgCnHQNgRyTvV+8YSzbaO9\/nb3ltTmAj7JlkEVjlJlSD\/gQYP6lZwzBu8ddmVA8CnvThfSGh3JjfIN410WOuxQmJaxlCwaY570dN9A9FF2XtKTBLNkrjrDB7soUR+ux+XBViuof9MxkYpymYo8HyHexloss9xk1xodVkFFdNe+DHQr6\/piY31cL3QBEsF6fP14kRwignDQS2s55uyHakg+nH39Qjs7uvxfLsMmRubwkj69IbzCynYZQeUBEOkoNYIPqbXkqRkSWKa2JG120Gtn1UaqaTNw2dGOh6ezw==","SigningCertURL":"https:\/\/sns.us-east-1.amazonaws.com\/SimpleNotificationService-a86cb10b4e1f29c941702d737128f7b6.pem","UnsubscribeURL":"https:\/\/sns.us-east-1.amazonaws.com\/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:us-east-1:006215690571:amirite-ses-bounce:29de3b5a-e3ef-4274-b121-6b1e1e6a50dc"}';
        $response = $this->call(
            Request::METHOD_POST,
            config('app.api_url').'/sns/ses-bounce',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
        $this->assertResponseOk($response);

        $this->assertUserEmailBounced($user->id);

        $rows = \DB::select(
            'SELECT * FROM `email_bounces` WHERE email = ?',
            [
                'bounce@simulator.amazonses.com'
            ]
        );
        $this->assertCount(1, $rows);
        $this->assertSame($user->id, $rows[0]->userId);
        $this->assertSame('bounce', $rows[0]->type);
        $this->assertSame($json, $rows[0]->message);
    }

    public function testSesComplaintNotification()
    {
        $user = $this->seedUser(
            [
                'email' => 'complaint@simulator.amazonses.com'
            ]
        );

        $json = '{"Type":"Notification","MessageId":"1a2a82ad-2e98-5d6f-8721-d438e3bbb616","TopicArn":"arn:aws:sns:us-east-1:006215690571:amirite-ses-complaint","Message":"{\"notificationType\":\"Complaint\",\"complaint\":{\"complaintSubType\":null,\"complainedRecipients\":[{\"emailAddress\":\"complaint@simulator.amazonses.com\"}],\"timestamp\":\"2020-01-13T01:41:55.000Z\",\"feedbackId\":\"0100016f9c90890b-6f1f0bd7-45fe-4b79-9911-7aa5042634e0-000000\",\"userAgent\":\"Amazon SES Mailbox Simulator\",\"complaintFeedbackType\":\"abuse\"},\"mail\":{\"timestamp\":\"2020-01-13T01:41:54.000Z\",\"source\":\"noreply@amirite.com\",\"sourceArn\":\"arn:aws:ses:us-east-1:006215690571:identity\/amirite.com\",\"sourceIp\":\"209.97.152.39\",\"sendingAccountId\":\"006215690571\",\"messageId\":\"0100016f9c90853d-be7cfb82-a9fb-450f-8731-4a5cc3c93d83-000000\",\"destination\":[\"complaint@simulator.amazonses.com\"]}}","Timestamp":"2020-01-13T01:41:55.712Z","SignatureVersion":"1","Signature":"R7r4FqB\/OO4a6JDH8x845XcoUis0Ob+4pTXOVehT1256VLSdidawRFymnCYar5cFCmYfAxEN6IN9q8U+KobTp5tMIVGmVa1EfalPu5Obz6x2cfTwTsWBeXkzmUxbfEpC4g4uuiRxv4ihgibZglXdX0UB9GYyj\/2f5kS1LYUcN8EZc2MouJK6sOl5qJuhtyZ1R2M3JCAZPEzIE1VM5CvgRprxBdPEyDeyiuYzTZ7Vkz7sl9a\/U1ZiKMgvHNtBR0a+chwTMaC4HZjlgteP5SeWIOyyI1badGe4pLxtY01dfiRF93XNQaIZiN9oES2hICwkR4yMjm+E4wV7Vn4qIh5wlA==","SigningCertURL":"https:\/\/sns.us-east-1.amazonaws.com\/SimpleNotificationService-a86cb10b4e1f29c941702d737128f7b6.pem","UnsubscribeURL":"https:\/\/sns.us-east-1.amazonaws.com\/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:us-east-1:006215690571:amirite-ses-complaint:38e94155-e316-42dd-836b-5c4a4fb600ed"}';
        $response = $this->call(
            Request::METHOD_POST,
            config('app.api_url').'/sns/ses-complaint',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
        $this->assertResponseOk($response);

        $this->assertUserEmailBounced($user->id);

        $rows = \DB::select(
            'SELECT * FROM `email_bounces` WHERE email = ?',
            [
                'complaint@simulator.amazonses.com'
            ]
        );
        $this->assertCount(1, $rows);
        $this->assertSame($user->id, $rows[0]->userId);
        $this->assertSame('complaint', $rows[0]->type);
        $this->assertSame($json, $rows[0]->message);
    }
}

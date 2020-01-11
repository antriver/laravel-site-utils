<?php

namespace Antriver\LaravelSiteScaffolding\Mail\Http;

use Antriver\LaravelSiteScaffolding\Debug\HeaderBagFormatter;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationManager;
use Aws\Sns\Message as SnsMessage;
use Aws\Sns\MessageValidator;
use Illuminate\Http\Request;

trait SnsControllerTrait
{
    /**
     * @param Request $request
     * @param EmailVerificationManager $emailVerificationManager
     */
    public function sesBounce(Request $request, EmailVerificationManager $emailVerificationManager)
    {
        $snsMessage = $this->getSnsMessageFromRequest();
        $this->notify('sns/ses-bounce', $request, $snsMessage);

        if ($data = $this->parseSnsMessageData($snsMessage)) {
            if (!empty($data->bounce) && !empty($data->bounce->bouncedRecipients)) {
                foreach ($data->bounce->bouncedRecipients as $bouncedRecipient) {
                    if (!empty($bouncedRecipient->emailAddress)) {
                        $emailVerificationManager->markUserEmailBounced($bouncedRecipient->emailAddress);
                        $this->log('bounce', $bouncedRecipient->emailAddress, $snsMessage, $emailVerificationManager);
                    }
                }
            }
        }
    }

    /**
     * @param Request $request
     * @param EmailVerificationManager $emailVerificationManager
     */
    public function sesComplaint(Request $request, EmailVerificationManager $emailVerificationManager)
    {
        $snsMessage = $this->getSnsMessageFromRequest();
        $this->notify('sns/ses-complaint', $request, $snsMessage);

        if ($data = $this->parseSnsMessageData($snsMessage)) {
            if (!empty($data->complaint) && !empty($data->complaint->complainedRecipients)) {
                foreach ($data->complaint->complainedRecipients as $complainedRecipient) {
                    if (!empty($complainedRecipient->emailAddress)) {
                        $emailVerificationManager->markUserEmailBounced($complainedRecipient->emailAddress);
                        $this->log(
                            'complaint',
                            $complainedRecipient->emailAddress,
                            $snsMessage,
                            $emailVerificationManager
                        );
                    }
                }
            }
        }
    }

    private function log(
        string $type,
        string $email,
        SnsMessage $message,
        EmailVerificationManager $emailVerificationManager
    ) {
        $emailVerificationManager->logBounce(
            $type,
            $email,
            $message['Message'] // This is json encoded already.
        );
    }

    /**
     * @param string $logMessage
     * @param Request $request
     * @param SnsMessage $message
     */
    private function notify(string $logMessage, Request $request, SnsMessage $message)
    {
        \Log::channel('slack-logs')->info(
            $logMessage,
            [
                'Headers' => HeaderBagFormatter::implodeValues($request->headers),
                'Message' => $message->toArray(),
            ]
        );
    }

    /**
     * @return SnsMessage
     */
    private function getSnsMessageFromRequest(): SnsMessage
    {
        $message = SnsMessage::fromRawPostData();

        // Validate the message (throws an exception if bad).
        $validator = new MessageValidator();
        $validator->validate($message);

        return $message;
    }

    private function parseSnsMessageData(SnsMessage $message)
    {
        $data = json_decode($message['Message']);

        return $data ?: null;
    }
}

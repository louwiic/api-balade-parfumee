<?php

namespace App\Services;
use \DrewM\MailChimp\MailChimp;

class MailChimpService
{
    public Mailchimp $mailchimp;

    public function __construct(MailChimp $mailChimp)
    {
        $this->mailChimp = $mailChimp;
        $this->mailChimp->verify_ssl = false; // use request http (only for test, need use https for encrypt the token api.)
    }
    public function subscribedMembers(): Response
    {
        $list_id = "11f0aee7a2"; // a list refers to an audience at Mailchimp

        $subscriber = array(
            'email_address' => 'nouveau@email.com',
            'status'        => 'subscribed',
            'merge_fields'  => array(
                'FNAME'     => 'Prénom',
                'LNAME'     => 'Nom'
            )
        );
        $result = $this->mailchimp->post("lists/$list_id/members", $subscriber);
        return $this->mailchimp->success();
    }
}
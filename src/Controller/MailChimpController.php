<?php

namespace App\Controller;

use DrewM\MailChimp\MailChimp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class MailChimpController extends AbstractController
{
    private MailChimp $mailChimp;

    public function __construct(MailChimp $mailChimp)
        {
            $this->mailChimp = $mailChimp;
            $this->mailChimp->verify_ssl = false; // use request http (only for test, need use https for encrypt the token api.)
        }

    #[Route('/get_list_mailChimp', name: 'get lists mailChimp')]
    public function mailChimp(Request $request): Response
    {
        $mailchimp = $this->mailChimp;

        $response = $this->mailChimp->get('lists');

        if ($mailchimp->success()) {
            return new JsonResponse($response['lists']);
        }
        return new Response( "Une erreur est survenue.");
    }
    private function addTagToListMember($listId, $subscriberHash, $tag): void
    {
        $tagData =  [
            "tags" => [["name" => $tag, "status" => "active"]]
        ];

       $this->mailchimp->post("lists/$listId/members/$subscriberHash/tags", $tagData);
    }
    private function addSubscriberWithTags($listId, $email, $firstName, $lastName, array $tags): bool
    {
        $subscriberHash = md5(strtolower($email));

        // Vérifier si l'email existe déjà dans la liste
        $existingMember = $this->mailchimp->get("lists/$listId/members/$subscriberHash");

        // Si l'email existe déjà, ajouter les tags uniquement s'ils n'existent pas déjà
        if ($existingMember && isset($existingMember['tags'])) {
            $existingTags = array_column($existingMember['tags'], 'name');
            $tagsToAdd = array_diff($tags, $existingTags);
            if (!empty($tagsToAdd)) {

                foreach ($tagsToAdd as $tag) {

                    ///$existingTag = $this->getTagIsExist($listId, $tag);

                   // if ($existingTag === false) {
                        //$this->createTag($listId, $tag);
                    //}
                    $this->addTagToListMember($listId, $subscriberHash, $tag);
                }
            }

            return true;
        }

        // Si l'email n'existe pas, ajouter le nouvel abonné avec les tags
        $subscriber = array(
            'email_address' => $email,
            'status'        => 'subscribed',
            'merge_fields'  => array(
                'FNAME'     => $firstName,
                'LNAME'     => $lastName
            ),
            'tags'          => $tags
        );

        $result = $this->mailchimp->post("lists/$listId/members", $subscriber);

        return $this->mailchimp->success();
    }

    private function getTagIsExist($listId, $tagName)
    {
        $queryParams = array(
            'name' => "$tagName"
        );

        $tags = $this->mailchimp->get("lists/$listId/tag-search", $queryParams);

        foreach ($tags['tags'] as $tag) {
            if ($tag['name'] === $tagName) {
                return true;
            }
        }

        return false;
    }
    private function createTag($listId, $tagName): void
    {
        $tagData = array(
            'name' => $tagName
        );

      //  dd($this->mailchimp->post("/lists/$listId/segments", $tagData));
    }
    #[Route('/subscriber_members_in_lists', name: 'subscriber_members_in_lists')]
    public function subscribedMembers(MailChimp $mailchimp, Request $request): Response
    {
        $this->mailchimp = $mailchimp;
        $list_id = "11f0aee7a2"; // a list refers to an audience at Mailchimp

        $subscriber = array(
            'email_address' => 'nouveau2@email.com',
            'status'        => 'subscribed',
            'merge_fields'  => array(
                'FNAME'     => 'Prénom',
                'LNAME'     => 'Nom'
            ),
             'tags'          => array('InscritAppBaladeParfume')
        );

        $result = $mailchimp->post("lists/$list_id/members", $subscriber);
        $this->addSubscriberWithTags($list_id, 'hordeofhorde@gmail.com', "Prénom", "Nom",  ['InscritAppBaladeParfume2']);
        if ($this->mailchimp ->success())
            return new Response( 'L\'abonné a été ajouté avec succès !');
        return new Response( 'kk');
    }
}
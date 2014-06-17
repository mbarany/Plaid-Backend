<?php
namespace Barany\Plaid\MainBundle\Controller;

use Barany\Plaid\MainBundle\Entity\Account;
use Barany\Plaid\MainBundle\Entity\Institution;
use Barany\Plaid\MainBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use Httpful;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends BaseController
{
    /**
     * @Router\Route("/api/institutions")
     * @Router\Method({"GET"})
     */
    public function institutions()
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult('Barany\Plaid\MainBundle\Entity\Institution', 'i')
            ->addFieldResult('i', 'id', 'id')
            ->addFieldResult('i', 'name', 'name')
            ->addFieldResult('i', 'code', 'code');
        $sql = <<<SQL
            SELECT i.id, i.name, i.code
            FROM institution i FORCE INDEX(name_idx)
            ORDER BY i.name ASC
SQL;
        $query = $this
            ->getDoctrine()
            ->getManager()
            ->createNativeQuery($sql, $rsm);

        /** @var Institution[] $institutions */
        $institutions = $query->getResult();
        return $this->renderJson($institutions);
    }

    /**
     * @Router\Route("/api/accounts")
     * @Router\Method({"GET"})
     * @return Response
     */
    public function accounts()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->renderJson($user ? $user->getAccounts() : []);
    }

    /**
     * @Router\Route("/api/account/{account_id}")
     * @Router\Method({"GET"})
     * @param int $account_id
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function account($account_id)
    {
        /** @var Account $account */
        $account = $this
            ->getDoctrine()
            ->getManager()
            ->find('Barany\Plaid\MainBundle\Entity\Account', $account_id);
        /** @var User $user */
        $user = $this->getUser();
        if (!$account || $account->getUser()->getId() != $user->getId()) {
            throw new NotFoundHttpException();
        }

        $plaidData = $this->getPlaidAccount($account);

        $accounts = [];
        foreach ($plaidData->body->accounts as $bankAccount) {
            $accounts[$bankAccount->_id] = [
                'info' => $bankAccount,
                'transactions' => [],
            ];
        }
        foreach ($plaidData->body->transactions as $transaction) {
            $accounts[$transaction->_account]['transactions'][] = $transaction;
        }

        return $this->renderJson(
            [
                'id' => $account->getId(),
                'institution' => $account->getInstitution()->toApi(),
                'accounts' => array_values($accounts),
            ]
        );
    }

    /**
     * @Router\Route("/api/authenticate")
     * @Router\Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function authenticate(Request $request)
    {
        $accessToken = $request->get('accessToken');
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken;
        $response = Httpful\Request::get($url)->send();
        if (!isset($response->body->email)) {
            throw new AccessDeniedHttpException();
        }
        $email = $response->body->email;
        /** @var User $user */
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('Barany\Plaid\MainBundle\Entity\User')
            ->findOneBy(['email' => $email]);
        if (!$user) {
            throw new AccessDeniedHttpException();
        }
        return $this->renderJson(['token' => $user->getApiTokens()->first()->getToken()]);
    }

    /**
     * @param Account $account
     * @return Httpful\Response
     */
    private function getPlaidAccount(Account $account) {
        $access_token = $account->getAccessToken();
        $params = [
            'client_id' => $this->container->getParameter('plaid_client_id'),
            'secret' => $this->container->getParameter('plaid_secret'),
            'access_token' => $access_token,
            'options' => json_encode(['pending' => true]),
        ];
        $concatedParams = array();
        foreach ($params as $k => $v) {
            $concatedParams[] = "$k=$v";
        }
        $request = Httpful\Request::get(
                $this->container->getParameter('plaid_api_endpoint') . 'connect?' . implode('&', $concatedParams),
                'application/json'
            )
            ->expects('application/json');

        return $request->send();
    }
}

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
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiController extends BaseController
{
    /**
     * @Router\Route("/api/institutions")
     */
    public function institutions()
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult('Barany\Plaid\MainBundle\Entity\Institution', 'i')
            ->addFieldResult('i', 'id', 'id')
            ->addFieldResult('i', 'name', 'name')
            ->addFieldResult('i', 'code', 'code');
        $sql = "SELECT i.id, i.name, i.code FROM institution i FORCE INDEX(name_idx) ORDER BY i.name ASC";
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
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function accounts(Request $request)
    {
        $user = $request->getSession()->get('User');
        if (!$user) {
            //@todo Handle this in security/firewall
            throw new UnauthorizedHttpException('None');
        }
        /** @var User $user */
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->find('Barany\Plaid\MainBundle\Entity\User', $user['id']);
        return $this->renderJson($user ? $user->getAccounts() : []);
    }

    /**
     * @Router\Route("/api/account/{account_id}")
     * @param Request $request
     * @param int $account_id
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function account(Request $request, $account_id)
    {
        /** @var Account $account */
        $account = $this
            ->getDoctrine()
            ->getManager()
            ->find('Barany\Plaid\MainBundle\Entity\Account', $account_id);
        $user = $request->getSession()->get('User');
        if (!$user) {
            //@todo Handle this in security/firewall
            throw new UnauthorizedHttpException('None');
        }
        if (!$account || $account->getUser()->getId() != $user['id']) {
            exit;
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
     * @param string $accessToken
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function authenticate(Request $request, $accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken;
        $response = Httpful\Request::get($url)->send();
        if (!isset($response->body->email)) {
            //@todo Handle this in security/firewall
            throw new UnauthorizedHttpException('None');
        }
        $email = $response->body->email;
        /** @var User $user */
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('Barany\Plaid\MainBundle\Entity\User')
            ->findOneBy(['email' => $email]);
        if (!$user) {
            //@todo Handle this in security/firewall
            throw new UnauthorizedHttpException('None');
        }
        $request->getSession()->set('User', array(
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ));
        return $this->renderJson(['token' => session_id()]);

        /**
         * @todo: Store a permanent API Token for each User (for each platform?)
         */

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

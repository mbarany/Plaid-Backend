<?php
namespace Barany\Controller;

use Barany\Core\AppController;
use Barany\Model\Account;
use Barany\Model\Institution;
use Barany\Model\User;
use Doctrine\ORM\Query\ResultSetMapping;
use Httpful\Request;
use Httpful\Response;

class Api extends AppController {
    public function institutions($httpRequest) {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult('Barany\Model\Institution', 'i')
            ->addFieldResult('i', 'id', 'id')
            ->addFieldResult('i', 'name', 'name')
            ->addFieldResult('i', 'code', 'code');
        $sql = "SELECT i.id, i.name, i.code FROM institution i FORCE INDEX(name_idx) ORDER BY i.name ASC";
        $query = $this
            ->getEntityManager()
            ->createNativeQuery($sql, $rsm);

        /** @var Institution[] $institutions */
        $institutions = $query->getResult();
        $this->renderJson($institutions);
    }

    public function accounts($httpRequest) {
//@todo: Use session uid
$userId = 1;
        /** @var User $user */
        $user = $this->getEntityManager()->find('Barany\Model\User', $userId);
        $this->renderJson($user ? $user->getAccounts() : []);
    }

    public function account($httpRequest) {
        /** @var Account $account */
        $account = $this->getEntityManager()->find('Barany\Model\Account', $httpRequest->account_id);

        $plaidData = $this->getPlaidAccount($account);

        $accounts = [];
        foreach ($plaidData->body->accounts as $bankAccount) {
            $accounts[$bankAccount->_id] = [
                'account' => $bankAccount,
                'transactions' => [],
            ];
        }
        foreach ($plaidData->body->transactions as $transaction) {
            $accounts[$transaction->_account]['transactions'][] = $transaction;
        }

        $this->renderJson(
            [
                'institution' => $account->getInstitution()->toApi(),
                'accounts' => array_values($accounts),
            ]
        );
    }

    /**
     * @param Account $account
     * @return Response
     */
    private function getPlaidAccount(Account $account) {
        $access_token = $account->getAccessToken();
        $params = array_merge(
            $this->getAppConfig()->getPlaidApiCredentials(),
            array(
                'access_token' => $access_token,
            )
        );
        $concatedParams = array();
        foreach ($params as $k => $v) {
            $concatedParams[] = "$k=$v";
        }
        $request = Request::get(
                $this->getAppConfig()->getPlaidApiEndpoint() . 'connect?' . implode('&', $concatedParams),
                'application/json'
            )
            ->expects('application/json');

        return $request->send();
    }
} 
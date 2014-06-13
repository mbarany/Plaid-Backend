<?php
namespace Barany\Plaid\MainBundle\Controller;

use Barany\Plaid\MainBundle\Entity\Exportable;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends Controller
{
    /**
     * @param mixed $data
     * @return JsonResponse
     */
    protected function renderJson($data = null) {
        if (null === $data || (!is_array($data) && !$data instanceof Collection)) {
            return new JsonResponse($data);
        }
        $exportedData = [];
        foreach ($data as $k => $v) {
            $exportedData[$k] = $v instanceof Exportable ? $v->toApi() : $v;
        }
        return new JsonResponse($exportedData);
    }
}

<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security as NelmioSecurity;
/**
 * @Route("/api/v1/transactions")
 */
class TransactionAPIController extends AbstractController
{
    private const TYPE = [
        'payment' => 0,
        'deposit' => 1
    ];
    /**
     * @OA\GET(
     *     tags={"Transactions"},
     *     path="/api/v1/transactions/",
     *     summary="Список транзакций",
     *     description="Список транзакций пользователя",
     *     operationId="transactions.index",
     *     security={
     *         { "Bearer":{} },
     *     },
     * )
     * @Route("/", name="app_transactions")
     */
    public function index(Request $request, TransactionRepository $transactionRepository, SerializerInterface $serializer): Response
    {
        $filters = [];
        $filters['type'] = $request->query->get('type') ? self::TYPE[$request->query->get('type')] : null;
        $filters['course_code'] = $request->query->get('course_code');
        $filters['skip_expired'] = $request->query->get('skip_expired');
        $transactions = $transactionRepository->findUserTransactionsByFilters($this->getUser(), $filters);
        $outTransactions =[];
        foreach ($transactions as $transaction){
            $outTransaction = [
                'id' => $transaction->getId(),
                'created_at' => $transaction->getDatetimeTransaction(),
                'type' => $transaction->getType(),
            ];

            if ($transaction->getType() == 'payment') {

                $outTransaction['course_code'] = $transaction->getCourse()->getCharCode();
            }
            $outTransaction['ammount']= $transaction->getValue();
            $outTransactions[] = $outTransaction;
        }


        $transactionResponse = $serializer->serialize($outTransactions, 'json');

        $response = new JsonResponse();
        $response->setContent($transactionResponse);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }
}


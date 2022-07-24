<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Service\PayService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security as NelmioSecurity;

/**
 * * @Route("/api/v1/courses")
 */
class CourseAPIController extends AbstractController
{
    /**
     * @OA\Get(
     *     tags={"Courses"},
     *     path="/api/v1/courses/",
     *     summary="Список курсов",
     *     description="Список курсов",
     *     operationId="courses.index",
     * )
     * @Route("/", name="app_course")
     */
    public function index(CourseRepository $courseRepository, SerializerInterface $serializer): Response
    {
        $courses = $courseRepository->findAll();
        $outCourses = [];
        foreach ($courses as $course){
            $outCourse = [
                'code' => $course->getCharCode(),
                'type' => $course->getType()
            ];
            if ($course->getType() != 'free'){
                $outCourse['price'] = $course->getCost();
            }
            $outCourses[] = $outCourse;
        }
        $coursesResponse = $serializer->serialize($outCourses, 'json');

        $response = new JsonResponse();
        $response->setContent($coursesResponse);
        return $response;
    }
    /**
     * @OA\Get(
     *     tags={"Courses"},
     *     path="/api/v1/courses/{code}",
     *     summary="Информация о курсе",
     *     description="Информация о курсе",
     *     operationId="courses.show1",
     * )
     * @Route("/{code}", name="app_one_course")
     */
    public function show1(CourseRepository $courseRepository, SerializerInterface $serializer, string $code): Response
    {
        $course = $courseRepository->findOneBy(['char_code' => $code]);
        $outCourse = [];
        if ($course != null){
            $outCourse = [
                'code' => $course->getCharCode(),
                'type' => $course->getType()
            ];
            if ($course->getType() != 'free'){
                $outCourse['price'] = $course->getCost();
            }
        }
        else {
            $outCourse = [
                'code' => 404,
                'message' =>'Такой курс отсутствует!'
            ];
        }

        $coursesResponse = $serializer->serialize($outCourse, 'json');

        $response = new JsonResponse();
        $response->setContent($coursesResponse);
        return $response;
    }

    /**
     * @OA\Post(
     *     tags={"Courses"},
     *     path="/api/v1/courses/{code}/pay",
     *     summary="Оплата курса",
     *     description="Оплата курса",
     *     operationId="courses.paying",
     *     security={
     *         { "Bearer":{} },
     *     },
     *)
     * @Route("{code}/pay", name="app_course_pay")
     */
    public function paying(
        string              $code,
        CourseRepository    $courseRepository,
        PayService      $payService,
        SerializerInterface $serializer) : Response {

        $course = $courseRepository->findOneBy(['char_code' => $code]);
        $response = new JsonResponse();

        $responseCode = '';
        $responseData = [];
        if (!$course) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Курс не найден'
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $user = $this->getUser();
        try {
            $transaction = $payService->pay($user, $course);
        } catch (\Exception $exception) {
            throw new HttpException($exception->getCode(), $exception->getMessage());
        }

        return $response;
    }
}


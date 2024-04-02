<?php
namespace App\Controllers\Api;

use App\Database\SqlBuilder\Param;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;
use App\Models\Postindex;
use PDO;
use OpenApi\Attributes as OA;


#[OA\Info(
    version: '1.0.0',
    title: 'My API'
)]
class PostindexController extends Controller {

    protected int $default_per_page = 50;

    #[OA\Get(
        path: '/api/postindex',
        summary: "Gets all posts",
        tags: ["Posts"],
        parameters: [
            new OA\Parameter(
                in: "query",
                name: "page",
                example: "1",
                description: "Paginates results"
            ),
            new OA\Parameter(
                in: "query",
                name: "per_page",
                example: "1",
                description: "How much items to show on a page"
            ),
            new OA\Parameter(
                in: "query",
                name: "settlement",
                example: "Київ",
                description: "Searching post by setted value"
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'AOK'),
        ]
    )]
    function index(Request $request, Response $response) : Response {
        $query_params = $this->indexQueryParams($request);

        [
            "per_page" => $per_page,
            "offset" => $offset,
            "settlement" => $settlement,
        ] = $query_params;

//        dd($relative_links);

        $model = Postindex::initSelect();

        if(!empty($settlement)){
            $model->append("
                WHERE
                    `region` LIKE :keyword OR
                    `district_old` LIKE :keyword OR
                    `district_new` LIKE :keyword OR
                    `settlement` LIKE :keyword OR
                    `region_en` LIKE :keyword OR
                    `district_new_en` LIKE :keyword OR
                    `settlement_en` LIKE :keyword OR
                    `post_office` LIKE :keyword OR
                    `post_office_en` LIKE :keyword
                ORDER BY
                    `region`,`district_old`,`district_new`,`settlement`,
                    `region_en`,`district_new_en`,`settlement_en`,`post_office`,
                    `post_office_en`
                ASC
            ", [new Param(":keyword", '%' . $settlement . '%', PDO::PARAM_STR)]);
        }else{
            $model->append("
                ORDER BY `post_code_of_post_office` ASC
            ");
        }

        $model_count = (clone $model)->format(function($sql) {
            return str_replace("*", "COUNT(*)", $sql);
        });

        $per_page = intval($per_page);
        $offset = intval($offset);

        $total = $model_count->fetch(PDO::FETCH_COLUMN);
        $model->append("LIMIT {$offset}, {$per_page}");
        $data = $model->fetchAll();

        $response_data = $this->indexResponseData($request, $query_params, $data, $total, 'postindex.index');

        return $this->jsonResponse($response, $response_data, 200);
    }

    #[OA\Get(
        path: '/api/postindex/{index}',
        summary: "Gets one post by index",
        tags: ["Posts"],
        parameters: [
            new OA\Parameter(
                in: "path",
                name: "index",
                example: "1435",
                description: "Post`s index to show"
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'AOK'),
            new OA\Response(response: 404, description: 'Post index not found'),
        ]
    )]
    function show(Request $request, Response $response) {
        $index = $request->getAttribute('index');

        $builder = Postindex::initSelect()->append("
            WHERE `post_code_of_post_office` = :post_code_of_post_office LIMIT 1
        ", [new Param(':post_code_of_post_office', $index, PDO::PARAM_INT)]);

//        dd($builder->columns());

        $result = $builder->fetch();

        if(empty($result))
            return $this->jsonResponse($response, ["data" => []], 404);

        return $this->jsonResponse($response, ["data" => $result]);
    }

    #[OA\Delete(
        path: '/api/postindex/{index}',
        summary: "Deletes one post by index",
        tags: ["Posts"],
        parameters: [
            new OA\Parameter(
                in: "path",
                name: "index",
                example: "1435",
                description: "Post index to delete"
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'AOK'),
            new OA\Response(response: 404, description: 'Post index not found'),
        ]
    )]
    function destroy(Request $request, Response $response) {
        $index = $request->getAttribute('index');

        $select_builder = Postindex::initSelect("COUNT(*)");
        $select_builder->append("
            WHERE `post_code_of_post_office` = :post_code_of_post_office
        ", [new Param(":post_code_of_post_office", $index, PDO::PARAM_INT)]);

        $isExist = $select_builder->fetch(PDO::FETCH_COLUMN);

        if(!$isExist)
            return $this->jsonResponse($response, [
                "msg" => "Check your request parameters.",
                "error" => "Not found index - `{$index}`."
            ], 404);

        $delete_builder = Postindex::initDelete();
        $delete_builder->append("
            WHERE `post_code_of_post_office` = :post_code_of_post_office
        ", [new Param(":post_code_of_post_office", $index, PDO::PARAM_INT)]);

        $delete_builder->execute();

        return $this->jsonResponse($response, [
            "msg" => "Successfully deleted index - {$index}.",
            "deleted_index" => (int)$index
        ], 200);
    }

    #[OA\Post(
        path: '/api/postindex',
        summary: "Adds post(s)",
        tags: ["Posts"],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        required: [
                            "post_code_of_post_office", "region", "district_old", "district_new",
                            "settlement", "postal_code", "settlement_en", "region_en",
                            "district_new_en", "post_office", "post_office_en"
                        ],
                        properties: [
                            new OA\Property(
                                property: "post_code_of_post_office",
                                type: "integer",
                                example: 1234
                            ),
                            new OA\Property(
                                property: "region",
                                type: "string",
                                example: "Хмільник"
                            ),
                            new OA\Property(
                                property: "district_old",
                                type: "string",
                                example: "Хмільник"
                            ),
                            new OA\Property(
                                property: "district_new",
                                type: "string",
                                example: "Хмільницький"
                            ),
                            new OA\Property(
                                property: "settlement",
                                type: "string",
                                example: "м. Хмільник"
                            ),
                            new OA\Property(
                                property: "postal_code",
                                type: "integer",
                                example: 22000
                            ),
                            new OA\Property(
                                property: "settlement_en",
                                type: "string",
                                example: "Vinnytska"
                            ),
                            new OA\Property(
                                property: "region_en",
                                type: "string",
                                example: "Vinnytska"
                            ),
                            new OA\Property(
                                property: "district_new_en",
                                type: "string",
                                example: "KHMILNYTSKYI"
                            ),
                            new OA\Property(
                                property: "post_office",
                                type: "string",
                                example: "Хмільник"
                            ),
                            new OA\Property(
                                property: "post_office_en",
                                type: "string",
                                example: "Khmіlnyk"
                            ),
                        ]
                    )
                ),
            ]
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created successfully'),
            new OA\Response(response: 422, description: 'Can`t be created'),
        ]
    )]
    function store(Request $request, Response $response) {
//        return $this->jsonResponse($response, [
//            "msg" => "Success",
//        ], 200);

        $parsedBody = $request->getParsedBody();
//        dd(111);
        $parse_data_first_key = array_key_first($parsedBody);
        if(!is_numeric($parse_data_first_key))
            $parsedBody = [$parsedBody];

        $error_msg = "All fields must be filled in correctly.";

        $validator = V::keySet(
            v::key("post_code_of_post_office", v::numericVal()->length(3, 6)->uniquePostindex()),
            v::key('region', v::stringType()->notEmpty()),
            v::key('district_old', v::stringType()->notEmpty()),
            v::key('district_new', v::stringType()->notEmpty()),
            v::key('settlement', v::stringType()->notEmpty()),
            v::key('postal_code', v::numericVal()),
            v::key('settlement_en', v::stringType()->notEmpty()),
            v::key('region_en', v::stringType()->notEmpty()),
            v::key('district_new_en', v::stringType()->notEmpty()),
            v::key('post_office', v::stringType()->notEmpty()),
            v::key('post_office_en', v::stringType()->notEmpty()),
        );

//        $validator = v::key('data',
//            is_numeric($parse_data_first_key) ?
//                v::arrayVal()->each(
//                    $validator
//                ) : $validator
//        );

        $idx_validation = 0;
        $errors = [];

        foreach ($parsedBody as $data) {
            try {
                $validator->assert($data);
                $idx_validation++;
            } catch (\Respect\Validation\Exceptions\NestedValidationException  $e) {
                $errors[] = [
                    "idx_validation" => $idx_validation,
                    "errors" => $e->getMessages()
                ];
                $idx_validation++;
            }
        }

        if(!empty($errors))
            return $this->jsonResponse($response, [
                "msg" => $error_msg,
                "errors" => $errors
            ], 422);


        $post_codes = array_column($parsedBody, 'post_code_of_post_office');
        if(count(array_unique($post_codes)) !== count($post_codes))
            return $this->jsonResponse($response, [
                "msg" => $error_msg,
                "errors" => "All `post_code_of_post_office` must be unique values"
            ], 422);

        $insert_builder = Postindex::initInsert();

        foreach ($parsedBody as $row) {
            $row['from_api'] = 1;

            $insert_builder->appendData($row, false, function($sql, $keys){
                return $sql . "(" . $keys . "), ";
            });
        }

        $insert_builder->format(function($sql) {
            return rtrim($sql, ", ");
        });

        $insert_builder->execute();

        return $this->jsonResponse($response, [
            "msg" => "Successfully added indexes - " . implode(", ", $post_codes) . ".",
            "added_indexes" => array_map(fn($itm) => (int)$itm, $post_codes),
        ], 201);
    }

}
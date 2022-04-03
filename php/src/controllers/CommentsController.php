<?php declare(strict_types=1);

require_once __DIR__ . '/../services/CommentsService.php';
require 'ApiResponse.php';

final class CommentsController
{
    private $commentsService;

    public function __construct($dbConnection)
    {
        $this->commentsService = new CommentsService($dbConnection);
    }

    public function processRequest(ApiRequest $request): ApiResponse
    {
        switch ($request->getRequestMethod()) {
            case 'GET':
                $id = $request->getId();
                if ($id !== null) {
                    $response = $this->findById($id);
                } else {
                    $response = $this->findAll();
                }
                break;
            case 'POST':
                $response = $this->insert($request->getRequestBody());
                break;
            case 'PUT':
                $id = $request->getId();
                if ($id !== null) {
                    $response = $this->update($id, $request->getRequestBody());
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            case 'DELETE':
                $id = $request->getId();
                if ($id !== null) {
                    $response = $this->deleteById($id);
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            default:
                $response = $this->methodNotAllowedResponse();
                break;
        }

        return $response;
    }

    private function update(string $id, string $requestBody): ApiResponse
    {
        try
        {
            $fieldErrors = [];
            $input = $this->getValidatedFields($requestBody, $fieldErrors);

            if (!empty($fieldErrors))
            {
                return new ApiResponse(ApiResponse::STATUS_BAD_REQUEST, null, ['fieldErrors' => $fieldErrors]);
            }

            $this->commentsService->update(intval($id), $input);
            return $this->findById($id);
        } catch (Exception $e)
        {
            return new ApiResponse(ApiResponse::STATUS_SERVER_ERROR, null, ['error' => $e->getMessage()]);
        }
    }

    private function insert(string $requestBody): ApiResponse
    {
        try
        {
            $fieldErrors = [];
            $input = $this->getValidatedFields($requestBody, $fieldErrors);

            if (!empty($fieldErrors))
            {
                return new ApiResponse(ApiResponse::STATUS_BAD_REQUEST, null, ['fieldErrors' => $fieldErrors]);
            }

            $commentId = $this->commentsService->insert($input);
            return $this->findById($commentId);
        } catch (Exception $e)
        {
            return new ApiResponse(ApiResponse::STATUS_SERVER_ERROR, null, ['error' => $e->getMessage()]);
        }
    }

    private function getValidatedFields(string $requestBody, Array &$fieldErrors): array
    {
        $input = json_decode($requestBody, true);

        if ($input['author'] === null || $input['author'] === '')
        {
            $fieldErrors['author'] = 'Missing author name';
        }

        if ($input['text'] === null || $input['text'] === '')
        {
            $fieldErrors['text'] = 'Missing text';
        }
        return $input;
    }

    private function deleteById(string $id): ApiResponse
    {
        try
        {
            $this->commentsService->deleteById(intval($id));
            return new ApiResponse(ApiResponse::STATUS_NO_CONTENT, null, null);
        } catch (Exception $e)
        {
            return new ApiResponse(ApiResponse::STATUS_SERVER_ERROR, null, ['error' => $e->getMessage()]);
        }
    }

    private function findById(string $id): ApiResponse
    {
        try
        {
            $comment = $this->commentsService->findById(intval($id));
            return new ApiResponse(ApiResponse::STATUS_OK, null, $comment);
        } catch (Exception $e)
        {
            return new ApiResponse(ApiResponse::STATUS_SERVER_ERROR, null, ['error' => $e->getMessage()]);
        }
    }

    private function findAll(): ApiResponse
    {
        try
        {
            $comments = $this->commentsService->findAll();
            return new ApiResponse(ApiResponse::STATUS_OK, null, $comments);
        } catch (Exception $e)
        {
            return new ApiResponse(ApiResponse::STATUS_SERVER_ERROR, null, ['error' => $e->getMessage()]);
        }
    }

    private function methodNotAllowedResponse(): ApiResponse
    {
        return new ApiResponse(ApiResponse::STATUS_METHOD_NOT_ALLOWED);
    }

    private function notFoundResponse(): ApiResponse
    {
        return new ApiResponse(ApiResponse::STATUS_NOT_FOUND);
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContentGeneration\AIService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AiController extends Controller
{
    /**
     * @throws Exception
     */
    final public function generateText(Request $request, AIService $aiService): JsonResponse
    {
        $prompt = $request->input('prompt') ?? throw new Exception('Prompt is required');
        $model = $request->input('model', 'gpt');

        $result = $aiService->generateText($model, $prompt);

        return response()->json(compact('model', 'result'));
    }


    /**
     * @throws Exception
     */
    final public function generateImage(Request $request, AIService $aiService): JsonResponse
    {
        $description = $request->input('description') ?? throw new Exception('Description is required');
        $model = $request->input('model', 'gpt');

        $imageUrl = $aiService->generateImage($model, $description);

        return response()->json(compact('model', 'imageUrl'));
    }

}

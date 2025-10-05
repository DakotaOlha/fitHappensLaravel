<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Validator;

class ColorTypeController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function detectColorType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eyes' => 'required|string|max:100',
            'hair' => 'required|string|max:100',
            'skin' => 'required|string|max:100',
        ], [
            'eyes.required' => 'Колір очей обов\'язковий',
            'hair.required' => 'Колір волосся обов\'язковий',
            'skin.required' => 'Відтінок шкіри обов\'язковий',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $eyes = $request->input('eyes');
            $hair = $request->input('hair');
            $skin = $request->input('skin');

            $prompt = "Визнач кольоротип людини (Весна, Літо, Осінь або Зима).
Відповідай тільки одним словом з цих чотирьох варіантів.

Дані:
- Очі: {$eyes}
- Волосся: {$hair}
- Шкіра: {$skin}";

            // Виправлено: викликаємо getColorType замість detectColorType
            $colorType = $this->openAIService->getColorType($prompt);

            return response()->json([
                'success' => true,
                'colorType' => $colorType,
                'message' => 'Кольоротип успішно визначено'
            ], 200);
        }
        catch (\Exception $exception){
            \Log::error('Помилка визначення кольоротипу: ' . $exception->getMessage());

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function getColorType(){
        $colorTypes = [
            [
                'name' => 'Весна',
                'description' => 'Теплий та світлий кольоротип з золотистими відтінками. Підходять персикові, коралові, золотисті кольори.',
                'palette' => ['#FFB07A', '#FFD700', '#98FB98', '#FFA07A', '#F0E68C'],
                'bgColor' => '#FFB07A',
                'bgColorDark' => '#FF8C42',
                'recommendedColors' => ['orange', 'yellow', 'coral', 'green'],
                'tips' => [
                    'Обирайте теплі та яскраві кольори',
                    'Уникайте чорного та сірого',
                    'Персиковий та золотистий - ваші кольори'
                ]
            ],
            [
                'name' => 'Літо',
                'description' => 'Холодний та м\'який кольоротип з сірими підтонами. Ідеальні пастельні та приглушені кольори.',
                'palette' => ['#B0C4DE', '#DDA0DD', '#F0F8FF', '#E6E6FA', '#D8BFD8'],
                'bgColor' => '#B0C4DE',
                'bgColorDark' => '#6495ED',
                'recommendedColors' => ['blue', 'pink', 'grey', 'violet'],
                'tips' => [
                    'Холодні пастельні відтінки вам підходять',
                    'Рожевий та лавандовий - ідеальний вибір',
                    'Уникайте надто яскравих кольорів'
                ]
            ],
            [
                'name' => 'Осінь',
                'description' => 'Теплий та глибокий кольоротип з золотистими підтонами. Підходять земляні, оранжеві та золотисті відтінки.',
                'palette' => ['#CD853F', '#DAA520', '#B22222', '#8B4513', '#FF6347'],
                'bgColor' => '#CD853F',
                'bgColorDark' => '#A0522D',
                'recommendedColors' => ['orange', 'burgundy', 'yellow', 'green'],
                'tips' => [
                    'Теплі землисті тони - ваша сила',
                    'Гірчичний та теракотовий підкреслять красу',
                    'Уникайте холодних відтінків'
                ]
            ],
            [
                'name' => 'Зима',
                'description' => 'Холодний та контрастний кольоротип. Ідеальні яскраві, чисті кольори та класичні поєднання.',
                'palette' => ['#000080', '#DC143C', '#FFFFFF', '#4169E1', '#8A2BE2'],
                'bgColor' => '#4169E1',
                'bgColorDark' => '#000080',
                'recommendedColors' => ['red', 'navy', 'grey', 'indigo'],
                'tips' => [
                    'Контрастні чисті кольори - ваш стиль',
                    'Чорний, білий, яскраво-червоний підходять',
                    'Уникайте приглушених тонів'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'colorTypes' => $colorTypes
        ], 200);
    }
}

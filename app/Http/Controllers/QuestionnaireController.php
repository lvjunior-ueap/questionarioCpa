<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function show()
    {
        $questionnaire = Questionnaire::where('active', true)
            ->with('questions.options')
            ->firstOrFail();

        return view('questionnaire', compact('questionnaire'));
    }

    public function store(Request $request)
    {
        $response = Response::create([
            'questionnaire_id' => $request->questionnaire_id
        ]);

        foreach ($request->answers as $questionId => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'value' => is_array($value)
                    ? json_encode($value)
                    : $value
            ]);
        }

        return redirect()->route('thanks');
    }
}

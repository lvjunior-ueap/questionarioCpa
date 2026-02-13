<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\Response;
use App\Models\Answer;

class SurveyService
{
    public function getActiveSurvey(): Survey
    {
        return Survey::where('active', true)->firstOrFail();
    }

    public function saveResponse(int $surveyId, int $audienceId, array $answers): Response
    {
        return \DB::transaction(function () use ($surveyId, $audienceId, $answers) {

            $response = Response::create([
                'survey_id' => $surveyId,
                'audience_id' => $audienceId,
            ]);

            foreach ($answers as $questionId => $value) {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'value' => $value,
                ]);
            }

            return $response;
        });
    }
}

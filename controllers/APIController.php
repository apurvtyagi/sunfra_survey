<?php

/**
 * The SurveysController class is a Controller that shows a user a list of surveys
 * in the database.
 *
 * @author Apurv Tyagi
 * @copyright Copyright (c) Netbeans
 */
class APIController extends Controller
{

    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $user = $this->getUserSession();
        $this->assign('user', $user);

        $getToken = isset($request['token']) ? $request['token'] : false;
        $getAction = isset($request['action']) ? $request['action'] : false;


        if (!$getToken) {
            $this->renderResponse('ERROR', 'Token is mandantory to access the API.');
        }

        if (!$getAction) {
            $this->renderResponse('ERROR', 'Please pass the method in the API.');
        }

        switch ($getAction):
            case 'getSurveyDetails':
                $response = [];
                $i = 0;
                $surveys = Survey::queryRecords($this->pdo, ['sort' => 'survey_name']);
                foreach($surveys as $survey){
                    $response[$i]['survey_id'] = $survey->survey_id;
                    $response[$i]['survey_name'] = $survey->survey_name;
                    $questions = Survey::getQuestions($this->pdo, ['survey_id' => $survey->survey_id, 'sort' => 'question_order']);
                    $response[$i]['questions'] = $this->parseQuestions($questions);
                    $getResponses = Survey::getSurveyResponsesAPI($this->pdo,$survey->survey_id,$questions);
                    $response[$i]['responses'] = $getResponses;
                    $i++;
                }
                $this->renderResponse('OK', 'Success',$response);
                break;
        endswitch;
    }
    
    private function parseQuestions(array $questions){
        $parseQuestions = array();
        foreach($questions as $question){
            unset($question->choices,$question->survey_id);
            $parseQuestions[] = $question;
        }
        return $parseQuestions;
    }

    private function renderResponse($status, $message, $data = array())
    {
        $result = array(
            'status' => $status,
            'message' => $message
        );

        if (!empty($data)) {
            $result['data'] = $data;
        }

        echo json_encode($result);
        exit(0);
    }
}

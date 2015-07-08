<?php

namespace FP\Larmo\Agents\WebHookAgent\Services\Travis;

use FP\Larmo\Agents\WebHookAgent\Services\ServiceAbstract;

class TravisData extends ServiceAbstract
{
    public function __construct($data, $requestHeaders = null)
    {
        $this->serviceName = 'travis';
        $this->data = $this->prepareData($data);
    }

    protected function prepareData($data)
    {
        $message = array(
            'type' => 'travis',
            'timestamp' => $data->finished_at,
            'author' => array(
                'name' => $data->committer_name,
                'email' => $data->committer_email
            ),
            'body' => 'The Travis CI build',
            'extras' => array(
                'build_url' => $data->build_url,
                'number_build' => $data->number,
                'type' => $data->type,
                'state' => $data->state,
                'pull_request_number' => $data->pull_request_number ? $data->pull_request_number : '',
                'compare_url' => $data->compare_url ? $data->compare_url : '',
                'repository' => array(
                    'name' => $data->repository->name,
                    'owner' => $data->repository->owner,
                    'branch' => $data->branch
                )
            )
        );

        return array($message);
    }
}

<?php

namespace Mini\Core;

use GuzzleHttp\Exception\GuzzleException;
use Mini\Migrations\ProjectMigration;
use Mini\Model\Project;

class Application
{
    /** @var null The controller */
    private $url_controller = null;

    /** @var null The method (of the above controller), often also named "action" */
    private $url_action = null;

    /** @var array URL parameters */
    private $url_params = array();

    /**
     * "Start" the application:
     * Analyze the URL elements and calls the according controller/method or the fallback
     */
    public function __construct()
    {
        if (!$this->migrate()) {
            return false;
        }

        if (!$this->getProjects()) {
            return false;
        }
        $this->splitUrl();
        if (!$this->url_controller) {
            $page = new \Mini\Controller\HomeController();
            $page->index();

        } elseif (file_exists(APP . 'Controller/' . ucfirst($this->url_controller) . 'Controller.php')) {
            $controller = "\\Mini\\Controller\\" . ucfirst($this->url_controller) . 'Controller';
            $this->url_controller = new $controller();

            if (strpos($this->url_action, 'draw')) {
                $page = new \Mini\Controller\HomeController();
                return $page->getProjects();
            }

            if (method_exists($this->url_controller, $this->url_action) &&
                is_callable(array($this->url_controller, $this->url_action))) {
                if (!empty($this->url_params)) {
                    call_user_func_array(array($this->url_controller, $this->url_action), $this->url_params);
                } else {
                    $this->url_controller->{$this->url_action}();
                }

            } else {
                if (strlen($this->url_action) == 0) {
                    $this->url_controller->index();
                } else {
                    $page = new \Mini\Controller\ErrorController();
                    $page->index();
                }
            }
        } else {
            $page = new \Mini\Controller\ErrorController();
            $page->index();
        }

    }

    private function splitUrl()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $url = trim($_SERVER['REQUEST_URI'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            $this->url_controller = isset($url[0]) ? $url[0] : null;
            $this->url_action = isset($url[1]) ? $url[1] : null;

            unset($url[0], $url[1]);

            $this->url_params = array_values($url);

            // for debugging. uncomment this if you have problems with the URL
//            echo 'Controller: ' . $this->url_controller . '<br>';
//            echo 'Action: ' . $this->url_action . '<br>';
//            echo 'Parameters: ' . print_r($this->url_params, true) . '<br>';
        }
    }

    private function migrate()
    {
        $success = false;
        try {
            $projectMigration = new ProjectMigration();
            $projectMigration->up();
            $success = true;
        } catch (\Exception $e) {
            $page = new \Mini\Controller\ErrorController();
            $page->index($e->getMessage());
        }

        return $success;
    }

    private function getProjects()
    {
        $success = false;
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->get('https://api.freelancehunt.com/v2/projects?filter[skill_id]=1,89,99', [
                'headers' => [
                    'Authorization' => 'Bearer ' . FREELANCE_API_TOKEN
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            foreach ($responseData['data'] as $item) {
                $skills = '';
                foreach ($item['attributes']['skills'] as $skill) {
                    $skills .= $skill['id'].',';
                }
                $project = new Project();
                $project->addProject([
                    'project_id' => $item['id'],
                    'name' => $item['attributes']['name'],
                    'link' => $item['links']['self']['web'],
                    'skills' => $skills,
                    'budget' => isset($item['attributes']['budget']) ? $item['attributes']['budget']['amount'] : 0,
                    'currency' => isset($item['attributes']['budget']) ? $item['attributes']['budget']['currency'] : '',
                    'user_name' => $item['attributes']['employer']['first_name'] . ' ' . $item['attributes']['employer']['last_name'],
                    'user_login' => $item['attributes']['employer']['login']
                ]);
            }
            $success = true;
        } catch (GuzzleException $e) {
            $page = new \Mini\Controller\ErrorController();
            $page->index($e->getMessage());
        }

        return $success;
    }

}

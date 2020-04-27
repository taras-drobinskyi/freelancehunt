<?php


namespace Mini\Model;

use Mini\Core\Model;

class Project extends Model
{
    const FIELDS = [
        'name', 'link', 'budget', 'currency', 'user_name', 'user_login', 'project_id', 'skills'
    ];

    public function getAllProjects()
    {
        $sql = "SELECT * FROM projects";
        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function addProject(array $params)
    {

        if (!$this->checkFields($params)) {
            throw new \Exception('Fields not match');
        }

        try {

            $sql = "INSERT IGNORE INTO projects (name, project_id, link, skills, budget, currency, user_name, user_login) 
                VALUES (:name, :project_id, :link, :skills, :budget, :currency, :user_name, :user_login)";
            $query = $this->db->prepare($sql);
            $parameters = [
                ':project_id' => $params['project_id'],
                ':name' => $params['name'],
                ':link' => $params['link'],
                ':skills' => $params['skills'],
                ':budget' => $params['budget'],
                ':currency' => $params['currency'],
                ':user_name' => $params['user_name'],
                ':user_login' => $params['user_login']
            ];

            // useful for debugging: you can see the SQL behind above construction by using:
//            echo '[ PDO DEBUG ]: ' . Helper::debugPDO($sql, $parameters);
//            exit();

            $query->execute($parameters);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    private function checkFields(array $params)
    {
        foreach ($params as $key => $value) {
            if (!in_array($key,self::FIELDS)){
                return false;
            }
        }

        return true;
    }
}
